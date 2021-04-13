<?php

$cookie_params = session_get_cookie_params();
$cookie_params['samesite'] = 'None';
session_set_cookie_params( $cookie_params );

ini_set('session.cookie_samesite', 'None');

// WordPress view bootstrapper
define( 'WP_USE_THEMES', true );

require __DIR__ . '/core/wp-blog-header.php';
