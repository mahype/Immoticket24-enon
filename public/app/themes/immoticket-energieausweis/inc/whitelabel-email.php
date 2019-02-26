<?php

abstract class EA_Whitelabel_Email {

	/**
	 * Whitelabel object
	 *
	 * @var EA_Whitelabel
	 */
	protected $whitelabel;

	/**
	 * EA_Whitelabel_Email constructor.
	 *
	 * @param EA_Whitelabel $whitelabel Whitelabel object.
	 */
	public function __construct( $whitelabel ) {
		$this->whitelabel = $whitelabel;

		$this->init_hooks();
	}

	/**
	 * Initializinng hooks.
	 */
	public function init_hooks() {

	}
}
