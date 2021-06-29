<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

use DateTime;
use Enon\Enon\Standards_Config;

class Energieausweis {
	/**
	 * Id.
	 *
	 * @var int
	 */
	private $id = 0;

	/**
	 * Post object.
	 *
	 * @var array|\WP_Post|null
	 */
	private $post = null;

	/**
	 * Errors.
	 *
	 * @var array|bool|mixed
	 */
	public $errors = array();

	/**
	 * Warnings.
	 *
	 * @var array|bool|mixed
	 */
	public $warnings = array();

	/**
	 * Progress.
	 *
	 * @var array|mixed
	 */
	private $progress = array();

	/**
	 * Ordered.
	 *
	 * @var null
	 */
	private $ordered = null;

	/**
	 * Paid.
	 *
	 * @var null
	 */
	private $paid = null;

	/**
	 * Energieausweis Schema
	 *
	 * @var \WPENON\Model\Schema|null
	 */
	public $schema = null;

	/**
	 * Schema name
	 * 
	 * @var string
	 */
	public $schema_name;

	/**
	 * Calculation data.
	 *
	 * @var array
	 */
	private $calculations = array();

	/**
	 * Owner data.
	 *
	 * @var null
	 */
	private $owner_data = null;

	public function __construct( $id ) {
		$this->id = $id;

		$this->post = get_post( $this->id );

		$this->errors   = \WPENON\Util\Storage::getErrors( $this->id );
		$this->warnings = \WPENON\Util\Storage::getWarnings( $this->id );
		$this->progress = get_post_meta( $this->id, '_wpenon_progress' );

		add_action( 'edd_update_payment_status', array( $this, '_checkOrderedPaidStatus' ) );

		/**
		 * Switching to new GEG if needed
		 */
		$date = new DateTime( date('Y-m-d' ) );
		$dateSwitch = new DateTime('2021-05-17');
		$this->schema_name = get_post_meta( $this->id, 'wpenon_standard', true );

		if( $date >= $dateSwitch && $this->schema_name !== 'enev2021-03' && ! $this->isOrdered() ) {
			update_post_meta( $this->id, '_finalized', false );
			update_post_meta( $this->id, 'wpenon_standard', 'enev2021-02' );
		}

		$this->_loadSchema();
	}

	public function __set( $field, $value ) {
		if ( $field == 'thumbnail_id' ) {
			\WPENON\Util\ThumbnailHandler::set( $this, $value );
		} elseif ( in_array( $field, array( 'register_response', 'data_response' ) ) ) {
			update_post_meta( $this->id, '_wpenon_' . $field, $value );
		} else {
			if ( $this->schema->isField( 'wpenon_' . $field ) ) {
				$field = 'wpenon_' . $field;
			}
			if ( $this->schema->isField( $field ) ) {
				$old_value = get_post_meta( $this->id, $field, true );

				if ( (string) $value !== (string) $old_value ) {
					update_post_meta( $this->id, $field, $value );

					if ( $field == 'wpenon_email' ) {
						$this->_loadOwnerData();
					} elseif ( in_array( $field, array( 'wpenon_type', 'wpenon_standard' ) ) ) {
						if ( 'wpenon_type' === $field ) {
							\WPENON\Util\EDDAdjustments::instance()->_setPriceDefaults( $this );
						}
						$this->_loadSchema();
					}
				}
			}
		}

		$this->calculations = array();
	}

