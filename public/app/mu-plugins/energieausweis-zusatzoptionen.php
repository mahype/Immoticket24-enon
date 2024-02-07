<?php
/*
Plugin Name: Energieausweis-Zusatzoptionen
Plugin URI: https://energieausweis-online-erstellen.de
Description: Zusatzoptionen beim Bestellen von Energieausweisen
Version: 1.0.0
Author: Felix Arntz
Author URI: https://felix-arntz.me
*/

function energieausweis_zusatzoptionen_headline( $headline ) {
	return 'Zusatzleistungen';
}

add_filter( 'eddcf_custom_fees_headline', 'energieausweis_zusatzoptionen_headline' );

function energieausweis_zusatzoptionen_description( $description ) {
	return 'Mit diesen zusätzlichen Optionen können Sie noch mehr aus Ihrem Energieausweis herausholen. Klicken Sie auf ein Fragezeichen, um mehr zur jeweiligen Leistung zu erfahren.';
}

add_filter( 'eddcf_custom_fees_description', 'energieausweis_zusatzoptionen_description' );

function energieausweis_zusatzoptionen_custom_fees( $fees ) {
	$settings = get_option( 'energieausweis_zusatzoptionen', array() );

	$experten_check = array(
		'id'             => 'experten_check',
		'label'          => energieausweis_zusatzoptionen_get_default( 'experten_check_label' ),
		'amount'         => energieausweis_zusatzoptionen_get_default( 'experten_check_price' ),
		'description_cb' => 'energieausweis_zusatzoption_experten_check_info',
		'email_note'     => 'Unsere Experten werden Ihren Energieausweis zeitnah überprüfen und Sie bei Fragen kontaktieren. Danach können Sie ihn umgehend herunterladen.',
	);

	if ( isset( $settings['experten_check_label'] ) ) {
		$experten_check['label'] = $settings['experten_check_label'];
	}
	if ( isset( $settings['experten_check_price'] ) ) {
		$experten_check['amount'] = floatval( $settings['experten_check_price'] );
	}

	$sendung_per_post = array(
		'id'             => 'sendung_per_post',
		'label'          => energieausweis_zusatzoptionen_get_default( 'sendung_per_post_label' ),
		'amount'         => energieausweis_zusatzoptionen_get_default( 'sendung_per_post_price' ),
		'description_cb' => 'energieausweis_zusatzoption_sendung_per_post_info',
		'email_note'     => 'Des weiteren werden wir Ihnen Ihre Bestellung wie gewünscht zusätzlich per Post zukommen lassen.',
	);

	if ( isset( $settings['sendung_per_post_label'] ) ) {
		$sendung_per_post['label'] = $settings['sendung_per_post_label'];
	}
	if ( isset( $settings['sendung_per_post_price'] ) ) {
		$sendung_per_post['amount'] = floatval( $settings['sendung_per_post_price'] );
	}

	$energieausweis_besprechung = array(
		'id'             => 'energieausweis_besprechung',
		'label'          => energieausweis_zusatzoptionen_get_default( 'energieausweis_besprechung_label' ),
		'amount'         => energieausweis_zusatzoptionen_get_default( 'energieausweis_besprechung_price' ),
		'description_cb' => 'energieausweis_zusatzoption_energieausweis_besprechung_info',
		'email_note'     => 'In Kürze werden Sie außerdem eine weitere Email erhalten, damit wir mit Ihnen einen Termin für ein Telefongespräch zur ausführlichen Erläuterung Ihres Energieausweises vereinbaren können.',
	);

	if ( isset( $settings['energieausweis_besprechung_label'] ) ) {
		$energieausweis_besprechung['label'] = $settings['energieausweis_besprechung_label'];
	}
	if ( isset( $settings['energieausweis_besprechung_price'] ) ) {
		$energieausweis_besprechung['amount'] = floatval( $settings['energieausweis_besprechung_price'] );
	}

	$kostenlose_korrektur = array(
		'id'             => 'kostenlose_korrektur',
		'label'          => energieausweis_zusatzoptionen_get_default( 'kostenlose_korrektur_label' ),
		'amount'         => energieausweis_zusatzoptionen_get_default( 'kostenlose_korrektur_price' ),
		'description_cb' => 'energieausweis_zusatzoption_kostenlose_korrektur_info',
		'email_note'     => '',
	);

	if ( isset( $settings['kostenlose_korrektur_label'] ) ) {
		$kostenlose_korrektur['label'] = $settings['kostenlose_korrektur_label'];
	}
	if ( isset( $settings['kostenlose_korrektur_price'] ) ) {
		$kostenlose_korrektur['amount'] = floatval( $settings['kostenlose_korrektur_price'] );
	}

	$premium_bewertung = array(
		'id'             => 'premium_bewertung',
		'label'          => energieausweis_zusatzoptionen_get_default( 'premium_bewertung_label' ),
		'amount'         => energieausweis_zusatzoptionen_get_default( 'premium_bewertung_price' ),
		'description_cb' => 'energieausweis_zusatzoption_premium_bewertung_info',
		'email_note'     => '',
	);

	if ( isset( $settings['experten_check_order'] ) && isset( $settings['sendung_per_post_order'] ) && isset( $settings['energieausweis_besprechung_order'] ) && isset( $settings['kostenlose_korrektur_order'] ) ) {
		$order = array(
			$settings['experten_check_order']             => $experten_check,
			$settings['sendung_per_post_order']           => $sendung_per_post,
			$settings['energieausweis_besprechung_order'] => $energieausweis_besprechung,
			$settings['kostenlose_korrektur_order']       => $kostenlose_korrektur,
			$settings['premium_bewertung_order']          => $premium_bewertung,
		);

		ksort( $order );
		foreach ( $order as $item ) {
			$fees[] = $item;
		}
	} else {
		$fees[] = $experten_check;
		$fees[] = $sendung_per_post;
		$fees[] = $energieausweis_besprechung;
		$fees[] = $kostenlose_korrektur;
		$fees[] = $premium_bewertung;
	}

	$fees = apply_filters( 'wpenon_custom_fees', $fees );

	return $fees;
}

