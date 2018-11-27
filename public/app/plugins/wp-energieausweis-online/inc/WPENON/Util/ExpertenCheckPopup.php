<?php
/**
 * @package WPENON
 * @version 1.0.0
 * @author Sven Wagener <sven@awesome.ug>
 */

namespace WPENON\Util;

class ExpertenCheckPopup {

	private static $instance;

	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {
		$this->init_hooks();
	}

	public function init_hooks() {
		add_action( 'wpenon_after_content', array( $this, 'print_html' ) );
		add_action( 'wpenon_after_content', array( $this, 'print_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_save_experten_check_value', array( $this, 'save_value' ) );
	}

	public function print_html() {
		?>
		<div id="wp-enon-experten-check-popup" title="<?php _e( 'Expertencheck', 'wpenon' ); ?>">
			<p><?php _e( 'Lassen Sie sich von einem Experten helfen! Wir bieten Ihnen für nur 30€ Hilfe von unseren Profis!', 'wp_enon' ); ?></p>
		</div>
		<?php
	}

	public function print_scripts() {
		?>
		<script>
			jQuery(document).ready(function($) {
				$('#wp-enon-experten-check-popup').dialog({
					resizable: false,
					height: "auto",
					width: 600,
					modal: true,
					buttons: {
						"<?php _e( 'Expertencheck buchen', 'wp_enon' ); ?>": function() {
							$( this ).dialog( "close" );
						},
						"<?php _e( 'Abbrechen', 'wp_enon' ); ?>": function() {
							$( this ).dialog( "close" );
						}
					}
				});
			});
		</script>
		<?php
	}

	public function save_value() {
		wp_die();
	}

	public function enqueue_scripts() {
		if( is_admin() ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
	}
}
