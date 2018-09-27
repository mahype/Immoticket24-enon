<?php
/**
 * Banner widget.
 *
 * @package immoticketenergieausweis
 */

class Immoticketenergieausweis_Banner_Widget extends WP_Widget {
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_energieausweis_banner',
			'description' => __( 'Ein Energieausweis Online Erstellen-Banner.', 'immoticketenergieausweis' ),
		);
		parent::__construct( 'energieausweis_banner', __( 'Energieausweis-Banner', 'immoticketenergieausweis' ), $widget_ops );
	
		if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Aktuelles Angebot', 'immoticketenergieausweis' ) : $instance['title'], $instance, $this->id_base );
		$target = ! empty( $instance['target'] ) ? $instance['target'] : home_url( '/' );

		echo $args['before_widget'];

		echo $args['before_title'] . $title . $args['after_title'];

		echo '<div id="energieausweis-canvas-wrap">';
		echo '<a href="' . esc_url( $target ) . '">';
		echo '<canvas id="energieausweis-canvas" width="250" height="350" style="background-color:#ffffff"></canvas>';
		echo '</a>';
		echo '</div>';

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['target'] = sanitize_text_field( $new_instance['target'] );

		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'target' => home_url( '/' ) ) );
		$title = sanitize_text_field( $instance['title'] );
		$target = $instance['target'];

		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('target'); ?>"><?php _e( 'Target URL:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('target'); ?>" name="<?php echo $this->get_field_name('target'); ?>" type="url" value="<?php echo esc_url( $target ); ?>" /></p>
		<?php
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'energieausweis-banner-createjs', IMMOTICKETENERGIEAUSWEIS_THEME_URL . '/assets/banner/createjs-2015.11.26.min.js', array(), IMMOTICKETENERGIEAUSWEIS_THEME_VERSION, true );
		wp_enqueue_script( 'energieausweis-banner-main', IMMOTICKETENERGIEAUSWEIS_THEME_URL . '/assets/banner/250_350.js', array( 'energieausweis-banner-createjs' ), IMMOTICKETENERGIEAUSWEIS_THEME_VERSION, true );
		wp_enqueue_script( 'energieausweis-banner-init', IMMOTICKETENERGIEAUSWEIS_THEME_URL . '/assets/banner/init.js', array( 'energieausweis-banner-createjs', 'energieausweis-banner-main', 'jquery' ), IMMOTICKETENERGIEAUSWEIS_THEME_VERSION, true );
	}
}
