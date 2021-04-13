<?php

$cookie_params = session_get_cookie_params();
$cookie_params['samesite'] = 'None';
$cookie_params['secure'] = true;
session_set_cookie_params( $cookie_params );

// WordPress view bootstrapper
define( 'WP_USE_THEMES', true );

require __DIR__ . '/core/wp-blog-header.php';
