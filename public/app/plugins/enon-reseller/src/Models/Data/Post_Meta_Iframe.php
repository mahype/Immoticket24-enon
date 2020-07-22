<?php
/**
 * Class for getting resellers post meta iframe data.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Data
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Data;

use Enon\WP\Models\Post_Meta;

/**
 * Class Post_Meta_Iframe.
 *
 * @since 1.0.0
 */
class Post_Meta_Iframe extends Post_Meta {
	/**
	 * Checks if title element is checked.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if is checked..
	 */
	public function isset_element_title() {
		$elements = $this->get( 'elements' );

		if ( ! empty( $elements ) && in_array( 'show_title', $elements ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if header element is checked.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if is checked.
	 */
	public function isset_element_description() {
		$elements = $this->get( 'elements' );

		if ( ! empty( $elements ) && in_array( 'show_description', $elements ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if newsletter terms element is checked.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if is checked.
	 */
	public function isset_element_newsletter_terms() {
		$elements = $this->get( 'elements' );

		if ( ! empty( $elements ) && in_array( 'show_newsletter_terms', $elements ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get reseller extra CSS.
	 *
	 * @since 1.0.0
	 *
	 * @return string Reseller extra CSS.
	 */
	public function get_extra_css() {
		return $this->get( 'extra_css' );
	}

	/**
	 * Get reseller extra JS.
	 *
	 * @since 1.0.0
	 *
	 * @return string Reseller extra JS.
	 */
	public function get_extra_js() {
		return $this->get( 'extra_js' );
	}

	/**
	 * Get reseller bw iframe html.
	 *
	 * @since 1.0.0
	 *
	 * @return string Reseller bw iframe html.
	 */
	public function get_iframe_bw_html() {
		return $this->get( 'iframe_bw_html' );
	}

	/**
	 * Get reseller vw iframe html.
	 *
	 * @since 1.0.0
	 *
	 * @return string Reseller vw iframe html.
	 */
	public function get_iframe_vw_html() {
		return $this->get( 'iframe_vw_html' );
	}
}
