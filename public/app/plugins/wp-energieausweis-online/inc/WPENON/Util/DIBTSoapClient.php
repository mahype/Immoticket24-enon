<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class DIBTSoapClient {
	public function __doRequest( $request, $location, $action, $version, $one_way = null ) {
		$namespace = "http://energieausweis.dibt.de/WebServiceEnergie/DibtEnergieAusweisService";

		$request = preg_replace( '/<ns1:(\w+)/', '<$1 xmlns="' . $namespace . '"', $request, 1 );
		$request = preg_replace( '/<ns1:(\w+)/', '<$1', $request );
		$request = str_replace( array( '/ns1:', 'xmlns:ns1="' . $namespace . '"' ), array( '/', '' ), $request );

		// parent call
		return parent::__doRequest( $request, $location, $action, $version, $one_way );
	}
}
