<?php
/*
Plugin Name: WP-PostRatings Migrate
Plugin URI:  https://wordpress.org/plugins/wp-postratings-migrate/
Description: This extension for WP-PostRatings allows to migrate ratings from one post to another.
Version:     1.0.0
Author:      Felix Arntz
Author URI:  https://leaves-and-love.net
License:     GNU General Public License v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: wp-postratings-migrate
Tags:        extension, ratings, post ratings, migration
*/

defined( 'ABSPATH' ) || exit;

/**
 * Plugin main class.
 *
 * @since 1.0.0
 */
class WP_PostRatings_Migrate {
	/**
	 * Plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Nonce action.
	 */
	const NONCE_ACTION = 'wp_postratings_migrate';

	/**
	 * The main instance of the class.
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var WP_PostRatings_Migrate|null
	 */
	private static $instance = null;

	/**
	 * Adds the necessary hooks for the plugin functionality.
	 *
	 * This method must only be called once.
	 * It will not execute on WordPress versions below 4.7.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function add_hooks() {
		/* Bail if WP-PostRatings is not activated. */
		if ( ! defined( 'WP_POSTRATINGS_VERSION' ) ) {
			return;
		}

		add_action( 'wp_ajax_postratings_migrate', array( $this, 'ajax_migrate' ), 10, 0 );
		add_action( 'wp_ajax_postratings_autocomplete', array( $this, 'ajax_autocomplete' ), 10, 0 );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10, 2 );
	}

	/**
	 * Migrates post ratings from one post to another.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param WP_Post|int $source Source post.
	 * @param WP_Post|int $target Target post.
	 * @param bool        $copy   Optional. Whether to copy the ratings instead of moving. Default false.
	 * @return bool|WP_Error True on success, error object on failure.
	 */
	public function migrate( $source, $target, $copy = false ) {
		global $wpdb;

		if ( ! isset( $wpdb->ratings ) ) {
			return new WP_Error( 'postratings-migrate-database-not-configured', __( 'The ratings database table could not be found.', 'wp-postratings-migrate' ) );
		}

		$source = get_post( $source );
		if ( ! $source ) {
			return new WP_Error( 'postratings-migrate-invalid-source', __( 'Invalid source.', 'wp-postratings-migrate' ) );
		}

		$target = get_post( $target );
		if ( ! $target ) {
			return new WP_Error( 'postratings-migrate-invalid-target', __( 'Invalid target.', 'wp-postratings-migrate' ) );
		}

		$source_ratings = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->ratings WHERE rating_postid = %d", $source->ID ) );
		if ( empty( $source_ratings ) ) {
			return new WP_Error( 'postratings-migrate-no-ratings', __( 'The source post does not have any ratings.', 'wp-postratings-migrate' ) );
		}

		$check_field = null;
		$check_field_values = array();

		$logging_method = intval( get_option( 'postratings_logging_method' ) );
		switch ( $logging_method ) {
			case 1:
			case 2:
			case 3:
				$check_field  = 'rating_ip';
				$check_values = $wpdb->get_col( $wpdb->prepare( "SELECT rating_ip FROM $wpdb->ratings WHERE rating_postid = %d", $target->ID ) );
				break;
			case 4:
				$check_field  = 'rating_userid';
				$check_values = $wpdb->get_col( $wpdb->prepare( "SELECT rating_userid FROM $wpdb->ratings WHERE rating_postid = %d", $target->ID ) );
				break;
		}

		$target_users = (int) get_post_meta( $target->ID, 'ratings_users', true );
		$target_score = (int) get_post_meta( $target->ID, 'ratings_score', true );

		$insert_values = array();
		$delete_ids    = array();
		$update_ids    = array();

		foreach ( $source_ratings as $source_rating ) {
			if ( null !== $check_field && in_array( $source_rating->$check_field, $check_field_values ) ) {
				if ( ! $copy ) {
					$delete_ids[] = $source_rating->rating_id;
				}
				continue;
			}

			if ( $copy ) {
				$insert_values[] = $wpdb->prepare( "( %d, %s, %d, %s, %s, %s, %s, %d )", $target->ID, $target->post_title, $source_rating->rating_rating, $source_rating->rating_timestamp, $source_rating->rating_ip, $source_rating->rating_host, $source_rating->rating_username, $source_rating->rating_userid );
			} else {
				$update_ids[] = $source_rating->rating_id;
			}

			$target_users += 1;
			$target_score += $source_rating->rating_rating;
		}

		if ( ! empty( $insert_values ) ) {
			$insert_string = implode( ', ', $insert_values );
			$result = $wpdb->query( "INSERT INTO $wpdb->ratings ( rating_postid, rating_posttitle, rating_rating, rating_timestamp, rating_ip, rating_host, rating_username, rating_userid ) VALUES $insert_string" );
			if ( ! $result ) {
				return new WP_Error( 'postratings-migrate-database-error', __( 'A database error occurred while trying to insert the copied ratings.', 'wp-postratings-migrate' ) );
			}
		}

		if ( ! empty( $update_ids ) ) {
			$update_string = implode( ',', wp_parse_id_list( $update_ids ) );
			$result = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->ratings SET rating_postid = %d, rating_posttitle = %s WHERE rating_id IN ( $update_string )", $target->ID, $target->post_title ) );
			if ( ! $result ) {
				return new WP_Error( 'postratings-migrate-database-error', __( 'A database error occurred while trying to update the moved ratings.', 'wp-postratings-migrate' ) );
			}
		}

		if ( ! empty( $delete_ids ) ) {
			$delete_string = implode( ',', wp_parse_id_list( $delete_ids ) );
			$wpdb->query( "DELETE FROM $wpdb->ratings WHERE rating_id IN ( $delete_string )" );
		}

		$target_average = round( $target_score / $target_users, 2 );

		update_post_meta( $target->ID, 'ratings_users',   $target_users );
		update_post_meta( $target->ID, 'ratings_score',   $target_score );
		update_post_meta( $target->ID, 'ratings_average', $target_average );

		if ( ! $copy ) {
			update_post_meta( $source->ID, 'ratings_users',   0 );
			update_post_meta( $source->ID, 'ratings_score',   0 );
			update_post_meta( $source->ID, 'ratings_average', 0 );
		}

		return true;
	}

	/**
	 * Migrates post ratings through an AJAX request.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function ajax_migrate() {
		$request = wp_unslash( $_REQUEST );

		if ( ! isset( $request['nonce'] ) ) {
			wp_send_json_error( __( 'Missing nonce.', 'wp-postratings-migrate' ) );
		}

		if ( ! check_ajax_referer( self::NONCE_ACTION, 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid nonce.', 'wp-postratings-migrate' ) );
		}

		if ( empty( $request['source_id'] ) ) {
			wp_send_json_error( __( 'Missing source ID.', 'wp-postratings-migrate' ) );
		}

		if ( empty( $request['target_id'] ) ) {
			wp_send_json_error( __( 'Missing target ID.', 'wp-postratings-migrate' ) );
		}

		$source = absint( $request['source_id'] );
		$target = absint( $request['target_id'] );
		$copy   = ( isset( $request['copy'] ) && $request['copy'] ) ? true : false;

		$result = $this->migrate( $source, $target, $copy );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		} else {
			if ( $copy ) {
				$message = sprintf( __( 'Ratings successfully copied from post %1$s to %2$s.', 'wp-postratings-migrate' ), $source, $target );
			} else {
				$message = sprintf( __( 'Ratings successfully moved from post %1$s to %2$s.', 'wp-postratings-migrate' ), $source, $target );
			}

			wp_send_json_success( $message );
		}
	}

	/**
	 * Handles the AJAX autocomplete posts functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function ajax_autocomplete() {
		$request = wp_unslash( $_REQUEST );

		if ( ! isset( $request['nonce'] ) ) {
			wp_die( -1 );
		}

		if ( ! check_ajax_referer( self::NONCE_ACTION, 'nonce', false ) ) {
			wp_die( -1 );
		}

		$exclude   = ! empty( $request['exclude_post_id'] ) ? array( absint( $request['exclude_post_id'] ) ) : array();
		$post_type = ! empty( $request['post_type'] ) ? $request['post_type'] : 'post';

		$posts = get_posts( array(
			'posts_per_page' => 20,
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'post__not_in'   => $exclude,
			's'              => $request['term'],
			'no_found_rows'  => true,
		) );

		$return = array();

		foreach ( $posts as $post ) {
			$return[] = array(
				'label' => $post->post_title,
				'value' => $post->ID,
			);
		}

		wp_die( wp_json_encode( $return ) );
	}

	/**
	 * Adds the meta box for migrating ratings.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string  $post_type Current post type.
	 * @param WP_Post $post      Current post object.
	 */
	public function add_meta_box( $post_type, $post ) {
		if ( ! current_user_can( 'manage_ratings' ) ) {
			return;
		}

		if ( ! get_post_meta( $post->ID, 'ratings_users', true ) && ! get_post_meta( $post->ID, 'ratings_score', true ) ) {
			return;
		}

		wp_enqueue_script( 'wp-postratings-migrate', plugin_dir_url( __FILE__ ) . 'wp-postratings-migrate.js', array( 'jquery-ui-autocomplete', 'wp-util' ), self::VERSION, true );

		add_meta_box( 'wp_postratings_migrate', __( 'Ratings', 'wp-postratings-migrate' ), array( $this, 'render_meta_box' ), null, 'side', 'default' );
	}

	/**
	 * Renders the meta box for migrating ratings.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_meta_box( $post ) {
		if ( function_exists( 'the_ratings' ) ) {
			$template = str_replace( '%RATINGS_IMAGES_VOTE%', '%RATINGS_IMAGES%<br />', stripslashes( get_option( 'postratings_template_vote' ) ) );
			echo expand_ratings_template( $template, $post, null, 0, false );
		}

		?>
		<h3><?php _e( 'Migrate Ratings', 'wp-postratings-migrate' ); ?></h3>

		<p class="post-attributes-label-wrapper">
			<label class="post-attributes-label" for="postratings-migrate-target"><?php _e( 'Target Post', 'wp-postratings-migrate' ); ?></label>
		</p>
		<input type="text" id="postratings-migrate-target" value="" data-post-id="<?php echo $post->ID; ?>" data-post-type="<?php echo esc_attr( $post->post_type ); ?>" data-nonce="<?php echo wp_create_nonce( self::NONCE_ACTION ); ?>" />
		<input type="hidden" id="postratings-migrate-target-value" value="" />

		<p>
			<input type="checkbox" id="postratings-migrate-copy" value="1" />
			<label for="postratings-migrate-copy"><?php _e( 'Copy ratings to this post?', 'wp-postratings-migrate' ); ?></label>
		</p>

		<p class="post-attributes-label-wrapper">
			<button type="button" id="postratings-migrate-submit" disabled="disabled"><?php _e( 'Migrate', 'wp-postratings-migrate' ); ?></button>
		</p>
		<?php
	}

	/**
	 * Returns the main instance of the class.
	 *
	 * It will be instantiated if it does not exist yet.
	 * In case of instantiation, the add_hooks() method will be called.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return WP_PostRatings_Migrate The main instance.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->add_hooks();
		}

		return self::$instance;
	}
}
add_action( 'plugins_loaded', array( 'WP_PostRatings_Migrate', 'instance' ) );
