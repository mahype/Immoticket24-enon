<?php

namespace Enev\Schema202103\Schema;

require_once( dirname( __FILE__ ) . '/bw.php' );

if ( ! class_exists( '\Enev\Schema202103\Schema\Bedarfsausweis_Schema_Sparkasse' ) ) :

	/**
	 * Class Bedarfsausweis_Schema
	 *
	 * @since 1.0.0
	 */
	class Bedarfsausweis_Schema_Sparkasse extends Bedarfsausweis_Schema {
		/**
		 * Bedarfsausweis_Schema_Sparkasse constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'wpenon_form_group_notice_before', array( $this, 'show_notice_misc' ) );
		}

		/**
		 * Showing notice on misc page.
		 *
		 * @since 1.0.0
		 */
		public function show_notice_misc() {
			?>
			<div class="alert alert-warning">
				<p>
					<?php _e( 'Bei diesen Angaben handelt es sich um die Zusatzangaben, die für Ihre Wertanalyse benötigt werden. Möchten Sie keine kostenlose qualifizierte Wertanalyse erhalten, können Sie diese Eingabefelder überspringen.', 'wpenon' ); ?>
				</p>
			</div>
			<?php
		}

		/**
		 * Get Basisdaten.
		 *
		 * @return array Basisdaten.
		 *
		 * @since 1.0.0
		 */
		public function get_basisdaten() {
			$basisdaten = parent::get_basisdaten();

			$basisdaten['groups']['gebaeude']['fields']['gebaeudetyp']['options'] = array(
				'freistehend'       => __( 'freistehendes Haus', 'wpenon' ),
				'reihenhaus'        => __( 'Reihenmittelhaus', 'wpenon' ),
				'reiheneckhaus'     => __( 'Reiheneckhaus', 'wpenon' ),
				'fertighaus'        => __( 'Fertighaus', 'wpenon' ),
				'doppelhaushaelfte' => __( 'Doppelhaushälfte', 'wpenon' ),
				'sonstiges'         => __( 'sonstiges Wohngebäude', 'wpenon' ),
			);

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
			$gebaeude = parent::get_gebaeude();

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
			$bauteile = parent::get_bauteile();

			$data['wand_daemmung_jahr'] = array(
				'type'        => 'select',
				'label'       => __( 'Zeitraum der nachträglichen Dämmung', 'wpenon' ),
				'description' => __( 'Wurden eine nachträgliche Dämmung vorgenommen und wenn ja vor wie viel Jahren?', 'wpenon' ),
				'options'     => array(
					'nein'  => __( 'Keine Verbesserung', 'wpenon' ),
					'0-5'   => __( 'Vor 0-5 Jahren', 'wpenon' ),
					'6-10'  => __( 'Vor 6-10 Jahren', 'wpenon' ),
					'11-15' => __( 'Vor 11-15 Jahren', 'wpenon' ),
					'16-25' => __( 'Vor 16- 25 Jahren', 'wpenon' ),
					'25'    => __( 'Vor über 25 Jahren', 'wpenon' ),
				),
				'required'    => true,
			);

			$bauteile = $this->insert_after_key( $bauteile, 'bauteile_basis', 'wand_h_daemmung', $data );

			unset( $data );

			$bauteile['groups']['bauteile_basis']['fields']['dach_bauart']['display']['callback']      = 'wpenon_show_on_array_blacklist';
			$bauteile['groups']['bauteile_basis']['fields']['dach_bauart']['display']['callback_args'] = array(
				'field::dach',
				'unbeheizt'
			);

			$data['dach_daemmung_jahr'] = array(
				'type'                  => 'int',
				'label'                 => __( 'Jahr der nachträglichen Dämmung', 'wpenon' ),
				'description'           => sprintf( __( 'Das Jahr in dem die Dämmung vorgenommen wurde.', 'wpenon' ), 'a' ),
				'min'                   => 1800,
				'max'                   => wpenon_get_reference_date( 'Y' ),
				'required'              => true,
				'display'               => array(
					'callback'      => 'wpenon_show_on_number_higher',
					'callback_args' => array( 'field::dach_daemmung', 0, false ),
				),
				'validate'              => 'wpenon_immoticket24_validate_year_greater_than',
				'validate_dependencies' => array( 'baujahr' ),
			);

			$bauteile = $this->insert_after_key( $bauteile, 'bauteile_dach', 'dach_daemmung', $data );

			unset( $data );

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
			$anlage = parent::get_anlage();

			$data['h_modernisierung'] = array(
				'type'     => 'select',
				'label'    => __( 'Modernisierung der Leitungssysteme', 'wpenon' ),
				'options'  => array(
					'nein'  => __( 'Keine Modernisierung durchgeführt', 'wpenon' ),
					'0-5'   => __( 'Vor 0-5 Jahren', 'wpenon' ),
					'6-10'  => __( 'Vor 6-10 Jahren', 'wpenon' ),
					'11-15' => __( 'Vor 11-15 Jahren', 'wpenon' ),
					'16-25' => __( 'Vor 16- 25 Jahren', 'wpenon' ),
					'25'    => __( 'Vor über 25 Jahren', 'wpenon' ),
				),
				'required' => true,
			);

			$anlage = $this->insert_after_key( $anlage, 'heizung', 'h_baujahr', $data );

			unset( $data );

			return $anlage;
		}

		/**
		 * Get sonstiges.
		 *
		 * @return array Sonstiges.
		 *
		 * @since 1.0.0
		 */
		public function get_sonstiges() {
			require( dirname( __FILE__ ) . '/bw-sonstiges-sparkasse.php' );

			return $sonstiges;
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
				'sonstiges'     => $this->get_sonstiges(),
			);

			return $schema;
		}
	}
endif;

$bedarfsausweis_schema = new Bedarfsausweis_Schema_Sparkasse();

return $bedarfsausweis_schema->get();
