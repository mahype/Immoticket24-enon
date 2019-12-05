<?php

namespace Enon\Enon;

use Enon\Models\Exceptions\Exception;

/**
 * Class for managing Standards.
 *
 * @package Enon\Enon
 *
 * @todo Renaming standards?
 */
class Standards {

	/**
	 * Standards.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $standards = array();

	/**
	 * Standards constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct()
	{
		$this->initiateStandards();
	}

	/**
	 * Initiating standards.
	 *
	 * @since 1.0.0
	 */
	private function initiateStandards() {
		$this->standards = array(
			'enev2013'    => array(
				'name'  => __( 'EnEV 2013', 'wpenon' ),
				'date' => '2013-11-18',
				'start'   => '2014-05-01'
			),
			'enev2017'    => array(
				'name'  => __( 'EnEV 2013 (ab 1.7.2017)', 'wpenon' ),
				'date' => '2013-11-18',
				'start'   => '2017-07-01',
			),
			'enev2019'    => array(
				'name'  => __( 'EnEV 2019 (ab 10.12.2019)', 'wpenon' ),
				'date' => '2013-11-18',
				'start'   => '2019-12-10'
			),
		);
	}

	public function getStandardValues( $slug )
	{
		return $this->standards[ $slug ];
	}

	/**
	 * Getting standard of a specific time.
	 *
	 * @since 1.0.0
	 *
	 * @param int $timestamp Timestamp.
	 *
	 * @return Standard $standard Standard key.
	 *
	 * @todo Searching for standard regardless of array element order.
	 */
	public function getStandardByTime( $timestamp )
	{
		foreach( $this->standards AS $key => $standard ) {
			if( strtotime( $standard['start'] ) > $timestamp ) {
				break;
			}

			$foundStandard = new Standard( $key, $this );
		}

		return $foundStandard;
	}

	/**
	 * Get current standard.
	 *
	 * @since 1.0.0
	 *
	 * @return Standard
	 */
	public function getCurrentStandard() {
		return $this->getStandardByTime( time() );
	}
}
