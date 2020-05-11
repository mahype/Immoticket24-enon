<?php

/**
 * EDD cart filter
 * If more then one items in cart, store the last item and unset the other one.
 * Also filter duplicated items from cart
 *
 * @since 03.05.2020
 * @wp-hook edd_cart_contents
 * @param $cart array
 */
add_filter('edd_cart_contents', function (array $cart ): array {
	return array_unique($cart, SORT_REGULAR);
}, 10);


/**
 * Add invoice link for an Energieausweis
 *
 * @since 10.05.2020
 * @wp-hook edd_stats_meta_box
 */
add_action('edd_stats_meta_box', function (){
	if(empty($_GET['post'])){
		return;
	}

	$post_id    = $_GET['post'];
	$invoice_id = get_post_meta($post_id, '_wpenon_attached_payment_id', true);
	$invoice    = get_post($invoice_id);

	if(empty($invoice_id)){
		return;
	}

	$invoice_url = admin_url('edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $invoice_id);

	echo '<hr />Zugehörige Rechnung: <a href="' . $invoice_url . '">' . $invoice->post_title . '</a>';
});

// custom functions

require_once dirname( __FILE__ ) . '/customer-csv-generator.php';

add_action( 'admin_menu', array( WPENON_Immoticket24_Customer_CSV_Generator::instance(), 'add_menu_page' ) );

function wpenon_immoticket24_add_email_tags() {
	edd_add_email_tag( 'certificate_data', __( 'Übersicht über die eingegebenen Energieausweis-Daten', 'wpenon' ), 'wpenon_immoticket24_email_tag_certificate_data' );
}

add_action( 'edd_add_email_tags', 'wpenon_immoticket24_add_email_tags' );

function wpenon_immoticket24_email_tag_certificate_data( $payment_id ) {
	$cart_details = edd_get_payment_meta_cart_details( $payment_id );

	if ( ! is_array( $cart_details ) || empty( $cart_details ) ) {
		return '';
	}

	$item = $cart_details[0];

	$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis( $item['id'] );
	if ( ! $energieausweis ) {
		return '';
	}

	$schema = $energieausweis->getSchema();

	$content_type = EDD()->emails->get_content_type();

	$output = '';
	foreach ( $schema->getFields( $energieausweis, true ) as $field_slug => $field ) {
		if ( ! $field['display'] ) {
			continue;
		}

		$label = $field['label'] . ':';
		if ( 'text/html' == $content_type ) {
			$label = '<strong>' . $label . '</strong>';
		}

		$value = $field['value'];
		if ( in_array( $field['type'], array( 'select', 'radio' ), true ) ) {
			if ( isset( $field['options'][ $value ] ) ) {
				$value = $field['options'][ $value ];
			}
		} elseif ( in_array( $field['type'], array( 'multiselect', 'multibox' ), true ) ) {
			$newvalue = array();
			foreach ( $value as $v ) {
				if ( isset( $field['options'][ $v ] ) ) {
					$newvalue[] = $field['options'][ $v ];
				} else {
					$newvalue[] = $v;
				}
			}
			$value = implode( ', ', $newvalue );
		} elseif ( 'checkbox' === $field['type'] ) {
			$value = $value ? __( 'Ja', 'wpenon' ) : __( 'Nein', 'wpenon' );
		}

		$unit = '';
		if ( ! empty( $field['unit'] ) ) {
			$unit = ' ' . $field['unit'];
			if ( 'text/html' != $content_type ) {
				$unit = str_replace( array( 'm&sup2;', 'm&sup3;' ), array( 'm^2', 'm^3' ), $unit );
			}
		}

		$output .= $label . ' ' . $value . $unit . "\n";
	}

	return $output;
}

function wpenon_immoticket24_print_no_consumption_modal() {
	list( $klima_year, $klima_month ) = array_map( 'absint', explode( '_', get_option( 'wpenon_immoticket24_klimafaktoren_end', '2014_03' ) ) );
	$klima_maximum_year = $klima_year - 2;
	?>
	<div id="wpit_invalid_certificate_modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document" style="margin-top:140px;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?php _e( 'Für dieses Gebäude kann kein Verbrauchsausweis erstellt werden', 'wpenon' ); ?></h4>
				</div>
				<div class="modal-body">
					<?php _e( 'Für vor 1978 errichtete Gebäude mit weniger als 5 Wohneinheiten ohne Wanddämmung darf gemäß EnEV kein Verbrauchsausweis erstellt werden.', 'wpenon' ); ?>
					<?php _e( 'Es ist jedoch möglich einen entsprechenden Bedarfsausweis zu erstellen. Klicken Sie den unten angezeigten Button, um Ihren Ausweis in einen Bedarfsausweis umzuwandeln.', 'wpenon' ); ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default"
					        data-dismiss="modal"><?php _e( 'Angaben bearbeiten', 'wpenon' ); ?></button>
					<button id="wpit_transfer_certificate" type="button"
					        class="wpit_transfer_certificate btn btn-primary"><?php _e( 'In Bedarfsausweis umwandeln', 'wpenon' ); ?></button>
				</div>
			</div>
		</div>
	</div>
	<div id="wpit_invalid_certificate_modal_climatefactors" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document" style="margin-top:140px;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?php _e( 'Für dieses Gebäude kann kein Verbrauchsausweis erstellt werden', 'wpenon' ); ?></h4>
				</div>
				<div class="modal-body">
					<?php _e( 'Für das angegebene Baujahr sind noch keine Klimafaktoren inklusive der beiden Folgejahre verfügbar. Deshalb kann aktuell für dieses Gebäude noch kein Verbrauchsausweis erstellt werden.', 'wpenon' ); ?>
					<?php _e( 'Es ist jedoch möglich einen entsprechenden Bedarfsausweis zu erstellen. Klicken Sie den unten angezeigten Button, um Ihren Ausweis in einen Bedarfsausweis umzuwandeln.', 'wpenon' ); ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default"
					        data-dismiss="modal"><?php _e( 'Angaben bearbeiten', 'wpenon' ); ?></button>
					<button id="wpit_transfer_certificate" type="button"
					        class="wpit_transfer_certificate btn btn-primary"><?php _e( 'In Bedarfsausweis umwandeln', 'wpenon' ); ?></button>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var _wpit_wand_touched = false;
		var _wpit_climatefactors_target_year = <?php echo esc_js( $klima_maximum_year ); ?>;

		function wpenon_immoticket24_check_certificate_valid(e) {
			// Strict check if no parameter given (when form is submitted).
			var strict = 'undefined' === typeof e;

			if ( ! jQuery('#wpit_transfer_certificate_input').length ) {
				var wohnungen = parseInt(jQuery('#wohnungen').val(), 10);
				var baujahr = parseInt(jQuery('#baujahr').val(), 10);
				var dach = jQuery('#dach').val();
				var wand_daemmung_on = jQuery('#wand_daemmung_on').val();
				var decke_daemmung_on = jQuery('#decke_daemmung_on').val();
				var dach_daemmung_on = jQuery('#dach_daemmung_on').val();

				if ( wohnungen >= 5 || baujahr > 1977 ) {
					return true;
				}

				if ( wand_daemmung_on === 'no' || ( ( dach === 'unbeheizt' || dach === 'nicht-vorhanden' ) && decke_daemmung_on === 'no' ) ) {
					jQuery('#wpit_invalid_certificate_modal').modal('show');
					return false;
				}

				if ( wand_daemmung_on === 'no' || ( dach === 'beheizt' && dach_daemmung_on === 'no' ) ) {
					jQuery('#wpit_invalid_certificate_modal').modal('show');
					return false;
				}
			}

			return true;
		}

		function wpenon_immoticket24_check_certificate_climatefactors_valid(e) {
			// Strict check if no parameter given (when form is submitted).
			var strict = 'undefined' === typeof e;

			if (!jQuery('#wpit_transfer_certificate_input').length) {
				var baujahr = parseInt(jQuery('#baujahr').val(), 10);

				if (strict || baujahr > 0) {
					if (baujahr > _wpit_climatefactors_target_year) {
						jQuery('#wpit_invalid_certificate_modal_climatefactors').modal('show');

						return false;
					}
				}
			}

			return true;
		}

		jQuery(document).on('change', '#wohnungen', wpenon_immoticket24_check_certificate_valid);
		jQuery(document).on('change', '#baujahr', wpenon_immoticket24_check_certificate_valid);
		jQuery(document).on('change', '#wand_daemmung', wpenon_immoticket24_check_certificate_valid);

		jQuery(document).on('change', '#baujahr', wpenon_immoticket24_check_certificate_climatefactors_valid);

		jQuery('#wpenon-generate-form').on('submit', function (e) {
			if (!wpenon_immoticket24_check_certificate_valid() || !wpenon_immoticket24_check_certificate_climatefactors_valid()) {
				e.preventDefault();
			}
		});

		jQuery('.wpit_transfer_certificate').on('click', function (e) {
			e.preventDefault();

			jQuery('#wpenon-generate-form').append('<input type="hidden" id="wpit_transfer_certificate_input" name="wpenon_type" value="bw" />');
			jQuery('#wpenon-generate-form').trigger('submit');
		});

		jQuery('#wand_daemmung').one('focus', function () {
			_wpit_wand_touched = true;

			jQuery('#wand_daemmung').one('focusout', wpenon_immoticket24_check_certificate_valid);
		});
	</script>
	<?php
}

function wpenon_immoticket24_enqueue_no_consumption_modal( $template_slug, $template_suffix ) {
	if ( 'edit' !== $template_slug || 'vw' !== $template_suffix ) {
		return;
	}

	add_action( 'wp_footer', 'wpenon_immoticket24_print_no_consumption_modal' );
}

add_action( 'wpenon_frontend_view_init', 'wpenon_immoticket24_enqueue_no_consumption_modal', 10, 2 );

function wpenon_immoticket24_script_vars( $vars ) {
	$vars['grundriss_formen'] = wpenon_immoticket24_get_grundriss_formen();
	$vars['anbau_formen']     = wpenon_immoticket24_get_anbau_formen();

	return $vars;
}

add_filter( 'wpenon_script_vars', 'wpenon_immoticket24_script_vars' );

function wpenon_immoticket24_enqueue_additional_js() {
	wp_enqueue_script( 'immoticket24-extra-js', WPENON_DATA_URL . '/assets/extra-scripts.js', array( 'wpenon-frontend' ), false, true );
}

// add_action( 'wp_enqueue_scripts', 'wpenon_immoticket24_enqueue_additional_js' );

function wpenon_immoticket24_pdf_seller_company_name( $name, $pdf ) {
	return 'Harsche-Energieberatung';
}

add_filter( 'wpenon_pdf_seller_company_name', 'wpenon_immoticket24_pdf_seller_company_name', 10, 2 );

function wpenon_immoticket24_pdf_seller_meta( $meta, $pdf ) {
	$meta = 'Inh. Roland Harsche' . "\n" . \WPENON\Util\Format::pdfEncode( '(geprüfter Gebäudeenergieberater' . "\n" . 'Öko-Zentrum NRW)' . "\n" . 'Gartenstraße 25, 53498 Bad Breisig' );

	return $meta;
}

add_filter( 'wpenon_pdf_seller_meta', 'wpenon_immoticket24_pdf_seller_meta', 10, 2 );

function wpenon_immoticket24_pdf_fonts( $fonts ) {
	$fonts['aussteller'] = array( 'Times', '', 10, 5 );

	return $fonts;
}

add_filter( 'wpenon_pdf_fonts', 'wpenon_immoticket24_pdf_fonts', 10, 1 );

function wpenon_immoticket24_make_anlagenkeys( $field_prefix, $field_suffix = '' ) {
	if ( ! empty( $field_suffix ) ) {
		$field_suffix = '_' . $field_suffix;
	}

	return array(
		$field_prefix . '150' . $field_suffix,
		$field_prefix . '500' . $field_suffix,
		$field_prefix . '2500' . $field_suffix,
	);
}

function wpenon_immoticket24_address_field_labels( $labels ) {
	$labels['card_address']    = __( 'Straße und Hausnummer', 'wpenon' );
	$labels['card_address_2']  = __( 'Zusätzliche Adresszeile (optional)', 'wpenon' );
	$labels['card_city']       = __( 'Ort', 'wpenon' );
	$labels['card_zip']        = __( 'Postleitzahl', 'wpenon' );
	$labels['billing_country'] = __( 'Land', 'wpenon' );
	$labels['card_state']      = __( 'Bundesland', 'wpenon' );

	return $labels;
}

add_filter( 'wpenon_address_field_labels', 'wpenon_immoticket24_address_field_labels' );

function wpenon_immoticket24_render_receipt_pdf( $payment, $seller_meta, $pdf ) {
	if ( $pdf->is_bulk() ) {
		return;
	}

	$business_contact = $seller_meta['firmenname'] . "\n" . $seller_meta['strassenr'] . "\n" . $seller_meta['plz'] . ' ' . $seller_meta['ort'] . "\n";
	$business_contact .= __( 'Telefon', 'wpenon' ) . ': ' . $seller_meta['telefon'] . "\n" . __( 'Email', 'wpenon' ) . ': ' . $seller_meta['email'];

	// Widerrufsbelehrung
	$pdf->createNewPage();
	$pdf->renderHeader();

	$pdf->SetPageFont( 'title' );
	$pdf->WriteCell( $pdf->escape( 'Widerrufsbelehrung' ), 'L', 1, 0 );

	$pdf->SetPageFont( 'small' );
	$pdf->WriteMultiCell( $pdf->escape( 'Verbraucher haben das Recht, binnen dreißig Tagen ohne Angabe von Gründen diesen Vertrag zu widerrufen.' ), 'L', 1, 0 );
	$pdf->WriteMultiCell( $pdf->escape( 'Die Widerrufsfrist beträgt dreißig Tage ab dem Tag des Vertragsabschlusses.' ), 'L', 1, 0 );
	$pdf->WriteMultiCell( $pdf->escape( 'Um Ihr Widerrufsrecht auszuüben, müssen Sie uns' ), 'L', 1, 0 );
	$pdf->WriteMultiCell( $pdf->escape( $business_contact ), 'L', 1, 0 );
	$pdf->WriteMultiCell( $pdf->escape( 'mittels einer eindeutigen Erklärung (z. B. ein mit der Post versandter Brief, Telefax oder E-Mail) über Ihren Entschluss, diesen Vertrag zu widerrufen, informieren.' ), 'L', 1, 0 );
	$pdf->WriteMultiCell( $pdf->escape( 'Zur Wahrung der Widerrufsfrist reicht es aus, dass Sie die Mitteilung über die Ausübung des Widerrufsrechts vor Ablauf der Widerrufsfrist absenden. Sie können dafür das beigefügte Muster-Widerrufsformular verwenden, das jedoch nicht vorgeschrieben ist.' ), 'L', 1, 0 );

	$pdf->SetStyle( 'B', true );
	$pdf->WriteCell( $pdf->escape( 'Folgen des Widerrufs' ), 'L', 1, 0 );
	$pdf->SetStyle( 'B', false );
	$pdf->WriteMultiCell( $pdf->escape( 'Wenn Sie diesen Vertrag widerrufen, haben wir Ihnen alle Zahlungen, die wir von Ihnen erhalten haben, einschließlich der Lieferkosten (mit Ausnahme der zusätzlichen Kosten, die sich daraus ergeben, dass Sie eine andere Art der Lieferung als die von uns angebotene, günstigste Standardlieferung gewählt haben), unverzüglich und spätestens binnen dreißig Tagen ab dem Tag zurückzuzahlen, an dem die Mitteilung über Ihren Widerruf dieses Vertrags bei uns eingegangen ist. Für diese Rückzahlung verwenden wir dasselbe Zahlungsmittel, das Sie bei der ursprünglichen Transaktion eingesetzt haben, es sei denn, mit Ihnen wurde ausdrücklich etwas anderes vereinbart; in keinem Fall werden Ihnen wegen dieser Rückzahlung Entgelte berechnet.' ), 'L', 1, 0 );
	$pdf->WriteMultiCell( $pdf->escape( 'Haben Sie verlangt, dass die Dienstleistungen während der Widerrufsfrist beginnen soll, so haben Sie uns einen angemessenen Betrag zu zahlen, der dem Anteil der bis zu dem Zeitpunkt, zu dem Sie uns von der Ausübung des Widerrufsrechts hinsichtlich dieses Vertrags unterrichten, bereits erbrachten Dienstleistungen im Vergleich zum Gesamtumfang der im Vertrag vorgesehenen Dienstleistungen entspricht.' ), 'L', 1, 0 );

	$pdf->SetStyle( 'B', true );
	$pdf->WriteCell( $pdf->escape( 'Ende der Widerrufsbelehrung' ), 'L', 1, 0 );
	$pdf->SetStyle( 'B', false );

	$pdf->renderFooter();

	// Widerrufsformular
	$pdf->createNewPage();
	$pdf->renderHeader();

	$pdf->SetPageFont( 'title' );
	$pdf->WriteCell( $pdf->escape( 'Widerrufsformular für den Verbraucher' ), 'L', 1, 0 );

	$pdf->SetPageFont( 'text' );
	$pdf->WriteMultiCell( $pdf->escape( 'Wenn Sie den Vertrag widerrufen wollen, dann füllen Sie dieses Formular aus und senden Sie es zurück an:' ), 'L', 1, 0 );
	$pdf->WriteMultiCell( $pdf->escape( $business_contact ), 'L', 1, 0 );

	$pdf->Ln( 3 );

	$pdf->WriteMultiCell( $pdf->escape( 'Hiermit widerrufe(n) ich/wir den von mir/uns abgeschlossenen Vertrag über die Erbringung der folgenden Dienstleistung:' ), 'L', 1, 0 );

	$pdf->Ln( 3 );

	$pdf->WriteCell( $pdf->escape( 'Bestellt am:' ), 'L', 0, 70 );
	$pdf->WriteCell( '', 'L', 1, 100, null, false, 'B' );
	$pdf->WriteCell( $pdf->escape( 'Name des/der Verbraucher(s):' ), 'L', 0, 70 );
	$pdf->WriteCell( '', 'L', 1, 100, null, false, 'B' );
	$pdf->WriteCell( $pdf->escape( 'Anschrift des/der Verbraucher(s):' ), 'L', 0, 70 );
	$pdf->WriteCell( '', 'L', 1, 100, null, false, 'B' );
	$pdf->WriteCell( '', 'L', 0, 70 );
	$pdf->WriteCell( '', 'L', 1, 100, null, false, 'B' );

	$pdf->Ln( 10 );

	$pdf->SetPageFont( 'small' );

	$pdf->WriteCell( '', 'L', 0, 80, null, false, 'B' );
	$pdf->WriteCell( '', 'L', 0, 10 );
	$pdf->WriteCell( '', 'L', 1, 80, null, false, 'B' );
	$pdf->WriteCell( $pdf->escape( 'Datum' ), 'L', 0, 80 );
	$pdf->WriteCell( '', 'L', 0, 10 );
	$pdf->WriteCell( $pdf->escape( 'Unterschrift des/der Verbraucher(s) (nur bei Mitteilung auf Papier)' ), 'L', 1, 80 );

	$pdf->renderFooter();
}

add_action( 'wpenon_render_receipt_pdf', 'wpenon_immoticket24_render_receipt_pdf', 10, 3 );

function wpenon_immoticket24_make_yearkey( $year, $table, $gedaemmt = false ) {
	switch ( $table ) {
		case 'uwerte':
		case 'uwerte2019':
			$steps = array( 1918, 1948, 1957, 1968, 1978, 1983, 1994 );
			foreach ( $steps as $step ) {
				if ( $year <= $step ) {
					return 'bis' . $step;
				}
			}

			return 'ab1995';
		case 'uwerte202001':
			$steps = array( 1918, 1948, 1957, 1968, 1978, 1983, 1994, 2001, 2006 );
			foreach ( $steps as $step ) {
				if ( $year <= $step ) {
					return 'bis' . $step;
				}
			}

			return 'ab2007';
		case 'h_erzeugung':
		case 'h_erzeugung2019':
		case 'h_erzeugung202001':
		case 'ww_erzeugung':
		case 'ww_erzeugung2019':
		case 'ww_erzeugung202001':
			$steps = array( 1986, 1994 );
			foreach ( $steps as $step ) {
				if ( $year <= $step ) {
					return (string) $step;
				}
			}

			return '1995';
		case 'h_verteilung':
		case 'h_verteilung2019':
		case 'ww_verteilung':
			$steps = array( 1978, 1994 );
			foreach ( $steps as $step ) {
				if ( $year <= $step ) {
					$ret = (string) $step;
					if ( $step == 1978 && $gedaemmt ) {
						$ret .= 'd';
					}

					return $ret;
				}
			}

			return '1995';
		case 'h_speicherung':
			if ( $year <= 1994 ) {
				return '1994';
			}

			return '1995';
		case 'ww_speicherung':
			return '';
		case 'h_uebergabe':
			return '';
		case 'l_erzeugung':
		case 'l_verteilung':
			$steps = array( 1989, 1994 );
			foreach ( $steps as $step ) {
				if ( $year <= $step ) {
					return (string) $step;
				}
			}

			return '1995';
		default:
	}

	return false;
}

function wpenon_immoticket24_get_dach_formen() {
	return array(
		'satteldach' => __( 'Satteldach', 'wpenon' ),
		'pultdach'   => __( 'Pultdach', 'wpenon' ),
		'walmdach'   => __( 'Walmdach', 'wpenon' ),
	);
}

function wpenon_immoticket24_get_grundriss_formen() {
	// erstes Array-Element: Seitenlänge eingeben / Berechnungsformel?
	// zweites Array-Element: Offset für Himmelsrichtung
	return array(
		'a' => array(
			'a'   => array( true, 0 ),
			'b'   => array( true, 1 ),
			'c'   => array( 'a', 2 ),
			'd'   => array( 'b', 3 ),
			'fla' => array(
				array( 'a', 'b' ),
			),
		),
		'b' => array(
			'a'   => array( true, 0 ),
			'b'   => array( true, 1 ),
			'c'   => array( true, 2 ),
			'd'   => array( true, 3 ),
			'e'   => array( 'a - c', 2 ),
			'f'   => array( 'b - d', 3 ),
			'fla' => array(
				array( 'a', 'f' ),
				array( 'c', 'd' ),
			),
		),
		'c' => array(
			'a'   => array( true, 0 ),
			'b'   => array( true, 1 ),
			'c'   => array( true, 2 ),
			'd'   => array( true, 1 ),
			'e'   => array( true, 2 ),
			'f'   => array( 'd', 3 ),
			'g'   => array( 'a - c - e', 2 ),
			'h'   => array( 'b', 3 ),
			'fla' => array(
				array( 'a', 'b' ),
				array( 'd', 'e' ),
			),
		),
		'd' => array(
			'a'   => array( true, 0 ),
			'b'   => array( true, 1 ),
			'c'   => array( true, 2 ),
			'd'   => array( true, 3 ),
			'e'   => array( true, 2 ),
			'f'   => array( true, 1 ),
			'g'   => array( 'a - c - e', 2 ),
			'h'   => array( 'b - d + f', 3 ),
			'fla' => array(
				array( 'a', 'b - d' ),
				array( 'c', 'd' ),
				array( 'f', 'g' ),
			),
		),
	);
}

function wpenon_immoticket24_get_grundriss_dropdown() {
	$formen = wpenon_immoticket24_get_grundriss_formen();
	foreach ( $formen as $key => &$value ) {
		$value = sprintf( __( 'Form %s', 'wpenon' ), strtoupper( $key ) );
	}

	return $formen;
}

function wpenon_immoticket24_get_anbau_formen() {
	// erstes Array-Element: Seitenlänge eingeben / Berechnungsformel?
	// zweites Array-Element: Offset für Himmelsrichtung
	return array(
		'a' => array(
			'b'   => array( true, 0 ),
			't'   => array( true, 1 ),
			's1'  => array( true, 3 ),
			's2'  => array( 'b', 2 ),
			'fla' => array(
				array( 'b', 't' ),
			),
		),
		'b' => array(
			'b'   => array( true, 0 ),
			't'   => array( true, 1 ),
			's1'  => array( true, 3 ),
			's2'  => array( true, 2 ),
			'fla' => array(
				array( 'b', 's2' ),
				array( 't - s1', 's1' ),
			),
		),
	);
}

function wpenon_immoticket24_get_anbau_dropdown() {
	$formen = wpenon_immoticket24_get_anbau_formen();
	foreach ( $formen as $key => &$value ) {
		$value = sprintf( __( 'Form %s', 'wpenon' ), strtoupper( $key ) );
	}

	return $formen;
}

function wpenon_immoticket24_get_himmelsrichtungen() {
	return array(
		's'  => __( 'Süden', 'wpenon' ),
		'so' => __( 'Südosten', 'wpenon' ),
		'o'  => __( 'Osten', 'wpenon' ),
		'no' => __( 'Nordosten', 'wpenon' ),
		'n'  => __( 'Norden', 'wpenon' ),
		'nw' => __( 'Nordwesten', 'wpenon' ),
		'w'  => __( 'Westen', 'wpenon' ),
		'sw' => __( 'Südwesten', 'wpenon' ),
	);
}

function wpenon_immoticket24_get_pufferspeicher_erzeuger() {
	return array_values( array_flip( array_filter( wpenon_get_table_results( 'h_erzeugung', array(), array( 'speicher' ) ) ) ) );
}

function wpenon_immoticket24_get_heizungsanlagen() {
	return wpenon_get_table_results( 'h_erzeugung', array(), array( 'name' ) );
}

function wpenon_immoticket24_get_warmwasseranlagen() {
	return wpenon_get_table_results( 'ww_erzeugung', array(), array( 'name' ) );
}

function wpenon_immoticket24_get_heizungsanlagen2019() {
	return wpenon_get_table_results( 'h_erzeugung2019', array(), array( 'name' ) );
}

function wpenon_immoticket24_get_heizungsanlagen202001() {
	return wpenon_get_table_results( 'h_erzeugung202001', array(), array( 'name' ) );
}

function wpenon_immoticket24_get_warmwasseranlagen2019() {
	return wpenon_get_table_results( 'ww_erzeugung2019', array(), array( 'name' ) );
}

function wpenon_immoticket24_get_warmwasseranlagen202001() {
	return wpenon_get_table_results( 'ww_erzeugung202001', array(), array( 'name' ) );
}

function wpenon_immoticket24_get_lueftungsanlagen() {
	return wpenon_get_table_results( 'l_erzeugung', array(), array( 'name' ) );
}

function wpenon_immoticket24_get_energietraeger( $with_units = false ) {
	$table_slug = 'energietraeger';
	if ( strtotime( '2016-01-01' ) <= strtotime( wpenon_get_reference_date( 'Y-m-d' ) ) ) {
		$table_slug = 'energietraeger2016';
	}

	if ( $with_units ) {
		$table_slug = 'energietraeger_umrechnungen';
	}

	$energietraeger = wpenon_get_table_results( $table_slug, array(), array( 'name' ) );

	if ( $with_units ) {
		$energietraeger = array_map( array( '\WPENON\Util\Format', 'unit' ), $energietraeger );
	}

	return $energietraeger;
}

function wpenon_immoticket24_get_energietraeger_name( $slug, $is_with_units = false ) {
	if ( $is_with_units ) {
		$slug = wpenon_get_table_results( 'energietraeger_umrechnungen', array(
			'bezeichnung' => array(
				'value'   => $slug,
				'compare' => '='
			)
		), array( 'energietraeger' ), true );
	}

	$table_slug = 'energietraeger';
	if ( strtotime( '2016-01-01' ) <= strtotime( wpenon_get_reference_date( 'Y-m-d' ) ) ) {
		$table_slug = 'energietraeger2016';
	}

	$energietraeger_name = wpenon_get_table_results( $table_slug, array(
		'bezeichnung' => array(
			'value'   => $slug,
			'compare' => '='
		)
	), array( 'name' ), true );

	return $energietraeger_name;
}

function wpenon_immoticket24_get_regenerativ_art_name( $slug ) {
	$regenerativ_arten = array(
		'keine' => 'Keine',
		'solar' => 'Solaranlage',
	);

	if ( array_key_exists( $slug, $regenerativ_arten ) ) {
		return $regenerativ_arten[ $slug ];
	}

	return 'Keine';
}

function wpenon_immoticket24_get_regenerativ_nutzung_name( $slug ) {
	$values = array(
		'warmwasser'                 => 'Warmwasser',
		'warmwasser_waermeerzeugung' => 'Warmwasser und Wärmeerzeugung',
	);

	if ( array_key_exists( $slug, $values ) ) {
		return $values[ $slug ];
	}

	return 'Keine';
}

function wpenon_immoticket24_get_fenster_bauarten() {
	$_fenster = wpenon_get_table_results( 'uwerte', array(
		'bezeichnung' => array(
			'value'   => 'fenster_%',
			'compare' => 'LIKE'
		)
	), array( 'name' ), false, 'name', 'ASC' );

	$fenster = array();
	foreach ( $_fenster as $bezeichnung => $name ) {
		$fenster[ str_replace( 'fenster_', '', $bezeichnung ) ] = $name;
	}

	return $fenster;
}

function wpenon_immoticket24_get_bauarten() {
	return array(
		'massiv' => __( 'Massiv', 'wpenon' ),
		'holz'   => __( 'Holz', 'wpenon' ),
	);
}

function wpenon_immoticket24_get_bauarten_boden() {
	return array(
		'massiv'     => __( 'Massiv', 'wpenon' ),
		'holz'       => __( 'Holz', 'wpenon' ),
		'stahlbeton' => __( 'Stahlbeton', 'wpenon' ),
	);
}


function wpenon_immoticket24_get_bauarten_keller() {
	return array(
		'holzhaus_holz'     => __( 'Holz', 'wpenon' ),
		'massiv_bis_20cm'   => __( 'Sonstige Massivwände bis 20 cm', 'wpenon' ),
		'massiv_ueber_20cm' => __( 'Sonstige Massivwände über 20 cm', 'wpenon' ),
	);
}

function wpenon_immoticket24_get_bauarten_holzhaus() {
	return array(
		'holz' => __( 'Holz', 'wpenon' ),
	);
}

function wpenon_immoticket24_get_bauarten_fachwerk() {
	return array(
		'lehm'       => __( 'Lehm-/Lehmziegelausfachung', 'wpenon' ),
		'vollziegel' => __( 'Vollziegel oder Massive Natursteinausfach', 'wpenon' ),
	);
}

function wpenon_immoticket24_get_bauarten_massiv() {
	return array(
		'bims'        => __( 'Hochlochziegel, Bimsbeton; z. B. Poroton', 'wpenon' ),
		'zweischalig' => __( 'Zweischalige Bauweise', 'wpenon' ),
		'bis_20cm'    => __( 'Sonstige Massivwände bis 20 cm', 'wpenon' ),
		'ueber_20cm'  => __( 'Sonstige Massivwände über 20 cm', 'wpenon' ),
	);
}

function wpenon_immoticket24_get_g_wert( $bauart, $reference = false ) {
	if ( ! $reference ) {
		switch ( $bauart ) {
			case 'holzeinfach':
				return 0.87;
			default:
				return 0.6;
		}
	}

	return 0.6;
}

function wpenon_get_construction_year( $construction_year, $field_year ) {
	// If field was already set
	if( 1800 !== (int) $field_year ) {
		return $field_year;
	}

	return $construction_year;
}

function wpenon_immoticket24_get_klimafaktoren_zeitraeume202001() {
	$zeitraeume = array();

	$reference = wpenon_get_reference_date( 'timestamp' );

	$_daten = \WPENON\Util\DB::getTableColumns( \WPENON\Util\Format::prefix( 'klimafaktoren202001' ) );
	$_daten = array_slice( $_daten, 1, count( $_daten ) - 25 );

	$daten = array();
	foreach ( $_daten as $_datum ) {
		$daten[] = $_datum->Field;
	}
	unset( $_datum );
	unset( $_daten );

	$year = $month = '';

	foreach ( $daten as $datum ) {
		if ( wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $datum, 2, true, 'timestamp' ) > $reference ) {
			break;
		}
		$zeitraeume[ $datum ] = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $datum, 0, false, 'data' ) . ' - ' . wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $datum, 2, true, 'data' );
	}

	$zeitraeume = array_reverse( $zeitraeume );

	return $zeitraeume;
}

