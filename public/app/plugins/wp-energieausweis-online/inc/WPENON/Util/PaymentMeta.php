<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class PaymentMeta {
	private static $instance;

	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private $settings = null;
	private $groups = array();

	private function __construct() {
		$this->settings = \WPENON\Util\Settings::instance();

		$this->groups = array(
			'contact' => array(
				'firmenname' => __( 'Firmenname', 'wpenon' ),
				'firmenlogo' => __( 'Firmenlogo', 'wpenon' ),
				'inhaber'    => __( 'Name des Inhabers', 'wpenon' ),
				'strassenr'  => __( 'Straße und Nr.', 'wpenon' ),
				'plz'        => __( 'Postleitzahl', 'wpenon' ),
				'ort'        => __( 'Ort', 'wpenon' ),
				'telefon'    => __( 'Telefonnummer', 'wpenon' ),
				'email'      => __( 'Kontakt-Emailadresse', 'wpenon' ),
			),
			'deposit' => array(
				'kontoinhaber'   => __( 'Kontoinhaber', 'wpenon' ),
				'kontonummer'    => __( 'Kontonummer', 'wpenon' ),
				'bankleitzahl'   => __( 'Bankleitzahl', 'wpenon' ),
				'kreditinstitut' => __( 'Kreditinstitut / Bank', 'wpenon' ),
				'iban'           => __( 'IBAN', 'wpenon' ),
				'bic'            => __( 'BIC (SWIFT)', 'wpenon' ),
				'prefix'         => __( 'Überweisungs-Präfix', 'wpenon' ),
			),
			'legal'   => array(
				'amtsgericht'  => __( 'Amtsgericht', 'wpenon' ),
				'steuernummer' => __( 'Steuernummer', 'wpenon' ),
				'ustid'        => __( 'USt-Identifikationsnummer', 'wpenon' ),
				'glaeubigerid' => __( 'Gläubiger-ID', 'wpenon' ),
			),
		);

		add_action( 'edd_insert_payment', array( $this, '_updateSellerMeta' ), 10, 2 );
		add_action( 'edd_update_edited_purchase', array( $this, '_updateSellerMeta' ), 10, 1 );
		add_action( 'edd_view_order_details_billing_after', array( $this, '_displaySellerMetaForm' ), 10, 1 );
	}

	public function getSellerMeta( $payment_id = null ) {
		$_meta = array();
		if ( $payment_id !== null ) {
			$_meta = get_post_meta( $payment_id, '_wpenon_seller_meta', true );
			if ( ! is_array( $_meta ) ) {
				$_meta = array();
			}
		}

		$meta = array();
		foreach ( $this->groups as $groupslug => $group ) {
			foreach ( $group as $key => $title ) {
				if ( isset( $_meta[ $key ] ) ) {
					$meta[ $key ] = $_meta[ $key ];
				} else {
					$meta[ $key ] = $this->settings->$key;
				}
			}
		}

		return $meta;
	}

	public function getBankAccountInfo( $payment_id = null, $mode = 'html' ) {
		$_meta = array();
		if ( $payment_id !== null ) {
			if ( is_object( $payment_id ) ) {
				$payment_id = $payment_id->ID;
			}
			$_meta = get_post_meta( $payment_id, '_wpenon_seller_meta', true );
			if ( ! is_array( $_meta ) ) {
				$_meta = array();
			}
		}

		$depositfields = array_merge( array(
			'amount' => __( 'Rechnungsbetrag', 'wpenon' ),
		), $this->groups['deposit'] );
		if ( $payment_id ) {
			$_meta['amount'] = edd_currency_filter( edd_format_amount( edd_get_payment_amount( $payment_id ) ), edd_get_payment_currency_code( $payment_id ) );
		} else {
			$_meta['amount'] = '-';
		}

		$depositfields = $this->groups['deposit'];
		if ( $payment_id !== null ) {
			$_meta['depositkey']         = ( isset( $_meta['prefix'] ) ? $_meta['prefix'] : $this->settings->prefix ) . get_the_title( $payment_id );
			$depositfields['depositkey'] = __( 'Verwendungszweck', 'wpenon' );
			unset( $depositfields['prefix'] );
		}

		$account_info = '';

		if ( $mode == 'plain' ) {
			foreach ( $depositfields as $key => $title ) {
				$account_info .= $title . ': ' . ( isset( $_meta[ $key ] ) ? $_meta[ $key ] : $this->settings->$key ) . "\n";
			}
		} elseif ( $mode == 'tabledata' ) {
			$account_info = array(
				'fields' => array(),
				'data'   => array( array() ),
			);
			foreach ( $depositfields as $key => $title ) {
				$account_info['fields'][]        = array( 'key' => $key, 'headline' => $title );
				$account_info['data'][0][ $key ] = isset( $_meta[ $key ] ) ? $_meta[ $key ] : $this->settings->$key;
			}
		} else {
			$account_info .= '<table cellpadding="0" cellspacing="2" border="0">';
			foreach ( $depositfields as $key => $title ) {
				$account_info .= '<tr><td>' . $title . ':</td><td>' . ( isset( $_meta[ $key ] ) ? $_meta[ $key ] : $this->settings->$key ) . '</td></tr>';
			}
			$account_info .= '</table>';
		}

		return $account_info;
	}

	public function getDepositKey( $payment_id, $prefix = null ) {
		if ( $prefix === null ) {
			$seller_meta = $this->getSellerMeta( $payment_id );
			$prefix      = isset( $seller_meta['prefix'] ) ? $seller_meta['prefix'] : '';
		}

		return $prefix . get_the_title( $payment_id );
	}

	public function getPostalCertificateTitles( $payment_id ) {
		$postal_titles = array();

		$cart_details = edd_get_payment_meta_cart_details( $payment_id );

		if ( is_array( $cart_details ) ) {
			foreach ( $cart_details as $item ) {
				if ( edd_has_variable_prices( $item['id'] ) ) {
					$price_id = isset( $item['item_number']['options']['price_id'] ) ? $item['item_number']['options']['price_id'] : 0;
					if ( edd_get_price_option_name( $item['id'], $price_id, $payment_id ) == \WPENON\Util\EDDAdjustments::instance()->getPostalDefaultName() ) {
						$postal_titles[ $item['id'] ] = get_the_title( $item['id'] );
					}
				}
			}
		}

		return $postal_titles;
	}

	public function _updateSellerMeta( $payment_id, $payment_data = array() ) {
		$meta = array();
		if ( isset( $_POST['_wpenon_seller_meta'] ) && is_array( $_POST['_wpenon_seller_meta'] ) ) {
			$meta = array_map( 'trim', $_POST['_wpenon_seller_meta'][0] );
		} else {
			$meta = $this->getSellerMeta( $payment_id );
		}
		update_post_meta( $payment_id, '_wpenon_seller_meta', $meta );
	}

	public function _displaySellerMetaForm( $payment_id ) {
		$meta = $this->getSellerMeta( $payment_id );
		?>
		<div id="wpenon-seller-details" class="postbox">
			<h3 class="hndle">
				<span><?php _e( 'Verkäuferinformationen', 'wpenon' ); ?></span>
			</h3>
			<div class="inside edd-clearfix">

				<div id="edd-order-address">

					<div class="order-data-address">
						<div class="data column-container">
							<div class="column">
								<?php foreach ( $this->groups['contact'] as $key => $title ) : ?>
									<p>
										<strong><?php echo $title; ?></strong><br/>
										<input type="text" name="_wpenon_seller_meta[0][<?php echo $key; ?>]"
										       value="<?php echo esc_attr( $meta[ $key ] ); ?>" class="medium-text"/>
									</p>
								<?php endforeach; ?>
							</div>
							<div class="column">
								<?php foreach ( $this->groups['deposit'] as $key => $title ) : ?>
									<p>
										<strong><?php echo $title; ?></strong><br/>
										<input type="text" name="_wpenon_seller_meta[0][<?php echo $key; ?>]"
										       value="<?php echo esc_attr( $meta[ $key ] ); ?>" class="medium-text"/>
									</p>
								<?php endforeach; ?>
							</div>
							<div class="column">
								<?php foreach ( $this->groups['legal'] as $key => $title ) : ?>
									<p>
										<strong><?php echo $title; ?></strong><br/>
										<input type="text" name="_wpenon_seller_meta[0][<?php echo $key; ?>]"
										       value="<?php echo esc_attr( $meta[ $key ] ); ?>" class="medium-text"/>
									</p>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div><!-- /#edd-order-address -->
			</div><!-- /.inside -->
		</div><!-- /#wpenon-seller-details -->
		<?php
	}
}
