<?php
/**
 * @version 1.0.2
 *
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class DIBTSoapRequest
{
    protected $api_url = '';

    protected $action = '';
    protected $args = array();

    protected $response = null;

    public function __construct($action, $args = array())
    {
        $slug = 'published';
        if (WPENON_DEBUG) {
            $slug = 'sandbox';
        }
        $this->api_url = 'https://energieausweis.dibt.de/'.$slug.'/WebServiceEnergieausweis/DibtEnergieAusweisServiceEx03.wsdl';

        $actions = $this->getValidActions();
        if (isset($actions[$action])) {
            $this->action = $action;
        } else {
            new \WPENON\Util\Error('fatal', __METHOD__, sprintf(__('Die Aktion %s ist keine gültige DIBT-API-Funktion.', 'wpenon'), $action), '1.0.0');
        }

        foreach ($actions[$this->action] as $key => $default) {
            $this->args[$key] = isset($args[$key]) ? $args[$key] : $default;
        }
    }

    public function send()
    {
        $response = null;

        $function = $this->action;
        $response_type = $this->action.'Result';

        $xml = '<'.$function.' xmlns="http://energieausweis.dibt.de/WebServiceEnergie/DibtEnergieAusweisService">';
        foreach ($this->args as $key => $value) {
            $xml .= '<'.$key.'>'.$value.'</'.$key.'>';
        }
        $xml .= '</'.$function.'>';

        $context = stream_context_create(
            array( 
                "ssl" => array (     
                    "verify_peer"=> false,     
                    "verify_peer_name"=> false, 
                ),
                'http' => array(     
                    'timeout' => 30     
                )
            )       
        );

        try {
            $soap = new \SoapClient($this->api_url.'?WSDL', [ 'stream_context' => $context ] );

            $request_body = new \SoapVar($xml, XSD_ANYXML);
            $response = $soap->$function($request_body);
	        $this->response = $response;
	        self::log( sprintf( 'Response: %s', var_export( $response, true ) ) );
        } catch (\SoapFault $exception) {
	        self::log( sprintf( 'Response: %s', var_export( $response, true ) ) );
	        self::log( sprintf( 'Response: %s', $exception->getMessage() ) );
	        self::log( sprintf( 'Exception: %s', var_export( $exception, true ) ) );
            new \WPENON\Util\Error('notice', __METHOD__, sprintf(__('DIBT Soap Fehler: %s', 'wpenon'), $exception->getMessage()), '1.0.0');
        }

        // $this->write_to_file( time() . '_dibt.xml', $xml );

	    $this->response = $response;

        if (!is_soap_fault($response)) {
            if (isset($response->$response_type)) {
                $response = $response->$response_type;
            }
            if (isset($response->any)) {
                $response = $response->any;
            }
            $this->response = $response;
        }

	    return  $response;
    }

    public function write_to_file($name, $data)
    {
        $file = fopen(dirname(ABSPATH).'/'.$name, 'w');
        fputs($file, $data);
        fclose($file);
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getArgs()
    {
        return $this->args;
    }

    public function getResponse()
    {
        return $this->response;
    }

    protected function getValidActions()
    {
        $actions = array(
            'Datenregistratur' => array(
                'doc' => '',
            ),
            'ZusatzdatenErfassung' => array(
                'doc' => '',
                'ausweisnummer' => '',
                'username' => '',
                'passwort' => '',
            ),
            'Restkontingent' => array(
                'Username' => '',
                'Passwort' => '',
            ),
        );

        return $actions;
    }

	public static function log( $message, $backtrace = false ) {
		if( $backtrace ) {
			ob_start();
			debug_print_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
			$trace = ob_get_contents();
			ob_end_clean();

			$message.= chr(13 ) . $trace;
		}

		$url = $_SERVER['REQUEST_URI'];
		$time = date('Y-m-d H:i:s' );
		$microtime = microtime();

		$line = $time . ' - ' . $microtime .  ' - ' . $url . chr(13) . $message . chr(13 );

		$file = fopen( WP_LOG_DIR . '/dibt.log', 'a' );
		fputs( $file, $line  );
		fclose( $file );
	}
}
