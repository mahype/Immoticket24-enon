<?php
/**
 * Plugin Name: Edd payment gateway stripe SEPA
 * Plugin URI: http://www.awesome.ug
 * Description: Edd payment gateway for SEPA payments.
 * Version: 1.0.0-beta.1
 * Author: Sven Wagener, Awesome UG
 * Author URI: http://www.awesome.ug
 * Author Email: very@awesome.ug
 * License: GPL2
 */

require dirname( __FILE__ ) .'/vendor/autoload.php';

if ( class_exists( 'Easy_Digital_Downloads' ) ) {
	(new \Awsm\WP_Wrapper\Plugin\Plugin() )
	->add_translation( 'awsm-edd-stripe-sepa', dirname(__DIR__) . '/languages' )
	->add_task(\Awsm\Edd\Payment\Stripe_Sepa\Payment_Gateway::class )
	->boot();
}





