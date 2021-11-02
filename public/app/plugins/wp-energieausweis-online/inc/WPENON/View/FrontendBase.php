<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\View;

class FrontendBase extends TemplateBase {
	protected $template_slug = '';
	protected $template_suffix = '';
	protected $data = array();

	public function __construct( $data = array() ) {
		if ( isset( $data['template'] ) && ! empty( $data['template'] ) ) {
			$this->template_slug = $data['template'];
		} else {
			new \WPENON\Util\Error( 'fatal', __METHOD__, __( 'Es wurde kein Template-Feld im Datenfeld angegeben.', 'wpenon' ), '1.0.0' );
		}

		if ( isset( $data['template_suffix'] ) && ! empty( $data['template_suffix'] ) ) {
			$this->template_suffix = $data['template_suffix'];
		}

		$this->data = $data;

		do_action( 'wpenon_frontend_view_init', $this->template_slug, $this->template_suffix, $this->data );
	}

	public function displayTemplate() {
		$file = $this->locateTemplate( $this->template_slug, $this->template_suffix );
		if ( $file ) {
			echo '<div class="wp-block-group wave2 has-background" style="background:linear-gradient(135deg,#cff023 0%,#65e58d 100%)"><div class="wp-block-group__inner-container"><div class="wpenon-wrapper">';
			$this->loadTemplate( $file, $this->data );
			echo '</div></div></div>';
		}
	}

	/**
	 * Returns Template Slug.
	 *
	 * @return mixed|string $template_slug Template slug
	 */
	public function get_template_slug() {
		return $this->template_slug;
	}

	/**
	 * Returns Template suffix.
	 *
	 * @return mixed|string $template_slug Template slug
	 */
	public function get_template_suffix() {
		return $this->template_suffix;
	}
}
