<?php
/**
 * Reseller website settings fieldset.
 *
 * @category Class
 * @package  Enon\Acf\Fieldsets
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Acf\Fieldsets;

use Enon\Acf\Models\Fieldset;

/**
 * Class Reseller_Website.
 *
 * @since 1.0.0
 */
class Fieldset_Reseller_Website implements Fieldset {
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
				'key' => 'field_website_name',
				'label' => __( 'Website name', 'enon' ),
				'name' => 'website_name',
				'type' => 'text',
				'instructions' => __( 'This is the website name, which appears in emails.', 'enon' ),
			),
			array(
				'key' => 'field_customer_edit_url',
				'label' => __( 'Customer Edit URL', 'enon' ),
				'instructions' => __( 'The edit url of the reseller website.', 'enon' ),
				'name' => 'customer_edit_url',
				'type' => 'url',
				'placeholder' => 'https://',
			),
			array(
				'key' => 'field_payment_successful_url',
				'label' => __( 'Payment successful URL', 'enon' ),
				'instructions' => __( 'This url will be shown after successful payment.', 'enon' ),
				'name' => 'payment_successful_url',
				'type' => 'url',
				'placeholder' => 'https://',
			),
			array(
				'key' => 'field_payment_failed_url',
				'label' => __( 'Payment failed URL', 'enon' ),
				'instructions' => __( 'This url will be shown after failed payment.', 'enon' ),
				'name' => 'payment_failed_url',
				'type' => 'url',
				'placeholder' => 'https://',
			),
		];

		return $data;
	}
}
