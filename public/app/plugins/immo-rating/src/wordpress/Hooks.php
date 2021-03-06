<?php

namespace AWSM\ImmoRating\WordPress;

use AWSM\LibWP\WP\Hooks\Action;
use AWSM\ImmoRating\ImmoRating;


ImmoRating::instance()->hooks()
    ->add( new Action( 'init', [ $variables['component'], 'addShortcode' ] ) )
    ->add( new Action( 'rest_api_init', [ $variables['component'], 'registerRestRoute' ] ) );