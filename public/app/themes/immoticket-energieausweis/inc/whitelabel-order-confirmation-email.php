<?php

/**
 * Class EA_Whitelabel_Order_Confirmation_Email
 */
class EA_Whitelabel_Order_Confirmation_Email extends EA_Whitelabel_Email {
	/**
	 * Initializing Hooks
	 */
	public function init_hooks() {
		parent::init_hooks();

		add_filter( 'wpenon_order_confirmation_to_address', array( $this, 'set_to_address' ) );
	}

	/**
	 * Returning token from email address.
	 *
	 * @param string $email
	 *
	 * @return string Tokens from email address.
	 */
	public function set_to_address( $email = '' ) {
		$email = $this->whitelabel->get_email();
		return $email;
	}
}
