<?php 

/**
 * Plugin Name: Immo Rating
 */
namespace AWSM\ImmoRating;

require_once __DIR__ . '/vendor/autoload.php';

use AWSM\ImmoRating\WordPress\Api;
use AWSM\LibWP\WP\Core\Plugin;

class ImmoRating extends Plugin {
    protected $components = [
        Api::class
    ];
}

ImmoRating::instance();