<?php

namespace Enev\Schema202401\Schema;

require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/schema.php' );
require_once( dirname( __DIR__ ) . '/Standard_Options.php' );
require_once( dirname( __DIR__ ) . '/modernizations/VW_Modernizations.php' );

use Enev\Schema202401\Modernizations\VW_Modernizations;
use Enev\Schema\Schema;

if ( ! class_exists( '\Enev\Schema202401\Schema\Verbrauchsausweis_Schema' ) ) :

	/**
	 * Class Verbrauchsausweis_Schema
	 *
	 * @since 1.0.0
	 */
	class Verbrauchsausweis_Schema extends Schema {
		/**
		 * Verbrauchsausweis_Schema constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			new VW_Modernizations();
		}

		/**
		 * Get Basisdaten.
		 *
		 * @return array Basisdaten.
		 *
		 * @since 1.0.0
		 */
		public function get_basisdaten() {
			require( dirname( __FILE__ ) . '/vw-basisdaten.php' );

			return $basisdaten;
		}

		/**
		 * Get Bauteile.
		 *
		 * @return array Basisdaten.
		 *
		 * @since 1.0.0
		 */
		public function get_bauteile() {
			require( dirname( __FILE__ ) . '/vw-bauteile.php' );

			return $bauteile;
		}

		/**
		 * Get Anlagedaten.
		 *
		 * @return array Anlagedaten.
		 *
		 * @since 1.0.0
		 */
		public function get_anlage() {
			require( dirname( __FILE__ ) . '/vw-anlage.php' );

			return $anlage;
		}

		/**
		 * Get Erfassung.
		 *
		 * @return array Erfassung.
		 *
		 * @since 1.0.0
		 */
		public function get_erfassung() {
			require( dirname( __FILE__ ) . '/vw-erfassung.php' );

			return $erfassung;
		}

		/**
		 * Get whole schema
		 *
		 * @return array Whole schema.
		 *
		 * @since 1.0.0
		 */
		public function get() {
			$schema = array(
				'vw_basisdaten' => $this->get_basisdaten(),
				'vw_bauteile'   => $this->get_bauteile(),
				'vw_anlage'     => $this->get_anlage(),
				'vw_erfassung'  => $this->get_erfassung(),
			);

			return $schema;
		}
	}

endif;

$verbrauchsausweis_schema = new Verbrauchsausweis_Schema();

return $verbrauchsausweis_schema->get();
