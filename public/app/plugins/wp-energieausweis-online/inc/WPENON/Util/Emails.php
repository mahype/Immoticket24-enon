<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class Emails {

	private static $instance;

	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'edd_add_email_tags', array( $this, '_addEmailTags' ) );

		add_action( 'edd_email_receipt_download_title', array( $this, '_addEmailEnergieausweisLink' ), 10, 4 );
		add_filter( 'edd_email_receipt_no_downloads_message', '__return_empty_string' );

		add_action( 'wpenon_energieausweis_create', array( $this, 'sendConfirmationEmail' ) );

		add_action( 'edd_insert_payment', array( $this, '_maybeSendOrderConfirmationEmail' ), 10, 2 );

		add_filter( 'edd_email_preview_template_tags', array( $this, '_processPreviewEmailTags' ) );

		add_filter( 'edd_admin_notices_disabled', array( $this, '_hackAdminNotices' ), 10, 2 );

		add_filter( 'edd_settings_emails', array( $this, '_addAdditionalEmailSettings' ) );

		add_filter( 'edd_email_footer_text', array( $this, '_adjustEmailFooterText' ) );
	}

	public function _addEmailTags() {
		edd_add_email_tag( 'bank_account_info', __( 'Ihre Bankdaten aufgelistet', 'wpenon' ), 'text/html' == EDD()->emails->get_content_type() ? array(
			$this,
			'_emailTagBankAccountInfo'
		) : array( $this, '_emailTagBankAccountInfoPlain' ) );
		edd_add_email_tag( 'pdf_link', __( 'Fügt einen Link hinzu, damit Benutzer direkt auf die PDF-Rechnung zugreifen können', 'wpenon' ), 'text/html' == EDD()->emails->get_content_type() ? array(
			$this,
			'_emailTagPDFLink'
		) : array( $this, '_emailTagPDFLinkPlain' ) );
		edd_add_email_tag( 'pdf_link_hide_common', __( 'Fügt einen Link zur PDF-Rechnung hinzu, in angepasster Variante ohne Header und Footer', 'wpenon' ), 'text/html' == EDD()->emails->get_content_type() ? array(
			$this,
			'_emailTagPDFHideCommonLink'
		) : array( $this, '_emailTagPDFHideCommonLinkPlain' ) );
		edd_add_email_tag( 'inline_receipt', __( 'Fügt eine Auflistung der Bestellung inklusive Preisen direkt in die Email ein', 'wpenon' ), 'text/html' == EDD()->emails->get_content_type() ? array(
			$this,
			'_emailTagInlineReceipt'
		) : array( $this, '_emailTagInlineReceiptPlain' ) );
		edd_add_email_tag( 'manual_notice_seller', __( 'Zeigt eine Mitteilung (für den Verkäufer) an, dass die Bestellung mit einer manuellen Zahlungsweise getätigt wurde (falls zutreffend)' ), array(
			$this,
			'_emailTagManualNoticeSeller'
		) );
		edd_add_email_tag( 'customer_contact_data', __( 'Zeigt die Kontaktdaten zum Kunden an.', 'wpenon' ), 'text/html' == EDD()->emails->get_content_type() ? array(
			$this,
			'_emailTagCustomerContactData'
		) : array( $this, '_emailTagCustomerContactDataPlain' ) );
		if ( WPENON_POSTAL ) {
			edd_add_email_tag( 'postal_notice_customer', __( 'Zeigt eine Mitteilung (für den Kunden) an, dass als Sendung per Post bestellte Energieausweise in Kürze versendet werden (falls zutreffend)', 'wpenon' ), array(
				$this,
				'_emailTagPostalNoticeCustomer'
			) );
			edd_add_email_tag( 'postal_notice_seller', __( 'Zeigt eine Mitteilung (für den Verkäufer) an, dass bestimmte Energieausweise zusätzlich als Sendung per Post bestellt worden sind (falls zutreffend)', 'wpenon' ), array(
				$this,
				'_emailTagPostalNoticeSeller'
			) );
		}
	}

	public function _emailTagBankAccountInfo( $payment_id, $mode = 'html' ) {
		return \WPENON\Util\PaymentMeta::instance()->getBankAccountInfo( $payment_id, $mode );
	}

	public function _emailTagBankAccountInfoPlain( $payment_id ) {
		return $this->_emailTagBankAccountInfo( $payment_id, 'plain' );
	}

	public function _emailTagPDFLink( $payment_id, $mode = 'html' ) {
		$cart_details = edd_get_payment_meta_cart_details( $payment_id );

		if ( is_array( $cart_details ) && count( $cart_details ) > 0 ) {
			$item = $cart_details[0];

			$url = \WPENON\Model\EnergieausweisManager::getVerifiedPermalink( $item['id'], 'receipt-view' );
			if ( 'plain' === $mode ) {
				return $url;
			}

			return '<a href="' . esc_url( $url ) . '">' . $url . '</a>';
		}

		return '';
	}

	public function _emailTagPDFLinkPlain( $payment_id ) {
		return $this->_emailTagPDFLink( $payment_id, 'plain' );
	}

	public function _emailTagPDFHideCommonLink( $payment_id, $mode = 'html' ) {
		$cart_details = edd_get_payment_meta_cart_details( $payment_id );

		if ( is_array( $cart_details ) && count( $cart_details ) > 0 ) {
			$item = $cart_details[0];

			$url = add_query_arg( 'hide_common', '1', \WPENON\Model\EnergieausweisManager::getVerifiedPermalink( $item['id'], 'receipt-view' ) );
			if ( 'plain' === $mode ) {
				return $url;
			}

			return '<a href="' . esc_url( $url ) . '">' . $url . '</a>';
		}

		return '';
	}

	public function _emailTagPDFHideCommonLinkPlain( $payment_id ) {
		return $this->_emailTagPDFLink( $payment_id, 'plain' );
	}

	public function _emailTagInlineReceipt( $payment_id, $mode = 'html' ) {
		$payment = get_post( $payment_id );

		$cart = edd_get_payment_meta_cart_details( $payment->ID, true );
		$fees = edd_get_payment_fees( $payment->ID, 'item' );

		$output = '';

		if ( 'plain' == $mode ) {
			if ( $cart ) {
				foreach ( $cart as $key => $item ) {
					$output .= "\t";

					if ( edd_item_quantities_enabled() ) {
						$output .= $item['quantity'] . 'x ';
					}
					$price_id = edd_get_cart_item_price_id( $item );
					$output   .= esc_html( $item['name'] );
					if ( ! is_null( $price_id ) ) {
						$output .= ' - ' . edd_get_price_option_name( $item['id'], $price_id, $payment->ID );
					}

					if ( edd_use_skus() ) {
						$output .= ' (' . __( 'SKU', 'easy-digital-downloads' ) . ': ' . edd_get_download_sku( $item['id'] ) . ')';
					}

					if ( empty( $item['in_bundle'] ) ) {
						$output .= ' - ' . edd_currency_filter( edd_format_amount( $item['subtotal'] ) );
					}

					$output .= "\n";
				}
			}
			if ( $fees ) {
				$output .= "\t" . '----------- ' . "\n";
				foreach ( $fees as $fee ) {
					$fee_subtotal = $fee['amount'] / ( 1.0 + edd_get_tax_rate() );

					$output .= "\t";

					$output .= esc_html( $fee['label'] );

					$output .= ' - ' . edd_currency_filter( edd_format_amount( $fee_subtotal ) );

					$output .= "\n";
				}
			}
			$output .= "\t" . '----------- ' . "\n";
			$output .= "\n";
			$output .= "\t" . __( 'Subtotal', 'easy-digital-downloads' ) . ' - ' . edd_currency_filter( edd_format_amount( edd_get_payment_subtotal( $payment->ID ) ) ) . "\n";
			if ( edd_use_taxes() ) {
				$output .= "\t" . __( 'Tax', 'easy-digital-downloads' ) . ' - ' . edd_currency_filter( edd_format_amount( edd_get_payment_tax( $payment->ID ) ) ) . "\n";
			}
			$output .= "\t" . __( 'Total Price', 'easy-digital-downloads' ) . ' - ' . edd_currency_filter( edd_format_amount( edd_get_payment_amount( $payment->ID ) ) ) . "\n";
		} else {
			ob_start();
			?>
			<table id="email_purchase_receipt" cellpadding="10" cellspacing="0" border="0" style="width:100%;border:0;">
				<thead>
				<th><?php _e( 'Name', 'easy-digital-downloads' ); ?></th>
				<?php if ( edd_use_skus() ) { ?>
					<th><?php _e( 'SKU', 'easy-digital-downloads' ); ?></th>
				<?php } ?>
				<?php if ( edd_item_quantities_enabled() ) : ?>
					<th><?php _e( 'Quantity', 'easy-digital-downloads' ); ?></th>
				<?php endif; ?>
				<th><?php _e( 'Price', 'easy-digital-downloads' ); ?></th>
				</thead>
				<tbody>
				<?php if ( $cart ) : ?>
					<?php foreach ( $cart as $key => $item ) : ?>

						<?php if ( empty( $item['in_bundle'] ) ) : ?>
							<tr>
								<td>
									<?php
									$price_id = edd_get_cart_item_price_id( $item );
									?>
									<div class="edd_purchase_receipt_product_name">
										<?php echo esc_html( $item['name'] ); ?>
										<?php if ( ! is_null( $price_id ) ) : ?>
											<span
												class="edd_purchase_receipt_price_name">&nbsp;&ndash;&nbsp;<?php echo edd_get_price_option_name( $item['id'], $price_id, $payment->ID ); ?></span>
										<?php endif; ?>
									</div>

								</td>
								<?php if ( edd_use_skus() ) : ?>
									<td><?php echo edd_get_download_sku( $item['id'] ); ?></td>
								<?php endif; ?>
								<?php if ( edd_item_quantities_enabled() ) { ?>
									<td><?php echo $item['quantity']; ?></td>
								<?php } ?>
								<td>
									<?php if ( empty( $item['in_bundle'] ) ) : // Only show price when product is not part of a bundle ?>
										<?php echo edd_currency_filter( edd_format_amount( $item['subtotal'] ) ); ?>
									<?php endif; ?>
								</td>
							</tr>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
				<?php if ( ( $fees ) ) : ?>
					<?php foreach ( $fees as $fee ) : ?>
						<?php $fee_subtotal = $fee['amount'] / ( 1.0 + edd_get_tax_rate() ); ?>
						<tr>
							<?php if ( edd_use_skus() ) : ?>
								<td></td>
							<?php endif; ?>
							<?php if ( edd_item_quantities_enabled() ) : ?>
								<td></td>
							<?php endif; ?>
							<td class="edd_fee_label"><?php echo esc_html( $fee['label'] ); ?></td>
							<td class="edd_fee_amount"><?php echo edd_currency_filter( edd_format_amount( $fee_subtotal ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				<tr>
					<?php if ( edd_use_skus() ) : ?>
						<td></td>
					<?php endif; ?>
					<?php if ( edd_item_quantities_enabled() ) : ?>
						<td></td>
					<?php endif; ?>
					<td class="edd_subtotal_label" style="text-align:right;">
						<strong><?php _e( 'Subtotal', 'easy-digital-downloads' ); ?></strong></td>
					<td class="edd_subtotal_amount"><?php echo edd_currency_filter( edd_format_amount( edd_get_payment_subtotal( $payment->ID ) ) ); ?></td>
				</tr>
				<?php if ( edd_use_taxes() ) : ?>
					<tr>
						<?php if ( edd_use_skus() ) : ?>
							<td></td>
						<?php endif; ?>
						<?php if ( edd_item_quantities_enabled() ) : ?>
							<td></td>
						<?php endif; ?>
						<td class="edd_tax_label" style="text-align:right;">
							<strong><?php _e( 'Tax', 'easy-digital-downloads' ); ?></strong></td>
						<td class="edd_tax_amount"><?php echo edd_currency_filter( edd_format_amount( edd_get_payment_tax( $payment->ID ) ) ); ?></td>
					</tr>
				<?php endif; ?>
				<tr>
					<?php if ( edd_use_skus() ) : ?>
						<td></td>
					<?php endif; ?>
					<?php if ( edd_item_quantities_enabled() ) : ?>
						<td></td>
					<?php endif; ?>
					<td class="edd_total_label" style="text-align:right;">
						<strong><?php _e( 'Total Price', 'easy-digital-downloads' ); ?></strong></td>
					<td class="edd_total_amount"><?php echo edd_currency_filter( edd_format_amount( edd_get_payment_amount( $payment->ID ) ) ); ?></td>
				</tr>
				</tbody>
			</table>
			<?php
			$output = ob_get_clean();
		}

		return $output;
	}

	public function _emailTagInlineReceiptPlain( $payment_id ) {
		return $this->_emailTagInlineReceipt( $payment_id, 'plain' );
	}

	public function _emailTagManualNoticeSeller( $payment_id ) {
		if ( ! edd_is_payment_complete( $payment_id ) ) {
			$gateway  = edd_get_payment_gateway( $payment_id );
			$supports = edd_get_gateway_supports( $gateway );

			$manual_notice = '';

			if ( in_array( 'manual_handling', $supports ) ) {
				$payment_overview_url = add_query_arg( array(
					'post_type' => 'download',
					'page'      => 'edd-payment-history'
				), admin_url( 'edit.php' ) );
				$manual_notice        = sprintf( __( 'Die Bestellung wurde durch die manuelle Zahlungsweise %1$s getätigt. Achten Sie bitte daher auf einen entsprechenden Zahlungseingang mit dem Verwendungszweck %2$s. Anschließend können Sie die Bestellung unter folgender Seite als bezahlt markieren: %3$s', 'wpenon' ), edd_get_gateway_checkout_label( $gateway ), \WPENON\Util\PaymentMeta::instance()->getDepositKey( $payment_id ), $payment_overview_url );
			}

			return $manual_notice;
		}

		return '';
	}

	public function _emailTagPostalNoticeCustomer( $payment_id ) {
		$postal_titles = \WPENON\Util\PaymentMeta::instance()->getPostalCertificateTitles( $payment_id );

		$postal_notice = '';

		if ( count( $postal_titles ) > 0 ) {
			$postal_notice = sprintf( _n( 'Der Energieausweis %s wird Ihnen in Kürze per Post zugesendet.', 'Die Energieausweise %s werden Ihnen in Kürze per Post zugesendet.', count( $postal_titles ), 'wpenon' ), implode( ', ', $postal_titles ) );
		}

		return $postal_notice;
	}

	public function _emailTagPostalNoticeSeller( $payment_id ) {
		$postal_titles = \WPENON\Util\PaymentMeta::instance()->getPostalCertificateTitles( $payment_id );

		$postal_notice = '';

		if ( count( $postal_titles ) > 0 ) {
			$postal_notice = sprintf( _n( 'Der Energieausweis %s wurde zusätzlich als Postversand bestellt.', 'Die Energieausweise %s wurden zusätzlich als Postversand bestellt.', count( $postal_titles ), 'wpenon' ), implode( ', ', $postal_titles ) );
			$postal_notice .= ' ' . __( 'Denken Sie nach Zahlungseingang bitte an den Versand.', 'wpenon' );
		}

		return $postal_notice;
	}

	public function _emailTagCustomerContactData( $payment_id, $mode = 'html' ) {
		$customer_id = edd_get_payment_customer_id( $payment_id );

		$customer = new \EDD_Customer( $customer_id );
		if ( ! $customer->id ) {
			return '';
		}

		$customer_meta = \WPENON\Util\CustomerMeta::getCustomerMeta( $customer_id );

		$data = array(
			__( 'Kundendaten:', 'wpenon' ),
			$customer->name,
		);

		if ( 'plain' !== $mode ) {
			$data[0] = '<strong>' . $data[0] . '</strong>';
		}

		if ( ! empty( $customer_meta['business_name'] ) ) {
			$data[] = $customer_meta['business_name'];
		}

		$data[] = __( 'Email-Adresse:', 'wpenon' ) . ' ' . $customer->email;

		if ( ! empty( $customer_meta['telefon'] ) ) {
			$data[] = __( 'Telefonnummer:', 'wpenon' ) . ' ' . $customer_meta['telefon'];
		} else {
			$data[] = __( 'Telefonnummer:', 'wpenon' ) . ' ' . __( 'Nicht angegeben', 'wpenon' );
		}

		if ( 'plain' === $mode ) {
			return implode( "\n", $data );
		}

		return implode( '<br />', $data );
	}

	public function _emailTagCustomerContactDataPlain( $payment_id ) {
		return $this->__emailTagCustomerContactData( $payment_id, 'plain' );
	}

	public function _addEmailEnergieausweisLink( $title, $item, $price_id = 0, $payment_id = 0 ) {
		if ( isset( $item['id'] ) && apply_filters( 'edd_email_show_links', true ) ) {
			$html = false;
			if ( strpos( $title, '<strong>' ) !== false ) {
				$html = true;
			}

			$verified_permalink = \WPENON\Model\EnergieausweisManager::getVerifiedPermalink( $item['id'], 'pdf-view' );
			if ( $html ) {
				// Add address if available.
				$product_address = get_post_meta( $item['id'], 'adresse_strassenr', true ) . ', ' . get_post_meta( $item['id'], 'adresse_plz', true ) . ' ' . get_post_meta( $item['id'], 'adresse_ort', true );
				if ( 0 !== strpos( $product_address, ',' ) ) {
					$title .= '<br/><small>' . $product_address . '</small>';
				}

				$title .= '<br/><a href="' . esc_url( $verified_permalink ) . '">' . __( 'Energieausweis hier herunterladen', 'wpenon' ) . '</a>';
			} else {
				$title .= "\n" . $verified_permalink;
			}
		}

		return $title;
	}

	public function _maybeSendOrderConfirmationEmail( $payment_id, $payment_data = array() ) {
		$gateway  = edd_get_payment_gateway( $payment_id );
		$supports = edd_get_gateway_supports( $gateway );
		if ( in_array( 'manual_handling', $supports ) ) {
			$this->sendOrderConfirmationEmail( $payment_id );
		}
	}

	public function sendConfirmationEmail( $energieausweis ) {
		if ( ! apply_filters( 'wpenon_send_certificate_create_confirmation_email', true ) ) {
			return false;
		}

		do_action('wpenon_confirmation_start', $energieausweis );

		$from_name = edd_get_option( 'from_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
		$from_name = apply_filters( 'wpenon_confirmation_from_name', $from_name, $energieausweis->ID, $energieausweis );

		$from_email = edd_get_option( 'from_email', get_bloginfo( 'admin_email' ) );
		$from_email = apply_filters( 'wpenon_confirmation_from_address', $from_email, $energieausweis->ID, $energieausweis );

		$to_email = apply_filters( 'wpenon_confirmation_to_address', $energieausweis->wpenon_email );

		$subject = apply_filters( 'wpenon_confirmation_subject', __( 'Ihr Energieausweis', 'wpenon' ), $energieausweis->ID, $energieausweis );

		$message = $this->getEmailConfirmationBodyContent( $energieausweis->ID, $energieausweis );

		$emails = EDD()->emails;

		$emails->__set( 'from_name', $from_name );
		$emails->__set( 'from_email', $from_email );
		$emails->__set( 'heading', __( 'Ihr Energieausweis', 'wpenon' ) );

		$headers = apply_filters( 'wpenon_confirmation_headers', $emails->get_headers(), $energieausweis->ID, $energieausweis );
		$emails->__set( 'headers', $headers );

		return $emails->send( $to_email, $subject, $message, array() );
	}

	public function getEmailConfirmationBodyContent( $energieausweis_id, $energieausweis ) {
		$energieausweis_site = apply_filters( 'wpenon_confirmation_site',  home_url() );
		$energieausweis_link = apply_filters( 'wpenon_confirmation_link',  $energieausweis->verified_permalink, $energieausweis );

		$default_email_body = __( 'Sehr geehrter Kunde,', 'wpenon' ) . "\n\n";
		$default_email_body .= sprintf( __( 'schön, dass Sie auf unserer Website %1$s mit der Erstellung eines Energieausweises (Kennung %2$s) begonnen haben.', 'wpenon' ), $energieausweis_site, $energieausweis->post_title ) . "\n\n";
		$default_email_body .= sprintf( __( 'Typ: %s', 'wpenon' ), $energieausweis->formatted_wpenon_type ) . "\n";
		$default_email_body .= sprintf( __( 'Gebäudeadresse: %s', 'wpenon' ), $energieausweis->adresse ) . "\n\n";
		$default_email_body .= __( 'Sie haben jederzeit die Möglichkeit die Erstellung des Energieausweises unter folgendem Link fortzusetzen:', 'wpenon' ) . "\n\n";
		$default_email_body .= '<a href="' . $energieausweis_link . '">' . $energieausweis_link . '</a>' . "\n\n";
		$default_email_body .= '<strong>' . __( 'Bitte gehen Sie vertraulich mit diesem Link um und geben Sie ihn unter keinen Umständen an Dritte weiter, da diese andernfalls Zugriff auf den Energieausweis bekommen würden.', 'wpenon' ) . '</strong>' . "\n\n";
		$default_email_body .= __( 'Über diesen Link können Sie die Daten für Ihren Energieausweis jederzeit bearbeiten.', 'wpenon' );
		if ( ! $energieausweis->isOrdered() ) {
			$default_email_body .= ' ' . __( 'Wenn Sie alle benötigten Angaben vollständig eingegeben haben, können Sie den Energieausweis bestellen.', 'wpenon' );
		}
		/*if ( ! $energieausweis->isPaid() ) {
			 $default_email_body .= ' ' . __( 'Nach Zahlungseingang wird der Energieausweis rechtsgültig ausgestellt und erhält eine Registrierungsnummer.', 'wpenon' );
		  }*/
		$default_email_body .= "\n\n";
		$default_email_body .= wp_specialchars_decode( apply_filters( 'wpenon_email_signature', get_bloginfo( 'name' ) ), ENT_QUOTES );

		$email = $default_email_body;

		$email_body = wpautop( $email );

		$email_body = apply_filters( 'wpenon_confirmation_' . EDD()->emails->get_template(), $email_body, $energieausweis_id, $energieausweis );

		return apply_filters( 'wpenon_confirmation', $email_body, $energieausweis_id, $energieausweis );
	}

	public function sendOrderConfirmationEmail( $payment_id, $admin_notice = true ) {
		$payment_data = edd_get_payment_meta( $payment_id );

		$from_name = edd_get_option( 'from_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
		$from_name = apply_filters( 'wpenon_order_confirmation_from_name', $from_name, $payment_id, $payment_data );

		$from_email = edd_get_option( 'from_email', get_bloginfo( 'admin_email' ) );
		$from_email = apply_filters( 'wpenon_order_confirmation_from_address', $from_email, $payment_id, $payment_data );

		$to_email = apply_filters( 'wpenon_order_confirmation_to_address', edd_get_payment_user_email( $payment_id ) );

		$subject = edd_get_option( 'order_confirmation_subject', __( 'Zahlungsaufforderung', 'wpenon' ) );
		$subject = apply_filters( 'wpenon_order_confirmation_subject', wp_strip_all_tags( $subject ), $payment_id );
		$subject = edd_do_email_tags( $subject, $payment_id );

		$attachments = array();
		$message     = edd_do_email_tags( $this->getEmailOrderConfirmationBodyContent( $payment_id, $payment_data ), $payment_id );

		$emails = EDD()->emails;

		$emails->__set( 'from_name', $from_name );
		$emails->__set( 'from_email', $from_email );
		$emails->__set( 'heading', __( 'Zahlungsaufforderung', 'wpenon' ) );

		$headers = apply_filters( 'wpenon_order_confirmation_headers', $emails->get_headers(), $payment_id, $payment_data );
		$emails->__set( 'headers', $headers );

		$emails->send( $to_email, $subject, $message, $attachments );

		if ( $admin_notice && ! edd_admin_notices_disabled( $payment_id ) ) {
			do_action( 'edd_admin_sale_notice', $payment_id, $payment_data );
		}
	}

	public function getEmailOrderConfirmationBodyContent( $payment_id, $payment_data ) {
		$default_email_body = __( 'Sehr geehrte/r', 'wpenon' ) . " {fullname},\n\n";
		$default_email_body .= __( 'vielen Dank für Ihre Bestellung. Bitte begleichen Sie die Rechnung umgehend, um die Bestellung abzuschließen und Ihren Energieausweis zu erhalten.', 'wpenon' ) . "\n\n";
		$default_email_body .= __( 'Die PDF-Rechnung für Ihre Bestellung finden Sie unter diesem Link: {pdf_link}', 'wpenon' ) . "\n\n";
		$default_email_body .= __( 'Im Folgenden finden Sie die nötigen Daten für die Zahlung:', 'wpenon' ) . "\n\n";
		$default_email_body .= "{bank_account_info}\n\n";
		$default_email_body .= "{sitename}";

		$email = edd_get_option( 'order_confirmation', false );
		$email = $email ? stripslashes( $email ) : $default_email_body;

		$email_body = wpautop( $email );

		$email_body = apply_filters( 'wpenon_order_confirmation_' . EDD()->emails->get_template(), $email_body, $payment_id, $payment_data );

		return apply_filters( 'wpenon_order_confirmation', $email_body, $payment_id, $payment_data );
	}

	public function _processPreviewEmailTags( $message ) {
		return str_replace( '{bank_account_info}', $this->_emailTagBankAccountInfo( null ), $message );
	}

	public function _hackAdminNotices( $ret, $payment_id = 0 ) {
		// do not send admin notification if payment is handled manually and is already complete
		if ( $ret && $payment_id > 0 ) {
			$gateway  = edd_get_payment_gateway( $payment_id );
			$supports = edd_get_gateway_supports( $gateway );
			if ( in_array( 'manual_handling', $supports ) && edd_is_payment_complete( $payment_id ) ) {
				return false;
			}
		}

		return $ret;
	}

	public function _addAdditionalEmailSettings( $settings ) {
		$first_part = array();
		foreach ( $settings as $key => $data ) {
			$first_part[ $key ] = $data;
			if ( $key == 'purchase_receipt' ) {
				break;
			}
		}

		$new_settings = array(
			'order_confirmation_subject' => array(
				'id'   => 'order_confirmation_subject',
				'name' => __( 'Betreff der Zahlungsaufforderung', 'wpenon' ),
				'desc' => __( 'Geben Sie die Betreffzeile der E-Mail für die Bestellbestätigung an', 'wpenon' ),
				'type' => 'text',
				'std'  => __( 'Zahlungsaufforderung', 'wpenon' ),
			),
			'order_confirmation'         => array(
				'id'   => 'order_confirmation',
				'name' => __( 'Zahlungsaufforderung', 'wpenon' ),
				'desc' => __( 'Geben Sie hier den E-Mail-Text für die Zahlungsaufforderung ein, die Benutzer bzw. Kunden unmittelbar nach der Bestellung erhalten (betrifft nur Bestellungen bestimmter Zahlungsweisen). HTML-Formatierungen sind erlaubt. Verfügbare Template-Tags sind:', 'wpenon' ) . '<br/>' . edd_get_emails_tags_list(),
				'type' => 'rich_editor',
				'std'  => __( 'Sehr geehrte/r', 'wpenon' ) . " {name},\n\n" . __( 'vielen Dank für Ihre Bestellung. Bitte begleichen Sie die Rechnung umgehend, um die Bestellung abzuschließen und Ihren Energieausweis zu erhalten.', 'wpenon' ) . "\n\n" . __( 'Im Folgenden finden Sie die nötigen Daten für die Zahlung:', 'wpenon' ) . "\n\n" . "{bank_account_info}\n\n" . "{sitename}",
			),
		);

		$legal_settings = array(
			'legal_information_page' => array(
				'id'          => 'legal_information_page',
				'name'        => __( 'Impressum Seite', 'wpenon' ),
				'desc'        => __( 'Ein Link zu dieser Seite wird mit sämtlichen Emails im Fußbereich gesendet.', 'wpenon' ),
				'type'        => 'select',
				'options'     => edd_get_pages(),
				'chosen'      => true,
				'placeholder' => __( 'Select a page', 'easy-digital-downloads' ),
			),
		);

		return array_merge( $first_part, $new_settings, $settings, $legal_settings );
	}

	public function _adjustEmailFooterText( $text ) {
		$impressum_page = edd_get_option( 'legal_infor mation_page', false );

		$text = sprintf( __( 'Diese Email wurde automatisch von %s versendet.', 'wpenon' ), '<a href="' . esc_url( home_url() ) . '">' . str_replace( array(
				'http://',
				'https://'
			), '', esc_url( home_url() ) ) . '</a>' );
		if ( ! empty( $impressum_page ) ) {
			$text .= '<br />';
			$text .= '<a href="' . esc_url( get_permalink( $impressum_page ) ) . '">' . __( 'Impressum', 'wpenon' ) . '</a>';
		}

		return apply_filters( 'wpenon_email_legal', $text );
	}
}