function wpenon_immoticket24_get_klimafaktoren_zeitraeume() {
	$zeitraeume = array();

	$reference = wpenon_get_reference_date( 'timestamp' );

	$_daten = \WPENON\Util\DB::getTableColumns( \WPENON\Util\Format::prefix( 'klimafaktoren' ) );
	$_daten = array_slice( $_daten, 1, count( $_daten ) - 25 );

	$daten = array();
	foreach ( $_daten as $_datum ) {
		$daten[] = $_datum->Field;
	}
	unset( $_datum );
	unset( $_daten );

	$year = $month = '';

	foreach ( $daten as $datum ) {
		if ( wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $datum, 2, true, 'timestamp' ) > $reference ) {
			break;
		}
		$zeitraeume[ $datum ] = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $datum, 0, false, 'data' ) . ' - ' . wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $datum, 2, true, 'data' );
	}

	$zeitraeume = array_reverse( $zeitraeume );

	return $zeitraeume;
}

function wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $datum, $index = 0, $end = false, $format = 'coll' ) {
	$year = $month = '';
	list( $year, $month ) = array_map( 'absint', explode( '_', $datum ) );

	$year += $index;
	$day  = 1;
	if ( $end ) {
		$year  += 1;
		$month = ( $month + 11 ) % 12;
		if ( $month === 0 ) {
			$year  -= 1;
			$month = 12;
		}
		$day = cal_days_in_month( CAL_GREGORIAN, $month, $year );
	}

	$date = strtotime( zeroise( $year, 4 ) . '-' . zeroise( $month, 2 ) . '-' . zeroise( $day, 2 ) );

	if ( $format == 'coll' ) {
		$format = __( 'M. Y', 'wpenon' );
	} elseif ( $format == 'data_short' ) {
		$format = __( 'm/Y', 'wpenon' );
	} elseif ( $format == 'data' ) {
		$format = __( 'd.m.Y', 'wpenon' );
	} elseif ( $format == 'slug' ) {
		$format = 'Y_m';
	} elseif ( $format == 'timestamp' ) {
		return $date;
	}

	return date_i18n( $format, $date );
}

