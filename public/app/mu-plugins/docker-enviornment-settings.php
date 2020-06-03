<?php
/*
Plugin Name: Docker Enviornment Settings
Plugin URI: https://energieausweis-online-erstellen.de
Description: Tut dinge für die lokale Docker-Umgebung
Version: 1.0.0
Author:
Author URI:
*/

/** add admin user for dev environment */
if(!empty($_ENV['VIRTUAL_HOST']) && $_ENV['VIRTUAL_HOST'] === 'enon.test') {

	add_action('init', function () {
		$user  = 'Dirk';
		$pass  = 'Diggler';
		$email = 'dirk@diggler.com';
		//if a username with the email ID does not exist, create a new user account
		if (!username_exists($user) && !email_exists($email)) {
			$user_id = wp_create_user($user, $pass, $email);
			$user    = new WP_User($user_id);
			//Set the new user as a Admin
			$user->set_role('administrator');
		}
	} );

	add_action( 'phpmailer_init', function ( PHPMailer $phpmailer ){
			$phpmailer->Host = 'mailhog';
			$phpmailer->Port = 1025;
			$phpmailer->IsSMTP();
	} );

}
