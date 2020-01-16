<?php
/**
 * Parent standard class.
 *
 * @category Class
 * @package  Enon\Enon\Standards
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Enon\Standards;

use Enon\Enon\Standards_Config;
use Enon\Models\Exceptions\Exception;

/**
 * Class Standard
 *
 * @since 1.0.0
 *
 * @package Enon\Enon
 */
abstract class Standard {

	/**
	 * Key.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $key;

	/**
	 * Name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Date.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $date;

	/**
	 * Start date.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $start_date;

	/**
	 * Standard constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key of standard.
	 *
	 * @throws Exception If config could not be loaded.
	 */
	public function __construct( $key ) {
		$standards_config = new Standards_Config();

		if ( ! $standards_config->keyExists( $key ) ) {
			throw new Exception( sprintf( 'Key \'%s\' does not exist on initiating \'%s\' class.', $key, get_called_class() ) );
		}

		$standards_value = $standards_config->getValue( $key );

		$this->key        = $key;
		$this->name       = $standards_value['name'];
		$this->date       = $standards_value['date'];
		$this->start_date = $standards_value['start_date'];
	}

	/**
	 * Get key.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * Get name.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Get date.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function getDate( $format = 'Y-m-D' ) {
		return date( $format, strtotime( $this->date ) );
	}

	/**
	 * Get start date.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function getstart_date( $format = 'Y-m-D' ) {
		return date( $format, strtotime( $this->start_date ) );
	}

	/**
	 * Get standard path.
	 *
	 * @return string Path of standard.
	 *
	 * @since 1.0.0
	 *
	 * @todo Get rid of statics
	 */
	public function getPath() {
		return WPENON_DATA_PATH . '/' . $this->getKey();
	}

	/**
	 * Get config file.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $param Parameters for loading file
	 * @return mixed
	 */
	abstract public function get_file( $param );

	/**
	 * Load config file.
	 *
	 * @param mixed $params    Parameters for loading file.
	 * @param array $variables Variables which will be loaded before requiring script.
	 * @return mixed Content of all file.
	 *
	 * @throws Exception File could not be loaded.
	 *
	 * @todo Rewriting included scripts!
	 */
	public function load( $params, $variables = array() ) {
		extract( $variables );

		$file = $this->get_file( $params );

		if ( ! file_exists( $file ) ) {
			throw new Exception( sprintf( 'Can not load file \'%s\'', $file ) );
		}

		return require $file;
	}
}
