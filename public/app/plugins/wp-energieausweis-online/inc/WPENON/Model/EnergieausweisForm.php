<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

class EnergieausweisForm {
	private static $instance;

	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private $manager = null;
	private $id = 0;
	private $type = '';
	private $action = '';

	private function __construct() {
		$this->manager = \WPENON\Model\EnergieausweisManager::instance();
	}

	public function handleOverviewPageRequest( $energieausweis = null) {
		$data = array();

		$this->id     = $energieausweis->id;
		$this->type   = $energieausweis->type;
		$this->action = 'overview';

		$thumbnail = array(
			'id'                 => $energieausweis->thumbnail_id,
			'error'              => false,
			'file_field_name'    => 'wpenon_thumbnail_file',
			'upload_button_name' => 'wpenon_thumbnail_upload',
			'delete_button_name' => 'wpenon_thumbnail_delete',
			'nonce_field_name'   => 'wpenon_nonce',
			'nonce_field_value'  => wp_create_nonce( 'wpenon-energieausweis-form' ),
		);

		$base_url = $this->filterURL( $energieausweis->verified_permalink, $this->action, $this->type );

		$data['template']                  = $this->action;
		$data['template_suffix']           = $this->type;
		$data['access_link']               = $base_url;
		$data['action_url']                = $base_url;
		$data['finalized']                 = $energieausweis->isFinalized();
		$data['ordered']                   = $energieausweis->isOrdered();
		$data['paid']                      = $energieausweis->isPaid();
		$data['allow_changes_after_order'] = $this->allowChangesAfterOrder( $energieausweis );
		$data['meta']                      = array(
			'gebauedefoto'		  => $energieausweis->gebauedefoto,
			'email'               => $energieausweis->wpenon_email,
			'type'                => $energieausweis->formatted_wpenon_type,
			'standard'            => $energieausweis->formatted_wpenon_standard,
			'standard_unformatted'=> $energieausweis->wpenon_standard,
			'ausstellungsdatum'   => $data['paid'] ? $energieausweis->formatted_ausstellungsdatum : false,
			'ausstellungszeit'    => $data['paid'] ? $energieausweis->formatted_ausstellungszeit : false,
			'registriernummer'    => $data['paid'] ? $energieausweis->registriernummer : false,
			'adresse_strassenr'   => $energieausweis->adresse_strassenr,
			'adresse_plz'         => $energieausweis->adresse_plz,
			'adresse_ort'         => $energieausweis->adresse_ort,
			'adresse_bundesland'  => $energieausweis->adresse_bundesland,
		); 
		$data['thumbnail']                 = $thumbnail;
		$data['calculations']              = $energieausweis->calculate();
		$data['energy_bar']                = array(
			array(
				'mode'         => $energieausweis->mode,
				'building'     => $energieausweis->building,
				'reference'    => isset( $data['calculations']['reference'] ) ? $data['calculations']['reference'] : 125,
				'value_top'    => isset( $data['calculations']['endenergie'] ) ? $data['calculations']['endenergie'] : false,
				'value_bottom' => isset( $data['calculations']['primaerenergie'] ) ? $data['calculations']['primaerenergie'] : false,
			)
		);
		if ( $energieausweis->building == 'n' ) {
			$data['energy_bar'][] = array(
				'mode'         => $energieausweis->mode . 's',
				'building'     => $energieausweis->building,
				'reference'    => isset( $data['calculations']['s_reference'] ) ? $data['calculations']['s_reference'] : 25,
				'value_top'    => isset( $data['calculations']['s_endenergie'] ) ? $data['calculations']['s_endenergie'] : false,
				'value_bottom' => isset( $data['calculations']['s_primaerenergie'] ) ? $data['calculations']['s_primaerenergie'] : false,
			);
		}
		$data['efficiency_class'] = isset( $data['calculations']['endenergie'] ) ? wpenon_get_class( $data['calculations']['endenergie'], $this->type ) : false;
		$data['edit_url']         = add_query_arg( 'action', 'edit', $base_url );
		$data['editoverview_url'] = add_query_arg( 'action', 'editoverview', $base_url );
		$data['buy_url']          = ( $data['finalized'] || $data['ordered'] ) ? add_query_arg( 'action', 'purchase', $base_url ) : false;
		$data['pdf_url']          = $data['finalized'] ? add_query_arg( 'action', 'pdf-view', $base_url ) : false;

		$data['purchase_function'] = ( $data['finalized'] && ! $data['ordered'] ) ? 'edd_download_shortcode' : false;

		return apply_filters( 'wpenon_overview_page_data', $data, $energieausweis );
	}

