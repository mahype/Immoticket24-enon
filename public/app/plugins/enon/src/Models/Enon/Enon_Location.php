<?php
/**
 * Determining class location.
 *
 * @category Class
 * @package  Enon\Models\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Enon;

use Awsm\WP_Wrapper\Tools\WP_Location;

/**
 * Class Enon_Location.
 *
 * @package Enon\Models\Enon
 *
 * @since 1.0.0
 */
class Enon_Location extends WP_Location {
	/**
	 * Registration page ID of cerfificate based on need.
	 *
	 * @var int
	 *
	 * @since 1.0.0
	 */
	private static $need_registration_page_id = 9;

	/**
	 * Registration page ID of cerfificate base on consumption.
	 *
	 * @var int
	 *
	 * @since 1.0.0
	 */
	private static $consumption_registration_page_id = 11;

	/**
	 * Registration page ID.
	 *
	 * @var int
	 *
	 * @since 1.0.0
	 */
	private static $overview_page_id = 2225;

	/**
	 * Funnel pages.
	 *
	 * @return bool True if is a funnel page, false if not.
	 *
	 * @since 1.0.0
	 */
	public static function ec_funnel() {
		if ( 
			self::ec_registration() ||
			self::ec_overview()     ||
			self::ec_edit()         ||
			self::ec_editoverview() ||
			self::cart()            ||
			self::success()
		) {
			return true;
		}

		return false;
	}

	/**
	 * Funnel pages after ec started.
	 *
	 * @return bool True if is a funnel page after ec creation started, false if not.
	 *
	 * @since 1.0.0
	 */
	public static function ec_funnel_started() {
		if (
			self::ec_overview()     ||
			self::ec_edit()         ||
			self::ec_editoverview() ||
			self::cart() 			||
			self::success()
		) {
			return true;
		}

		return false;
	}

	/**
	 * Registration page.
	 *
	 * @return bool True if is registration page, false if not.
	 *
	 * @since 1.0.0
	 */
	public static function ec_registration() {
		if ( self::page() && ( self::page_id( self::$need_registration_page_id ) || self::page_id( self::$consumption_registration_page_id ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Editing overview page.
	 *
	 * @return bool True if is registration page, false if not.
	 *
	 * @since 1.0.0
	 */
	public static function ec_overview() {
		$action = wpenon_get_controller()->get_action();
		if ( self::frontend() &&  wpenon_get_controller()->get_action() === 'overview' ) {
			return true;
		}

		return false;
	}

	/**
	 * Edit page.
	 *
	 * @return bool True if is edit page, false if not.
	 *
	 * @since 1.0.0
	 */
	public static function ec_edit() {
		if ( self::frontend() && wpenon_get_controller()->get_action() === 'edit' ) {
			return true;
		}

		return false;
	}

	/**
	 * Edit page.
	 *
	 * @return bool True if is edit page, false if not.
	 *
	 * @since 1.0.0
	 */
	public static function ec_editoverview() {
		if ( self::frontend() && wpenon_get_controller()->get_action() === 'editoverview' ) {
			return true;
		}

		return false;
	}

	/**
	 * Purchase page.
	 *
	 * @return bool True if is edit page, false if not.
	 *
	 * @since 1.0.0
	 */
	public static function ec_purchase() {
		if ( self::frontend() && wpenon_get_controller()->get_action() === 'purchase' ) {
			return true;
		}

		return false;
	}

	/**
	 * Cart page.
	 *
	 * @return bool True if is cart page, false if not.
	 *
	 * @since 1.0.0
	 */
	public static function cart() {
		if ( edd_is_checkout() ) {
			return true;
		}

		return false;
	}

	/**
	 * Success page.
	 *
	 * @return bool True if is success page, false if not.
	 *
	 * @since 1.0.0
	 */
	public static function success() {
		if ( edd_is_success_page() ) {
			return true;
		}

		return false;
	}

	/**
	 * Failed page.
	 *
	 * @return bool True if is failed page, false if not.
	 *
	 * @since 1.0.0
	 */
	public static function failed() {
		if ( edd_is_failed_transaction_page() ) {
			return true;
		}

		return false;
	}
}
