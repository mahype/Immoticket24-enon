<?php
/**
 * Class ExpertenCheckPopup
 *
 * @package WPENON
 * @version 1.0.0
 * @author Sven Wagener <sven@awesome.ug>
 */

namespace WPENON\Util;

class ExpertenCheckPopup {

	/**
	 * Class instance.
	 *
	 * @var ExpertenCheckPopup
	 *
	 * @since 1.0.0
	 *
	 * @todo Singleton should go.
	 */
	private static $instance;

	/**
	 * Instatiating Object.
	 *
	 * @return ExpertenCheckPopup
	 *
	 * @since 1.0.0
	 *
	 * @todo Singleton should go.
	 */
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * ExpertenCheckPopup constructor.
	 *
	 * @since 1.0.0
	 *
	 * @todo Singleton should go.
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialising Hooks.
	 *
	 * @since 1.0.0
	 */
	public function init_hooks() {
		add_action( 'wpenon_additional_fiels', array( $this, 'additional_fields' ), 10, 2 );
		add_action( 'wpenon_after_content', array( $this, 'print_html' ), 10, 2 );
		add_action( 'wpenon_after_content', array( $this, 'print_scripts' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Print html after WPENON content.
	 *
	 * @param \WPENON\Model\Energieausweis $energieausweis Energieausweis object.
	 * @param \WPENON\View\FrontendBase $view Frontend base view.
	 *
	 * @since 1.0.0
	 */
	public function print_html( $energieausweis, $view ) {
		if ( $view->get_template_slug() !== 'create' ) {
			return;
		}
		?>
		<div id="wp-enon-experten-check-popup" title="<?php _e( 'Expertencheck', 'wpenon' ); ?>">
			<p><?php _e( 'Lassen Sie sich von einem Experten helfen! Wir bieten Ihnen für nur 19,95€ Hilfe von unseren Profis!', 'wp_enon' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Adds own fields
	 *
	 * @param array $fields Given Fields
	 *
	 * @return array Merged Fields.
	 *
	 * @since 1.0.0
	 */
	public function additional_fields( $fields ) {
		return array_merge( $fields, array(
			'wpenon_expertencheck' => array(
				'type'    => 'hidden',
				'default' => 'false',
			),
		) );
	}

	/**
	 * Print scripts after WPENON content.
	 *
	 * @param \WPENON\Model\Energieausweis $energieausweis Energieausweis object.
	 * @param \WPENON\View\FrontendBase $view Frontend base view.
	 *
	 * @since 1.0.0
	 */
	public function print_scripts( $energieausweis, $view ) {
		if ( $view->get_template_slug() !== 'create' ) {
			return;
		}

		?>
		<script>
			jQuery(document).ready(function ($) {

				$('#wp-enon-experten-check-popup').dialog({
					resizable: false,
					height: "auto",
					width: 600,
					modal: true,
					buttons: {
						"<?php _e( 'Expertencheck buchen', 'wp_enon' ); ?>": function () {
							$('#wpenon_expertencheck').val('true');
							$(this).dialog("close");
						},
						"<?php _e( 'Abbrechen', 'wp_enon' ); ?>": function () {
							$(this).dialog("close");
						}
					}
				});
			});
		</script>
		<?php
	}

	/**
	 * Enqueueing Scripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		if ( is_admin() ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
	}
}