function wpenon_immoticket24_validate_year_greater_than( $value, $field ) {
	$value = \WPENON\Util\Validate::int( $value, $field );
	if ( isset( $value['error'] ) || isset( $value['warning'] ) ) {
		return $value;
	}

	$value = $value['value'];
	$error = '';
	if ( isset( $field['validate_dependencies'][0] ) && $field['validate_dependencies'][0] ) {
		$reference_year = absint( $field['validate_dependencies'][0] );
		if ( $value < $reference_year ) {
			$error = __( 'Das eingegebene Jahr ist kleiner als das Baujahr des Gebäudes.', 'wpenon' );
		}
	}

	return \WPENON\Util\Validate::formatResponse( $value, $field, $error );
}

function wpenon_immoticket24_validate_area_lower_than( $value, $field ) {
	$value = \WPENON\Util\Validate::float( $value, $field );
	if ( isset( $value['error'] ) || isset( $value['warning'] ) ) {
		return $value;
	}

	$value = $value['value'];
	$error = '';
	if ( isset( $field['validate_dependencies'][0] ) && $field['validate_dependencies'][0] ) {
		$reference_area = \WPENON\Util\Parse::float( $field['validate_dependencies'][0] );
		if ( $value > $reference_area ) {
			$error = __( 'Die eingegebene Fläche ist größer als die Wohnfläche des Gebäudes.', 'wpenon' );
		}
	}

	return \WPENON\Util\Validate::formatResponse( $value, $field, $error );
}

