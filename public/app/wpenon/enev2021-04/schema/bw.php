<?php

namespace Enev\Schema202103\Schema;

use Enev\Schema\Schema;

require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/schema.php' );
require_once( dirname( __DIR__ ) . '/Standard_Options.php' );
require_once( dirname( __DIR__ ) . '/modernizations/BW_Modernizations.php' );

use Enev\Schema202103\Modernizations\BW_Modernizations;


if ( ! class_exists( '\Enev\Schema202103\Schema\Bedarfsausweis_Schema' ) ) :
	/**
	 * Class Bedarfsausweis_Schema
	 *
	 * @since 1.0.0
	 */
	class Bedarfsausweis_Schema extends Schema {
		/**
		 * Verbrauchsausweis_Schema constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			new BW_Modernizations();
		}

		/**
		 * Get Basisdaten.
		 *
		 * @return array Basisdaten.
		 *
		 * @since 1.0.0
		 */
		public function get_basisdaten() {
			require( dirname( __FILE__ ) . '/bw-basisdaten.php' );

			return $basisdaten;
		}

		/**
		 * Get Gebaeudedaten.
		 *
		 * @return array Gebaeudedaten.
		 *
		 * @since 1.0.0
		 */
		public function get_gebaeude() {
			require( dirname( __FILE__ ) . '/bw-gebaeude.php' );

			return $gebaeude;
		}

		/**
		 * Get Bauteile.
		 *
		 * @return array Basisdaten.
		 *
		 * @since 1.0.0
		 */
		public function get_bauteile() {
			require( dirname( __FILE__ ) . '/bw-bauteile.php' );

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
			require( dirname( __FILE__ ) . '/bw-anlage.php' );

			return $anlage;
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
				'bw_basisdaten' => $this->get_basisdaten(),
				'bw_gebaeude'   => $this->get_gebaeude(),
				'bw_bauteile'   => $this->get_bauteile(),
				'bw_anlage'     => $this->get_anlage(),
			);

			return $schema;
		}
	}

endif;

$bedarfsausweis_schema = new Bedarfsausweis_Schema();

return $bedarfsausweis_schema->get();
