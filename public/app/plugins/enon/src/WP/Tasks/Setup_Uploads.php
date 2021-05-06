<?php
/**
 * Setting up uploads
 *
 * @category Class
 * @package  Enon\Config\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP\Tasks;

use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Setup_Gutenberg.
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Setup_Uploads implements Filters, Task {
	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->add_filters();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'wp_check_filetype_and_ext', [ __CLASS__, 'wpse_file_and_ext_webp' ], 10, 4 );
		add_filter( 'upload_mimes', [ __CLASS__, 'wpse_mime_types_webp' ] );
	}

	/**
     * Sets the extension and mime type for .webp files.
     *
     * @param array  $wp_check_filetype_and_ext File data array containing 'ext', 'type', and
     *                                          'proper_filename' keys.
     * @param string $file                      Full path to the file.
     * @param string $filename                  The name of the file (may differ from $file due to
     *                                          $file being in a tmp directory).
     * @param array  $mimes                     Key is the file extension with value as the mime type.
     */
    public function wpse_file_and_ext_webp( $types, $file, $filename, $mimes ) {
        if ( false !== strpos( $filename, '.webp' ) ) {
            $types['ext'] = 'webp';
            $types['type'] = 'image/webp';
        }

        return $types;
    }

    /**
     * Adds webp filetype to allowed mimes
     * 
     * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/upload_mimes
     * 
     * @param array $mimes Mime types keyed by the file extension regex corresponding to
     *                     those types. 'swf' and 'exe' removed from full list. 'htm|html' also
     *                     removed depending on '$user' capabilities.
     *
     * @return array
     */
    public function wpse_mime_types_webp( $mimes ) {
        $mimes['webp'] = 'image/webp';
        return $mimes;
    }
}
