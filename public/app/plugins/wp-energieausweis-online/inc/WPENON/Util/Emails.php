<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

use WPENON\Model\Energieausweis;

use Enon\Edd\Models\Payment;
use Enon\WP\Models\Options_Confirmation_Email;
use Enon\WP\Models\Options_Billing_Email;

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

		add_action( 'wpenon_energieausweis_create', array( $this, 'send_confirmation_email' ) );

		add_action( 'edd_insert_payment', array( $this, '_maybeSendOrderConfirmationEmail' ), 10, 2 );

		add_filter( 'edd_email_preview_template_tags', array( $this, '_processPreviewEmailTags' ) );

		add_filter( 'edd_admin_notices_disabled', array( $this, '_hackAdminNotices' ), 10, 2 );

		add_filter( 'edd_settings_emails', array( $this, '_addAdditionalEmailSettings' ) );
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
			$this->send_bill_email( $payment_id );
		}
	}

	/**
	 * Send confirmation email.
	 *
	 * @param Energieausweis $energieausweis Energieausweis object.
	 *
	 * @return bool Whether the email contents were sent successfully.
	 *
	 * @since 1.0.0
	 */
	public function send_confirmation_email( $energieausweis ) {
		if ( ! apply_filters( 'wpenon_send_certificate_create_confirmation_email', true ) ) {
			return false;
		}

		$options_email = new Options_Confirmation_Email();

		$sender_name  = $options_email->get_sender_name();
		$sender_email = $options_email->get_sender_email();
		$subject      = $options_email->get_subject();
		$content      = $options_email->get_content();

		$sender_name  = apply_filters( 'wpenon_confirmation_sender_name', $sender_name, $energieausweis );
		$sender_email = apply_filters( 'wpenon_confirmation_sender_email', $sender_email, $energieausweis );
		$subject      = apply_filters( 'wpenon_confirmation_subject', $subject, $energieausweis );
		$content      = apply_filters( 'wpenon_confirmation_content', $content, $energieausweis );

		$recipient_email = apply_filters( 'wpenon_confirmation_recipient_address', $energieausweis->wpenon_email );

		$message         = $this->replace_tags_confirmation_content( $energieausweis, $content );

		do_action( 'wpenon_confirmation_start', $energieausweis );

		$emails = EDD()->emails;
		$emails->__set( 'from_name', $from_name );
		$emails->__set( 'from_address', $from_email );
		$emails->__set( 'heading', __( 'Ihr Energieausweis', 'wpenon' ) );

		return $emails->send( $recipient_email, $subject, $message, array() );
	}

	/**
	 * Replacing tags in confitmation body content.
	 *
	 * @param Energieausweis $energieausweis Energieausweis Object.
	 * @param string         $content        Email content.
	 *
	 * @return string $body Email body content.
	 *
	 * @since 1.0.0
	 */
	private function replace_tags_confirmation_content( $energieausweis, $content ) {
		$confirmation_site    = apply_filters( 'wpenon_confirmation_site', home_url(), $energieausweis );
		$energieausweis_link  = apply_filters( 'wpenon_confirmation_energieausweis_link', $energieausweis->verified_permalink, $energieausweis );
		$energieausweis_title = apply_filters( 'wpenon_confirmation_energieausweis_title', $energieausweis->post_title, $energieausweis );
		$energieausweis_type  = apply_filters( 'wpenon_confirmation_energieausweis_type', $energieausweis->formatted_wpenon_type, $energieausweis );
		$customer_address     = apply_filters( 'wpenon_confirmation_customer_address', $energieausweis->adresse, $energieausweis );

		$template_tags = [
			'site'             => $confirmation_site,
			'en-link'          => $energieausweis_link,
			'en-title'         => $energieausweis_title,
			'en-type'          => $energieausweis_type,
			'customer-address' => $customer_address,
			'not-ordered'      => '',
		];

		if ( ! $energieausweis->isOrdered() ) {
			$values['not-ordered'] = __( 'Wenn Sie alle benötigten Angaben vollständig eingegeben haben, können Sie den Energieausweis bestellen.', 'wpenon' );
		}

		// Replacing template tags in mail body.
		foreach ( $template_tags as $template_tag => $value ) {
			$content = str_replace( '{{' . $template_tag . '}}', $value, $content );
		}

		return $content;
	}

	/**
	 * Send bill email.
	 *
	 * @param int  $payment_id Edd payment id.
	 * @param bool $admin_notice
	 *
	 * @since 1.0.0
	 */
	public function send_bill_email( $payment_id ) {
		$options_email = new Options_Billing_Email();
		$payment       = new Payment( $payment_id );

		$sender_name     = $options_email->get_sender_name();
		$sender_email    = $options_email->get_sender_email();
		$subject         = $options_email->get_subject();
		$content         = $options_email->get_content();
		$recipient_email = edd_get_payment_user_email( $payment_id );
		$energieausweis  = $payment->get_energieausweis_old(); // @todo switch to new energieausweis object.

		$sender_name     = apply_filters( 'wpenon_bill_sender_name', $sender_name, $energieausweis );
		$sender_email    = apply_filters( 'wpenon_bill_sender_email', $sender_email, $energieausweis );
		$subject         = apply_filters( 'wpenon_bill_subject', $subject, $energieausweis );
		$content         = apply_filters( 'wpenon_bill_content', $content, $energieausweis );
		$recipient_email = apply_filters( 'wpenon_bill_to_address', edd_get_payment_user_email( $payment_id ) );

		$attachments = array();
		$message     = $this->replace_tags_bill_content( $payment, $energieausweis, $content );

		$emails = EDD()->emails;
		$emails->__set( 'from_name', $sender_name );
		$emails->__set( 'from_email', $sender_email );
		$emails->__set( 'heading', $subject );

		$emails->send( $recipient_email, $subject, $message, $attachments );

		do_action( 'edd_admin_sale_notice', $payment_id, $payment_data );
	}

	/**
	 * Replacing tags in confitmation body content.
	 *
	 * @param Payment        $payment        Payment object.
	 * @param Energieausweis $energieausweis Energieausweis Object.
	 * @param string         $content        Email content.
	 *
	 * @return string $content Email body content.
	 *
	 * @since 1.0.0
	 */
	public function replace_tags_bill_content( $payment, $energieausweis, $content ) {
		$customer = $payment->get_customer();

		$customer_name = apply_filters( 'wpenon_bill_customer_name', $customer->name, $payment, $energieausweis );
		$receipt_link  = apply_filters( 'wpenon_bill_receipt_link', $this->_emailTagPDFLink( $payment->get_id() ), $energieausweis ); // @todo: Replace th _emailTagPDFLink function.
		$receipt_title = apply_filters( 'wpenon_bill_receipt_title', $payment->get_title(), $energieausweis );

		$template_tags = [
			'customer-name' => $customer_name,
			'receipt-url'   => $receipt_link,
			'receipt-title' => $receipt_title,
		];

		// Replacing template tags in mail body.
		foreach ( $template_tags as $template_tag => $value ) {
			$content = str_replace( '{{' . $template_tag . '}}', $value, $content );
		}

		return $content;
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
