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

use Awsm\WP_Wrapper\Tools\Logger_Trait;
use Awsm\WP_Wrapper\Tools\Logger;

/**
 * Class Task_Settings_Page.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class Add_Translations implements Task, Filters {
	use Logger_Trait;

	/**
	 * AffiliateWP constructor.
	 *
	 * @param Logger $logger Logger object.
	 * @since 1.0.0
	 */
	public function __construct( Logger $logger ) {
		$this->logger = $logger;
	}

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
		add_filter( 'gettext', array( $this, 'translate' ), 10, 1 );
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
	public function translate( $translation ) {
		switch ( $translation ) {
			case 'Hast du einen Rabatt-Code?':
				$translation = 'Haben Sie einen Gutschein-Code?';
				break;
			case 'Rabatt':
				$translation = 'Gutschein?';
				break;
			case 'Rabatt eingeben':
				$translation = 'Gutschein eingeben';
				break;
			case 'Dein Warenkorb ist leer':
				$translation = 'Ihr Warenkorb ist leer';
				break;
		}

		return $translation;
	}
}
