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
				'key'           => 'field_bill_sender_name',
				'label'         => __( 'Absender Name', 'enon' ),
				'name'          => 'bill_sender_name',
				'type'          => 'text',
				'instructions'  => '',
				'required'      => 0,
				'default_value' => 'Immoticket24.de GmbH',
			),
			array(
				'key'           => 'field_bill_sender_email',
				'label'         => __( 'Absender Email', 'enon' ),
				'name'          => 'bill_sender_email',
				'type'          => 'email',
				'instructions'  => '',
				'required'      => 1,
				'default_value' => 'christian@energieausweis-online-erstellen.de',
			),
			array(
				'key'           => 'field_bill_subject',
				'label'         => __( 'Betreff', 'enon' ),
				'name'          => 'bill_subject',
				'type'          => 'text',
				'instructions'  => '',
				'required'      => 0,
				'default_value' => 'Zahlungsaufforderung',
			),
			array(
				'key'          => 'field_bill_content',
				'label'        => 'Inhalt',
				'name'         => 'bill_content',
				'type'         => 'wysiwyg',
				'instructions' => '',
				'required'     => 0,
			),
		];

		return $data;
	}
}
