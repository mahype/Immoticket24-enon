<?php
namespace WPENON\ServiceWorker;

use WPENON\ServiceWorker\Interfaces;

class Services implements Interfaces\Service{

	private $serviceName = '';

	public function __construct() {
		#new ImagesMapping\Service();
		new DeactivateOptimizePress\Service();
	}

	public function getParseServiceArguments(array $arguments):array {
		return $this->parseServiceArguments($arguments);
	}

	public function setServiceName(string $name){
		$this->serviceName = $name;
	}

	public function getServiceName(){
		return $this->serviceName;
	}


	/**
	 * Filter a array for arguments by serviceName
	 *
	 * @param array $arguments
	 *
	 * @return array
	 */
	private function parseServiceArguments(array $arguments):array {
		$parsedArguments = [];

		if(empty($arguments)){
			return $parsedArguments;
		}

		foreach(array_keys($arguments) as $argument){
			$is_validArgument = strpos($argument, $this->getServiceName());

			if($is_validArgument === false){
				continue;
			}

			$argumentNewKey = str_replace($this->getServiceName() . '_', '', $argument);
			$parsedArguments[$argumentNewKey] = $arguments[$argument];
		};

		return $parsedArguments;
	}

}

