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
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->load_hooks();
	}

	/**
	 * Load targeting scripts into hooks.
	 *
	 * @since 1.0.0
	 */
	private function load_hooks() {
		add_action( 'wp_head', array( $this, 'base_script' ), 1 );
		add_action( 'ea_finished_bedarfsausweis', array( $this, 'finished_bedarfsausweis' ) );
		add_action( 'ea_finished_verbrauchsausweis', array( $this, 'finished_verbrauchsausweis' ) );
	}

	/**
	 * Loads the base script on every site to the header.
	 *
	 * @since 1.0.0
	 */
	abstract public function base_script();

	/**
	 * Loads the scripts after a bedarfsausweis had a conversion.
	 *
	 * @since 1.0.0
	 */
	abstract public function conversion_bedarfsausweis();

	/**
	 * Loads the scripts after a verbrauchsausweis had a conversion.
	 *
	 * @since 1.0.0
	 */
	abstract public function conversion_verbrauchsausweis();
}