add_filter( 'eddcf_custom_fees', 'energieausweis_zusatzoptionen_custom_fees' );

function energieausweis_zusatzoptionen_filter_custom_fees( $fees, $cart ) {
	$has_verkauf = false;

	foreach ( $cart->get_contents_details() as $item ) {
		$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis( $item['id'] );
		if ( ! $energieausweis ) {
			continue;
		}

		if ( 'verkauf' === $energieausweis->anlass ) {
			$has_verkauf = true;
			break;
		}
	}

	if ( ! $has_verkauf ) {
		foreach ( $fees as $index => $fee ) {
			if ( 'premium_bewertung' === $fee['id'] ) {
				unset( $fees[ $index ] );
				break;
			}
		}

		return array_values( $fees );
	}

	return $fees;
}

add_filter( 'eddcf_filter_custom_fees', 'energieausweis_zusatzoptionen_filter_custom_fees', 10, 2 );

function energieausweis_zusatzoptionen_settings( $wpod ) {
	$options = array(
		'experten_check'                 => array(
			'title'  => 'Experten-Check',
			'fields' => array(
				'experten_check_label'       => array(
					'title'    => 'Name',
					'type'     => 'text',
					'default'  => energieausweis_zusatzoptionen_get_default( 'experten_check_label' ),
					'required' => true,
				),
				'experten_check_description' => array(
					'title'    => 'Beschreibung',
					'type'     => 'wysiwyg',
					'default'  => energieausweis_zusatzoptionen_get_default( 'experten_check_description' ),
					'required' => true,
					'rows'     => 8,
				),
				'experten_check_price'       => array(
					'title'    => 'Preis',
					'type'     => 'number',
					'default'  => energieausweis_zusatzoptionen_get_default( 'experten_check_price' ),
					'required' => true,
					'min'      => 0.01,
					'step'     => 0.01,
				),
				'experten_check_order'       => array(
					'title'       => 'Reihenfolge',
					'description' => 'Je kleiner die Nummer, desto höher die Priorität der Zusatzoption in der Auflistung.',
					'type'        => 'number',
					'default'     => energieausweis_zusatzoptionen_get_default( 'experten_check_order' ),
					'required'    => true,
					'min'         => 1,
					'step'        => 1,
				),
			),
		),
		'sendung_per_post'               => array(
			'title'  => 'Sendung per Post',
			'fields' => array(
				'sendung_per_post_label'       => array(
					'title'    => 'Name',
					'type'     => 'text',
					'default'  => energieausweis_zusatzoptionen_get_default( 'sendung_per_post_label' ),
					'required' => true,
				),
				'sendung_per_post_description' => array(
					'title'    => 'Beschreibung',
					'type'     => 'wysiwyg',
					'default'  => energieausweis_zusatzoptionen_get_default( 'sendung_per_post_description' ),
					'required' => true,
					'rows'     => 8,
				),
				'sendung_per_post_price'       => array(
					'title'    => 'Preis',
					'type'     => 'number',
					'default'  => energieausweis_zusatzoptionen_get_default( 'sendung_per_post_price' ),
					'required' => true,
					'min'      => 0.01,
					'step'     => 0.01,
				),
				'sendung_per_post_order'       => array(
					'title'       => 'Reihenfolge',
					'description' => 'Je kleiner die Nummer, desto höher die Priorität der Zusatzoption in der Auflistung.',
					'type'        => 'number',
					'default'     => energieausweis_zusatzoptionen_get_default( 'sendung_per_post_order' ),
					'required'    => true,
					'min'         => 1,
					'step'        => 1,
				),
			),
		),
		'energieausweis_besprechung'     => array(
			'title'  => 'Energieausweis-Besprechung',
			'fields' => array(
				'energieausweis_besprechung_label'       => array(
					'title'    => 'Name',
					'type'     => 'text',
					'default'  => energieausweis_zusatzoptionen_get_default( 'energieausweis_besprechung_label' ),
					'required' => true,
				),
				'energieausweis_besprechung_description' => array(
					'title'    => 'Beschreibung',
					'type'     => 'wysiwyg',
					'default'  => energieausweis_zusatzoptionen_get_default( 'energieausweis_besprechung_description' ),
					'required' => true,
					'rows'     => 8,
				),
				'energieausweis_besprechung_price'       => array(
					'title'    => 'Preis',
					'type'     => 'number',
					'default'  => energieausweis_zusatzoptionen_get_default( 'energieausweis_besprechung_price' ),
					'required' => true,
					'min'      => 0.01,
					'step'     => 0.01,
				),
				'energieausweis_besprechung_order'       => array(
					'title'       => 'Reihenfolge',
					'description' => 'Je kleiner die Nummer, desto höher die Priorität der Zusatzoption in der Auflistung.',
					'type'        => 'number',
					'default'     => energieausweis_zusatzoptionen_get_default( 'energieausweis_besprechung_order' ),
					'required'    => true,
					'min'         => 1,
					'step'        => 1,
				),
			),
		),
		'kostenlose_korrektur'           => array(
			'title'  => 'Kostenlose Korrektur',
			'fields' => array(
				'kostenlose_korrektur_label'       => array(
					'title'    => 'Name',
					'type'     => 'text',
					'default'  => energieausweis_zusatzoptionen_get_default( 'kostenlose_korrektur_label' ),
					'required' => true,
				),
				'kostenlose_korrektur_description' => array(
					'title'    => 'Beschreibung',
					'type'     => 'wysiwyg',
					'default'  => energieausweis_zusatzoptionen_get_default( 'kostenlose_korrektur_description' ),
					'required' => true,
					'rows'     => 8,
				),
				'kostenlose_korrektur_price'       => array(
					'title'    => 'Preis',
					'type'     => 'number',
					'default'  => energieausweis_zusatzoptionen_get_default( 'kostenlose_korrektur_price' ),
					'required' => true,
					'min'      => 0.01,
					'step'     => 0.01,
				),
				'kostenlose_korrektur_order'       => array(
					'title'       => 'Reihenfolge',
					'description' => 'Je kleiner die Nummer, desto höher die Priorität der Zusatzoption in der Auflistung.',
					'type'        => 'number',
					'default'     => energieausweis_zusatzoptionen_get_default( 'kostenlose_korrektur_order' ),
					'required'    => true,
					'min'         => 1,
					'step'        => 1,
				),
			),
		),
		'premium_bewertung'              => array(
			'title'  => 'Premium-Bewertung',
			'fields' => array(
				'premium_bewertung_label'       => array(
					'title'    => 'Name',
					'type'     => 'text',
					'default'  => energieausweis_zusatzoptionen_get_default( 'premium_bewertung_label' ),
					'required' => true,
				),
				'premium_bewertung_description' => array(
					'title'    => 'Beschreibung',
					'type'     => 'wysiwyg',
					'default'  => energieausweis_zusatzoptionen_get_default( 'premium_bewertung_description' ),
					'required' => true,
					'rows'     => 8,
				),
				'premium_bewertung_price'       => array(
					'title'   => 'Preis',
					'type'    => 'number',
					'default' => energieausweis_zusatzoptionen_get_default( 'premium_bewertung_price' ),
					'step'    => 0.01,
				),
				'premium_bewertung_order'       => array(
					'title'       => 'Reihenfolge',
					'description' => 'Je kleiner die Nummer, desto höher die Priorität der Zusatzoption in der Auflistung.',
					'type'        => 'number',
					'default'     => energieausweis_zusatzoptionen_get_default( 'premium_bewertung_order' ),
					'required'    => true,
					'min'         => 1,
					'step'        => 1,
				),
			),
		),
		'eingabesupport' => array(
			'title'  => 'Professioneller Eingabesupport',
			'fields' => array(
				'premium_bewertung_label'       => array(
					'title'    => 'Name',
					'type'     => 'text',
					'default'  => energieausweis_zusatzoptionen_get_default( 'eingabesupport_label' ),
					'required' => true,
				),
				'premium_bewertung_description' => array(
					'title'    => 'Beschreibung',
					'type'     => 'wysiwyg',
					'default'  => energieausweis_zusatzoptionen_get_default( 'eingabesupport_description' ),
					'required' => true,
					'rows'     => 8,
				),
				'premium_bewertung_price'       => array(
					'title'   => 'Preis',
					'type'    => 'number',
					'default' => energieausweis_zusatzoptionen_get_default( 'eingabesupport_price' ),
					'step'    => 0.01,
				),
				'premium_bewertung_order'       => array(
					'title'       => 'Reihenfolge',
					'description' => 'Je kleiner die Nummer, desto höher die Priorität der Zusatzoption in der Auflistung.',
					'type'        => 'number',
					'default'     => energieausweis_zusatzoptionen_get_default( 'eingabesupport_order' ),
					'required'    => true,
					'min'         => 1,
					'step'        => 1,
				),
			),
		)
	);

	$options = apply_filters( 'wpenon_zusatzoptionen_settings', $options );

	$wpod->add_components( array(
		'download' => array(
			'screens' => array(
				'energieausweis_zusatzoptionen' => array(
					'title'      => 'Zusatzoptionen',
					'label'      => 'Zusatzoptionen',
					'capability' => 'manage_options',
					'tabs'       => array(
						'energieausweis_zusatzoptionen' => array(
							'title'       => 'Zusatzoptionen-Beschreibungen',
							'description' => 'Hier können Sie die Texte bearbeiten, welche als Beschreibung für die Zusatzoptionen bei der Energieausweis-Bestellung dienen.',
							'mode'        => 'draggable',
							'capability'  => 'manage_options',
							'sections'    => $options,
						),
					),
				),
			),
		),
	), 'energieausweis_zusatzoptionen' );
}

