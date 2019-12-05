<?php

namespace Enon\Enon;

/**
 * Class Standard
 *
 * @since 1.0.0
 *
 * @package Enon\Enon
 */
class Standard
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
	 * @param string $key Key of standard.
	 * @param Standards $standards Standards object.
	 * @since 1.0.0
	 *
	 */
	public function __construct( $key, Standards $standards )
	{
		$standardValues = $standards->getStandardValues( $key );

		$this->key = $key;
		$this->name = $standardValues[ 'name' ];
		$this->date = $standardValues[ 'date' ];
		$this->startDate = $standardValues[ 'startDate' ];
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
	 * Get schema file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Type of Energieausweis (vw/bw)
	 *
	 * @return string The location of the schema file.
	 */
	public function getSchemaFile( $type )
	{
		$schema_file = $this->getStandardPath() . '/schema/' . $type . '.php';

		return  apply_filters( 'wpenon_schema_file', $schema_file, $this->getKey(), $type );
	}

	/**
	 * Get standard path.
	 *
	 * @since 1.0.0
	 *
	 * @return string Path of standard.
	 *
	 * @todo Get rid of statics
	 */
	public function getStandardPath()
	{
		return  WPENON_DATA_PATH . '/' . $this->getKey();
	}
}