	public function __get( $field ) {
		if ( strtolower( $field ) == 'id' ) {
			return $this->id;
		}

		if ( $field == 'thumbnail_id' ) {
			return \WPENON\Util\ThumbnailHandler::get( $this );
		}

		if ( in_array( $field, array( 'register_response', 'data_response' ) ) ) {
			return get_post_meta( $this->id, '_wpenon_' . $field, true );
		}

		if ( $field == 'verified_permalink' ) {
			return \WPENON\Model\EnergieausweisManager::getVerifiedPermalink( $this->id );
		}

		if ( $field == 'permalink' ) {
			return get_permalink( $this->id );
		}

		if ( $field == 'mode' || $field == 'building' ) {
			$type = get_post_meta( $this->id, 'wpenon_type', true );
			if ( $field == 'building' ) {
				return substr( $type, 1, 1 );
			}

			return substr( $type, 0, 1 );
		}

		if ( $field == 'adresse' ) {
			return get_post_meta( $this->id, 'adresse_strassenr', true ) . ', ' . get_post_meta( $this->id, 'adresse_plz', true ) . ' ' . get_post_meta( $this->id, 'adresse_ort', true );
		}

		$formatted = false;
		if ( strpos( $field, 'formatted_' ) === 0 ) {
			$field     = substr( $field, 10 );
			$formatted = true;
		}

		if ( $this->schema->isField( 'wpenon_' . $field ) ) {
			$field = 'wpenon_' . $field;
		}
		if ( $this->schema->isField( $field ) ) {
			$field_args = $this->schema->getField( $field );

			$ret = get_post_meta( $this->id, $field, true );
			if ( $ret === '' ) {
				$ret = $field_args['default'];
			} else {
				switch ( $field_args['type'] ) {
					case 'checkbox':
						$ret = \WPENON\Util\Parse::boolean( $ret );
						break;
					case 'multiselect':
					case 'multibox':
						$ret = \WPENON\Util\Parse::arr( $ret );
						break;
					case 'float':
						$ret = \WPENON\Util\Parse::float( $ret );
						break;
					case 'float_length':
					case 'float_length_wall':
						$ret = \WPENON\Util\Parse::float_length( $ret );
						break;
					case 'int':
						$ret = \WPENON\Util\Parse::int( $ret );
						break;
					default:
						// Compatibility with old entries
						if ( 'ausstellungsdatum' === $field ) {
							$ret = date( 'Y-m-d', strtotime( $ret ) );
						}
				}
			}

			if ( $formatted ) {
				$ret = $this->schema->getFormattedFieldValue( $field, $ret );
			}

			return $ret;
		}

		if ( $formatted ) {
			$field = 'formatted_' . $field;
		}

		if ( isset( $this->post->$field ) ) {
			return $this->post->$field;
		}
		$field = 'post_' . $field;
		if ( isset( $this->post->$field ) ) {
			return $this->post->$field;
		}

		return null;
	}

	public function __isset( $field ) {
		return $this->__get( $field ) !== null;
	}

	public function get_progress() {
		return $this->progress;
	}

	public function is_progressed( $field ) {
		return in_array( $field, $this->progress, true );
	}

	public function add_to_progress( $field ) {
		if ( in_array( $field, $this->progress, true ) ) {
			return;
		}

		$this->progress[] = $field;
		add_post_meta( $this->id, '_wpenon_progress', $field );
	}

	public function remove_from_progress( $field ) {
		$key = array_search( $field, $this->progress, true );
		if ( false === $key ) {
			return;
		}

		array_splice( $this->progress, $key, 1 );
		delete_post_meta( $this->id, '_wpenon_progress', $field );
	}

	public function getCreationDate() {
		return $this->post->post_date;
	}

	public function calculate() {
		if ( $this->isFinalized() ) {
			if ( count( $this->calculations ) < 1 ) {
				$this->calculations = \WPENON\Model\EnergieausweisManager::loadCalculations( $this );
			}

			return $this->calculations;
		}

		return array();
	}

	public function getPDF( $output_mode = 'I', $preview = false ) {
		$old_standards = [
			'enev2013', 
			'enev2017',
			'enev2019',
			'enev2020-01',
			'enev2020-02',
		];

		if( in_array( $this->standard, $old_standards ) ) {
			$pdf = new \WPENON\Model\EnergieausweisPDF( sprintf( __( 'Energieausweis-%s', 'wpenon' ), $this->post->post_title ), $this->type, $this->standard, $preview );
		} else {
			$pdf = new \WPENON\Model\EnergieausweisPDFGEG( sprintf( __( 'Energieausweis-%s', 'wpenon' ), $this->post->post_title ), $this->type, $this->standard, $preview );
		}
		
		if ( $this->isFinalized() ) {
			$this->calculate();
			$pdf->create( $this );
		} else {
			$pdf->create( null );
		}

		return $pdf->finalize( $output_mode );
	}

	public function getDataPDF( $output_mode = 'I' ) {
		$pdf = new \WPENON\Model\EnergieausweisDataPDF( sprintf( __( 'Energieausweis-Daten-%s', 'wpenon' ), $this->post->post_title ), $this->type, $this->standard );
		$pdf->create( $this );

		return $pdf->finalize( $output_mode );
	}

	public function getDataAnonymizedPDF( $output_mode = 'I' ) {
		$pdf = new \WPENON\Model\EnergieausweisDataPDF( sprintf( __( 'Energieausweis-Daten-%s', 'wpenon' ), $this->post->post_title ), $this->type, $this->standard, true );
		$pdf->create( $this );

		return $pdf->finalize( $output_mode );
	}

