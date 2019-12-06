<?php

namespace Enon\Enon\Standards;

use Enon\Enon\StandardsConfig;
use Enon\Models\Exceptions\Exception;

/**
 * Class Standard
 *
 * @since 1.0.0
 *
 * @package Enon\Enon
 */
abstract class Standard
{
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
	private $startDate;

	/**
	 * Standard constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Key of standard.
	 *
	 * @throws Exception If config could not be loaded.
	 */
	public function __construct( $key )
	{
		$standardsConfig = new StandardsConfig();

		if( ! $standardsConfig->keyExists( $key ) ) {
			throw new Exception( sprintf( 'Key \'%s\' does not exist on initiating \'%s\' class.', $key, get_called_class() ) );
		}

		$standardValue = $standardsConfig->getValue( $key );

		$this->key = $key;
		$this->name = $standardValue[ 'name' ];
		$this->date = $standardValue[ 'date' ];
		$this->startDate = $standardValue[ 'startDate' ];
	}

	/**
	 * Get key.
	 *
	 * @return string
	 * @since 1.0.0
	 *
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Get name.
	 *
	 * @return string
	 * @since 1.0.0
	 *
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get date.
	 *
	 * @return string
	 * @since 1.0.0
	 *
	 */
	public function getDate( $format = 'Y-m-D' )
	{
		return date( $format, strtotime( $this->date ) );
	}

	/**
	 * Get start date.
	 *
	 * @return string
	 * @since 1.0.0
	 *
	 */
	public function getStartDate( $format = 'Y-m-D' )
	{
		return date( $format, strtotime( $this->startDate ) );
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
	public function getStandardPath()
	{
		return WPENON_DATA_PATH . '/' . $this->getKey();
	}

	/**
	 * Get config file.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	abstract function getFile( $type );

	/**
	 * Load config file.
	 *
	 * @param string $type Type (vw/bw).
	 * @return mixed Content of all file.
	 *
	 * @throws Exception File could not be loaded.
	 */
	public function load( $type )
	{
		$file = $this->getFile( $type );

		if ( ! file_exists( $file ) ) {
			throw new Exception( sprintf( 'Can not load file \'%s\'', $file ) );
		}

		return require $file;
	}
}
