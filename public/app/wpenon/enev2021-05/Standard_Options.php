<?php

namespace Enev\Schema202105\Schema;

/**
 * Class \Enev\Schema202104\Schema\Standard_Options.
 *
 * @since 1.0.0
 */
class Standard_Options {
	/**
	 * Get wall thickness.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public static function get_wandstaerken() {
		$start = 18;
		$end   = 50;

		for( $i = $start; $i <= $end; $i++ ) {
			$options[$i] = $i;
		}

		return $options;
	}

	/**
	 * Building construction type.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public static function get_bauarten() {
		$construction_types = array(
			'massiv' => __( 'Massiv', 'wpenon' ),
			'holz'   => __( 'Holz', 'wpenon' ),
		);

		return $construction_types;
	}

	

	/**
	 * Floor construction type.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public static function get_bauarten_boden() {
		$construction_types = array(
			'massiv'     => __( 'Massiv', 'wpenon' ),
			'holz'       => __( 'Holz', 'wpenon' ),
			'stahlbeton' => __( 'Stahlbeton', 'wpenon' ),
		);

		return $construction_types;
	}

	/**
	 * Basement construction type.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public static function get_bauarten_keller( $width = 0 ) {
		if ( $width === 0 ) {
			return array(
				'massiv_bims'                      => __( 'Hochlochziegel, Bimsbetonhohlstein; z. B. Poroton', 'wpenon' ),
				'massiv_zweischalig'               => __( 'Zweischalige Bauweise', 'wpenon' ),
				'massiv_holzhaus_holz'             => __( 'Holz', 'wpenon' ),
				'massiv_bis_20cm'                  => __( 'Sonstige Massivwände bis 20 cm', 'wpenon' ),
				'massiv_ueber_20cm'                => __( 'Sonstige Massivwände über 20 cm', 'wpenon' ),
				'massiv_vollziegel_bis_20cm'       => __( 'Vollziegel, Kalksandstein oder Bimsbetonvollstein bis 20 cm', 'wpenon' ),
				'massiv_vollziegel_20cm_bis_30_cm' => __( 'Vollziegel, Kalksandstein oder Bimsbetonvollstein 20 - 30 cm', 'wpenon' ),
				'massiv_vollziegel_ueber_30cm'     => __( 'Vollziegel, Kalksandstein oder Bimsbetonvollstein über 30 cm', 'wpenon' ),
			);
		}

		$construction_types = array(
			'massiv_bims'                      => __( 'Hochlochziegel, Bimsbetonhohlstein; z. B. Poroton', 'wpenon' ),
			'massiv_zweischalig'               => __( 'Zweischalige Bauweise', 'wpenon' ),
			'massiv_holzhaus_holz'             => __( 'Holz', 'wpenon' ),
		);

		if( $width <= 20 ) {
			$construction_types = array_merge( $construction_types, array(				
				'massiv_bis_20cm'                  => __( 'Sonstige Massivwände bis 20 cm', 'wpenon' ),
				'massiv_vollziegel_bis_20cm'       => __( 'Vollziegel, Kalksandstein oder Bimsbetonvollstein bis 20 cm', 'wpenon' ),
			) );
		}

		if( $width > 20 && $width <= 30 ) {
			$construction_types = array_merge( $construction_types, array(		
				'massiv_ueber_20cm'                => __( 'Sonstige Massivwände über 20 cm', 'wpenon' ),
				'massiv_vollziegel_20cm_bis_30_cm' => __( 'Vollziegel, Kalksandstein oder Bimsbetonvollstein 20 - 30 cm', 'wpenon' ),
			) );
		}

		if( $width > 30 ) {
			$construction_types = array_merge( $construction_types, array(				
				'massiv_ueber_20cm'                => __( 'Sonstige Massivwände über 20 cm', 'wpenon' ),
				'massiv_vollziegel_ueber_30cm'     => __( 'Vollziegel, Kalksandstein oder Bimsbetonvollstein über 30 cm', 'wpenon' ),
			) );
		}

		return $construction_types;
	}

	/**
	 * Wooden house construction types.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public static function get_bauarten_holzhaus() {
		$construction_types = array(
			'holzhaus_holz' => __( 'Holz', 'wpenon' ),
		);

		return $construction_types;
	}

	/**
	 * Fachwerkhaus construction types.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public static function get_bauarten_fachwerkaus() {
		$construction_types = array(
			'fachwerk_lehm'       => __( 'Lehm-/Lehmziegelausfachung', 'wpenon' ),
			'fachwerk_vollziegel' => __( 'Vollziegel oder Massive Natursteinausfach', 'wpenon' ),
		);

		return $construction_types;
	}

	/**
	 * Get massiv wall construction types.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public static function get_bauarten_massiv( $width = 0 ) {
		if ( $width === 0 ) {
			return array(
				'massiv_bims'                      => __( 'Hochlochziegel, Bimsbetonhohlstein; z. B. Poroton', 'wpenon' ),
				'massiv_zweischalig'               => __( 'Zweischalige Bauweise', 'wpenon' ),
				'massiv_bis_20cm'                  => __( 'Sonstige Massivwände bis 20 cm', 'wpenon' ),
				'massiv_ueber_20cm'                => __( 'Sonstige Massivwände über 20 cm', 'wpenon' ),
				'massiv_vollziegel_bis_20cm'       => __( 'Vollziegel, Kalksandstein oder Bimsbetonvollstein bis 20 cm', 'wpenon' ),
				'massiv_vollziegel_20cm_bis_30_cm' => __( 'Vollziegel, Kalksandstein oder Bimsbetonvollstein 20 - 30 cm', 'wpenon' ),
				'massiv_vollziegel_ueber_30cm'     => __( 'Vollziegel, Kalksandstein oder Bimsbetonvollstein über 30 cm', 'wpenon' ),
			);
		}

		$construction_types = array(
			'massiv_bims'                      => __( 'Hochlochziegel, Bimsbetonhohlstein; z. B. Poroton', 'wpenon' ),
			'massiv_zweischalig'               => __( 'Zweischalige Bauweise', 'wpenon' ),
		);

		if( $width <= 20 ) {
			$construction_types = array_merge( $construction_types, array(				
				'massiv_bis_20cm'                  => __( 'Sonstige Massivwände bis 20 cm', 'wpenon' ),
				'massiv_vollziegel_bis_20cm'       => __( 'Vollziegel, Kalksandstein oder Bimsbetonvollstein bis 20 cm', 'wpenon' ),
			) );
		}

		if( $width > 20 && $width <= 30 ) {
			$construction_types = array_merge( $construction_types, array(		
				'massiv_ueber_20cm'                => __( 'Sonstige Massivwände über 20 cm', 'wpenon' ),		
				'massiv_vollziegel_20cm_bis_30_cm' => __( 'Vollziegel, Kalksandstein oder Bimsbetonvollstein 20 - 30 cm', 'wpenon' ),
			) );
		}

		if( $width > 30 ) {
			$construction_types = array_merge( $construction_types, array(				
				'massiv_ueber_20cm'                => __( 'Sonstige Massivwände über 20 cm', 'wpenon' ),
				'massiv_vollziegel_ueber_30cm'     => __( 'Vollziegel, Kalksandstein oder Bimsbetonvollstein über 30 cm', 'wpenon' ),
			) );
		}

		return $construction_types;
	}

	/**
	 * Plot forms.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public static function get_grundriss_formen() {
		$formen = wpenon_immoticket24_get_grundriss_formen(); // Todo: Getting class for it.

		foreach ( $formen as $key => &$value ) {
			// phpcs:ignore
			$value = sprintf( __( 'Form %s', 'wpenon' ), strtoupper( $key ) );
		}

		return $formen;
	}

	/**
	 * Roof forms.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public static function get_dach_formen() {
		$formen = array(
			'satteldach' => __( 'Satteldach', 'wpenon' ),
			'pultdach'   => __( 'Pultdach', 'wpenon' ),
			'walmdach'   => __( 'Walmdach', 'wpenon' ),
		);

		return $formen;
	}

	/**
	 * Extension form.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public static function get_anbau_formen() {
		$formen = wpenon_immoticket24_get_anbau_formen();

		foreach ( $formen as $key => &$value ) {
			// phpcs:ignore
			$value = sprintf( __( 'Form %s', 'wpenon' ), strtoupper( $key ) );
		}

		return $formen;
	}

	/**
	 * Window construction type.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public static function get_fenster_bauarten() {
		$_windows = wpenon_get_table_results(
			'uwerte202001',
			array(
				'bezeichnung' => array(
					'value'   => 'fenster_%',
					'compare' => 'LIKE',
				),
			),
			array( 'name' ),
			false,
			'name',
			'ASC'
		);

		$windows = array();
		foreach ( $_windows as $slug => $name ) {
			$windows[ str_replace( 'fenster_', '', $slug ) ] = $name;
		}

		return $windows;
	}
}