	public function handleEditPageRequest( $energieausweis ) {
		$data = array();

		$this->id     = $energieausweis->id;
		$this->type   = $energieausweis->type;
		$this->action = 'edit';

		$skip_field_validation = $energieausweis->isOrdered() && ! $energieausweis->isPaid();

		$errors   = array();
		$warnings = array();

		if ( ! $skip_field_validation ) {
			$schema = $energieausweis->getSchema();

			if ( $this->_isEnergieausweisPostRequest() ) {
				$this->_verifyNonceField();

				$schema->validateFields( $_POST, $energieausweis );
			}

			$errors   = $schema->getErrors( $energieausweis );
			$warnings = $schema->getWarnings( $energieausweis );
		}

		// allow to switch the Energieausweis type
		if ( ! $energieausweis->isOrdered() && $this->_isEnergieausweisPostRequest() && isset( $_POST['wpenon_type'] ) && $this->type != $_POST['wpenon_type'] ) {
			$this->_verifyNonceField();

			$new_type    = wp_unslash( $_POST['wpenon_type'] );
			$valid_types = \WPENON\Model\EnergieausweisManager::getAvailableTypes();

			if ( isset( $valid_types[ $new_type ] ) ) {
				$energieausweis->type = $new_type;
				$this->type           = $energieausweis->type;

				$schema = $energieausweis->getSchema();
				$schema->validateFields( $_POST, $energieausweis );

				$errors   = $schema->getErrors( $energieausweis );
				$warnings = $schema->getWarnings( $energieausweis );
			}
		}

		$base_url = $this->filterURL( $energieausweis->verified_permalink, $this->action, $this->type );

		$data['template']                  = $this->action;
		$data['template_suffix']           = $this->type;
		$data['access_link']               = $base_url;
		$data['action_url']                = add_query_arg( 'action', $this->action, $base_url );
		$data['finalized']                 = $energieausweis->isFinalized();
		$data['ordered']                   = $energieausweis->isOrdered();
		$data['paid']                      = $energieausweis->isPaid();
		$data['allow_changes_after_order'] = $this->allowChangesAfterOrder( $energieausweis );
		$data['errors']                    = $errors;
		$data['warnings']                  = $warnings;
		$data['eingabesupport']            = isset( $_POST['wpenon_eingabesupport'] ) ? $_POST['wpenon_eingabesupport']: '';
		$data['schema']                    = ( ! $data['ordered'] || $data['paid'] ) ? $schema->get( $energieausweis, $this->_getActiveTab(), true ) : array();
		$data['additional']                = ( ! $data['ordered'] || $data['paid'] ) ? \WPENON\Model\Schema::parseFields( $this->_mergeAdditionalFields( array(
			'_wpenon_progress' => array(
				'type'    => 'hidden',
				'default' => implode( ',', $energieausweis->get_progress() ),
			),
		) ) ) : array();

		return apply_filters( 'wpenon_edit_page_data', $data, $energieausweis );
	}

	public function handleEditoverviewPageRequest( $energieausweis ) {
		$data = array();

		$this->id     = $energieausweis->id;
		$this->type   = $energieausweis->type;
		$this->action = 'editoverview';

		$errors   = array();
		$warnings = array();

		$schema = $energieausweis->getSchema();

		$base_url = $this->filterURL( $energieausweis->verified_permalink, $this->action, $this->type );

		$data['template']                  = $this->action;
		$data['template_suffix']           = $this->type;
		$data['access_link']               = $base_url;
		$data['action_url']                = add_query_arg( 'action', $this->action, $base_url );
		$data['finalized']                 = $energieausweis->isFinalized();
		$data['ordered']                   = $energieausweis->isOrdered();
		$data['paid']                      = $energieausweis->isPaid();
		$data['edit_url']                  = add_query_arg( 'action', 'edit', $base_url );
		$data['allow_changes_after_order'] = $this->allowChangesAfterOrder( $energieausweis );
		$data['schema']                    = $schema->get( $energieausweis, $this->_getActiveTab(), true );

		$data['purchase_function'] = ( $data['finalized'] && ! $data['ordered'] ) ? 'edd_download_shortcode' : false;

		return apply_filters( 'wpenon_editoverview_page_data', $data, $energieausweis );
	}

	public function handlePurchasePageRequest( $energieausweis ) {
		$data = array();

		$this->id     = $energieausweis->id;
		$this->type   = $energieausweis->type;
		$this->action = 'purchase';

		$base_url = $this->filterURL( $energieausweis->verified_permalink, $this->action, $this->type );

		$data['template']                  = $this->action;
		$data['template_suffix']           = $this->type;
		$data['access_link']               = $base_url;
		$data['action_url']                = add_query_arg( 'action', $this->action, $base_url );
		$data['finalized']                 = $energieausweis->isFinalized();
		$data['ordered']                   = $energieausweis->isOrdered();
		$data['paid']                      = $energieausweis->isPaid();
		$data['allow_changes_after_order'] = $this->allowChangesAfterOrder( $energieausweis );
		$data['payment']                   = $data['ordered'] ? $energieausweis->getPayment() : null;
		$data['bank_account_data']         = \WPENON\Util\PaymentMeta::instance()->getBankAccountInfo( $data['payment'], 'tabledata' );
		$data['receipt_url']               = $data['payment'] !== null ? add_query_arg( 'action', 'receipt-view', $base_url ) : false;

		$data['receipt_function'] = $data['payment'] !== null ? 'wpenon_receipt_shortcode' : false;

		$data['purchase_function'] = ( $data['finalized'] && ! $data['ordered'] ) ? 'edd_download_shortcode' : false;

		$data['payments'] = array();

		$customer = new \EDD_Customer( $energieausweis->wpenon_email );
		if ( ! empty( $customer->id ) ) {
			$payment_ids      = explode( ',', $customer->payment_ids );
			$data['payments'] = edd_get_payments( array( 'post__in' => $payment_ids ) );
		}

		return apply_filters( 'wpenon_purchase_page_data', $data, $energieausweis );
	}

