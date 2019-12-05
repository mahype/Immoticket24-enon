<?php

namespace Enon\Enon;

/**
 * Class Standard
 *
 * @since 1.0.0
 *
 * @package Enon\Enon
 */
class Standard {
	/**
	 * Slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $slug;

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
	 * @param string $slug         Slug of standard.
	 * @param Standards $standards Standards object.
	 */
	public function __construct( $slug, Standards $standards )
	{
		$standardValues = $standards->getStandardValues( $slug );

		$this->slug  = $slug;
		$this->name  = $standardValues['name'];
		$this->date  = $standardValues['date'];
		$this->start = $standardValues['start'];
	}

	/**
	 * Get slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * Get name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get date.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function getDate()
	{
		return $this->date;
	}

	/**
	 * Get start date.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}


	/**
	 * Get schema file.
	 *
	 * @since 1.0.0
	 *
	 * @return string The location of the schema file.
	 */
	public function getSchemaFile()
	{

	}
}
