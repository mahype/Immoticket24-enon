<?php
/**
 * Class for adding reseller CPT.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\WP
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;

use Enon_Reseller\Models\Reseller_Data;

/**
 * Class Task_CPT_Reseller.
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\Tasks\Core
 */
class Add_CPT_Reseller implements Task, Actions, Filters {

	/**
	 * AffiliateWP constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'init', array( $this, 'add' ) );
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 100 );

		add_action( 'manage_reseller_posts_custom_column', array( $this, 'set_custom_column_values' ), 10, 2 );
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'manage_reseller_posts_columns', array( $this, 'set_posts_columns' ), 1000, 1 );
	}

	/**
	 * Removing meta Boxes.
	 *
	 * @since 1.0.0
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'wpseo_meta', 'reseller', 'normal' );
	}

	/**
	 * Adding post type.
	 *
	 * @since 1.0.0
	 */
	public function add() {
		$labels = array(
			'name'               => _x( 'Reseller', 'post type general name', 'enon' ),
			'singular_name'      => _x( 'Reseller', 'post type singular name', 'enon' ),
			'menu_name'          => _x( 'Reseller', 'admin menu', 'enon' ),
			'name_admin_bar'     => _x( 'Reseller', 'add new on admin bar', 'enon' ),
			'add_new'            => _x( 'Hinzufügen', 'reseller', 'enon' ),
			'add_new_item'       => __( 'Neuen Reseller hinzufügen', 'enon' ),
			'new_item'           => __( 'Neuer reseller', 'enon' ),
			'edit_item'          => __( 'Reseller bearbeiten', 'enon' ),
			'view_item'          => __( 'Reseller ansehen', 'enon' ),
			'all_items'          => __( 'Alle Reseller', 'enon' ),
			'search_items'       => __( 'Reseller suchen', 'enon' ),
			'parent_item_colon'  => __( 'Parent Reseller:', 'enon' ),
			'not_found'          => __( 'Kein Reseller gefunden.', 'enon' ),
			'not_found_in_trash' => __( 'Kein Reseller im Papierkorb gefunden.', 'enon' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Beschreibung.', 'enon' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'reseller' ),
			'capability_type'	 => array('reseller','resellers'),
			'map_meta_cap'       => false,
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'menu_icon'          => 'dashicons-businessman',
			'supports'           => array( 'thumbnail' ),
		);

		register_post_type( 'reseller', $args );
	}

	/**
	 * Set Post columns.
	 *
	 * @param array $columns Post columns.
	 *
	 * @return array $columns Filtered post columns.
	 *
	 * @since 1.0.0
	 */
	public function set_posts_columns( $columns ) {
		unset( $columns['title'] );
		unset( $columns['author'] );
		unset( $columns['date'] );
		unset( $columns['wpseo-links'] );
		unset( $columns['ratings'] );

		$columns['company_name']  = __( 'Company Name', 'enon' );
		$columns['contact_name']  = __( 'Contact Name', 'enon' );
		$columns['contact_email'] = __( 'Contact Email', 'enon' );
		$columns['iframe_url']    = __( 'Iframe URL', 'enon' );

		return $columns;
	}

	/**
	 * Set post column values.
	 *
	 * @param string $column Column name.
	 * @param int    $post_id Post id.
	 *
	 * @since 1.0.0
	 */
	public function set_custom_column_values( $column, $post_id ) {
		$reseller_data = new Reseller_Data( $post_id );

		$post_status = get_post_status( $post_id );

		switch ( $column ) {
			case 'company_name':
				echo sprintf( '<a href="%s">%s</a>', get_edit_post_link( $post_id ), esc_attr( $reseller_data->general->get_company_name() ) ); // phpcs:ignore
				break;
			case 'contact_name':
				echo esc_attr( $reseller_data->general->get_contact_name() );
				break;
			case 'contact_email':
				echo esc_attr( $reseller_data->general->get_contact_email() );
				break;
			case 'iframe_url':
				if ( 'publish' === $post_status ) {
					// translators: Link to Bedarfsausweis.
					$bedarfs_link = sprintf( __( '[<a href="%s" target="_blank">Bedarfsausweis</a>]' ), $reseller_data->get_iframe_bedarfsausweis_url() );
					// translators: Link to Verbrauchsausweis.
					$verbrauchs_link = sprintf( __( '[<a href="%s" target="_blank">Verbrauchsausweis</a>]' ), $reseller_data->get_iframe_verbrauchsausweis_url() );
					echo $bedarfs_link . ' ' . $verbrauchs_link;
				} else {
					echo esc_attr( __( 'Reseller have to be published before getting URL.', 'enon' ) );
				}
				break;
		}
	}
}
