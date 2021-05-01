<?php
/**
 * Standards config.
 *
 * @category Class
 * @package  Enon\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Enon;

use Enon\Enon\Standards\Schema;

/**
 * Class for managing Standards.
 *
 * @package Enon\Enon
 *
 * @todo Renaming standards?
 */
class Standards_Config extends Config {

	/**
	 * Initiating standards.
	 *
	 * @since 1.0.0
	 *
	 * @todo Loading dynamically.
	 */
	protected function initiate() {
		$this->config_data = array(
			'enev2013' => array(
				'name'       => __( 'EnEV 2013', 'wpenon' ),
				'date'       => '2013-11-18',
				'start_date' => '2014-05-01',
			),
			'enev2017' => array(
				'name'       => __( 'EnEV 2013 (ab 1.7.2017)', 'wpenon' ),
				'date'       => '2013-11-18',
				'start_date' => '2017-07-01',
			),
			'enev2019' => array(
				'name'       => __( 'EnEV 2013 (ab 11.12.2019)', 'wpenon' ),
				'date'       => '2013-11-18',
				'start_date' => '2019-12-11',
			),
			'enev2020-01' => array(
				'name'       => __( 'EnEV 2013 (ab 28.02.2020)', 'wpenon' ),
				'date'       => '2013-11-18',
				'start_date' => '2020-03-11',
			),
			'enev2020-02' => array(
				'name'       => __( 'EnEV 2013 (ab 28.05.2020)', 'wpenon' ),
				'date'       => '2013-11-18',
				'start_date' => '2020-05-28',
			),
			'enev2021-01' => array(
				'name'       => __( 'GEG 2021 (ab 08.08.2020)', 'wpenon' ),
				'date'       => '2020-08-08',
				'start_date' => '2021-04-30',
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
		foreach ( $this->config_data as $key => $standard ) {
			if ( strtotime( $standard['start_date'] ) > $timestamp ) {
				break;
			}

			$found_standard = $key;
		}

		return $found_standard;
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
