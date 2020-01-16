<?php
/**
 * Configuration.
 *
 * @category Class
 * @package  Enon\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Enon;

/**
 * Class Config.
 *
 * @since 1.0.0
 */
abstract class Config {
	/**
	 * Config.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $config_data = array();

	/**
	 * Standards constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->initiate();
	}

	/**
	 * Initiating config values.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	abstract protected function initiate();

	/**
	 * Get data.
	 *
	 * @since 1.0.0
	 *
	 * @return array Data.
	 */
	public function get() {
		return $this->config_data;
	}

	/**
	 * Get value by key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key to get value for.
	 * @return mixed Config value to get.
	 */
	public function get_value( $key ) {
		if ( ! isset( $this->config_data[ $key ] ) ) {
			return false;
		}

		return $this->config_data[ $key ];
	}

	/**
	 * Get config Keys.
	 *
	 * @since 1.0.0
	 *
	 * @return array Config keys.
	 */
	public function get_keys() {
		return array_keys( $this->config_data );
	}

	/**
	 * Checks if config key exists.
	 *
	 * @since 1.0.0
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function key_exists( $key ) {
		if ( ! isset( $this->config_data[ $key ] ) ) {
			return false;
		}

		return true;
	}
}