add_action( 'wpod', 'energieausweis_zusatzoptionen_settings', 10, 1 );

function energieausweis_zusatzoption_experten_check_info() {
	$settings = get_option( 'energieausweis_zusatzoptionen', array() );

	if ( isset( $settings['experten_check_description'] ) ) {
		echo wpautop( $settings['experten_check_description'] );
	} else {
		echo energieausweis_zusatzoptionen_get_default( 'experten_check_description' );
	}
}

function energieausweis_zusatzoption_sendung_per_post_info() {
	$settings = get_option( 'energieausweis_zusatzoptionen', array() );

	if ( isset( $settings['sendung_per_post_description'] ) ) {
		echo wpautop( $settings['sendung_per_post_description'] );
	} else {
		echo energieausweis_zusatzoptionen_get_default( 'sendung_per_post_description' );
	}
}

function energieausweis_zusatzoption_energieausweis_besprechung_info() {
	$settings = get_option( 'energieausweis_zusatzoptionen', array() );

	if ( isset( $settings['energieausweis_besprechung_description'] ) ) {
		echo wpautop( $settings['energieausweis_besprechung_description'] );
	} else {
		echo energieausweis_zusatzoptionen_get_default( 'energieausweis_besprechung_description' );
	}
}

function energieausweis_zusatzoption_kostenlose_korrektur_info() {
	$settings = get_option( 'energieausweis_zusatzoptionen', array() );

	if ( isset( $settings['kostenlose_korrektur_description'] ) ) {
		echo wpautop( $settings['kostenlose_korrektur_description'] );
	} else {
		echo energieausweis_zusatzoptionen_get_default( 'kostenlose_korrektur_description' );
	}
}

