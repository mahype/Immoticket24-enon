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
				'label'        => __( 'Name der Webseite', 'enon' ),
				'name'         => 'website_name',
				'type'         => 'text',
				'instructions' => __( 'Der Name der Seite wird in Emails angezeigt.', 'enon' ),
			),
			array(
				'key'          => 'field_customer_edit_vw_url',
				'label'        => __( 'Verbrauchsausweis URL', 'enon' ),
				'instructions' => __( 'Die URL auf der Kunde die Iframe Skripte für Verbrauchsausweise eingebunden hat.', 'enon' ),
				'name'         => 'customer_edit_vw_url',
				'type'         => 'url',
				'placeholder'  => 'https://',
			),
			array(
				'key'          => 'field_customer_edit_bw_url',
				'label'        => __( 'Bedarfsausweis URL', 'enon' ),
				'instructions' => __( 'Die URL auf der Kunde die Iframe Skripte für Verbrauchsausweise eingebunden hat.', 'enon' ),
				'name'         => 'customer_edit_bw_url',
				'type'         => 'url',
				'placeholder'  => 'https://',
			),
			array(
				'key'          => 'field_payment_successful_url',
				'label'        => __( 'Zahlung erfolgreich URL', 'enon' ),
				'instructions' => __( 'Diese URL wird für den Fall angezeigt, dass die Zahlung erfolgreich eingegangen ist.', 'enon' ),
				'name'         => 'payment_successful_url',
				'type'         => 'url',
				'placeholder'  => 'https://',
			),
			array(
				'key'          => 'field_payment_pending_url',
				'label'        => __( 'Zahlung wartend URL', 'enon' ),
				'instructions' => __( 'Diese URL wird für den Fall angezeigt, dass die Zahlung noch nicht eingegangen ist.', 'enon' ),
				'name'         => 'payment_pending_url',
				'type'         => 'url',
				'placeholder'  => 'https://',
			),
			array(
				'key'          => 'field_payment_failed_url',
				'label'        => __( 'Zahlung fehlgeschlagen URL', 'enon' ),
				'instructions' => __( 'Diese URL wird für den Fall angezeigt, dass die Zahlung fehlgeschlagen ist.', 'enon' ),
				'name'         => 'payment_failed_url',
				'type'         => 'url',
				'placeholder'  => 'https://',
			),
			array(
				'key' => 'field_redirect_via_js',
				'label' => 'Weiterleitung nach Zahlung',
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
					'yes' => 'Direkt auf der Seite des Kunden',
					'no' => 'über den Iframe der Seite des Kunden',
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
