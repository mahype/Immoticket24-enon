<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\View;

abstract class TemplateBase {
	public function displaySubTemplate( $template_slug, $template_suffix = '', $data = array() ) {
		$file = $this->locateTemplate( $template_slug, $template_suffix );
		if ( $file ) {
			$this->loadTemplate( $file, $data );
		}
	}

	protected function locateTemplate( $template_slug, $template_suffix = '' ) {
		$template_directories = array(
			TEMPLATEPATH . '/wpenon_templates',
			WPENON_DATA_PATH . '/templates',
			WPENON_PATH . '/templates',
		);

		if ( STYLESHEETPATH != TEMPLATEPATH ) {
			array_unshift( $template_directories, STYLESHEETPATH . '/wpenon_templates' );
		}
		$template_directories = apply_filters( 'wpenon_template_directories', $template_directories );

		if ( is_admin() ) {
			$admin_template_directories = array();
			foreach ( $template_directories as $template_directory ) {
				$admin_template_directories[] = $template_directory . '/admin';
			}
			$template_directories = array_merge( $admin_template_directories, $template_directories );
		}

		$template_names = array( $template_slug . '.php' );
		if ( ! empty( $template_suffix ) ) {
			array_unshift( $template_names, $template_slug . '-' . $template_suffix . '.php' );
		}

		foreach ( $template_names as $template_name ) {
			foreach ( $template_directories as $template_directory ) {
				if ( file_exists( $template_directory . '/' . $template_name ) ) {
					return $template_directory . '/' . $template_name;
				}
			}
		}

		return false;
	}

	protected function loadTemplate( $file, $data = array() ) {
		include $file;
	}
}