function energieausweis_zusatzoption_premium_bewertung_info() {
	$settings = get_option( 'energieausweis_zusatzoptionen', array() );

	if ( isset( $settings['premium_bewertung_description'] ) ) {
		echo wpautop( $settings['premium_bewertung_description'] );
	} else {
		echo energieausweis_zusatzoptionen_get_default( 'premium_bewertung_description' );
	}
}

function energieausweis_zusatzoption_eingabesupport_info() {
	$settings = get_option( 'energieausweis_zusatzoptionen', array() );

	if ( isset( $settings['eingabesupport_description'] ) ) {
		echo wpautop( $settings['eingabesupport_description'] );
	} else {
		echo energieausweis_zusatzoptionen_get_default( 'eingabesupport_description' );
	}
}

function energieausweis_zusatzoptionen_get_default( $field = '' ) {
	switch ( $field ) {
		case 'experten_check_label':
			return 'Energieausweis-Check vom Experten (empfohlen)';
		case 'experten_check_description':
			return '<p>Das wichtigste Erfolgskriterium für einen Energieausweis sind die Angaben.</p><p>Wenn es Ihnen wie den meisten unserer Kunden geht und Sie sich nicht ganz sicher sind, ob Sie uns die entscheidenden Angaben zur Verfügung gestellt haben, dann empfehlen wir Ihnen diesen Check von unserem Experten. Dieser wird Sie dann kontaktieren, um mit Ihnen Ihren Ausweis zu besprechen.</p><p><strong>Bitte beachten Sie:</strong> Fehlerhafte Angaben können die Ermittlung der Energiekennwerte beeinflussen, daher erhalten Sie mit dieser manuellen Plausibilitätsprüfung ein Stück mehr Sicherheit!</p>';
		case 'experten_check_price':
			return 19.95;
		case 'experten_check_order':
			return 1;
		case 'sendung_per_post_label':
			return 'Ausweis und Rechnung zusätzlich per Post';
		case 'sendung_per_post_description':
			return '<p>Ihren Energieausweis sowie Ihre Rechnung erhalten Sie automatisch bei jeder Bestellung als PDF-Datei.</p><p>Wenn Sie Ihren Energieausweis und Ihre Rechnung jedoch lieber in den Händen halten möchten, können Sie diese Unterlagen selbstverständlich auch zusätzlich per Postweg anfordern.</p>';
		case 'sendung_per_post_price':
			return 4.95;
		case 'sendung_per_post_order':
			return 2;
		case 'energieausweis_besprechung_label':
			return 'Gespräch: Das sollten Sie zu Ihrem Energieausweis wissen (von Kunden empfohlen)';
		case 'energieausweis_besprechung_description':
			return '<p>Viele Kunden, die einen Energieausweis benötigen, können mit den Ergebnissen wenig anfangen. Damit Sie die Ergebnisse Ihres Energieausweises auch verstehen, haben Sie die Möglichkeit mit dieser Zusatzoption sich von unserem Experten Ihren Energieausweis erklären zu lassen. Nach Ihrer Bestellung stimmen wir uns zu einem Telefongespräch ab, bei dem wir Ihnen Ihren Energieausweis verständlich erklären.</p><p>Sie erfahren:</p><ul><li>wie Sie das Ergebnis des Energieausweises interpretieren</li><li>welche Angaben für Interessenten und die Vermarktung wichtig sind</li><li>wie Sie Interessenten (bei Verkauf oder Vermietung) das Ergebnis des Ausweises erklären</li></ul>';
		case 'energieausweis_besprechung_price':
			return 19.95;
		case 'energieausweis_besprechung_order':
			return 3;
		case 'kostenlose_korrektur_label':
			return 'NEU! Kostenlose Korrektur bei Feststellung falscher Angabe';
		case 'kostenlose_korrektur_description':
			return '<p>Bei Auswahl dieser Option können Sie innerhalb von maximal 6 Wochen nach Bestellabschluss den Energieausweis für das gleiche Gebäude korrigieren und kostenlos neu bestellen.</p>';
		case 'kostenlose_korrektur_price':
			return 9.95;
		case 'kostenlose_korrektur_order':
			return 4;
		case 'premium_bewertung_label':
			return 'Premium-Bewertung Ihrer Immobilie - KOSTENLOSE TESTPHASE!';
		case 'premium_bewertung_description':
			return '<p>Bei Auswahl dieser Option nehmen wir nach Abschluss Ihrer Bestellung mit Ihnen Kontakt auf, um den Wert Ihrer Immobilie zu ermitteln und Ihnen hierfür eine Verkaufsempfehlung zu geben. Die Bewertung Ihrer Immobilie ist kostenfrei.</p>';
		case 'premium_bewertung_price':
			return 0;
		case 'premium_bewertung_order':
			return 5;
		case 'eingabesupport_label':
			return 'NEU: Professioneller Eingabesupport!';
		case 'eingabesupport_description':
			return '<p>Bei Auswahl dieser Option nehmen wir nach Abschluss Ihrer Bestellung mit Ihnen Kontakt auf, um den Wert Ihrer Immobilie zu ermitteln und Ihnen hierfür eine Verkaufsempfehlung zu geben. Die Bewertung Ihrer Immobilie ist kostenfrei.</p>';
		case 'eingabesupport_price':
			return 0;
		case 'eingabesupport_order':
			return 6;
	}
}

