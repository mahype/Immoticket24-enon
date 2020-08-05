<?php
/**
 * Mediathek Thumbnail Validator
 *
 * @category Class
 * @package  Enon\Edd\Tasks
 * @author   Rene Reimann
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use Awsm\WP_Wrapper\Tools\Logger;

/**
 * Class Mediathek_Thumbnail_Validator.
 *
 * @since 1.0.0
 *
 * @package Enon\WordPress
 */
class Mediathek_Thumbnail_Validator implements Task, Filters {
	/**
	 * Loading Plugin scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param Logger $logger Logger object.
	 */
	public function __construct() {}

	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_filters();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'wp_prepare_attachment_for_js', [ $this, 'validate_attachemnt_url' ], 1, 3 );
	}

	/**
	 * @hook $attachment
	 * @param array $attachment
	 */
	public function validate_attachemnt_url( array $attachment ) {
		if ( empty( $attachment ) ) {
			return;
		}

		$url_parts  = pathinfo( $attachment['url'] );
		$upload_dir = wp_upload_dir();

		$attachment_basepath = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $url_parts['dirname'] );
		$attachment_path = $attachment_basepath . '/' . $url_parts['basename'];

		if ( ! file_exists( $attachment_path ) ) {
			$glob_path = $attachment_basepath . '/' . $url_parts['filename'] . '*.*';

			foreach ( glob( $glob_path ) as $matched_attachment_url ) {
				$attachment['sizes']['full']['url'] = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $matched_attachment_url );
				$attachment['url'] = $attachment['sizes']['full']['url'];
				continue;
			}
		}

		return $attachment;
	}
}
