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
				'name'       => __( 'GEG 2021 (ab 01.05.2020)', 'wpenon' ),
				'date'       => '2020-08-08',
				'start_date' => '2021-05-01',
			),
			'enev2021-02' => array(
				'name'       => __( 'GEG 2021 (ab 13.05.2020)', 'wpenon' ),
				'date'       => '2020-08-08',
				'start_date' => '2021-05-13',
			),
			'enev2021-03' => array(
				'name'       => __( 'GEG 2021 (ab 18.05.2020)', 'wpenon' ),
				'date'       => '2020-08-08',
				'start_date' => '2021-05-17',
			),
		);
	}

	/**
	 * Get standards before given date
	 * 
	 * @param string Date
	 * 
	 * @return array
	 * 
	 * @since 1.0.0
	 */
	public function getStandardsBefore( string $date )
	{
		$standards = [];
		foreach( $this->config_data AS $key => $standard )
		{
			if( strtotime( $standard['start_date'] ) < strtotime( $date ) ) {
				$standards[ $key ] = $standard;
			} 
		}
		
		return $standards;
	}

	/**
	 * Standards path
	 * 
	 * @param string 
	 */
	public function getStandardsPath( string $standardName = null )
	{
		if( empty( $standardName ) ) {
			$standardName = $this->getCurrent();
		}
		return WPENON_DATA_PATH . '/' . $standardName;
	}

	/**
	 * Get Enev XML Template file
	 * 
	 * @param string Energieausweis mode (bw or vw)
	 * @param string XML mode (datenerfassung or zusatzdatenerfassung)
	 * @param string Schema name e.g. enev2021-03
	 * 
	 * @since 1.0.0
	 */
	public function getEnevXMLTemplatefile( string $mode, string $xmlMode, $schemaName = null )
	{
		if( empty( $schemaName ) ) {
			$schemaName = $this->getCurrent();
		}


		if( $xmlMode == 'datenerfassung')
		{
			$XMLTemplateFilename = ucfirst( $xmlMode ) . '.php';
		} else {
			$XMLTemplateFilename = ucfirst( $xmlMode ) . ucwords( $mode ) . 'W.php';
		}
		$XMLTemplateFile     = $this->getStandardsPath( $schemaName ) . '/datenerfassung/templates/' . $XMLTemplateFilename;

		return $XMLTemplateFile;
	}

	/**
	 * Get standard key by time.
	 *
	 * @since 1.0.0
	 *
	 * @param int $timestamp Timestamp
	 * @return string Standard key
	 */
	public function getByTime( $timestamp ) {
		foreach ( $this->config_data as $key => $standard ) {
			if ( strtotime( $standard['start_date'] ) > $timestamp ) {
				break;
			}

			$standardName = $key;
		}

		return $standardName;
	}

	/**
	 * Get current standard.
	 *
	 * @since 1.0.0
	 *
	 * @return string Standard name.
	 */
	public function getCurrent() {
		return $this->getByTime( time() );
	}
}
