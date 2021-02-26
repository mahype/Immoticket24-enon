<?php

namespace Enev\Schema202002\Schema;

require_once( dirname( __FILE__ ) . '/vw.php' );

if ( ! class_exists( '\Enev\Schema202002\Schema\Verbrauchsausweis_Schema_Sparkasse' ) ) :

	/**
	 * Class Verbrauchsausweis_Schema_Sparkasse
	 *
	 * @since 1.0.0
	 */
	class Verbrauchsausweis_Schema_Sparkasse extends Verbrauchsausweis_Schema {
		/**
		 * Verbrauchsausweis_Schema_Sparkasse constructor.
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
				'freistehend'            => __( 'freistehendes Haus', 'wpenon' ),
				'reihenhaus'             => __( 'Reihenmittelhaus', 'wpenon' ),
				'reiheneckhaus'          => __( 'Reiheneckhaus', 'wpenon' ),
				'doppelhaushaelfte'      => __( 'Doppelhaushälfte', 'wpenon' ),
				'fertighausfachwerkhaus' => __( 'Fertighaus/Fachwerkhaus', 'wpenon' ),
				'mehrfamilienhaus'       => __( 'Mehrfamilienhaus', 'wpenon' ),
				'sonstiges'              => __( 'sonstiges Wohngebäude', 'wpenon' ),
			);

			$data['gebaeudekonstruktion'] = array(
				'type'     => 'radio',
				'label'    => __( 'Gebäudekonstruktion', 'wpenon' ),
				'options'  => array(
					'massiv'   => __( 'Massivhaus', 'wpenon' ),
					'holz'     => __( 'Holzhaus', 'wpenon' ),
					'fachwerk' => __( 'Fachwerkhaus', 'wpenon' ),
				),
				'required' => true,
			);

			$basisdaten = $this->insert_after_key( $basisdaten, 'gebaeude', 'gebaeudeteil', $data );

			unset( $data );

			$data['geschosse'] = array(
				'type'        => 'int',
				'label'       => __( 'Anzahl der Geschosse', 'wpenon' ),
				'description' => __( 'Geben Sie die Anzahl der Geschosse im Gebäude ein.', 'wpenon' ),
				'default'     => 1,
				'min'         => 1,
				'required'    => true,
			);

			$basisdaten = $this->insert_after_key( $basisdaten, 'gebaeude', 'gebaeudekonstruktion', $data );

			unset( $data );

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
			$bauteile = parent::get_bauteile();

			$data['jahr_wand_daemmung'] = array(
				'type'        => 'int',
				'label'       => __( 'Jahr der Dämmung', 'wpenon' ),
				'description' => __( 'Geben Sie das Jahr der Dämmung an.', 'wpenon' ),
				'default'     => '',
				'min'         => 1995,
				'max'         => wpenon_get_reference_date( 'Y' ),
				'display'     => array(
					'callback'      => 'wpenon_immoticket24_show_jahr_daemmung',
					'callback_args' => array( 'field::wand_daemmung_on' ),
				),
			);

			$bauteile = $this->insert_after_key( $bauteile, 'bauteile_basis', 'wand_daemmung_on', $data );

			unset( $data );

			$data['jahr_decke_daemmung'] = array(
				'type'        => 'int',
				'label'       => __( 'Jahr der Dämmung', 'wpenon' ),
				'description' => __( 'Geben Sie das Jahr der Dämmung an.', 'wpenon' ),
				'default'     => '',
				'min'         => 1995,
				'max'         => wpenon_get_reference_date( 'Y' ),
				'display'     => array(
					'callback'      => 'wpenon_immoticket24_show_jahr_daemmung',
					'callback_args' => array( 'field::decke_daemmung_on', 'field::dach' ),
				),
			);

			$bauteile = $this->insert_after_key( $bauteile, 'bauteile_basis', 'decke_daemmung_on', $data );

			unset( $data );

			$data['jahr_boden_daemmung'] = array(
				'type'        => 'int',
				'label'       => __( 'Jahr der Dämmung', 'wpenon' ),
				'description' => __( 'Geben Sie das Jahr der Dämmung an.', 'wpenon' ),
				'default'     => '',
				'min'         => 1995,
				'max'         => wpenon_get_reference_date( 'Y' ),
				'display'     => array(
					'callback'      => 'wpenon_immoticket24_show_jahr_daemmung',
					'callback_args' => array( 'field::boden_daemmung_on' ),
				),
			);

			$bauteile = $this->insert_after_key( $bauteile, 'bauteile_basis', 'boden_daemmung_on', $data );

			unset( $data );

			$data['jahr_dach_daemmung'] = array(
				'type'        => 'int',
				'label'       => __( 'Jahr der Dämmung', 'wpenon' ),
				'description' => __( 'Geben Sie das Jahr der Dämmung an.', 'wpenon' ),
				'default'     => '',
				'min'         => 1995,
				'max'         => wpenon_get_reference_date( 'Y' ),
				'display'     => array(
					'callback'      => 'wpenon_immoticket24_show_jahr_daemmung',
					'callback_args' => array( 'field::dach_daemmung_on' ),
				),
			);

			$bauteile = $this->insert_after_key( $bauteile, 'bauteile_basis', 'dach_daemmung_on', $data );

			unset( $data );

			$data['unterkellerung'] = array(
				'type' => 'select',
				'label' => __('Unterkellerung', 'wpenon'),
				'options' => array(
					'teilunterkellert' => __('Teilunterkellert', 'wpenon'),
					'vollunterkellert' => __('Voll unterkellert', 'wpenon'),
				),
				'display' => array(
					'callback' => 'wpenon_immoticket24_show_unterkellerung',
					'callback_args' => array('field::keller'),
				),
				'required' => true,
			);

			$bauteile = $this->insert_after_key( $bauteile, 'bauteile_keller', 'keller_daemmung_on', $data );

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
		 * Get erfasssung.
		 *
		 * @return array Erfasssung.
		 *
		 * @since 1.0.0
		 */
		public function get_erfassung() {
			$erfassung = parent::get_erfassung();

			return $erfassung;
		}

		/**
		 * Get sonstiges.
		 *
		 * @return array Sonstiges.
		 *
		 * @since 1.0.0
		 */
		public function get_sonstiges() {
			require( dirname( __FILE__ ) . '/vw-sonstiges-sparkasse.php' );

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
				'vw_basisdaten' => $this->get_basisdaten(),
				'vw_bauteile'   => $this->get_bauteile(),
				'vw_anlage'     => $this->get_anlage(),
				'vw_erfassung'  => $this->get_erfassung(),
				'sonstiges'     => $this->get_sonstiges(),
			);

			return $schema;
		}
	}

endif;

$verbrauchsausweis_schema = new Verbrauchsausweis_Schema_Sparkasse();

return $verbrauchsausweis_schema->get();