function wpenon_immoticket24_validate_at_least_one_fenster( $value, $field ) {
	$value = \WPENON\Util\Validate::float( $value, $field );
	if ( isset( $value['error'] ) || isset( $value['warning'] ) ) {
		return $value;
	}

	$value = $value['value'];
	$error = '';
	if ( $value == 0.0 ) {
		$all_fensters_empty = true;
		foreach ( $field['validate_dependencies'] as $dependency ) {
			$other_fenster = \WPENON\Util\Parse::float( $dependency );
			if ( $other_fenster > 0.0 ) {
				$all_fensters_empty = false;
				break;
			}
		}

		if ( $all_fensters_empty ) {
			$error = __( 'Mindestens eine der angegebenen Fensterflächen muss größer als 0 sein.', 'wpenon' );
		}
	}

	return \WPENON\Util\Validate::formatResponse( $value, $field, $error );
}

function wpenon_immoticket24_get_modernisierungsempfehlungen( $energieausweis = null ) {
	$_modernisierungsempfehlungen = array(
		'heizung'            => array(
			'bauteil'      => 'Heizung',
			'beschreibung' => 'Austausch der Heizungsanlage',
			'gesamt'       => true,
			'einzeln'      => true,
			'amortisation' => '',
			'kosten'       => '',
			'dibt_value'   => 'Heizung',
		),
		'heizkessel'         => array(
			'bauteil'      => 'Heizkessel',
			'beschreibung' => 'Erneuerung des Heizkessels',
			'gesamt'       => true,
			'einzeln'      => true,
			'amortisation' => '',
			'kosten'       => '',
			'dibt_value'   => 'Heizung',
		),
		'rohrleitungssystem' => array(
			'bauteil'      => 'Rohrleitungssystem',
			'beschreibung' => 'Dämmung freiliegender Heizungsrohre',
			'gesamt'       => true,
			'einzeln'      => true,
			'amortisation' => '',
			'kosten'       => '',
			'dibt_value'   => 'Wärmeverteilung / -abgabe',
		),
		'dach'               => array(
			'bauteil'      => 'Dach',
			'beschreibung' => 'Dämmstärken von mindestens 14 cm oder mehr',
			'gesamt'       => true,
			'einzeln'      => true,
			'amortisation' => '',
			'kosten'       => '',
			'dibt_value'   => 'Dach',
		),
		'decke'              => array(
			'bauteil'      => 'Oberste Geschossdecke',
			'beschreibung' => 'Dämmstärken von mindestens 14 cm oder mehr',
			'gesamt'       => true,
			'einzeln'      => true,
			'amortisation' => '',
			'kosten'       => '',
			'dibt_value'   => 'Sonstiges',
		),
		'wand'               => array(
			'bauteil'      => 'Wände',
			'beschreibung' => 'Dämmstärken von mindestens 14 cm oder mehr',
			'gesamt'       => true,
			'einzeln'      => true,
			'amortisation' => '',
			'kosten'       => '',
			'dibt_value'   => 'Außenwand gg. Erdreich',
		),
		'boden'              => array(
			'bauteil'      => 'Kellerdecke',
			'beschreibung' => 'Dämmstärken von mindestens 12 cm oder mehr',
			'gesamt'       => true,
			'einzeln'      => true,
			'amortisation' => '',
			'kosten'       => '',
			'dibt_value'   => 'Kellerdecke',
		),
		'fenster'            => array(
			'bauteil'      => 'Fenster',
			'beschreibung' => 'Maximaler Uw - Wert bei 1,3 [W/m&sup2;K]',
			'gesamt'       => true,
			'einzeln'      => true,
			'amortisation' => '',
			'kosten'       => '',
			'dibt_value'   => 'Fenster',
		),
		'solarthermie'       => array(
			'bauteil'      => 'Solarthermie',
			'beschreibung' => 'Solare Unterstützung für Warmwasser und Heizung',
			'gesamt'       => true,
			'einzeln'      => true,
			'amortisation' => '',
			'kosten'       => '',
			'dibt_value'   => 'Sonstiges',
		),
	);

	if ( ! $energieausweis ) {
		return $_modernisierungsempfehlungen;
	}

	$modernisierungsempfehlungen = array();

	if ( wpenon_immoticket24_is_empfehlung_active( 'heizung', $energieausweis ) ) {
		$heatings = array( 'h' );
		if ( isset( $energieausweis->h2_info ) && $energieausweis->h2_info ) {
			$heatings[] = 'h2';
			if ( isset( $energieausweis->h3_info ) && $energieausweis->h3_info ) {
				$heatings[] = 'h3';
			}
		}

		$current_year = absint( current_time( 'Y' ) );
		$kessel       = array(
			'gasraumheizer',
			'elektronachtspeicherheizung',
			'oelofenverdampfungsbrenner',
		);

		foreach ( $heatings as $heating ) {
			$type_field = $heating . '_erzeugung';
			$year_field = $heating . '_baujahr';

			if ( in_array( $energieausweis->$type_field, $kessel, true ) && ! empty( $energieausweis->$year_field ) && $energieausweis->$year_field <= $current_year - 30 ) {
				$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['heizung'];
			}
		}
	}

	if ( wpenon_immoticket24_is_empfehlung_active( 'heizkessel', $energieausweis ) ) {
		$heatings = array( 'h' );
		if ( isset( $energieausweis->h2_info ) && $energieausweis->h2_info ) {
			$heatings[] = 'h2';
			if ( isset( $energieausweis->h3_info ) && $energieausweis->h3_info ) {
				$heatings[] = 'h3';
			}
		}

		$current_year = absint( current_time( 'Y' ) );
		$kessel       = array(
			'standardkessel',
		);

		foreach ( $heatings as $heating ) {
			$type_field = $heating . '_erzeugung';
			$year_field = $heating . '_baujahr';

			if ( in_array( $energieausweis->$type_field, $kessel, true ) && ! empty( $energieausweis->$year_field ) && $energieausweis->$year_field <= $current_year - 25 ) {
				$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['heizkessel'];
			}

		}
	}

	$minimum_date_rohleitung = strtotime( '2019-11-16 0:00' );
	$energieausweis_date     = strtotime( $energieausweis->date );

	if ( wpenon_immoticket24_is_empfehlung_active( 'rohrleitungssystem', $energieausweis ) && $energieausweis_date > $minimum_date_rohleitung ) {
		if ( 'bw' === $energieausweis->wpenon_type && $energieausweis->verteilung_baujahr <= 1978 && true !== $energieausweis->verteilung_gedaemmt && 'unbeheizt' == $energieausweis->keller ) {
			$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['rohrleitungssystem'];
		}
	}

	if ( ( $energieausweis_date > strtotime( '2019-12-11' ) ) && intval( $energieausweis->baujahr ) < 1995 || $energieausweis_date < strtotime( '2019-12-12' ) ) {

		if ( wpenon_immoticket24_is_empfehlung_active( 'dach', $energieausweis ) ) {
			$energieausweis_mode          = $energieausweis->mode;
			$energieausweis_dach          = $energieausweis->dach;
			$energieausweis_dach_daemmung = $energieausweis->dach_daemmung;

			if ( 'b' === $energieausweis_mode && ( $energieausweis_dach === 'beheizt' || $energieausweis_dach === 'nicht-vorhanden' ) && $energieausweis_dach_daemmung < 14.0 ) {
				$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['dach'];
			} elseif ( 'v' === $energieausweis_mode && $energieausweis_dach === 'beheizt' && $energieausweis_dach_daemmung < 14.0 ) {
				$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['dach'];
			}
		}

		if ( wpenon_immoticket24_is_empfehlung_active( 'decke', $energieausweis ) ) {
			$energieausweis_mode           = $energieausweis->mode;
			$energieausweis_dach           = $energieausweis->dach;
			$energieausweis_decke_daemmung = $energieausweis->decke_daemmung;

			if ( 'b' === $energieausweis_mode && $energieausweis_dach === 'unbeheizt' && $energieausweis_decke_daemmung < 14.0 ) {
				$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['decke'];
			} elseif ( 'v' === $energieausweis_mode && ( $energieausweis_dach === 'unbeheizt' || $energieausweis_dach === 'nicht-vorhanden' ) && $energieausweis_decke_daemmung < 14.0 ) {
				$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['decke'];
			} elseif ( 76736 === (int) $energieausweis->id ) { // Hacky fix for a weird bug.
				$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['decke'];
			}
		} elseif ( 76736 === (int) $energieausweis->id ) { // Hacky fix for a weird bug.
			$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['decke'];
		}

		if ( wpenon_immoticket24_is_empfehlung_active( 'wand', $energieausweis ) ) {
			if ( ! $energieausweis->wand_porenbeton || in_array( $energieausweis->wand_porenbeton, array( 'nein', 'unbekannt' ), true ) ) {
				if( ! $energieausweis->wand_bauart_massiv || $energieausweis->wand_bauart_massiv !== 'wand_massiv_vollziegel_ueber_30cm' ) {

					if ( $energieausweis->mode == 'v' && $energieausweis->wand_daemmung < 4.0 ) {
						$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['wand'];
					} elseif ( $energieausweis->mode == 'b' ) {
						foreach ( array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h' ) as $wand ) {
							$laengenslug   = 'wand_' . $wand . '_laenge';
							$nachbarslug   = 'wand_' . $wand . '_nachbar';
							$daemmungsslug = 'wand_' . $wand . '_daemmung';

							$wand_laenge   = $energieausweis->$laengenslug;
							$wand_daemmung = $energieausweis->$daemmungsslug;
							$wand_nachbar  = $energieausweis->$nachbarslug;

							if ( $wand_laenge > 0.0 && ! $wand_nachbar && $wand_daemmung < 4.0 ) {
								$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['wand'];
								break;
							}
						}
					}
				}
			}
		}

		if ( wpenon_immoticket24_is_empfehlung_active( 'boden', $energieausweis ) ) {
			$energieausweis_keller         = $energieausweis->keller;
			$energieausweis_boden_daemmung = $energieausweis->boden_daemmung;

			if ( $energieausweis_keller === 'unbeheizt' && $energieausweis_boden_daemmung < 6.0 ) {
				$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['boden'];
			}
		}
	}

	if ( wpenon_immoticket24_is_empfehlung_active( 'fenster', $energieausweis ) ) {
		if ( $energieausweis->mode == 'v' ) {
			if ( wpenon_immoticket24_needs_fenster_recommendations( $energieausweis->fenster_bauart, $energieausweis->fenster_baujahr ) ) {
				$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['fenster'];
			}
		} elseif ( $energieausweis->mode == 'b' ) {
			$fenster_manuell = $energieausweis->fenster_manuell;
			if ( $fenster_manuell || $energieausweis_date > strtotime( '2019-12-12' ) ) {
				foreach ( array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h' ) as $fenster ) {
					$flaecheslug = 'fenster_' . $fenster . '_flaeche';
					$bauartslug  = 'fenster_' . $fenster . '_bauart';
					$baujahrslug = 'fenster_' . $fenster . '_baujahr';

					if ( $energieausweis->$flaecheslug > 0.0 && wpenon_immoticket24_needs_fenster_recommendations( $energieausweis->$bauartslug, $energieausweis->$baujahrslug ) ) {
						$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['fenster'];
						break;
					}
				}
			} else {
				if ( wpenon_immoticket24_needs_fenster_recommendations( $energieausweis->fenster_bauart, $energieausweis->fenster_baujahr ) ) {
					$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['fenster'];
				}
			}
		}
	}

	if ( wpenon_immoticket24_is_empfehlung_active( 'solarthermie', $energieausweis ) ) {
		$regenerativ_art   = trim( $energieausweis->regenerativ_art );
		$regenerativ_aktiv = isset( $energieausweis->regenerativ_aktiv ) ? $energieausweis->regenerativ_aktiv : false;
		if ( ( empty( $regenerativ_art ) || strtolower( $regenerativ_art ) == 'keine' ) && ! $regenerativ_aktiv ) {
			$modernisierungsempfehlungen[] = $_modernisierungsempfehlungen['solarthermie'];
		}
	}

	/**
	 * Filtering modernization recommendations.
	 *
	 * @param array $modernisierungsempfehlungen
	 * @param \WPENON\Model\Energieausweis $energieausweis
	 *
	 * @since 1.0.0
	 */
	$modernisierungsempfehlungen = apply_filters( 'enon_filter_modernization_recommendations', $modernisierungsempfehlungen, $energieausweis );

	return $modernisierungsempfehlungen;
}

