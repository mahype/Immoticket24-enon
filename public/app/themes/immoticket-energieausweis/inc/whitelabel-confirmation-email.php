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

		add_filter( 'wpenon_confirmation_from_address', array( $this, 'set_from_address' ) );
		add_filter( 'wpenon_confirmation_from_name', array( $this, 'set_from_name' ) );
		add_filter( 'wpenon_confirmation_link', array( $this, 'set_link' ), 10, 2 );
		add_filter( 'wpenon_confirmation_site', array( $this, 'set_site' ), 10, 1 );

		add_filter( 'wpenon_email_legal', array( $this, 'set_legal' ) );
		add_filter( 'wpenon_alternative_email_footer', array( $this, 'set_alternative_footer' ) );
		add_filter( 'wpenon_email_signature', array( $this, 'set_signature' ), 20 );
	}

	/**
	 * Returning token from email address.
	 *
	 * @param string $email
	 *
	 * @return string Tokens from email address.
	 */
	public function set_from_address( $email = '' ) {
		$email_from_address = $this->whitelabel->get_email_from_address();
		return $email_from_address;
	}

	/**
	 * Returning token from email name.
	 *
	 * @param string $email
	 *
	 * @return string Tokens from email name.
	 */
	public function set_from_name( $email = '' ) {
		$from_name = $this->whitelabel->get_email_from_name();
		return $from_name;
	}

	/**
	 * Set site name to signature.
	 *
	 * @param string $link Link to filter.
	 * @param \WPENON\Model\Energieausweis Energieausweis
	 *
	 * @return string Filtered signature.
	 */
	public function set_site( $url ) {
		$sitename = $this->whitelabel->get_sitename();
		return $sitename;
	}

	/**
	 * Set site name to signature.
	 *
	 * @param string $link Link to filter.
	 * @param \WPENON\Model\Energieausweis Energieausweis
	 *
	 * @return string Filtered signature.
	 */
	public function set_link( $link, $energieausweis ) {
		$redirect_url = $this->whitelabel->get_verfied_url( $this->whitelabel->get_customer_edit_url(), $energieausweis->id );

		if( false === $redirect_url ) {
			return $link;
		}

		return $redirect_url;
	}

	/**
	 * Set businessdata
	 *
	 * @param array $businessdata
	 *
	 * @return array $businessdata
	 */
	public function set_alternative_footer( $alternative_footer ) {
		$footer = '<div style="font-size:14px;">';
		$footer.= wpautop( $this->whitelabel->get_email_footer() );
		$footer.= '</div>';
		$footer.= '<small>' . sprintf( __( 'Diese Email wurde automatisch von <a href="%s">%s</a> versendet.', 'wpenon' ), $this->whitelabel->get_customer_edit_url(), $this->whitelabel->get_sitename() ) . '</small>';
		return $footer;
	}

	/**
	 * Set email footer.
	 *
	 * @param string $footer Footer to filter.
	 *
	 * @return string Filtered footer.
	 */
	public function set_legal( $footer = '' ) {
		$legal = sprintf( __( 'Diese Email wurde automatisch von <a href="%s">%s</a> versendet.', 'wpenon' ), $this->whitelabel->get_customer_edit_url(), $this->whitelabel->get_sitename() );
		return $this->whitelabel->get_email_footer($legal);
	}

	/**
	 * Set signature.
	 *
	 * @param string $siganture Signature to filter.
	 *
	 * @return string Filtered footer.
	 */
	public function set_signature( $text = '' ) {
		$signature = sprintf( __( 'Mit freundlichen Grüßen,
		
		Ihr Team von %s.', 'wpenon' ), $this->whitelabel->get_sitename() );
		return $signature;
	}
}