function energieausweis_zusatzoptionen_require_phone_number_premium_bewertung( $fields ) {
	$fees = edd_get_cart_fees();

	if ( isset( $fees['premium_bewertung'] ) ) {
		$fields['wpenon_telefon'] = array(
			'error_id'      => 'it_missing_phone_number',
			'error_message' => 'Bitte geben Sie Ihre Telefonnummer an.',
		);
	}

	return $fields;
}

add_filter( 'edd_purchase_form_required_fields', 'energieausweis_zusatzoptionen_require_phone_number_premium_bewertung' );

function energieausweis_zusatzoptionen_require_phone_number_premium_bewertung_script() {
	if ( ! edd_is_checkout() ) {
		return;
	}

	?>
	<script type="text/javascript">
		document.getElementById('edd_checkout_wrap').addEventListener('click', function (event) {
			var element = event.target;
			var phoneLabel;

			if ('edd_custom_fee_premium_bewertung' !== element.getAttribute('id') && 'edd_custom_fee_premium_bewertung' !== element.getAttribute('for')) {
				return;
			}

			phoneLabel = document.querySelector('label[for="wpenon-telefon"]');
			if (!phoneLabel) {
				return;
			}

			if (document.getElementById('edd_custom_fee_premium_bewertung').checked) {
				phoneLabel.innerHTML = phoneLabel.innerHTML.replace('(optional)', '<span class="edd-required-indicator">*</span>');
			} else {
				phoneLabel.innerHTML = phoneLabel.innerHTML.replace('<span class="edd-required-indicator">*</span>', '(optional)');
			}
		});
	</script>
	<?php
}

