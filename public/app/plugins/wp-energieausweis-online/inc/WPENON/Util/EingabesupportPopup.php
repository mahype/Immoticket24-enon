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
		add_filter( 'eddcf_filter_custom_fees', array( $this, 'filter_custom_fees' ), 10, 2 );

		add_action( 'edd_before_checkout_cart', array( $this, 'add_fees_to_cart' ) );

		add_action( 'wpenon_after_content', array( $this, 'print_html' ), 10, 2 );
		add_action( 'wpenon_after_content', array( $this, 'print_dialog_scripts' ), 10, 2 );
		// add_action( 'edd_checkout_form_top', array( $this, 'print_checkout_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add own fields to form on Energeausweis creation.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $fields Fields.
	 *
	 * @return array $fields Merged Fields.
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
	 * Saving values after creating Energieausweis.
	 *
	 * @since 1.0.0
	 *
	 * @param \WPENON\Model\Energieausweis $energieausweis Energieausweis Object.
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure.
	 */
	public function update_fields( $energieausweis ) {
		$eingabesupport = filter_var( $_POST['wpenon_eingabesupport'], FILTER_VALIDATE_BOOLEAN, array( 'flags' => FILTER_NULL_ON_FAILURE ) );

		if ( null === $eingabesupport ) {
			return false;
		}

		if( $eingabesupport ) {
			$this->send_mail( $energieausweis );
		}

		return update_post_meta( $energieausweis->id, 'eingabesupport', $eingabesupport );
	}

	/**
	 * Adding fees to cart if selected before.
	 *
	 * @since 1.0.0
	 */
	public function add_fees_to_cart(){
		if( ! $this->has_selected_eingabesupport() ) {
			return;
		}

		$fees = eddcf_get_custom_fees();
		$fee = array_intersect_key( $fees[ 'eingabesupport' ], array_flip( array( 'id', 'amount', 'label', 'type' ) ) );
		EDD()->fees->add_fee( $fee );
	}

	/**
	 * Sending Mail to support@immoticket24.de.
	 *
	 * @since 1.0.0
	 *
	 * @param \WPENON\Model\Energieausweis $energieausweis Energieausweis Object.
	 */
	private function send_mail( $energieausweis ) {
		$from_name   = edd_get_option( 'from_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
		$from_email  = edd_get_option( 'from_email', get_bloginfo( 'admin_email' ) );
		$heading     = edd_get_option( 'eingabesupport_heading', __( 'Eingabesupport', 'easy-digital-downloads' ) );

		$subject     = __( 'Eingabesupport', 'wpenon' );
		$message     = $this->get_email_body( $energieausweis );

		$emails = EDD()->emails;
		$emails->__set( 'from_name' , $from_name );
		$emails->__set( 'from_email', $from_email );
		$emails->__set( 'heading'   , $heading );

		$headers = apply_filters( 'edd_receipt_headers', $emails->get_headers(), 0, array() );
		$emails->__set( 'headers', $headers );

		$emails->send( 'support@immoticket24.de', $subject, $message );
	}

	/**
	 * Get Email Body.
	 *
	 * @since 1.0.0
	 *
	 * @param \WPENON\Model\Energieausweis $energieausweis Energieausweis Object.
	 *
	 * @return string                      $body           Email body.
	 */
	private function get_email_body( $energieausweis ) {
		$body = 'Folgender Kunde hat den Eingabesupport gebucht:
		
Energieausweis: ' . $energieausweis->post_title . '
Gebäudeanschrift: ' . $energieausweis->adresse . '
Email-Adresse: ' . $energieausweis->getOwnerData( 'wpenon_email' ) . '

URL:            ' . admin_url( 'post.php?post=' . $energieausweis->id . '&action=edit', 'https' );
		
		return $body;
	}

	/**
	 * Checks if Eingabesupport was selected.
	 *
	 * @since 1.0.0
	 *
	 * @param int $energieausweis_id Energieausweis ID.
	 *
	 * @return bool True if eingabesuppport was selected, false if not.
	 */
	public function has_selected_eingabesupport( $energieausweis_id = '' ) {
		// Getting ID by cart content id no ID is given
		if( empty( $energieausweis_id ) ) {
			$items = edd_get_cart_content_details();

			foreach ( $items as $item ) {
				$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis( $item['id'] );
				if ( ! $energieausweis ) {
					continue;
				}

				if( $this->has_selected_eingabesupport( $item['id'] ) ) {
					$energieausweis_id = $item['id'];
					break;
				}
			}
		}

		if( empty( $energieausweis_id ) ) {
			return false;
		}

		if ( true === (bool) get_post_meta( $energieausweis_id, 'eingabesupport', true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Adding fees
	 *
	 * @since 1.0.0
	 *
	 * @param array $fees EDD fees.
	 *
	 * @return array $fees Filtered EDD fees.
	 */
	public function add_custom_fees( $fees ) {
		$eingabesupport = array(
			'id'             => 'eingabesupport',
			'label'          => $this->get_default( 'label' ),
			'amount'         => $this->get_default( 'price' ),
			'description_cb' => 'energieausweis_zusatzoption_eingabesupport_info',
			'email_note'     => '',
		);

		$fees = array_merge( $fees, array( $eingabesupport  ) );

		return $fees;
	}

	/**
	 * Filter Fees before showing.
	 *
	 * @since 1.0.0
	 *
	 * @param array     $fees EDD fees in an array.
	 * @param \EDD_Cart $cart EDD Cart object.
	 *
	 * @return array    $fees EDD fees in an array.
	 */
	public function filter_custom_fees( $fees, $cart ) {
		$found = false;

		foreach ( $cart->get_contents_details() as $item ) {
			$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis( $item['id'] );
			if ( ! $energieausweis ) {
				continue;
			}

			if( $this->has_selected_eingabesupport( $item['id'] ) ) {
				$found = true;
			}
		}

		if( ! $found ) {
			if( array_key_exists( 'eingabesupport', $fees ) ) {
				unset( $fees['eingabesupport'] );
			}

			return $fees;
		}

		return $fees;
	}

	/**
	 * Adding Zusatzoptionen Settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Settings Zusatzoptionen.
	 *
	 * @return array $settings Settings Zusatzoptionen.
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
	 * Get default values.
	 *
	 * @since 1.0.0
	 *
	 * @param string           $type     Type label, description, price or order.
	 *
	 * @return bool|string|int           Value of default type.
	 */
	private function get_default( $type ) {
		switch ( $type ) {
			case 'label':
				return __( 'Professioneller Eingabesupport', 'wpenon' );
			case 'description':
				return  __( '<p>Mit Eingabe-Support von Anfang bis Ende! Damit werden alle Ihre Fragen geklärt. Wir unterstützen Sie telefonisch bei der Eingabe der Gebäudedaten von Anfang der Eingabe bis Bestellabschluss.</p>', 'wpenon' );
			case 'price':
				return 9.95;
			case 'order':
				return 6;
			default:
				return false;
		}
	}



	/**
	 * Print html after WPENON content.
	 *
	 * @since 1.0.0
	 *
	 * @param \WPENON\Model\Energieausweis $energieausweis Energieausweis object.
	 * @param \WPENON\View\FrontendBase $view Frontend base view.
	 */
	public function print_html( $energieausweis, $view ) {
		if ( $view->get_template_slug() !== 'create' ) {
			return;
		}
		?>
		<div id="wp-enon-eingabehilfe-popup" class="modal fade" role="dialog">
			<div class="modal-dialog" style="margin-top:140px;">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title"><?php _e( 'NEU: Telefonischer Eingabe-Support bis Bestellabschluss', 'wpenon' ); ?></h4>
					</div>
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/banner/images/support.jpg" class="modal-banner" />
					<div class="modal-body">
						<?php _e( 'Gerne unterstützen wir Sie telefonisch bei der Eingabe der Gebäudedaten von Anfang der Eingabe bis Bestellabschluss. Jetzt für 9,95 Euro buchen.', 'wp_enon' ); ?>
					</div>
					<div class="modal-footer">
						<button id="wp-enon-eingabehilfe-no" type="button" class="btn btn-default"><?php _e( 'Ohne Eingabe-Support weiter', 'wp_enon' ); ?></button>
						<button id="wp-enon-eingabehilfe-yes" type="button" class="btn btn-primary"><?php _e( 'Mit Eingabe-Support weiter', 'wp_enon' ); ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Print scripts after WPENON content.
	 *
	 * @since 1.0.0
	 *
	 * @param \WPENON\Model\Energieausweis $energieausweis Energieausweis object.
	 * @param \WPENON\View\FrontendBase $view Frontend base view.
	 */
	public function print_dialog_scripts( $energieausweis, $view ) {
		if ( $view->get_template_slug() !== 'create' ) {
			return;
		}

		?>
		<script>
			jQuery(document).ready(function ($) {
				var $form = $( '#wpenon-generate-form' );
				var $modal = $( '#wp-enon-eingabehilfe-popup' );
				var $gdprAcceptance = $( '#gdpr_acceptance' );

				if ( ! $form.length || ! $modal.length || ! $gdprAcceptance.length ) {
					return;
				}

				$modal.modal({
					show: false
				});



				function onFormSubmitEingabesupport( e ) {
					setTimeout( function() {
						$modal.modal( 'show' );
					}, 1000 );

					$modal.css( 'z-index', 1050 );

					e.preventDefault();
					return false;
				}

				$form.on( 'submit', onFormSubmitEingabesupport );

				$( '#wp-enon-eingabehilfe-yes' ).on( 'click', function() {
					$('#wpenon_eingabesupport').val('true');
					$form.off( 'submit', onFormSubmitEingabesupport );
					$modal.modal( 'hide' );
					$form.submit();
				});

				$( '#wp-enon-eingabehilfe-no' ).on( 'click', function() {
					$('#wpenon_eingabesupport').val('false');
					$form.off( 'submit', onFormSubmitEingabesupport );
					$modal.modal( 'hide' );
					$form.submit();
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
