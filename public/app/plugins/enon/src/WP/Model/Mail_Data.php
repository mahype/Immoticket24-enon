<?php
/**
 * Class for getting enon settings/options.
 *
 * @category Class
 * @package  Enon\WP\Model
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP\Model;

use Enon\ACF\Models\ACF_Settings;

/**
 * Class Settings.
 *
 * @since 1.0.0
 */
class Mail_Data extends ACF_Settings {
	/**
	 * Get confirmation sender name.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_confirmation_sender_name() {
		return $this->get( 'confirmation_sender_name' );
	}

	/**
	 * Get confirmation sender email.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_confirmation_sender_email() {
		return $this->get( 'confirmation_sender_email' );
	}

	/**
	 * Get confirmation subject.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_confirmation_subject() {
		return $this->get( 'confirmation_subject' );
	}

	/**
	 * Get confirmation content.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_confirmation_content() {
		return $this->get( 'confirmation_content' );
	}

	/**
	 * Get bill sender name.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_bill_sender_name() {
		return $this->get( 'bill_sender_name' );
	}

	/**
	 * Get bill sender email.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_bill_sender_email() {
		return $this->get( 'bill_sender_email' );
	}

	/**
	 * Get bill subject.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_bill_subject() {
		return $this->get( 'bill_subject' );
	}

	/**
	 * Get bill content.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_bill_content() {
		return $this->get( 'bill_content' );
	}

	/**
	 * Fieldset for ACF.
	 *
	 * @since 1.0.0
	 *
	 * @return array $fieldset ACF options page Fieldset.
	 */
	public function fieldset() {
		return array_merge( $this->fieldset_confirmation_email(), $this->fieldset_bill_email() );
	}

	/**
	 * Fieldset confirmation email.
	 *
	 * @return array $fieldset Confirmation email fieldset.
	 *
	 * @since 1.0.0
	 */
	private function fieldset_confirmation_email() {
		$fieldset = array(
			array(
				'key' => 'field_tab_confirmation_email',
				'label' => __( 'Bestellbestätigung', 'enon' ),
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			),
			array(
				'key' => 'field_confirmation_sender_name',
				'label' => __( 'Absender Name', 'enon' ),
				'name' => 'confirmation_sender_name',
				'type' => 'text',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'Immoticket24.de GmbH',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_confirmation_sender_email',
				'label' => __( 'Anbsender Email', 'enon' ),
				'name' => 'confirmation_sender_email',
				'type' => 'email',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'christian@energieausweis-online-erstellen.de',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_confirmation_subject',
				'label' => __( 'Betreff', 'enon' ),
				'name' => 'confirmation_subject',
				'type' => 'text',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'Ihr Energieausweis',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_confirmation_content',
				'label' => 'Inhalt',
				'name' => 'confirmation_content',
				'type' => 'wysiwyg',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'tabs' => 'all',
				'toolbar' => 'full',
				'media_upload' => 1,
				'delay' => 0,
			),
		);

		return $fieldset;
	}

	/**
	 * Fieldset bill email.
	 *
	 * @return array $fieldset Confirmation email fieldset.
	 *
	 * @since 1.0.0
	 */
	private function fieldset_bill_email() {
		$fieldset = array(
			array(
				'key' => 'field_bill',
				'label' => __( 'Rechnung', 'enon' ),
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			),
			array(
				'key' => 'field_bill_sender_name',
				'label' => __( 'Absender Name', 'enon' ),
				'name' => 'bill_sender_name',
				'type' => 'text',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'Immoticket24.de GmbH',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_bill_sender_email',
				'label' => __( 'Absender Email', 'enon' ),
				'name' => 'bill_sender_email',
				'type' => 'email',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'christian@energieausweis-online-erstellen.de',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array(
				'key' => 'field_bill_subject',
				'label' => __( 'Betreff', 'enon' ),
				'name' => 'bill_subject',
				'type' => 'text',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'Zahlungsaufforderung',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_bill_content',
				'label' => 'Inhalt',
				'name' => 'bill_content',
				'type' => 'wysiwyg',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'tabs'          => 'all',
				'toolbar'       => 'full',
				'media_upload'  => 1,
				'delay'         => 0,
			),
		);

		return $fieldset;
	}
}
