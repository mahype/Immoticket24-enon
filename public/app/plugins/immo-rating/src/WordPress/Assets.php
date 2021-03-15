<?php 

namespace AWSM\ImmoRating\WordPress;

use AWSM\ImmoRating\ImmoRating;

use AWSM\LibWP\WP\Assets\CSS;
use AWSM\LibWP\WP\Assets\JS;
use AWSM\LibWP\WP\Core\LocationCallbacks;

$assetsDir = ImmoRating::instance()->info()->getPath() . '/src/assets/';

ImmoRating::instance()->assets()
    ->add( new JS( $assetsDir . 'bundle.js' ), LocationCallbacks::frontend() )
    ->add( new CSS( $assetsDir . 'bundle.css' ), LocationCallbacks::frontend() );