<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

class ReceiptPDF extends \WPENON\Util\UFPDF {
  private $wpenon_title = '';

  private $wpenon_width = 210;
  private $wpenon_height = 295;
  private $wpenon_margin_h = 15;
  private $wpenon_margin_v = 15;

  private $wpenon_payment = null;
  private $wpenon_seller_meta = array();

  private $is_bulk = false;
  private $hide_common = false;

  public function __construct( $title, $is_bulk = false, $hide_common = false ) {
    $this->wpenon_title = $title;

    $this->wpenon_colors = array(
      'background'  => array( 255, 255, 255 ),
      'text'        => array( 0, 0, 0 ),
      'gray'        => array( 222, 222, 222 ),
      'link'        => array( 34, 122, 209 ),
    );
    $this->wpenon_fonts = array(
      'text'        => array( 'Arial', '', 12, 7 ),
      'small'       => array( 'Arial', '', 9, 5 ),
      'headline'    => array( 'Arial', 'B', 20, 10 ),
      'title'       => array( 'Arial', '', 15, 12 ),
      'subtotal'    => array( 'Arial', 'B', 12, 7 ),
      'total'       => array( 'Arial', 'B', 15, 8 ),
      'address'     => array( 'Arial', '', 12, 6 ),
      'footer'      => array( 'Arial', '', 7, 4 ),
    );

    $this->is_bulk = $is_bulk;
    $this->hide_common = $hide_common;

    parent::__construct( 'P', 'mm', 'A4' );

    $this->SetTitle( $this->wpenon_title );
    $this->SetAutoPageBreak( true, $this->wpenon_margin_v );
    $this->SetMargins( $this->wpenon_margin_h, $this->wpenon_margin_v, $this->wpenon_margin_h );
  
    $this->SetPageTextColor( 'text' );
    $this->SetPageDrawColor( 'text' );
    $this->SetPageFillColor( 'gray' );
  }

  public function __isset( $name ) {
    if ( strpos( $name, 'wpenon_' ) !== 0 ) {
      $name = 'wpenon_' . $name;
    }
    return property_exists( $this, $name );
  }

  public function __get( $name ) {
    if ( strpos( $name, 'wpenon_' ) !== 0 ) {
      $name = 'wpenon_' . $name;
    }
    if ( property_exists( $this, $name ) ) {
      return $this->$name;
    }
    return null;
  }

  public function create( $payment ) {
    if ( is_object( $payment ) && isset( $payment->ID ) && isset( $payment->cart_details ) ) {
      $this->wpenon_payment = $payment;
      $paymentmeta = \WPENON\Util\PaymentMeta::instance();
      $this->wpenon_seller_meta = $paymentmeta->getSellerMeta( $payment->ID );

      $this->createNewPage();
      $this->renderPage();
    }
  }

  public function finalize( $output_mode = 'I' ) {
    return $this->Output( $this->wpenon_title . '.pdf', $output_mode );
  }

