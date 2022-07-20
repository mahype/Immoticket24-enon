<?php
/**
 * Reseller website settings fieldset.
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
 * Class Reseller_Website.
 *
 * @since 1.0.0
 */
class Fieldset_Website implements Fieldset {
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
				'key'          => 'field_website_name',
				'label'        => __( 'Website name', 'enon' ),
				'name'         => 'website_name',
				'type'         => 'text',
				'instructions' => __( 'This is the website name, which appears in emails.', 'enon' ),
			),
			array(
				'key'          => 'field_customer_edit_vw_url',
				'label'        => __( 'Customer Edit Verbrauchsausweis URL', 'enon' ),
				'instructions' => __( 'The edit url for Verbrauchsausweis of the reseller website.', 'enon' ),
				'name'         => 'customer_edit_vw_url',
				'type'         => 'url',
				'placeholder'  => 'https://',
			),
			array(
				'key'          => 'field_customer_edit_bw_url',
				'label'        => __( 'Customer Edit Bedarfsausweis URL', 'enon' ),
				'instructions' => __( 'The edit url for Verbrauchsausweis of the reseller website.', 'enon' ),
				'name'         => 'customer_edit_bw_url',
				'type'         => 'url',
				'placeholder'  => 'https://',
			),
			array(
				'key'          => 'field_payment_successful_url',
				'label'        => __( 'Payment successful URL', 'enon' ),
				'instructions' => __( 'This url will be shown after successful payment.', 'enon' ),
				'name'         => 'payment_successful_url',
				'type'         => 'url',
				'placeholder'  => 'https://',
			),
			array(
				'key'          => 'field_payment_pending_url',
				'label'        => __( 'Payment pending URL', 'enon' ),
				'instructions' => __( 'This url will be shown after pending payment.', 'enon' ),
				'name'         => 'payment_pending_url',
				'type'         => 'url',
				'placeholder'  => 'https://',
			),
			array(
				'key'          => 'field_payment_failed_url',
				'label'        => __( 'Payment failed URL', 'enon' ),
				'instructions' => __( 'This url will be shown after failed payment.', 'enon' ),
				'name'         => 'payment_failed_url',
				'type'         => 'url',
				'placeholder'  => 'https://',
			),
			array(
				'key' => 'field_redirect_via_js',
				'label' => 'Redirect über Javascript an die Kundenseite senden',
				'name' => 'redirect_via_js',
				'type' => 'radio',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'yes' => 'Ja',
					'no' => 'Nein',
				),
				'allow_null' => 0,
				'other_choice' => 0,
				'default_value' => 'no',
				'layout' => 'horizontal',
				'return_format' => 'value',
				'save_other_choice' => 0,
			),
			array(
				'key'         => 'field_privacy_url',
				'label'       => __( 'Datenschutzerklärung URL', 'enon' ),
				'name'        => 'privacy_url',
				'type'        => 'url',
				'placeholder' => 'https://',
			),
		];

		return $data;
	}
}