function wpenon_immoticket24_needs_fenster_recommendations( $fenster_bauart, $fenster_baujahr = 1990 ) {
	if ( $fenster_baujahr >= 1995 ) {
		return false;
	}

	if ( in_array( $fenster_bauart, array( 'waermedaemmglas', 'waermedaemmglas2fach' ) ) ) {
		return false;
	}

	if ( in_array( $fenster_bauart, array( 'aluminium', 'kunststoff', 'stahl' ) ) && $fenster_baujahr >= 2005 ) {
		return false;
	}

	return true;
}

function wpenon_immoticket24_show_empfehlungen_toggle( $group ) {
	if ( ! is_admin() ) {
		return;
	}

	$post = null;

	if ( ! $post ) {
		if ( isset( $_GET['post'] ) ) {
			$post = get_post( absint( $_GET['post'] ) );
			if ( ! $post ) {
				return;
			}
		} else {
			return;
		}
	}

	if ( 'download' != $post->post_type ) {
		return;
	}

	$empfehlungen = wpenon_immoticket24_get_modernisierungsempfehlungen();

	echo '<h4>' . __( 'Modernisierungsempfehlungen', 'wpenon' ) . '</h4>';
	echo '<p class="description">' . __( 'Diese Einstellungen können nur durch den Administrator verändert werden.', 'wpenon' ) . '</p>';

	wp_nonce_field( 'wpenon_immoticket24_empfehlungen_toggle', 'wpenon_immoticket24_empfehlungen_toggle', false );

	foreach ( $empfehlungen as $key => $data ) {
		$meta_value = get_post_meta( $post->ID, 'wpenon_immoticket24_disable_empfehlung_' . $key, true );
		echo '<p><label>';
		echo '<input type="checkbox" id="wpenon_immoticket24_disable_empfehlung_' . $key . '" name="wpenon_immoticket24_disable_empfehlung_' . $key . '" value="1"' . ( $meta_value ? ' checked' : '' ) . '>';
		echo sprintf( __( 'Empfehlung zu %s nicht anzeigen?', 'wpenon' ), $data['bauteil'] );
		echo '</label></p>';
	}
}