  public function renderPage() {
    $override = apply_filters( 'wpenon_override_receipt_pdf', false, $this );

    if ( ! $override ) {
      $this->renderHeader();

      $this->SetXY( $this->wpenon_margin_h + 3, 50 );

      $this->SetPageFont( 'address' );

      $address = '';
      if ( isset( $this->wpenon_payment->user_info['business_name'] ) && ! empty( $this->wpenon_payment->user_info['business_name'] ) ) {
        $address .= $this->wpenon_payment->user_info['business_name'] . "\n";
      }
      $address .= $this->wpenon_payment->user_info['first_name'] . ' ' . $this->wpenon_payment->user_info['last_name'] . "\n";
      if ( isset( $this->wpenon_payment->user_info['address']['line1'] ) && ! empty( $this->wpenon_payment->user_info['address']['line1'] ) ) {
        $address .= $this->wpenon_payment->user_info['address']['line1'] . "\n";
      }
      if ( isset( $this->wpenon_payment->user_info['address']['line2'] ) && ! empty( $this->wpenon_payment->user_info['address']['line2'] ) ) {
        $address .= $this->wpenon_payment->user_info['address']['line2'] . "\n";
      }
      if ( isset( $this->wpenon_payment->user_info['address']['zip'] ) && ! empty( $this->wpenon_payment->user_info['address']['zip'] ) && isset( $this->wpenon_payment->user_info['address']['city'] ) && ! empty( $this->wpenon_payment->user_info['address']['city'] ) ) {
        $address .= $this->wpenon_payment->user_info['address']['zip'] . ' ' . $this->wpenon_payment->user_info['address']['city'];
      }
      $this->WriteMultiCell( $this->escape( $address ), 'L', 1, 0 );

      $this->SetXY( $this->wpenon_margin_h, 85 );

      $this->SetPageFont( 'title' );
      $this->WriteCell( $this->escape( get_the_title( $this->wpenon_payment->ID ) ), 'L', 1, 0 );

      $this->SetPageFont( 'text' );
      $this->WriteCell( $this->escape( __( 'Rechnungsdatum', 'wpenon' ) . ': ' . \WPENON\Util\Format::date( $this->wpenon_payment->date ) ), 'L', 1, 0 );
      
      $this->Ln( 20 );

      $this->WriteCell( $this->escape( __( 'Leistung', 'wpenon' ) ), 'L', 0, 150, 8, true );
      $this->WriteCell( $this->escape( __( 'Preis', 'wpenon' ) ), 'R', 1, 0, 8, true );

      $this->Ln( 4 );

      $types = \WPENON\Model\EnergieausweisManager::getAvailableTypes();

      $tax_rate = 0;
      if ( isset( $this->wpenon_payment->tax ) && ! empty( $this->wpenon_payment->total ) && ! empty( $this->wpenon_payment->tax ) ) {
        $tax_value = $this->wpenon_payment->total / ( $this->wpenon_payment->total - $this->wpenon_payment->tax );
        $tax_rate = \WPENON\Util\Format::int( ( $tax_value - 1.0 ) * 100 );
      }

      $subtotal = 0.0;

      foreach ( $this->wpenon_payment->cart_details as $item ) {
        $product_title = $item['name'];
        $type = get_post_meta( $item['id'], 'wpenon_type', true );
        if ( isset( $types[ $type ] ) ) {
          $product_title .= ' (' . $types[ $type ] . ')';
        }
        $price_mode = edd_get_cart_item_price_name( $item );
        if ( empty( $price_mode ) ) {
          $price_mode = __( 'Download', 'wpenon' );
        }
        $product_title .= ', ' . $price_mode;

        $product_secondary = get_post_meta( $item['id'], 'adresse_strassenr', true ) . ', ' . get_post_meta( $item['id'], 'adresse_plz', true ) . ' ' . get_post_meta( $item['id'], 'adresse_ort', true );
        $registry_id = get_post_meta( $item['id'], 'registriernummer', true );
        if ( ! empty( $registry_id ) ) {
          $product_secondary .= ' (' . __( 'Registriernummer', 'wpenon' ) . ': ' . $registry_id . ')';
        }

        $product_subtotal = $item['item_price'];
        if ( floatval( $item['item_price'] ) > 0.0 ) {
          $product_subtotal = $item['item_price'] / ( 1.0 + $tax_rate / 100.0 );
        }

        $subtotal += $product_subtotal;

        $this->WriteCell( $this->escape( $product_title ), 'L', 0, 150 );
        $this->WriteCell( $this->escape( edd_currency_filter( edd_format_amount( $product_subtotal ), edd_get_payment_currency_code( $this->wpenon_payment->ID ) ) ), 'R', 1, 0 );
        $this->SetPageFont( 'small' );
        $this->WriteCell( '', 'L', 0, 5 );
        $this->WriteCell( $this->escape( $product_secondary ), 'L', 1, 0 );
        $this->SetPageFont( 'text' );
      }
      
      foreach ( $this->wpenon_payment->fees as $index => $fee ) {
        $fee_subtotal = $fee['amount'];
        if ( floatval( $fee['amount'] ) > 0.0 ) {
          $fee_subtotal = $fee['amount'] / ( 1.0 + $tax_rate / 100.0 );
        }

        $subtotal += $fee_subtotal;

        $this->WriteCell( $this->escape( $fee['label'] ), 'L', 0, 150 );
        $this->WriteCell( $this->escape( edd_currency_filter( edd_format_amount( $fee_subtotal ), edd_get_payment_currency_code( $this->wpenon_payment->ID ) ) ), 'R', 1, 0 );
      }

      $this->Ln( 8 );

      $this->SetPageFont( 'subtotal' );

      $this->WriteCell( $this->escape( __( 'Nettobetrag', 'wpenon' ) ), 'R', 0, 150 );
      $this->WriteCell( $this->escape( edd_currency_filter( edd_format_amount( $subtotal ), edd_get_payment_currency_code( $this->wpenon_payment->ID ) ) ), 'R', 1, 0 );

      $discount = edd_get_payment_meta( $this->wpenon_payment->ID, '_edd_payment_discount_id', true );
      if ( $discount ) {
        $discount = edd_get_discount( $discount );
        if ( $discount ) {
          $this->WriteCell( $this->escape( sprintf( __( 'Gutscheincode: %s', 'wpenon' ), edd_get_discount_code( $discount->ID ) ) ), 'R', 0, 150 );
          $this->WriteCell( $this->escape( sprintf( __( '- %s', 'wpenon' ), edd_format_discount_rate( edd_get_discount_type( $discount->ID ), edd_get_discount_amount( $discount->ID ) ) ) ), 'R', 1, 0 );
        }
      }

      if ( ! $discount ) {
        $uinfo = edd_get_payment_meta_user_info( $this->wpenon_payment->ID );
        if ( isset( $uinfo['discount'] ) && $uinfo['discount'] && $uinfo['discount'] !== 'none' ) {
          $this->WriteCell( $this->escape( sprintf( __( 'Gutscheincode: %s', 'wpenon' ), $uinfo['discount'] ) ), 'R', 0, 150 );
          $this->WriteCell( $this->escape( sprintf( '- %s', edd_currency_filter( edd_format_amount( abs( $subtotal - ( $this->wpenon_payment->total - $this->wpenon_payment->tax ) ) ) ) ) ), 'R', 1, 0 );
        }
      }

      if ( $this->wpenon_payment->tax ) {
        $this->WriteCell( $this->escape( sprintf( __( 'zzgl. %s %% USt', 'wpenon' ), $tax_rate ) ), 'R', 0, 150 );
        $this->WriteCell( $this->escape( edd_currency_filter( edd_format_amount( $this->wpenon_payment->tax ), edd_get_payment_currency_code( $this->wpenon_payment->ID ) ) ), 'R', 1, 0 );
      }

      $this->SetPageFont( 'total' );

      $this->WriteCell( $this->escape( __( 'Bruttobetrag', 'wpenon' ) ), 'R', 0, 150 );
      $this->WriteCell( $this->escape( edd_currency_filter( edd_format_amount( $this->wpenon_payment->total ), edd_get_payment_currency_code( $this->wpenon_payment->ID ) ) ), 'R', 1, 0 );
    
      $this->Ln( 10 );

      if ( $this->wpenon_payment->total > 0.0 ) {
        $this->SetPageFont( 'text' );

        if ( ! $this->wpenon_payment->tax ) {
          $this->WriteCell( $this->escape( __( 'Gemäß § 19 UStG wird wegen Kleinunternehmerstatus keine Mehrwertsteuer erhoben.', 'wpenon' ) ), 'L', 1, 0 );
        }

        if ( ! edd_is_payment_complete( $this->wpenon_payment->ID ) ) {
          $this->WriteCell( $this->escape( __( 'Die Rechnung wurde noch nicht beglichen.', 'wpenon' ) ), 'L', 1, 0 );
        } else {
          $payment_message = sprintf( __( 'Die Rechnung wurde am %1$s via %2$s beglichen.', 'wpenon' ), \WPENON\Util\Format::date( edd_get_payment_completed_date( $this->wpenon_payment->ID ) ), edd_get_gateway_checkout_label( edd_get_payment_gateway( $this->wpenon_payment->ID ) ) );
          if ( get_post_meta( $this->wpenon_payment->ID, '_wpenon_deposit_refunded', true ) ) {
            $payment_message .= ' ' . __( '(Lastschrift zurückgegangen)', 'wpenon' );
          }
          $this->WriteCell( $this->escape( $payment_message ), 'L', 1, 0 );
        }
      }

      $this->renderFooter();

      do_action( 'wpenon_render_receipt_pdf', $this->wpenon_payment, $this->wpenon_seller_meta, $this );
    }
  }