	public function getXML( $mode, $output_mode = 'I', $raw = false ) {
		$standardsConfig = new Standards_Config();
		$old_schemas = array_keys( $standardsConfig->getStandardsBefore( '2021-05-17' ) );

		/** XML until Enev GEG update */
		if( in_array( $this->schema_name, $old_schemas ) ) {
			$xml = new \WPENON\Model\EnergieausweisXML( $mode, sprintf( __( 'Energieausweis-%1$s-%2$s', 'wpenon' ), $this->post->post_title, ucfirst( $mode ) ), $this->type, $this->standard );
			if ( $this->isFinalized() ) {
				$this->calculate();
				$xml->create( $this, $raw );
			} else {
				$xml->create( null, $raw );
			}

			return $xml->finalize( $output_mode );
		}

		/** 
		 * XML output with GEG 2020 
		 */
		$energieausweis = $this; // Data needed for Template
		$xmlFile        = $standardsConfig->getEnevXMLTemplatefile( $this->mode, $mode );#

		ob_start();		
		require $xmlFile;
		$xml = ob_get_clean();
		$xml = preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~','$1',$xml); // Removing empty spaces

		switch ( $output_mode ) {
			case 'S':
				return $xml;
			case 'D':
			case 'I':
			default:
				$disposition = 'inline';
				if ( $output_mode == 'D' ) {
					$disposition = 'attachment';
				}
				header( 'Content-Type: text/xml; charset=utf-8' );
				header( 'Content-Disposition: ' . $disposition . '; filename="' . $this->title . '.xml"' );
				echo $xml;
				exit;
		}		
	}

	public function getFieldOptionLabels( $field_slug ) {
		$field = $this->schema->getField( $field_slug );
		if ( isset( $field['options'] ) ) {
			return $field['options'];
		}

		return array();
	}

	public function getFieldOptionLabel( $field_slug, $value = null ) {
		$field = $this->schema->getField( $field_slug, $this );
		$value = $value ? $value : ( isset( $field['value'] ) ? $field['value'] : '' );
		if ( isset( $field['options'] ) && isset( $field['options'][ $value ] ) ) {
			return $field['options'][ $value ];
		}

		return '';
	}

	public function getOwnerData( $field = '' ) {
		if ( $this->owner_data === null ) {
			$this->_loadOwnerData();
		}
		if ( ! empty( $field ) ) {
			if ( isset( $this->owner_data[ $field ] ) ) {
				return $this->owner_data[ $field ];
			} else {
				return false;
			}
		}

		return $this->owner_data;
	}

	/**
	 * Get Schema
	 *
	 * @return \WPENON\Model\Schema
	 */
	public function getSchema() {
		return $this->schema;
	}

	public function checkValidationErrors( $errors, $warnings = array() ) {
		$this->errors   = $errors;
		$this->warnings = $warnings;

		$status = false;
		if ( count( $errors ) == 0 ) {
			$status = true;
		}

		if ( $status ) {
			update_post_meta( $this->id, '_finalized', $status );
		} else {
			delete_post_meta( $this->id, '_finalized' );
		}
	}

	public function isFinalized() {
		return (bool) get_post_meta( $this->id, '_finalized', true );
	}

	public function isOrdered( $force = false ) {
		if ( $this->ordered === null || $force ) {
			$this->_checkOrderedPaidStatus();
		}

		return $this->ordered;
	}

	public function isPaid( $force = false ) {
		if ( $this->paid === null || $force ) {
			$this->_checkOrderedPaidStatus();
		}

		return $this->paid;
	}

	public function isRegistered() {
		return ! empty( trim( get_post_meta( $this->id, 'registriernummer', true ) ) );
	}

	public function isDataSent() {
		return (bool) get_post_meta( $this->id, '_datasent', true );
	}

