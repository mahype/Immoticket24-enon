<?php

/**
 * Plugin initialization file
 *
 * @package ENON
 * @since 1.0.0
 *
 * @wordpress-plugin
 * Plugin Name: ENON - Core Plugin (neu)
 * Plugin URI:  https://energieausweis-online-erstellen.de
 * Description: Core Plugin for Energieausweis Online site.
 * Version:     1.0.0
 * Author:      Awesome UG
 * Author URI:  https://www.awesome.ug
 * Text Domain: enon
 */

namespace Enon\Core;

require dirname( __FILE__ ) . '/vendor/autoload.php';

use Enon\Config\Gutenberg;
use Enon\Config\Menu;
use Enon\Misc\Remove_Optimizepress;
use Enon\Misc\Google_Tag_Manager;
use Awsm\WP_Plugin\Building_Plans\Plugin;
use Awsm\WP_Plugin\Loaders\Assets_Loader;
use Awsm\WP_Plugin\Loaders\Hooks_Loader;
use Awsm\WP_Plugin\Loaders\Loader;

/**
 * Class Enon.
 *
 * @package Enon\Core
 */
class Enon implements Plugin {
	use Loader, Assets_Loader, Hooks_Loader;

	/**
	 * Plugin slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $slug = 'enon';

	/**
	 * Plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin name.
	 */
	private $name = 'Enon';

	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin name.
	 */
	private $version = '1.0.0';

	/**
	 * Get plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin name.
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get plugin slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin slug.
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Get plugin version.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin version.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Runnning plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin name.
	 */
	public function run() {
		self::load();

		// Configuration
		Gutenberg::Load();
		Menu::load();

		// Misc
		Remove_Optimizepress::load();
		Google_Tag_Manager::load();

	}
}


/**
 * Running plugin.
 *
 * @since 1.0.0
 */
function enon_start() {
	$enon = new Enon();
	$enon->run();
}

enon_start();
