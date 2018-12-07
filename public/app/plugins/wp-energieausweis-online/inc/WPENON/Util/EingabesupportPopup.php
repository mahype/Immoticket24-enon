<?php
/**
 * Class EingabehilfePopup
 *
 * @package WPENON
 * @version 1.0.0
 * @author Sven Wagener <sven@awesome.ug>
 */

namespace WPENON\Util;

use WPENON\Model\Energieausweis;

class EingabehilfePopup {

	/**
	 * Class instance.
	 *
	 * @var EingabehilfePopup
	 *
	 * @since 1.0.0
	 *
	 * @todo Singleton should go.
	 */
	private static $instance;

	/**
	 * Instatiating Object.
	 *
	 * @return EingabehilfePopup
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
	 * EingabehilfePopup constructor.
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
		add_action( 'wpenon_energieausweis_create', array( $this, 'update_fields' ), 10, 1 );
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
		<div id="wp-enon-eingabehilfe-popup" title="<?php _e( 'Eingabesupport', 'wpenon' ); ?>">
			<p><?php _e( 'Eingabe-Support von Anfang bis Ende! Damit werden alle Ihre Fragen geklärt. Wir unterstützen Sie telefonisch bei der Eingabe der Gebäudedaten von Anfang der Eingabe bis Bestellabschluss. Jetzt für 34,95 Euro buchen.', 'wp_enon' ); ?></p>
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
			'wpenon_eingabesupport' => array(
				'type'    => 'hidden',
				'default' => 'false',
			),
		) );
	}

	/**
	 * Saving values
	 *
	 * @param int $energieausweis Energieausweis ID.
	 *
	 * @since 1.0.0
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure.
	 */
	public function update_fields( $energieausweis_id ) {
		if( ! is_int( $energieausweis_id ) ) {
			return false;
		}

		$eingabesupport = filter_var( $_POST['wpenon_eingabesupport'], FILTER_VALIDATE_BOOLEAN, array('flags' => FILTER_NULL_ON_FAILURE) );

		if( NULL === $eingabesupport ) {
			return false;
		}

		return update_post_meta( $energieausweis_id, 'eingabesupport', $eingabesupport );
	}

	/**
	 * Checks if Eingabesupport was selected.
	 *
	 * @param int $energieausweis_id
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_selected( $energieausweis_id ) {
		if( true === get_post_meta( $energieausweis_id, 'eingabesupport', true ) ) {
			return true;
		}

		return false;
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

				$('#wp-enon-eingabehilfe-popup').dialog({
					resizable: false,
					height: "auto",
					width: 600,
					modal: true,
					buttons: {
						"<?php _e( 'Eingabesupport Buchen', 'wp_enon' ); ?>": function () {
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
