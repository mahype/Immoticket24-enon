<?php

/**
 * Class EA_Whitelabel_Confirmation_Email
 */
class EA_Whitelabel_Confirmation_Email extends EA_Whitelabel_Email {
	/**
	 * Initializing Hooks
	 */
	public function init_hooks() {
		parent::init_hooks();

		add_filter( 'wpenon_order_confirmation_from_address', array($this, 'set_from_address' ) );
		add_filter( 'wpenon_order_confirmation_from_name', array($this, 'set_from_name' ) );

		add_filter( 'wpenon_email_signature', array( $this, 'set_signature' ) );
		add_filter( 'wpenon_email_footer', array( $this, 'set_footer' ) );
	}

	/**
	 * Returning token from email address.
	 *
	 * @param string $email
	 *
	 * @return string Tokens from email address.
	 */
	public function set_from_address( $email = '' ) {
		return $this->whitelabel->get_email_from_address();
	}

	/**
	 * Returning token from email name.
	 *
	 * @param string $email
	 *
	 * @return string Tokens from email name.
	 */
	public function set_from_name( $email = '' ) {
		return $this->whitelabel->get_email_from_name();
	}

	/**
	 * Set site name to signature.
	 *
	 * @param string $siganture Siganture to set.
	 *
	 * @return string Filtered signnature.
	 */
	public function set_signature( $siganture = '' ) {
		return $this->whitelabel->get_sitename();
	}

	/**
	 * Set email footer.
	 *
	 * @param string $siganture Footer to set.
	 *
	 * @return string Filtered footer.
	 */
	public function set_footer( $footer = '' ) {
		return $this->whitelabel->get_email_footer();
	}
}
