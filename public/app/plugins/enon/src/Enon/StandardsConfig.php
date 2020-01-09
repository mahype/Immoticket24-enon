<?php

namespace Enon\Enon;

use Enon\Enon\Standards\Schema;

/**
 * Class for managing Standards.
 *
 * @package Enon\Enon
 *
 * @todo Renaming standards?
 */
class StandardsConfig extends Config {

	/**
	 * Initiating standards.
	 *
	 * @since 1.0.0
	 *
	 * @todo Loading dynamically.
	 */
	protected function initiate() {
		 $this->configData = array(
			 'enev2013' => array(
				 'name' => __( 'EnEV 2013', 'wpenon' ),
				 'date' => '2013-11-18',
				 'startDate' => '2014-05-01',
			 ),
			 'enev2017' => array(
				 'name' => __( 'EnEV 2013 (ab 1.7.2017)', 'wpenon' ),
				 'date' => '2013-11-18',
				 'startDate' => '2017-07-01',
			 ),
			 'enev2019' => array(
				 'name' => __( 'EnEV 2013 (ab 11.12.2019)', 'wpenon' ),
				 'date' => '2013-11-18',
				 'startDate' => '2019-12-11',
			 ),
		 );
	}

	/**
	 * Get standard key by time.
	 *
	 * @since 1.0.0
	 *
	 * @param int $timestamp Timestamp.
	 * @return string Standard key.
	 */
	public function getByTime( $timestamp ) {
		foreach ( $this->configData as $key => $standard ) {
			if ( strtotime( $standard['startDate'] ) > $timestamp ) {
				break;
			}

			$foundStandard = $key;
		}

		return $foundStandard;
	}

	/**
	 * Get current standard.
	 *
	 * @since 1.0.0
	 *
	 * @return string Standard key.
	 */
	public function getCurrent() {
		return $this->getByTime( time() );
	}
}
