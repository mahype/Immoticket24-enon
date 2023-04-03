<?php
// Before removing this file, please verify the PHP ini setting `auto_prepend_file` does not point to this.

if (file_exists(__DIR__.'/public/app/plugins/wordfence/waf/bootstrap.php')) {
	define("WFWAF_LOG_PATH", __DIR__.'/public/app/wflogs/');
	include_once __DIR__.'/public/app/plugins/wordfence/waf/bootstrap.php';		
}