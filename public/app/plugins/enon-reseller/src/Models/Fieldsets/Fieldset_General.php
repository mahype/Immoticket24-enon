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
				'key'          => 'field_company_id',
				'label'        => __( 'Company ID', 'enon' ),
				'name'         => 'company_id',
				'type'         => 'text',
				'instructions' => 'This field is very important to fetch correct templates or sender classes. Please DO NOT CHANGE if you are not know what you are doing.',
				'placeholder'  => '',
				'prepend'      => '',
				'append'       => '',
				'maxlength'    => '',
			),
			array(
				'key'          => 'field_company_name',
				'label'        => __( 'Company Name', 'enon' ),
				'name'         => 'company_name',
				'type'         => 'text',
				'instructions' => __( 'Resellers company name.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_contact_name',
				'label'        => __( 'Kontakt Name', 'enon' ),
				'name'         => 'contact_name',
				'type'         => 'text',
				'instructions' => __( 'The name of the contact person on the company.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_contact_email',
				'label'        => __( 'Kontakt Email', 'enon' ),
				'name'         => 'contact_email',
				'type'         => 'email',
				'instructions' => __( 'The email of the contact person on the company. All emails from the system will be sent to this address.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_email_delivery',
				'label'        => __( 'Emailversand', 'enon' ),
				'name'         => 'email_delivery',
				'type'         => 'checkbox',
				'choices' => array(
					'send_confirmation_to_customer' => 'Bestätigunsmail &rarr; Kunde',
					'send_confirmation_to_reseller' => 'Bestätigunsmail &rarr; Reseller',
					'send_bill_to_customer' => 'Rechnungsmail &rarr; Kunden',
					'send_bill_to_reseller' => 'Rechnungsmail &rarr; Reseller',
				),
				'default_value' => array(
					'send_confirmation_to_customer',
					'send_bill_to_customer',
				),
				'instructions' => __( 'An wen sollen welche Emails gesendet werden?', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_send_bill_to_reseller',
				'label'        => __( 'Rechnung zum Reseller senden', 'enon' ),
				'name'         => 'send_bill_to_reseller',
				'type'         => 'checkbox',
				'choices' => array(
					'send_bill_to_reseller' => 'Ja',
				),
				'instructions' => __( 'Check if bill email should be sent to reseller instead of customer.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_price_bw',
				'label'        => __( 'Preis Bedarfsausweis', 'enon' ),
				'name'         => 'price_bw',
				'type'         => 'number',
				'instructions' => __( 'Set individual price for reseller or leave empty for standard price.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_price_vw',
				'label'        => __( 'Preis Verbrauchsausweis', 'enon' ),
				'name'         => 'price_vw',
				'type'         => 'number',
				'instructions' => __( 'Set individual price for reseller or leave empty for standard price.', 'enon' ),
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
				'instructions'  => __( 'The token which have to be set by the reseller.', 'enon' ),
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
		];

		return $data;
	}
}
