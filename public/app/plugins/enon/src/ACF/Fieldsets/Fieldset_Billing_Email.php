<?php
/**
 * Billing email fieldset.
 *
 * @category Class
 * @package  Enon\ACF\Fieldsets
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\ACF\Fieldsets;

use Enon\ACF\Models\Fieldset;

/**
 * Class Billing_Email.
 *
 * @since 1.0.0
 */
class Fieldset_Billing_Email implements Fieldset {
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
				'key'   => 'field_bill_email_sender_address',
				'label' => __( 'E-Mail sender address', 'enon' ),
				'name'  => 'bill_email_sender_address',
				'type'  => 'email',
			),
			array(
				'key'   => 'field_bill_email_sender_name',
				'label' => __( 'E-Mail sender name', 'enon' ),
				'name'  => 'bill_email_sender_name',
				'type'  => 'text',
			),
			array(
				'key'     => 'field_bill_email_subject',
				'label'   => __( 'E-Mail Subject', 'enon' ),
				'name'    => 'bill_email_subject',
				'type'    => 'text',
				'default' => 'Zahlungsaufforderung',
			),
			array(
				'key'   => 'field_bill_email_content',
				'label' => __( 'E-Mail Content', 'enon' ),
				'name'  => 'bill_email_content',
				'type'  => 'wysiwyg',
			),
		];

		return $data;
	}
}