  public function createNewPage() {
    $this->addPage( 'P', array( $this->wpenon_width, $this->wpenon_height ) );
  }

  public function renderHeader() {
    if ( ! $this->hide_common ) {
      $firmenlogo = $this->wpenon_seller_meta['firmenlogo'];
      if ( ! empty( $firmenlogo ) ) {
        $this->Image( \WPENON\Util\ThumbnailHandler::urlToPath( $firmenlogo ), $this->wpenon_margin_h, $this->wpenon_margin_h, 80 );
      } else {
        $this->SetPageFont( 'headline' );
        $this->WriteCell( $this->escape( $this->wpenon_seller_meta['firmenname'] ), 'L', 1, 80 );
      }
    }

    $this->SetXY( $this->wpenon_margin_h, 40 );
  }

  public function renderFooter() {
    if ( $this->hide_common ) {
      return;
    }

    $this->SetXY( $this->wpenon_margin_h, -55 );

    $this->Cell( 0, 3, '', 'B', 1, 'L' );

    $this->SetXY( $this->wpenon_margin_h, -50 );

    $this->SetPageFont( 'footer' );

    $this->SetStyle( 'B', true );

    $this->WriteCell( $this->escape( __( 'Kontakt', 'wpenon' ) ), 'L', 0, 60 );
    $this->WriteCell( $this->escape( __( 'Bankdaten', 'wpenon' ) ), 'L', 0, 60 );
    $this->WriteCell( $this->escape( __( 'Rechtliche Informationen', 'wpenon' ) ), 'L', 1, 0 );

    $this->SetStyle( 'B', false );

    $business_contact = $this->wpenon_seller_meta['firmenname'] . "\n" . $this->wpenon_seller_meta['strassenr'] . "\n" . $this->wpenon_seller_meta['plz'] . ' ' . $this->wpenon_seller_meta['ort'] . "\n";
    $business_contact .= __( 'Telefon', 'wpenon' ) . ': ' . $this->wpenon_seller_meta['telefon'] . "\n" . __( 'Email', 'wpenon' ) . ': ' . $this->wpenon_seller_meta['email'];
    
    $bank_info = __( 'Kontoinhaber', 'wpenon' ) . ': ' . $this->wpenon_seller_meta['kontoinhaber'] . "\n" . __( 'Kontonummer', 'wpenon' ) . ': ' . $this->wpenon_seller_meta['kontonummer'] . "\n" . __( 'Bankleitzahl', 'wpenon' ) . ': ' . $this->wpenon_seller_meta['bankleitzahl'] . "\n" . $this->wpenon_seller_meta['kreditinstitut'] . "\n";
    $bank_info .= __( 'IBAN', 'wpenon' ) . ': ' . $this->wpenon_seller_meta['iban'] . "\n" . __( 'BIC (SWIFT)', 'wpenon' ) . ': ' . $this->wpenon_seller_meta['bic'];
    
    $legal = __( 'Geschäftsführer', 'wpenon' ) . ': ' . $this->wpenon_seller_meta['inhaber'] . "\n" . __( 'Amtsgericht', 'wpenon' ) . ': ' . $this->wpenon_seller_meta['amtsgericht'];
    if ( ! empty( $this->wpenon_seller_meta['steuernummer'] ) ) {
      $legal .= "\n" . __( 'Steuernummer', 'wpenon' ) . ': ' . $this->wpenon_seller_meta['steuernummer'];
    }
    if ( ! empty( $this->wpenon_seller_meta['ustid'] ) ) {
      $legal .= "\n" . __( 'USt-Identifikationsnummer', 'wpenon' ) . ': ' . $this->wpenon_seller_meta['ustid'];
    }
    if ( isset( $this->wpenon_seller_meta['glaeubigerid'] ) && ! empty( $this->wpenon_seller_meta['glaeubigerid'] ) ) {
      $legal .= "\n" . __( 'Gläubiger-ID', 'wpenon' ) . ': ' . $this->wpenon_seller_meta['glaeubigerid'];
    }

    $this->SetXY( $this->wpenon_margin_h, -45 );
    $this->WriteMultiCell( $this->escape( $business_contact ), 'L', 0, 60 );
    $this->SetXY( 75, -45 );
    $this->WriteMultiCell( $this->escape( $bank_info ), 'L', 0, 60 );
    $this->SetXY( 135, -45 );
    $this->WriteMultiCell( $this->escape( $legal ), 'L', 0, 0 );
  }

  public function escape( $value ) {
    return \WPENON\Util\Format::pdfEncode( $value );
  }
  
  public function is_bulk() {
    return $this->is_bulk;
  }
}