	public function getPayment() {
		$payments = array();

		$active_statuses = apply_filters( 'wpenon_payment_active_statuses', array( 'pending', 'publish' ) );

		$payment_ids = get_post_meta( $this->id, '_wpenon_attached_payment_id' );

		if ( count( $payment_ids ) > 0 ) {
			$payments = edd_get_payments( array(
				'output'   => 'payments',
				'status'   => $active_statuses,
				'post__in' => array_map( 'absint', $payment_ids ),
			) );
			if ( empty( $payments ) ) {
				$payments = array();
				foreach ( $payment_ids as $payment_id ) {
					$payment = edd_get_payment( $payment_id );
					if ( $payment ) {
						$payments[] = $payment;
					}
				}
			}
		} else {
			return null;
		}

		// preferably return a complete payment (in case multiple payments exist for an Energieausweis due to an error)
		foreach ( $payments as $payment ) {
			if ( edd_is_payment_complete( $payment->ID ) ) {
				return $payment;
			}
		}

		if ( isset( $payments[0] ) ) {
			return $payments[0];
		}

		return null;
	}

	public function _checkOrderedPaidStatus() {
		$this->ordered = null;
		$this->paid    = null;
		$payment       = $this->getPayment();
		if ( $payment !== null ) {
			$this->ordered = true;
			if ( edd_is_payment_complete( $payment->ID ) ) {
				$this->paid = true;
			}
		}
		if ( $this->ordered === null ) {
			$this->ordered = false;
		}
		if ( $this->paid === null ) {
			$this->paid = false;
		}
	}

	public function duplicate() {
		global $wpdb;

		$post_id = wp_insert_post( array(
			'post_type'    => 'download',
			'post_status'  => 'publish',
			'post_title'   => \WPENON\Model\EnergieausweisManager::_generateTitle( null, false ),
			'post_content' => '',
		) );
		if ( ! $post_id ) {
			return false;
		}

		$post_id = (int) $post_id;

		$type     = get_post_meta( $this->id, 'wpenon_type', true );
		$standard = $this->standard;

		update_post_meta( $post_id, 'wpenon_type', $type );
		update_post_meta( $post_id, 'wpenon_standard', $standard );

		$defaults = \WPENON\Util\EDDAdjustments::instance()->_getPriceDefaults( $type );
		foreach ( $defaults as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

		$meta_insert = array(
			$wpdb->prepare( '( %d, %s, %s )', $post_id, 'ausstellungsdatum', '' ),
			$wpdb->prepare( '( %d, %s, %s )', $post_id, 'registriernummer', '' ),
			$wpdb->prepare( '( %d, %s, %s )', $post_id, 'wpenon_secret', md5( microtime() ) ),
		);

		$whitelist = array( '_thumbnail_id', '_wpenon_progress', '_finalized' );
		$blacklist = array( '_registered', '_datasent' );

		$private_fields = \WPENON\Model\EnergieausweisManager::getPrivateFields();
		foreach ( $private_fields as $field_slug => $field_data ) {
			if ( ! empty( $field_data['set_once'] ) ) {
				$whitelist[] = $field_slug;
			} else {
				$blacklist[] = $field_slug;
			}
		}

		$metadata = get_post_meta( $this->id );
		foreach ( $metadata as $meta_key => $meta_values ) {
			if ( in_array( $meta_key, $blacklist, true ) ) {
				continue;
			}

			if ( ! $this->schema->isField( $meta_key ) && ! in_array( $meta_key, $whitelist, true ) ) {
				continue;
			}

			foreach ( $meta_values as $meta_value ) {
				if ( is_array( $meta_value ) ) {
					$meta_value = serialize( $meta_value );
				}

				$meta_insert[] = $wpdb->prepare( '( %d, %s, %s )', $post_id, $meta_key, $meta_value );
			}
		}

		$meta_insert = implode( ', ', $meta_insert );
		$wpdb->query( "INSERT INTO {$wpdb->postmeta} ( post_id, meta_key, meta_value ) VALUES {$meta_insert}" );

		return $post_id;
	}

	private function _loadOwnerData() {
		$this->owner_data = array( 'email' => get_post_meta( $this->id, 'wpenon_email', true ) );

		$customer = EDD()->customers->get_customer_by( 'email', $this->owner_data['email'] );
		if ( is_object( $customer ) ) {
			$this->owner_data = get_object_vars( $customer );

			$name                      = explode( ' ', $this->owner_data['name'] );
			$this->owner_data['first'] = $name[0];
			$this->owner_data['last']  = isset( $name[1] ) ? $name[1] : '';

			$customer_meta = \WPENON\Util\CustomerMeta::instance()->getCustomerMeta( $this->owner_data['id'] );

			$this->owner_data = array_merge( $this->owner_data, $customer_meta );
		}
	}

	private function _loadSchema() {
		$this->schema = \WPENON\Model\EnergieausweisManager::loadSchema( $this );
	}
}
