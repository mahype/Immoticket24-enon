<?php
/**
 * Reseller iframe settings fieldset.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Fieldsets;
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Fieldsets;

use Enon\Models\Fieldsets\Fieldset;
use Enon_Reseller\Models\Data\Post_Meta_General;

/**
 * Class Reseller_Iframe.
 *
 * @since 1.0.0
 */
class Fieldset_Iframe implements Fieldset {
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
				'key' => 'field_elements',
				'label' => __( 'Welche Elemente sollen angezeigt werden', 'enon' ),
				'name' => 'elements',
				'type' => 'checkbox',
				'choices' => array(
					'show_title'             => __( 'Titel', 'enon' ),
					'show_description'       => __( 'Beschreibung', 'enon' ),
					'show_newsletter_terms'  => __( 'Newsletter-Checkbox', 'enon' ),
					'show_coupon_code_field' => __( 'Coupon Code Feld anzeigen', 'enon' ),
				),
				'default_value' => array(
					0 => 'show_title',
					1 => 'show_description',
					2 => 'show_newsletter_terms',
					3 => 'show_couponcode_field',
				),
				'return_format' => 'value',
			),
			array(
				'key' => 'field_newsletter_terms',
				'label' => __( 'Newsletter checkbox text', 'enon' ),
				'name' => 'newsletter_terms',
				'type' => 'acf_code_field',
				'instructions' => __( 'Text für die Newsletter checkbox. Ist der Text leer, wird der Standardtext genommen', 'enon' ),
				'mode' => 'html',
				'theme' => 'monokai',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_elements',
                            'operator' => '==',
                            'value' => 'show_newsletter_terms',
                        ),
                    ),
                ),
            ),
            array(
				'key' => 'field_extra_js',
				'label' => __( 'Extra JS', 'enon' ),
				'name' => 'extra_js',
				'type' => 'acf_code_field',
				'instructions' => __( 'Add some extra JavaScript for reseller.', 'enon' ),
				'mode' => 'javascript',
				'theme' => 'monokai',
				'default_value' => $this->get_default_extra_js(),
			),
            array(
				'key' => 'field_extra_css',
				'label' => __( 'Extra CSS', 'enon' ),
				'name' => 'extra_css',
				'type' => 'acf_code_field',
				'instructions' => __( 'Add some extra CSS for reseller.', 'enon' ),
				'mode' => 'css',
				'theme' => 'monokai',
			),
			array(
				'key' => 'field_iframe_bw_html',
				'label' => __( 'Iframe Bedarfsausweis HTML', 'enon' ),
				'name' => 'iframe_bw_html',
				'type' => 'acf_code_field',
				'instructions' => __( 'Just copy the code and paste it where the frame have to be shown.', 'enon' ),
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'disabled' => true,
				'default_value' => $this->get_iframe_html( 'bw' ),
				'value' => $this->get_iframe_html( 'bw' ),
				'placeholder' => '',
				'mode' => 'htmlmixed',
				'theme' => 'monokai',
			),
			array(
				'key' => 'field_iframe_vw_html',
				'label' => __( 'Iframe Verbrauchausweis HTML', 'enon' ),
				'name' => 'iframe_vw_html',
				'type' => 'acf_code_field',
				'instructions' => __( 'Just copy the code and paste it where the frame have to be shown.', 'enon' ),
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'disabled' => true,
				'default_value' => $this->get_iframe_html( 'vw' ),
				'value' =>$this->get_iframe_html( 'vw' ),
				'placeholder' => '',
				'mode' => 'htmlmixed',
				'theme' => 'monokai',
			),
		];

		return $data;
	}

	/**
	 * Get iframe html.
	 *
	 * @param string $energy_certificate_type bw or vw.
	 *
	 * @return string Iframe HTML.
	 *
	 * @since 1.0.0
	 */
	private function get_iframe_html( string $energy_certificate_type, int $reseller_id = 0 ) : string {
		if ( empty ( $reseller_id ) ) {
			$reseller_id = (int) filter_input( INPUT_GET, 'post' );
		}

		if ( empty ( $reseller_id ) ) {
			return '';
		}

		$general = new Post_Meta_General( $reseller_id );

		switch ( $energy_certificate_type ) {
			case 'bw':
				$url = home_url( '/energieausweis2/bedarfsausweis-wohngebaeude/?iframe_token=' . $general->get_token() );
				break;
			case 'vw':
				$url = home_url( '/energieausweis2/verbrauchsausweis-wohngebaeude/?iframe_token='  . $general->get_token() );
				break;
			default:
				return '';
		}

		$js_url = home_url( '/scripts/dist/reseller.min.js' );

		$iframe_html = '<iframe class="iframe-energieausweis-online" src="' . $url .'" frameBorder="0" scrolling="no" style="width: 100%; height:500px;"></iframe><script type="text/javascript" src="' . $js_url . '"></script>';

		return $iframe_html;
	}

	/**
	 * Get default extra js.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	private function get_default_extra_js() {
		return '';
	}
}