add_action( 'wp_footer', 'energieausweis_zusatzoptionen_require_phone_number_premium_bewertung_script' );

function energieausweis_zusatzoptionen_render_premium_bewertung_list_table_filter() {
    $option = $_REQUEST['zusatzoption_filter'];
	?>
    <select name="zusatzoption_filter" id="zusatzoption_filter" class="edd-select">
        <option value=""><?php _e( 'Alle Zusatzoptionen', 'wpenon' ); ?></option>
        <option value="premium_bewertung"  <?php selected( $option, 'premium_bewertung' ); ?>><?php _e( 'Premium-Bewertung enthalten?', 'wpenon' ); ?></option>
        <option value="experten_check"  <?php selected( $option, 'experten_check' ); ?>><?php _e( 'Experten-Check', 'wpenon' ); ?></option>
        <option value="sendung_per_post"  <?php selected( $option, 'sendung_per_post' ); ?>><?php _e( 'Sendung per Post', 'wpenon' ); ?></option>
        <option value="energieausweis_besprechung"  <?php selected( $option, 'energieausweis_besprechung' ); ?>><?php _e( 'Energieausweis Besprechung', 'wpenon' ); ?></option>
        <option value="kostenlose_korrektur"  <?php selected( $option, 'kostenlose_korrektur' ); ?>><?php _e( 'Kostenlose Korrektur', 'wpenon' ); ?></option>
        <option value="eingabesupport"  <?php selected( $option, 'eingabesupport' ); ?>><?php _e( 'Eingabesupport', 'wpenon' ); ?></option>
        <option value="check_evm"  <?php selected( $option, 'check_evm' ); ?>><?php _e( 'Check EVM', 'wpenon' ); ?></option>
    </select>
	<?php
}

