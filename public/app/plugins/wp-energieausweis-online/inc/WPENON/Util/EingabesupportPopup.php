<?php
/**
 * Class EingabesupportPopup
 *
 * @package WPENON
 * @version 1.0.0
 * @author Sven Wagener <sven@awesome.ug>
 */

namespace WPENON\Util;

use WPENON\Model\Energieausweis;

class EingabesupportPopup {

	/**
	 * Class instance.
	 *
	 * @var EingabesupportPopup
	 *
	 * @since 1.0.0
	 *
	 * @todo Singleton should go.
	 */
	private static $instance;

	/**
	 * Instatiating Object.
	 *
	 * @return EingabesupportPopup
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
	 * EingabesupportPopup constructor.
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

		add_filter( 'wpenon_zusatzoptionen_settings', array( $this, 'zusatzoptionen_settings' ), 10, 1 );
		add_filter( 'wpenon_custom_fees', array( $this, 'add_custom_fees' ), 10, 1 );

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
	 * @param int \WPENON\Model\Energieausweis $energieausweis Energieausweis Object.
	 *
	 * @since 1.0.0
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure.
	 */
	public function update_fields( $energieausweis ) {

		$eingabesupport = filter_var( $_POST['wpenon_eingabesupport'], FILTER_VALIDATE_BOOLEAN, array( 'flags' => FILTER_NULL_ON_FAILURE ) );

		if ( null === $eingabesupport ) {
			return false;
		}

		$this->send_mail( $energieausweis->ID );

		return update_post_meta( $energieausweis->ID, 'eingabesupport', $eingabesupport );
	}


	private function send_mail( $energieausweis_id ) {
		$from_name   = edd_get_option( 'from_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
		$from_email  = edd_get_option( 'from_email', get_bloginfo( 'admin_email' ) );
		$heading     = edd_get_option( 'eingabesupport_heading', __( 'Eingabesupport', 'easy-digital-downloads' ) );

		$subject     = _( 'Eingabesupport', 'wpenon' );
		$message     = $this->get_email_body( $energieausweis_id );

		$emails = EDD()->emails;
		$emails->__set( 'from_name' , $from_name );
		$emails->__set( 'from_email', $from_email );
		$emails->__set( 'heading'   , $heading );

		$headers = apply_filters( 'edd_receipt_headers', $emails->get_headers(), 0, array() );
		$emails->__set( 'headers', $headers );

		// $emails->send( 'support@immoticket24.de', $subject, $message );
		$emails->send( 'sven@awesome.ug', $subject, $message );
	}

	private function get_email_body( $energieausweis_id ) {
		$body = 'Folgender Kunde hat den Eingabesupport gebucht:';

		return $body;
	}

	/**
	 * Checks if Eingabesupport was selected.
	 *
	 * @since 1.0.0
	 *
	 * @param int $energieausweis_id
	 *
	 * @return bool
	 */
	public function is_selected( $energieausweis_id ) {
		if ( true === get_post_meta( $energieausweis_id, 'eingabesupport', true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Adding fees
	 *
	 * @since 1.0.0
	 *
	 * @param array $fees
	 *
	 * @return array $fees
	 */
	public function add_custom_fees( $fees ) {
		$eingabesupport = array(
			'id'             => 'eingabesupport',
			'label'          => $this->get_default( 'label' ),
			'amount'         => $this->get_default( 'price' ),
			'description_cb' => 'energieausweis_zusatzoption_eingabesupport_info',
			'email_note'     => '',
		);

		$fees = array_merge( $fees, $eingabesupport );

		return $fees;
	}

	/**
	 * Adding Zusatzoptionen Settings
	 *
	 * @param $settings
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function zusatzoptionen_settings( $settings ) {
		$eingabesupport_settings = array(
			'eingabesupport' => array(
				'title'  => 'Eingabesupport',
				'fields' => array(
					'eingabesupport_label'       => array(
						'title'    => 'Name',
						'type'     => 'text',
						'default'  => $this->get_default( 'label'  ),
						'required' => true,
					),
					'eingabesupport_description' => array(
						'title'    => 'Beschreibung',
						'type'     => 'wysiwyg',
						'default'  => $this->get_default( 'description' ),
						'required' => true,
						'rows'     => 8,
					),
					'eingabesupport_price'       => array(
						'title'    => 'Preis',
						'type'     => 'number',
						'default'  => $this->get_default( 'price' ),
						'required' => true,
						'min'      => 0.01,
						'step'     => 0.01,
					),
					'eingabesupport_order'       => array(
						'title'       => 'Reihenfolge',
						'description' => 'Je kleiner die Nummer, desto höher die Priorität der Zusatzoption in der Auflistung.',
						'type'        => 'number',
						'default'     => $this->get_default( 'order' ),
						'required'    => true,
						'min'         => 1,
						'step'        => 1,
					),
				),
			),
		);

		$settings = array_merge( $settings, $eingabesupport_settings );

		return $settings;
	}

	/**
	 * Get default values
	 *
	 * @param string $type
	 *
	 * @return bool|string|int
	 */
	private function get_default( $type ) {
		switch ( $type ) {
			case 'label':
				return __( 'NEU: Professioneller Eingabesupport!', 'wpenon' );
			case 'description':
				return  __( '<p>Bei Auswahl dieser Option nehmen wir nach Abschluss Ihrer Bestellung mit Ihnen Kontakt auf, um den Wert Ihrer Immobilie zu ermitteln und Ihnen hierfür eine Verkaufsempfehlung zu geben. Die Bewertung Ihrer Immobilie ist kostenfrei.</p>', 'wpenon' );
			case 'price':
				return 34.95;
			case 'order':
				return 6;
			default:
				return false;
		}
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
				var $form = $( '#wpenon-generate-form' );

				function onFormSubmitEingabesupport( e ) {
					$('#wp-enon-eingabehilfe-popup').dialog({
						resizable: false,
						height: "auto",
						width: 600,
						modal: true,
						buttons: {
							"<?php _e( 'Eingabesupport Buchen', 'wp_enon' ); ?>": function () {
								$('#wpenon_expertencheck').val('true');
								$(this).dialog("close");
								$form.off( 'submit', onFormSubmitEingabesupport );
								$form.submit();
							},
							"<?php _e( 'Abbrechen', 'wp_enon' ); ?>": function () {
								$(this).dialog("close");
								$form.off( 'submit', onFormSubmitEingabesupport );
								$form.submit();
							}
						}
					});

					e.preventDefault();
					return true;
				}

				$form.on( 'submit', onFormSubmitEingabesupport );
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
