<?php

namespace EA\Tools\Targeting;

/**
 * Targetting service class.
 *
 * This class provides basic functionality for loading scripts to footer and at the end of purchasing an Energieausweis.
 *
 * @since 1.0.0
 */
abstract class Service {
	/**
	 * Loading nesesary properties and functions.
	 *
	 * @since 1.1.0
	 */
	public static function init() {
		self::load_hooks();
	}

	/**
	 * Load targeting scripts into hooks.
	 *
	 * @since 1.1.0
	 */
	private static function load_hooks() {
		add_action( 'wp_head', array( __CLASS__, 'anywhere_script' ) );
		add_action( 'ea_finished_bedarfsausweis', array( __CLASS__, 'finished_bedarfsausweis' ) );
		add_action( 'ea_finished_verbrauchsausweis', array( __CLASS__, 'finished_verbrauchsausweis' ) );
	}

	/**
	 * Loads the base script on every site to the header.
	 *
	 * @since 1.1.0
	 */
	abstract protected static function base_script();

	/**
	 * Loads the scripts after a bedarfsausweis had a conversion.
	 *
	 * @since 1.1.0
	 */
	abstract protected static function conversion_bedarfsausweis();

	/**
	 * Loads the scripts after a verbrauchsausweis had a conversion.
	 *
	 * @since 1.1.0
	 */
	abstract protected static function conversion_verbrauchsausweis();
}