add_action( 'edd_payment_advanced_filters_after_fields', 'energieausweis_zusatzoptionen_render_premium_bewertung_list_table_filter' );

function energieausweis_zusatzoptionen_apply_premium_bewertung_list_table_filter( $payments_query ) {

	
    switch ( $_REQUEST['zusatzoption_filter']) {
        case 'premium_bewertung':
            $payments_query->__set( 'meta_query', array(
                'key'     => '_edd_payment_meta',
                'value'   => 'premium_bewertung',
                'compare' => 'LIKE',
            ) );
            add_filter( 'query', function ( $sql ) {
                return str_replace( 'premium\\\\_bewertung', 'premium_bewertung', $sql );
            } );
            break;

        case 'experten_check':
            $payments_query->__set( 'meta_query', array(
                'key'     => '_edd_payment_meta',
                'value'   => 'experten_check',
                'compare' => 'LIKE',
            ) );
            add_filter( 'query', function ( $sql ) {
                return str_replace( 'experten\\\\_check', 'experten_check', $sql );
            } );
            break;

        case 'sendung_per_post':
            $payments_query->__set( 'meta_query', array(
                'key'     => '_edd_payment_meta',
                'value'   => 'sendung_per_post',
                'compare' => 'LIKE',
            ) );
            add_filter( 'query', function ( $sql ) {
                return str_replace( 'sendung\\\\_per\\\\_post', 'sendung_per_post', $sql );
            } );
            break;

        case 'energieausweis_besprechung':
            $payments_query->__set( 'meta_query', array(
                'key'     => '_edd_payment_meta',
                'value'   => 'energieausweis_besprechung',
                'compare' => 'LIKE',
            ) );
            add_filter( 'query', function ( $sql ) {
                return str_replace( 'energieausweis\\\\_besprechung', 'energieausweis_besprechung', $sql );
            } );
            break;

        case 'kostenlose_korrektur':
            $payments_query->__set( 'meta_query', array(
                'key'     => '_edd_payment_meta',
                'value'   => 'kostenlose_korrektur',
                'compare' => 'LIKE',
            ) );
            add_filter( 'query', function ( $sql ) {
                return str_replace( 'kostenlose\\\\_korrektur', 'kostenlose_korrektur', $sql );
            } );
            break;

        case 'eingabesupport':
            $payments_query->__set( 'meta_query', array(
                'key'     => '_edd_payment_meta',
                'value'   => 'eingabesupport',
                'compare' => 'LIKE',
            ) );
            add_filter( 'query', function ( $sql ) {
                return str_replace( 'eingabesupport', 'eingabesupport', $sql );
            } );
            break;

        case 'check_evm':
            $payments_query->__set( 'meta_query', array(
                'key'     => '_edd_payment_meta',
                'value'   => 'check_evm',
                'compare' => 'LIKE',
            ) );
            add_filter( 'query', function ( $sql ) {
                return str_replace( 'check\\\\_evm', 'check_evm', $sql );
            } );
            break;
    }

}

add_action( 'edd_pre_get_payments', 'energieausweis_zusatzoptionen_apply_premium_bewertung_list_table_filter' );

function energieausweis_zusatzoptionen_apply_premium_bewertung_list_table_count_join_filter( $join ) {
	global $wpdb;

	$enabled = ! empty( $_REQUEST['zusatzoption_filter'] );
	if ( ! $enabled ) {
		return;
	}

	$join .= "LEFT JOIN $wpdb->postmeta ez ON (p.ID = ez.post_id)";

	return $join;
}

add_filter( 'edd_count_payments_join', 'energieausweis_zusatzoptionen_apply_premium_bewertung_list_table_count_join_filter' );

function energieausweis_zusatzoptionen_apply_premium_bewertung_list_table_count_where_filter( $where ) {
	$enabled = ! empty( $_REQUEST['zusatzoption_filter'] );
	if ( ! $enabled ) {
		return;
	}

	$where .= " AND ez.meta_key = '_edd_payment_meta' AND ez.meta_value LIKE '%premium_bewertung%'";

	return $where;
}

add_filter( 'edd_count_payments_where', 'energieausweis_zusatzoptionen_apply_premium_bewertung_list_table_count_where_filter' );
