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

abstract class Config {
	/**
	 * Config.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $configData = array();

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
		return $this->configData;
	}

	/**
	 * Get value by key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key to get value for.
	 * @return mixed Config value to get.
	 */
	public function getValue( $key ) {
		if ( ! isset( $this->configData[ $key ] ) ) {
			return false;
		}

		return $this->configData[ $key ];
	}

	/**
	 * Get config Keys.
	 *
	 * @since 1.0.0
	 *
	 * @return array Config keys.
	 */
	public function getKeys() {
		return array_keys( $this->configData );
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
	public function keyExists( $key ) {
		if ( ! isset( $this->configData[ $key ] ) ) {
			return false;
		}

		return true;
	}
}