add_action( 'wpenon_form_group_basisdaten_after', 'wpenon_immoticket24_show_empfehlungen_toggle', 10, 1 );

function wpenon_immoticket24_save_empfehlungen_toggle( $post_data, $energieausweis ) {
	if ( ! isset( $post_data['wpenon_immoticket24_empfehlungen_toggle'] ) || ! wp_verify_nonce( $post_data['wpenon_immoticket24_empfehlungen_toggle'], 'wpenon_immoticket24_empfehlungen_toggle' ) ) {
		return;
	}

	$empfehlungen = wpenon_immoticket24_get_modernisierungsempfehlungen();
	foreach ( $empfehlungen as $key => $data ) {
		$val = false;
		if ( isset( $post_data[ 'wpenon_immoticket24_disable_empfehlung_' . $key ] ) && $post_data[ 'wpenon_immoticket24_disable_empfehlung_' . $key ] ) {
			$val = true;
		}
		update_post_meta( $energieausweis->id, 'wpenon_immoticket24_disable_empfehlung_' . $key, $val );
	}
}

add_action( 'wpenon_save_meta_boxes', 'wpenon_immoticket24_save_empfehlungen_toggle', 10, 2 );

function wpenon_immoticket24_is_empfehlung_active( $key, $energieausweis ) {
	return ! get_post_meta( $energieausweis->id, 'wpenon_immoticket24_disable_empfehlung_' . $key, true );
}

function wpenon_immoticket24_maybe_prevent_completion( $val, $payment_id, $new_status, $old_status ) {
	if ( ! $val ) {
		return $val;
	}

	$completed = array( 'complete', 'completed', 'publish' );

	if ( ! in_array( $new_status, $completed ) ) {
		return $val;
	}

	if ( ! class_exists( 'WPENON\Model\EnergieausweisManager' ) ) {
		return $val;
	}

	$posts = get_posts( array(
		'post_type'      => 'download',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => '_wpenon_attached_payment_id',
				'value'   => $payment_id,
				'compare' => '=',
				'type'    => 'NUMERIC',
			),
		),
	) );

	if ( ! isset( $posts[0] ) ) {
		return $val;
	}

	$energieausweis = WPENON\Model\EnergieausweisManager::getEnergieausweis( $posts[0] );
	if ( ! $energieausweis ) {
		return $val;
	}

	$calculations = $energieausweis->calculate();
	if ( ! $calculations ) {
		return $val;
	}

	if ( $calculations['qh_e_b'] <= 5.0 || $calculations['qw_e_b'] <= 5.0 ) {
		wpenon_immoticket24_send_needs_review_email( $payment_id, $energieausweis );

		return false;
	}

	$boundaries = 'v' === $energieausweis->mode ? array( 5.0, 250.0 ) : array( 5.0, 400.0 );
	if ( $calculations['endenergie'] <= $boundaries[0] || $calculations['endenergie'] >= $boundaries[1] ) {
		wpenon_immoticket24_send_needs_review_email( $payment_id, $energieausweis );

		return false;
	}

	return true;
}

add_filter( 'edd_should_update_payment_status', 'wpenon_immoticket24_maybe_prevent_completion', 10, 4 );

function wpenon_immoticket24_allow_manual_completion_trigger() {
	global $edd_payments_page;

	add_action( 'load-' . $edd_payments_page, 'wpenon_immoticket24_allow_manual_completion' );
}

add_action( 'admin_menu', 'wpenon_immoticket24_allow_manual_completion_trigger', 100 );

function wpenon_immoticket24_allow_manual_completion() {
	remove_filter( 'edd_should_update_payment_status', 'wpenon_immoticket24_maybe_prevent_completion', 10 );
}

