<?php
/**
 * Reseller company settings fieldset.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Fieldsets;
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Fieldsets;

use Enon\Models\Fieldsets\Fieldset;

/**
 * Class Reseller_Company.
 *
 * @since 1.0.0
 */
class Fieldset_General implements Fieldset {
	/**
	 * Get the fieldset.
	 *
	 * @return array $data Fieldset data.
	 *
	 * @since 1.0.0
	 */
	public function get() : array {
		$data = [			
			array(
				'key'          => 'field_company_name',
				'label'        => __( 'Firmenname', 'enon' ),
				'name'         => 'company_name',
				'type'         => 'text',
				'instructions' => __( 'Firmenname des Resellers.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_contact_name',
				'label'        => __( 'Kontakt Name', 'enon' ),
				'name'         => 'contact_name',
				'type'         => 'text',
				'instructions' => __( 'Name der Kontaktperson in der Firma.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_contact_email',
				'label'        => __( 'Kontakt Email', 'enon' ),
				'name'         => 'contact_email',
				'type'         => 'email',
				'instructions' => __( 'Die Email-Adresse der Kontaktperson. Alle Emails aus dem System gehen an diese Adresse.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_email_settings',
				'label'        => __( 'Optionen für ausgehende Emails', 'enon' ),
				'name'         => 'email_settings',
				'type'         => 'checkbox',
				'choices' => array(
					'redirect_bill_to_reseller' => __( '"Zahlungsaufforderung" zum Reseller umleiten.', 'enon' ),
					'send_order_confirmation_to_reseller' => __( '"Neue Energieausweis-Bestellung" an Reseller senden.', 'enon' ),
				),
				'default_value' => array(
					1 => 'send_order_confirmation_to_reseller'
				),
				'required'     => 0,
            ),
            array(
				'key' => 'field_marketing',
				'label' => __( 'Marketing-Tools', 'enon' ),
				'name' => 'marketing',
				'type' => 'checkbox',
				'choices' => array(
					'klicktipp'            => __( 'Klicktipp - Email-Adressen bei Newsletter-Einwilligung an Klicktipp senden.', 'enon' ),
				),
				'default_value' => array(
					0 => 'klicktipp'
				),
				'return_format' => 'value',
			),
			array(
				'key'          => 'field_price_bw',
				'label'        => __( 'Preis Bedarfsausweis', 'enon' ),
				'name'         => 'price_bw',
				'type'         => 'number',
				'instructions' => __( 'Individuer Preis des Bedarfsausweises für die Kunden des Resellers. Wird kein Wert eingetragen, so gilt der Preis auf energieausweis-online-erstellen.de.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_price_vw',
				'label'        => __( 'Preis Verbrauchsausweis', 'enon' ),
				'name'         => 'price_vw',
				'type'         => 'number',
				'instructions' => __( 'Individuer Preis des Verbrauchsausweises für die Kunden des Resellers. Wird kein Wert eingetragen, so gilt der Preis auf energieausweis-online-erstellen.de.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_custom_fees',
				'label'        => __( 'Zusätzliche Leistungen', 'enon' ),
				'name'         => 'custom_fees',
				'type'         => 'checkbox',
				'choices' => array(
					'energieausweis_besprechung' => 'Energieausweis Besprechung',
					'experten_check' => 'Experten Check',
					'sendung_per_post' => 'Sendung per Post',
					'kostenlose_korrektur' => 'Kostenlose Korrektur',
					'premium_bewertung' => 'Premium Bewertung',
				),
				'default_value' => array(
					'energieausweis_besprechung',
					'experten_check',
					'sendung_per_post',
					'kostenlose_korrektur',
					'premium_bewertung',
				),
				'instructions' => __( 'Zusätzliche Leistungen des Resellers.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'           => 'field_token',
				'label'         => __( 'Token', 'enon' ),
				'name'          => 'token',
				'type'          => 'text',
				'default_value' => substr( md5( rand() ), 0, 14 ),
				'instructions'  => __( 'Der Token, der für den Reseller genutzt werden soll.', 'enon' ),
				'required'      => 0,
			),
			array(
				'key'          => 'field_affiliateId',
				'label'        => __( 'Affiliate ID', 'enon' ),
				'name'         => 'affiliate_id',
				'type'         => 'number',
				'instructions' => __( 'Affiliate WP id.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_company_id',
				'label'        => __( 'Firmen ID', 'enon' ),
				'name'         => 'company_id',
				'type'         => 'text',				
				'placeholder'  => '',
				'prepend'      => '',
				'append'       => '',
				'maxlength'    => '',
				'instructions' => __( 'Eindeutige Firmen ID. Bitte NICHT den Firmennamen eintragen. Die Id wird für die Auswahl von eigens angelegten Templates und Skripte für Reseller benötigt.', 'enon' ),
			)
		];

		return $data;
	}
}
