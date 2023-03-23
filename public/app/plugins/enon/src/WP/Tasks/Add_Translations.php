<?php
/**
 * Load translations
 *
 * @category Class
 * @package  Enon\WP\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP\Tasks;

use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use Enon\Logger;

/**
 * Class Task_Settings_Page.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class Add_Translations implements Task, Filters {
	/**
	 * Running scripts.
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
		add_filter( 'gettext', array( $this, 'translate' ), 10, 3 );
	}

	/**
	 * Register enon menu in admin.
	 *
	 * @param string $translation  Translated text.
	 *
	 * @return string Alternative translation.
	 *
	 * @since 1.0.0
	 */
	public function translate( $translation, $text, $domain ) {
		switch ( $translation ) {
			case 'Hast du einen Rabatt-Code?':
				return 'Haben Sie einen Gutschein-Code?';
			case 'Rabatt':
				return 'Gutschein?';
			case 'Rabatt eingeben':
				return 'Gutschein eingeben';
			case 'Dein Warenkorb ist leer':
				return 'Ihr Warenkorb ist leer';
			case 'All %s':
				return 'Alle %s';
			case 'Categories':
				return 'Kategorien';
			default:
				return $translation;
		}
	}
}
