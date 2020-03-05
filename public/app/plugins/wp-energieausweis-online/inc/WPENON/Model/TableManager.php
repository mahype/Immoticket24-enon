<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

class TableManager {
	private static $instance;

	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private $tables = array();

	private function __construct() {
		$this->_loadTables();

		$this->registerTables();

		if ( is_admin() ) {
			add_action( 'wpenon_install', array( $this, 'installTables' ) );
			add_action( 'wpenon_uninstall', array( $this, 'uninstallTables' ) );

			add_action( 'admin_menu', array( $this, 'setupMenu' ) );
		}
	}

	public function getTable( $slug ) {
		$slug = \WPENON\Util\Format::prefix( $slug );
		if ( isset( $this->tables[ $slug ] ) ) {
			return $this->tables[ $slug ];
		}

		return null;
	}

	public function setupMenu() {
		$topmenu = null;
		foreach ( $this->tables as $slug => $table ) {
			$topmenu = $table->addToMenu( $topmenu );
		}
	}

	public function registerTables() {
		foreach ( $this->tables as $slug => $table ) {
			$table->register();
		}
	}

	public function installTables() {
		foreach ( $this->tables as $slug => $table ) {
			$table->install();
		}
	}

	public function uninstallTables() {
		foreach ( $this->tables as $slug => $table ) {
			$table->uninstall();
		}
	}

	private function _loadTables() {
		$this->_loadDefaultTables();

		if ( is_dir( WPENON_DATA_PATH . '/tables' ) && ( $handle = opendir( WPENON_DATA_PATH . '/tables' ) ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				$slug = basename( $file );
				if ( substr( $slug, - 4 ) == '.php' ) {
					$slug   = substr( $slug, 0, - 4 );
					$schema = require WPENON_DATA_PATH . '/tables/' . $file;
					if ( is_array( $schema ) && count( $schema ) > 0 ) {
						$slug                  = \WPENON\Util\Format::prefix( $slug );
						$this->tables[ $slug ] = new \WPENON\Model\Table( $slug, $schema );
					}
				}
			}

			ksort( $this->tables );
			closedir( $handle );
		}
	}

	private function _loadDefaultTables() {
		$slug                  = \WPENON\Util\Format::prefix( 'regionen' );
		$this->tables[ $slug ] = new \WPENON\Model\Table( $slug, array(
			'title'         => __( 'Regionen', 'wpenon' ),
			'description'   => __( 'Diese Tabelle enthält Zuordnungen zwischen Postleitzahlen und den dazugehörigen Städten und Bundesländern.', 'wpenon' ) . ' ' . __( 'Diese Daten werden sowohl für den Bedarfsausweis als auch für den Verbrauchsausweis benötigt.', 'wpenon' ),
			'asterisks'     => array(
				'PLZ' => __( 'Postleitzahl', 'wpenon' ),
			),
			'primary_field' => 'postleitzahl',
			'search_field'  => 'postleitzahl',
			'search_before' => false,
			'fields'        => array(
				'postleitzahl' => array(
					'title' => __( 'PLZ<sup>1</sup>', 'wpenon' ),
					'type'  => 'VARCHAR(5)',
				),
				'ort'          => array(
					'title' => __( 'Ort', 'wpenon' ),
					'type'  => 'VARCHAR(100)',
				),
				'kreiszahl'    => array(
					'title' => __( 'Kreisschlüssel', 'wpenon' ),
					'type'  => 'INT',
				),
				'kreis'        => array(
					'title' => __( 'Kreis', 'wpenon' ),
					'type'  => 'VARCHAR(100)',
				),
				'landzahl'     => array(
					'title' => __( 'Länderschlüssel', 'wpenon' ),
					'type'  => 'TINYINT',
				),
				'land'         => array(
					'title' => __( 'Bundesland', 'wpenon' ),
					'type'  => 'VARCHAR(100)',
				),
			),
		) );
	}
}