function wpenon_immoticket24_send_needs_review_email( $payment_id, $energieausweis ) {
	$payment_title = get_the_title( $payment_id );

	$from_name  = edd_get_option( 'from_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
	$from_email = edd_get_option( 'from_email', get_bloginfo( 'admin_email' ) );

	$subject = sprintf( __( 'Neue Bestellung %s - Überprüfung benötigt', 'wpenon' ), $payment_title );

	$headers = "From: " . stripslashes_deep( html_entity_decode( $from_name, ENT_COMPAT, 'UTF-8' ) ) . " <$from_email>\r\n";
	$headers .= "Reply-To: " . $from_email . "\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";

	$message = 'Hallo,' . "\n\n";
	$message .= 'Es wurde eine neue Bestellung getätigt, die überprüft werden muss, da der Energieausweis einen ungewöhnlichen Endenergiewert aufweist.' . "\n\n";
	$message .= '{download_list}' . "\n\n\n\n";
	$message .= 'Rabbatt-Code;' . "\n\n";
	$message .= '{discount_codes}' . "\n\n";
	$message .= sprintf( 'Die betroffene Zahlung hat die Nummer %s.', $payment_title ) . ' ';
	$message .= 'Bitte setzen Sie sie nach erfolgter Prüfung auf Abgeschlossen, um die Zahlung zu vervollständigen und dem Kunden den Energieausweis zuzusenden.' . "\n\n";
	$message .= 'Vielen Dank!';

	$message = edd_do_email_tags( $message, $payment_id );

	$emails = EDD()->emails;
	$emails->__set( 'from_name', $from_name );
	$emails->__set( 'from_email', $from_email );
	$emails->__set( 'headers', $headers );
	$emails->__set( 'heading', __( 'Überprüfung benötigt', 'wpenon' ) );

	$emails->send( edd_get_admin_notice_emails(), $subject, $message, array() );
}

function wpenon_immoticket24_show_certificate_checked( $group ) {
	if ( ! is_admin() ) {
		return;
	}

	$post = null;

	if ( ! $post ) {
		if ( isset( $_GET['post'] ) ) {
			$post = get_post( absint( $_GET['post'] ) );
			if ( ! $post ) {
				return;
			}
		} else {
			return;
		}
	}

	if ( 'download' != $post->post_type ) {
		return;
	}

	echo '<h4>' . __( 'Freischaltung', 'wpenon' ) . '</h4>';
	echo '<p class="description">' . __( 'Diese Einstellung legt fest, ob ein Energieausweis freigeschaltet ist oder nicht. Ein nicht freigeschalteter Ausweis ist nur als PDF-Preview verfügbar.', 'wpenon' ) . '</p>';

	$meta_value = true;
	if ( metadata_exists( 'post', $post->ID, 'wpenon_immoticket24_certificate_checked' ) ) {
		$meta_value = (bool) get_post_meta( $post->ID, 'wpenon_immoticket24_certificate_checked', true );
	}

	echo '<p><label>';
	echo '<input type="checkbox" id="wpenon_immoticket24_certificate_checked" name="wpenon_immoticket24_certificate_checked" value="1"' . ( $meta_value ? ' checked' : '' ) . '>';
	echo __( 'Energieausweis freigeschaltet?', 'wpenon' );
	echo '</label></p>';
}

add_action( 'wpenon_form_group_basisdaten_after', 'wpenon_immoticket24_show_certificate_checked', 10, 1 );

function wpenon_immoticket24_save_certificate_checked( $post_data, $energieausweis ) {
	$certificate_checked = false;
	if ( isset( $post_data['wpenon_immoticket24_certificate_checked'] ) && $post_data['wpenon_immoticket24_certificate_checked'] ) {
		$certificate_checked = true;
	}
	update_post_meta( $energieausweis->id, 'wpenon_immoticket24_certificate_checked', $certificate_checked );

	if ( $certificate_checked && $energieausweis->isPaid() && ! $energieausweis->isRegistered() ) {
		WPENON\Controller\General::instance()->_handlePaymentCompleteActions( $energieausweis->id );
	}
}

add_action( 'wpenon_save_meta_boxes', 'wpenon_immoticket24_save_certificate_checked', 10, 2 );

function wpenon_immoticket24_maybe_check_certificate( $post_id, $payment_id ) {
	$fees = edd_get_payment_fees( $payment_id );

	$fee_ids = wp_list_pluck( $fees, 'id' );
	if ( ! in_array( 'experten_check', $fee_ids ) ) {
		update_post_meta( $post_id, 'wpenon_immoticket24_certificate_checked', true );
	} else {
		update_post_meta( $post_id, 'wpenon_immoticket24_certificate_checked', false );
	}
}

add_action( 'edd_complete_download_purchase', 'wpenon_immoticket24_maybe_check_certificate', 5, 2 );

function wpenon_immoticket24_maybe_prevent_payment_complete_actions( $value, $energieausweis ) {
	if ( ! $value ) {
		return $value;
	}

	if ( metadata_exists( 'post', $energieausweis->id, 'wpenon_immoticket24_certificate_checked' ) && ! get_post_meta( $energieausweis->id, 'wpenon_immoticket24_certificate_checked', true ) ) {
		$value = false;
	}

	return $value;
}

add_filter( 'wpenon_execute_complete_actions', 'wpenon_immoticket24_maybe_prevent_payment_complete_actions', 10, 2 );

function wpenon_immoticket24_is_pdf_preview( $value, $energieausweis ) {
	if ( $value ) {
		return $value;
	}

	if ( metadata_exists( 'post', $energieausweis->id, 'wpenon_immoticket24_certificate_checked' ) && ! get_post_meta( $energieausweis->id, 'wpenon_immoticket24_certificate_checked', true ) ) {
		$value = true;
	}

	return $value;
}

add_filter( 'wpenon_is_pdf_preview', 'wpenon_immoticket24_is_pdf_preview', 10, 2 );

function wpenon_immoticket24_show_fenster_table() {
	?>
	<div style="max-width:600px;margin:0 auto;">
		<p class="text-center"><em>Hinweis zu den Angaben bei den Fenstern</em></p>
		<table class="table table-bordered" style="background-color:rgba(255,255,255,0.1)">
			<thead>
			<tr>
				<th id="fenster_tabelle_bauart">Fensterbauart</th>
				<th id="fenster_tabelle_baujahr">Baujahr</th>
				<th id="fenster_tabelle_uwert">U-Wert</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<th headers="fenster_tabelle_bauart" scope="row">Einfach-Verglasung</th>
				<td>bis ca. 1978</td>
				<td>5,0</td>
			</tr>
			<tr>
				<th headers="fenster_tabelle_bauart" scope="row">Isolierverglasung<br>(2 Glasscheiben)</th>
				<td>ca. 1974 bis 2000</td>
				<td>4,3 bis 1,8</td>
			</tr>
			<tr>
				<th headers="fenster_tabelle_bauart" scope="row">2fach Wärmedämmglas<br>(2 Scheiben mit einer
					reflektierenden Beschichtung im Scheibenzwischenraum)
				</th>
				<td>ab 1995</td>
				<td>1,1</td>
			</tr>
			<tr>
				<th headers="fenster_tabelle_bauart" scope="row">3fach Wärmedämmglas<br>(3 Scheiben mit zwei
					reflektierenden Beschichtungen im Scheibenzwischenraum)
				</th>
				<td>ab 2005</td>
				<td>0,7</td>
			</tr>
			</tbody>
		</table>
	</div>
	<?php
}

add_action( 'wpenon_form_group_bauteile_fenster_before', 'wpenon_immoticket24_show_fenster_table', 1 );

function wpenon_immoticket24_show_wohnung_warning() {
	?>
	<div class="alert alert-warning">
		<p>
			<strong><?php _e( 'Achtung:', 'wpenon' ); ?></strong>
			<?php _e( 'Beachten Sie, dass für eine einzelne Wohnung kein Energieausweis erstellt werden kann, da Energieausweise stets gebäudebezogen sind. Geben Sie daher immer Werte des gesamten Gebäudes an.', 'wpenon' ); ?>
		</p>
	</div>
	<?php
}

add_action( 'wpenon_form_group_gebaeude_before', 'wpenon_immoticket24_show_wohnung_warning' );

function wpenon_immoticket24_show_gebaeude_table() {
	if ( current_time( 'timestamp' ) >= strtotime( '2017-07-01' ) ) {
		return;
	}

	?>
	<div style="max-width:600px;margin:0 auto 15px;font-size:120%;">
		<div style="padding:5px 10px;border:1px solid #333;text-align:center;">
			<strong><em>Tipp:</em></strong> Das versteht man unter den verschiedenen Gebäudetypen
		</div>
		<div style="padding:5px 10px;border:1px solid #333;border-top:0;">
			<span style="display:inline-block;min-width:170px;">einseitig angebaut</span> <span
				style="display:inline-block;width:30px;text-align:center;">=</span> Reiheneckhaus, Doppelhaushälfte
			<br>
			<span style="display:inline-block;min-width:170px;">zweiseitig angebaut</span> <span
				style="display:inline-block;width:30px;text-align:center;">=</span> Reihenhaus
		</div>
	</div>
	<?php
}

add_action( 'wpenon_form_group_gebaeude_before', 'wpenon_immoticket24_show_gebaeude_table' );

function wpenon_immoticket24_show_warmwasser_warning() {
	$energieausweis = WPENON\Model\EnergieausweisManager::getEnergieausweis();
	if ( ! $energieausweis || 'v' !== $energieausweis->mode ) {
		return;
	}

	?>
	<div class="alert alert-warning">
		<p>
			<strong><?php _e( 'Achtung:', 'wpenon' ); ?></strong>
			<?php _e( 'Wählen Sie nur dann &#8220;separat angegeben&#8221; aus, wenn Sie die Verbrauchswerte für Warmwasser gesondert vorliegen haben. Ansonsten wählen Sie im Falle einer separaten Anlage bitte &#8220;unbekannt&#8221; aus.', 'wpenon' ); ?>
		</p>
	</div>
	<?php
}

add_action( 'wpenon_form_group_warmwasser_before', 'wpenon_immoticket24_show_warmwasser_warning' );

function wpenon_immoticket24_migrate_old_energietraeger_fields_on_demand( $energieausweis ) {
	$energietraeger_fields = array( 'h_energietraeger', 'h2_energietraeger', 'h3_energietraeger', 'ww_energietraeger' );
	foreach ( $energietraeger_fields as $energietraeger_field ) {
		if ( false !== strpos( $energieausweis->$energietraeger_field, '_kwhheizwert' ) ) {
			$energieausweis->$energietraeger_field = str_replace( '_kwhheizwert', '_kwh', $energieausweis->$energietraeger_field );
		} elseif ( false !== strpos( $energieausweis->$energietraeger_field, '_kwhbrennwert' ) ) {
			$energieausweis->$energietraeger_field = str_replace( '_kwhbrennwert', '_kwh', $energieausweis->$energietraeger_field );
		}
	}
}

add_filter( 'wpenon_admin_edit_certificate', 'wpenon_immoticket24_migrate_old_energietraeger_fields_on_demand', 10, 1 );

function wpenon_immoticket24_migrate_old_energietraeger_fields_on_demand_filter( $data, $energieausweis ) {
	wpenon_immoticket24_migrate_old_energietraeger_fields_on_demand( $energieausweis );

	return $data;
}

add_filter( 'wpenon_overview_page_data', 'wpenon_immoticket24_migrate_old_energietraeger_fields_on_demand_filter', 10, 2 );
add_filter( 'wpenon_edit_page_data', 'wpenon_immoticket24_migrate_old_energietraeger_fields_on_demand_filter', 10, 2 );
add_filter( 'wpenon_editoverview_page_data', 'wpenon_immoticket24_migrate_old_energietraeger_fields_on_demand_filter', 10, 2 );
add_filter( 'wpenon_purchase_page_data', 'wpenon_immoticket24_migrate_old_energietraeger_fields_on_demand_filter', 10, 2 );

function wpenon_immoticket24_check_global_certificate_validation_results( $results, $energieausweis ) {
	if ( property_exists( $energieausweis, 'type' ) && 'bw' === $energieausweis->type ) {
		$grundriss_form = ! empty( $results['validated']['grundriss_form'] ) ? $results['validated']['grundriss_form'] : $energieausweis->grundriss_form;

		$wand_a_laenge = \WPENON\Util\Parse::float( $results['validated']['wand_a_laenge'] );
		$wand_b_laenge = \WPENON\Util\Parse::float( $results['validated']['wand_b_laenge'] );
		$wand_c_laenge = \WPENON\Util\Parse::float( $results['validated']['wand_c_laenge'] );
		$wand_d_laenge = \WPENON\Util\Parse::float( $results['validated']['wand_d_laenge'] );

		switch ( $grundriss_form ) {
			case 'b':
				if ( ! empty( $results['validated']['wand_a_laenge'] ) && ! empty( $results['validated']['wand_c_laenge'] ) && $wand_a_laenge <= $wand_c_laenge ) {
					$results['errors']['wand_c_laenge'] = sprintf( __( 'Wand %1$s muss kürzer als Wand %2$s sein.', 'wpenon' ), 'c', 'a' );
				}
				if ( ! empty( $results['validated']['wand_b_laenge'] ) && ! empty( $results['validated']['wand_d_laenge'] ) && $wand_b_laenge <= $wand_d_laenge ) {
					$results['errors']['wand_d_laenge'] = sprintf( __( 'Wand %1$s muss kürzer als Wand %2$s sein.', 'wpenon' ), 'd', 'b' );
				}
				break;
			case 'c':
				$wand_e_laenge = \WPENON\Util\Parse::float( $results['validated']['wand_e_laenge'] );

				if ( ! empty( $results['validated']['wand_a_laenge'] ) && ! empty( $results['validated']['wand_c_laenge'] ) && ! empty( $results['validated']['wand_e_laenge'] ) && $wand_a_laenge <= $wand_c_laenge + $wand_e_laenge ) {
					$results['errors']['wand_c_laenge'] = sprintf( __( 'Wand %1$s und Wand %2$s müssen zusammen kürzer als Wand %3$s sein.', 'wpenon' ), 'c', 'e', 'a' );
					$results['errors']['wand_e_laenge'] = sprintf( __( 'Wand %1$s und Wand %2$s müssen zusammen kürzer als Wand %3$s sein.', 'wpenon' ), 'c', 'e', 'a' );
				}
				break;
			case 'd':
				$wand_e_laenge = \WPENON\Util\Parse::float( $results['validated']['wand_e_laenge'] );

				if ( ! empty( $results['validated']['wand_a_laenge'] ) && ! empty( $results['validated']['wand_c_laenge'] ) && ! empty( $results['validated']['wand_e_laenge'] ) && $wand_a_laenge <= $wand_c_laenge + $wand_e_laenge ) {
					$results['errors']['wand_c_laenge'] = sprintf( __( 'Wand %1$s und Wand %2$s müssen zusammen kürzer als Wand %3$s sein.', 'wpenon' ), 'c', 'e', 'a' );
					$results['errors']['wand_e_laenge'] = sprintf( __( 'Wand %1$s und Wand %2$s müssen zusammen kürzer als Wand %3$s sein.', 'wpenon' ), 'c', 'e', 'a' );
				}
				if ( ! empty( $results['validated']['wand_b_laenge'] ) && ! empty( $results['validated']['wand_d_laenge'] ) && $wand_b_laenge <= $wand_d_laenge ) {
					$results['errors']['wand_d_laenge'] = sprintf( __( 'Wand %1$s muss kürzer als Wand %2$s sein.', 'wpenon' ), 'd', 'b' );
				}
				break;
		}
	}

	return $results;
}

add_filter( 'wpenon_validation_results', 'wpenon_immoticket24_check_global_certificate_validation_results', 10, 2 );

function wpenon_immoticket24_render_payments_per_page_filter() {
	$choices = array(
		'-1'  => __( 'Alle anzeigen', 'wpenon' ),
		'30'  => sprintf( __( '%s anzeigen', 'wpenon' ), number_format_i18n( 30 ) ),
		'60'  => sprintf( __( '%s anzeigen', 'wpenon' ), number_format_i18n( 60 ) ),
		'90'  => sprintf( __( '%s anzeigen', 'wpenon' ), number_format_i18n( 90 ) ),
		'120' => sprintf( __( '%s anzeigen', 'wpenon' ), number_format_i18n( 120 ) ),
		'150' => sprintf( __( '%s anzeigen', 'wpenon' ), number_format_i18n( 150 ) ),
		'180' => sprintf( __( '%s anzeigen', 'wpenon' ), number_format_i18n( 180 ) ),
	);

	$selected_choice = '30';
	if ( ! empty( $_GET['per_page'] ) && isset( $choices[ $_GET['per_page'] ] ) ) {
		$selected_choice = $_GET['per_page'];
	}

	echo EDD()->html->select( array(
		'options'          => $choices,
		'name'             => 'per_page',
		'id'               => 'payments-per-page',
		'selected'         => $selected_choice,
		'show_option_all'  => false,
		'show_option_none' => false
	) );
}

add_action( 'edd_payment_advanced_filters_after_fields', 'wpenon_immoticket24_render_payments_per_page_filter' );

function wpenon_immoticket24_filter_payments_per_page( $per_page ) {
	/* Hack EDD: Add the following line into the constructor of the EDD_Payment_History_Table class:
	$this->per_page = apply_filters( 'edd_wpenon_payments_per_page', $this->per_page ); */

	if ( ! empty( $_GET['per_page'] ) ) {
		$per_page = (int) $_GET['per_page'];
	}

	return $per_page;
}

add_filter( 'edd_wpenon_payments_per_page', 'wpenon_immoticket24_filter_payments_per_page' );

function wpenon_immoticket24_maybe_generate_payment_csv_bulk() {
	if ( 'edit.php' !== $GLOBALS['pagenow'] ) {
		return;
	}

	if ( empty( $_GET['post_type'] ) || 'download' !== $_GET['post_type'] ) {
		return;
	}

	if ( empty( $_GET['page'] ) || 'edd-payment-history' !== $_GET['page'] ) {
		return;
	}

	if ( empty( $_GET['action'] ) || 'wpenon-csv-view' !== $_GET['action'] ) {
		return;
	}

	$ids = isset( $_GET['payment'] ) ? $_GET['payment'] : array();
	$ids = array_map( 'absint', (array) $ids );

	if ( empty( $ids ) ) {
		return;
	}

	$filename = __( 'energieausweis-zahlungen.csv', 'wpenon' );

	$csv_settings = array(
		'terminated' => ';',
		'enclosed'   => '"',
		'escaped'    => '"',
	);

	$headings = array(
		'name'     => __( 'Name, Vorname', 'wpenon' ),
		'subtotal' => __( 'Nettobetrag', 'wpenon' ),
		'tax'      => __( 'MwSt.', 'wpenon' ),
		'total'    => __( 'Bruttobetrag', 'wpenon' ),
	);

	$query = new EDD_Payments_Query( array(
		'output'   => 'payments',
		'number'   => - 1,
		'post__in' => $ids,
		'orderby'  => 'post__in',
	) );

	$payments = $query->get_payments();

	$charset = 'UTF-8'; // WPENON_DEFAULT_CHARSET

	header( 'Content-Type: text/csv; charset=' . $charset );
	header( 'Content-Disposition: inline; filename=' . $filename );
	//header( 'Content-Disposition: attachment; filename=' . $filename );

	$output = fopen( 'php://output', 'w' );

	fputcsv( $output, \WPENON\Util\Format::csvEncode( $headings, $charset ), $csv_settings['terminated'], $csv_settings['enclosed'] );

	foreach ( $payments as $payment ) {
		$result = array(
			'name'     => $payment->last_name . ', ' . $payment->first_name,
			'subtotal' => $payment->total - $payment->tax,
			'tax'      => $payment->tax,
			'total'    => $payment->total,
		);

		fputcsv( $output, \WPENON\Util\Format::csvEncode( $result, $charset ), $csv_settings['terminated'], $csv_settings['enclosed'] );
	}

	fclose( $output );
	exit;
}

add_action( 'current_screen', 'wpenon_immoticket24_maybe_generate_payment_csv_bulk' );

function wpenon_immoticket24_add_payment_csv_bulk_action( $actions ) {
	$actions['wpenon-csv-view'] = __( 'CSV-Datei herunterladen', 'wpenon' );

	return $actions;
}

add_filter( 'edd_payments_table_bulk_actions', 'wpenon_immoticket24_add_payment_csv_bulk_action', 100 );
