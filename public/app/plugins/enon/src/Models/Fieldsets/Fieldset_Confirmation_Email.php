<?php
/**
 * Confirmation email fieldset.
 *
 * @category Class
 * @package  Enon\Acf\Fieldsets
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Fieldsets;

/**
 * Class Confirmation_Email.
 *
 * @since 1.0.0
 */
class Fieldset_Confirmation_Email implements Fieldset {
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
				'key'           => 'field_confirmation_sender_name',
				'label'         => __( 'Absender Name', 'enon' ),
				'name'          => 'confirmation_sender_name',
				'type'          => 'text',
				'instructions'  => '',
				'required'      => 0,
				'default_value' => 'Immoticket24.de GmbH',
			),
			array(
				'key'           => 'field_confirmation_sender_email',
				'label'         => __( 'Anbsender Email', 'enon' ),
				'name'          => 'confirmation_sender_email',
				'type'          => 'email',
				'instructions'  => '',
				'required'      => 0,
				'default_value' => 'christian@energieausweis-online-erstellen.de',
			),
			array(
				'key'           => 'field_confirmation_subject',
				'label'         => __( 'Betreff', 'enon' ),
				'name'          => 'confirmation_subject',
				'type'          => 'text',
				'instructions'  => '',
				'required'      => 0,
				'default_value' => 'Ihr Energieausweis',
			),
			array(
				'key'           => 'field_confirmation_content',
				'label'         => 'Inhalt',
				'name'          => 'confirmation_content',
				'type'          => 'wysiwyg',
				'instructions'  => '',
				'required'      => 0,
				'default_value' => '',
			),
		];

		return $data;
	}
}