	public function handleCreatePageRequest( $type ) {
		$data = array();

		$this->type   = $type;
		$this->action = 'create';

		$schema = $this->getCreateSchema( $type );

		$values           = new \stdClass();
		$values->errors   = array();
		$values->warnings = array();

		$errors   = array();
		$warnings = array();

		if ( $this->_isEnergieausweisPostRequest() ) {
			$this->_verifyNonceField();

			$schema->validateFields( $_POST, $values, true );

			if ( count( $values->errors ) == 0 && count( $values->warnings ) == 0 ) {
				$custom_meta = get_object_vars( $values );
				unset( $custom_meta['errors'] );
				unset( $custom_meta['warnings'] );

				$energieausweis = $this->manager->create( $this->type, '', $custom_meta );

				if ( is_a( $energieausweis, '\WPENON\Model\Energieausweis' ) ) {
					$base_url = $this->filterURL( $energieausweis->verified_permalink, 'overview', $this->type );
					wp_redirect( $base_url );
					exit;
				} else {
					$error_messages[] = __( 'Es ist ein Systemfehler aufgetreten, wodurch der Energieausweis nicht erzeugt werden konnte.', 'wpenon' ) . ' ' . __( 'Bitte wenden Sie sich an den Administrator.', 'wpenon' );
				}
			}

			$errors   = $schema->getErrors( $values );
			$warnings = $schema->getWarnings( $values );
		}

		$base_url = $this->filterURL( get_permalink(), 'create', $this->type );

		$data['template']        = 'create';
		$data['template_suffix'] = $this->type;
		$data['action_url']      = $base_url;
		$data['errors']          = $errors;
		$data['warnings']        = $warnings;
		$data['schema']          = $schema->get( $values, $this->_getActiveTab(), true, true );
		$data['additional']      = \WPENON\Model\Schema::parseFields( $this->_mergeAdditionalFields() );

		return apply_filters( 'wpenon_create_page_data', $data, null );
	}

	public function getCreateSchema( $type = '', $standard = '' ) {
		return new \WPENON\Model\Schema( array(
			'basis' => array(
				'title'  => __( 'Energieausweis-Basisdaten', 'wpenon' ),
				'active' => true,
				'groups' => array(
					'basisdaten' => array(
						'title'  => __( 'Energieausweis-Basisdaten', 'wpenon' ),
						'fields' => \WPENON\Model\EnergieausweisManager::getPrivateFields( $type, $standard, true ),
					),
				),
			),
		) );
	}

	private function allowChangesAfterOrder( $energieausweis ) {
		$settings = \WPENON\Util\Settings::instance();

		$allow_changes_after_order = $settings->allow_changes_after_order ? true : false;

		return apply_filters( 'wpenon_allow_changes_after_order', $allow_changes_after_order, $energieausweis );
	}

	private function filterURL( $url, $template, $template_suffix, $context = '' ) {
		return apply_filters( 'wpenon_filter_url', $url, $template, $template_suffix, $context );
	}

	private function _isEnergieausweisPostRequest() {
		return ( isset( $_POST ) && count( $_POST ) > 0 );
	}

	private function _getActiveTab() {
		if ( $this->_isEnergieausweisPostRequest() && isset( $_POST['wpenon_active_tab'] ) && ! empty( $_POST['wpenon_active_tab'] ) ) {
			return $_POST['wpenon_active_tab'];
		}

		return false;
	}

	private function _mergeAdditionalFields( $custom_fields = array() ) {
		$fields = array(
			'wpenon_active_tab' => array(
				'type'    => 'hidden',
				'default' => $this->_getActiveTab(),
			),
			'wpenon_nonce'      => array(
				'type'    => 'hidden',
				'default' => wp_create_nonce( 'wpenon-energieausweis-form' ),
			),
		);

		$fields = apply_filters( 'wpenon_additional_fiels', $fields );

		return array_merge( $fields, $custom_fields );
	}

	private function _verifyNonceField() {
		if ( isset( $_POST['wpenon_nonce'] ) && wp_verify_nonce( $_POST['wpenon_nonce'], 'wpenon-energieausweis-form' ) ) {
			return true;
		} else {
			wp_die( sprintf( __( 'Die Anfrage stammt aus einer unsicheren Quelle %s.', 'wpenon' ), $_POST['wpenon_nonce'] ), __( 'Ungültige Anfrage', 'wpenon' ) );
		}

		return false;
	}
}
