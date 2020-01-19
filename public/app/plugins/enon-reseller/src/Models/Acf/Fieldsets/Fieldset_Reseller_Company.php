<?php
/**
 * Reseller company settings fieldset.
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
 * Class Reseller_Company.
 *
 * @since 1.0.0
 */
class Company implements Fieldset {
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
				'label'        => __( 'Company Name', 'enon' ),
				'name'         => 'company_name',
				'type'         => 'text',
				'instructions' => __( 'Resellers company name.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_contact_name',
				'label'        => __( 'Contact Name', 'enon' ),
				'name'         => 'contact_name',
				'type'         => 'text',
				'instructions' => __( 'The name of the contact person on the company.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_contact_email',
				'label'        => __( 'Contact Email', 'enon' ),
				'name'         => 'contact_email',
				'type'         => 'email',
				'instructions' => __( 'The email of the contact person on the company. All emails from the system will be sent to this address.', 'enon' ),
				'required'     => 0,
			),
			array(
				'key'          => 'field_send_data_to_reseller',
				'label'        => __( 'Send order to reseller', 'enon' ),
				'name'         => 'send_order_to_reseller',
				'type'         => 'checkbox',
				'instructions' => __( 'Check if order email should be sent to reseller.', 'enon' ),
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
