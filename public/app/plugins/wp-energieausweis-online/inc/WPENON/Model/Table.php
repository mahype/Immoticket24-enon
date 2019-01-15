<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

class Table {
	private $slug = '';
	private $schema = '';
	private $hook = '';
	private $list_table = null;

	private $_results_cache = null;
	private $_current_row_request = array();
	private $_current_col_request = array();
	private $_current_orderby = null;
	private $_current_order = 'ASC';

	public function __construct( $slug, $schema ) {
		$this->slug   = $slug;
		$this->schema = $this->_validateSchema( $schema );
	}

	public function getSchema() {
		return $this->schema;
	}

	public function getHook() {
		return $this->hook;
	}

	public function addToMenu( $topmenu = null ) {
		$hook = false;
		if ( is_array( $topmenu ) && isset( $topmenu['slug'] ) ) {
			$hook = add_submenu_page( $topmenu['slug'], $this->schema['title'], $this->schema['title'], WPENON_TABLE_CAP, $this->slug, array(
				$this,
				'display'
			) );
			if ( isset( $topmenu['label'] ) && is_string( $topmenu['label'] ) ) {
				global $submenu;
				if ( isset( $submenu[ $topmenu['slug'] ] ) ) {
					$submenu[ $topmenu['slug'] ][0][0] = $topmenu['label'];
					unset( $topmenu['label'] );
				}
			}
		} else {
			$hook    = add_menu_page( $this->schema['title'], __( 'Tabellen', 'wpenon' ), WPENON_TABLE_CAP, $this->slug, array(
				$this,
				'display'
			), 'dashicons-feedback', WPENON_MENU_POSITION + 1 );
			$topmenu = array(
				'slug'  => $this->slug,
				'label' => $this->schema['title'],
			);
		}

		if ( $hook ) {
			$this->hook = $hook;
			$list_table = $this->_getListTable();
			add_action( 'load-' . $hook, array( $list_table, 'process_request' ) );
		}

		return $topmenu;
	}

	public function display() {
		$list_table = $this->_getListTable();
		$list_table->prepare_items();

		echo '<div class="wrap">';
		echo '<h2>' . wp_kses_post( $this->schema['title'] ) . '</h2>';
		if ( ! empty( $this->schema['description'] ) ) {
			echo '<p>' . wp_kses_post( $this->schema['description'] ) . '</p>';
		}

		$list_table->messages();

		echo '<form action="" method="get">';
		echo '<input type="hidden" name="page" value="' . esc_attr( $this->slug ) . '" />';
		$list_table->display();
		$list_table->asterisks();
		echo '</form>';

		echo '<form action="" method="post" enctype="multipart/form-data">';
		$list_table->table_actions();
		echo '</form>';

		echo '</div>';
	}

	public function getResults( $rows = array(), $cols = array(), $single = false, $orderby = null, $order = 'ASC' ) {
		if ( $this->_results_cache === null ) {
			$this->_results_cache = \WPENON\Util\DB::getResults( $this->slug );
		}

		$results = $this->_results_cache;

		if ( $orderby ) {
			$this->_current_orderby = $orderby;
			$this->_current_order   = 'DESC' === strtoupper( $order ) ? 'DESC' : 'ASC';
			uasort( $results, array( $this, '_orderbyCallback' ) );
			$this->_current_orderby = null;
			$this->_current_order   = 'ASC';
		}

		if ( count( $rows ) > 0 ) {
			$this->_current_row_request = $rows;
			$results                    = array_filter( $results, array( $this, '_rowFilter' ) );
			$this->_current_row_request = array();
		}

		if ( count( $cols ) > 0 ) {
			$this->_current_col_request = $cols;
			$results                    = array_map( array( $this, '_colFilter' ), $results );
			$this->_current_col_request = array();
		}

		if ( $single ) {
			if ( count( $results ) > 0 ) {
				return array_shift( $results );
			}

			return false;
		}

		return $results;
	}

	public function _orderbyCallback( $a, $b ) {
		if ( ! $this->_current_orderby ) {
			return 0;
		}

		$field     = $this->_current_orderby;
		$direction = $this->_current_order;

		$a = (array) $a;
		$b = (array) $b;

		if ( ! isset( $a[ $field ] ) || ! isset( $b[ $field ] ) ) {
			return 0;
		}

		if ( $a[ $field ] == $b[ $field ] ) {
			return 0;
		}

		$results = 'DESC' === $direction ? array( 1, - 1 ) : array( - 1, 1 );

		if ( is_numeric( $a[ $field ] ) && is_numeric( $b[ $field ] ) ) {
			return ( $a[ $field ] < $b[ $field ] ) ? $results[0] : $results[1];
		}

		return 0 > strcmp( $a[ $field ], $b[ $field ] ) ? $results[0] : $results[1];
	}

	public function _rowFilter( $row ) {
		$request = $this->_current_row_request;

		if ( count( $request ) < 1 ) {
			return true;
		}

		$relation = ( isset( $request['relation'] ) && strtoupper( $request['relation'] ) == "OR" ) ? "OR" : "AND";
		if ( isset( $request['relation'] ) ) {
			unset( $request['relation'] );
		}

		foreach ( $request as $col_name => $col_data ) {
			if ( ! isset( $row->$col_name ) ) {
				return false;
			}

			$value   = isset( $col_data['value'] ) ? $col_data['value'] : '';
			$compare = isset( $col_data['compare'] ) ? $col_data['compare'] : '=';

			if ( is_array( $value ) ) {
				if ( ! in_array( $row->$col_name, $value ) ) {
					return false;
				}
			} else {
				$condition = false;
				switch ( $compare ) {
					case '>':
						$condition = $row->$col_name > $value;
						break;
					case '<':
						$condition = $row->$col_name < $value;
						break;
					case '>=':
						$condition = $row->$col_name >= $value;
						break;
					case '<=':
						$condition = $row->$col_name <= $value;
						break;
					case 'LIKE':
						$done = false;
						if ( is_string( $value ) ) {
							$start = strpos( $value, '%' ) === 0;
							$end   = strpos( $value, '%', strlen( $value ) - 1 ) !== false;
							$value = trim( $value, '%' );
							if ( $start || $end ) {
								if ( $start && $end ) {
									$condition = strpos( $row->$col_name, $value ) !== false;
								} elseif ( $start ) {
									$condition = strpos( $row->$col_name, $value ) === strlen( $row->$col_name ) - strlen( $value );
								} else {
									$condition = strpos( $row->$col_name, $value ) === 0;
								}
								$done = true;
							}
						}
						if ( ! $done ) {
							$condition = $row->$col_name == $value;
						}
						break;
					case '=':
					default:
						$condition = $row->$col_name == $value;
						break;
				}
				if ( $relation == 'AND' && ! $condition ) {
					return false;
				} elseif ( $relation == 'OR' && $condition ) {
					return true;
				}
			}
		}

		if ( $relation == 'OR' ) {
			return false;
		}

		return true;
	}

	public function _colFilter( $row ) {
		$request = $this->_current_col_request;

		if ( count( $request ) < 1 ) {
			return $row;
		}

		if ( count( $request ) == 1 ) {
			$key = $request[0];

			return $row->$key;
		}

		$vars = get_object_vars( $row );
		foreach ( $vars as $key => $value ) {
			if ( ! in_array( $key, $request ) ) {
				unset( $row->$key );
			}
		}

		return $row;
	}

	public function register() {
		\WPENON\Util\DB::registerTable( $this->slug );
	}

	public function install() {
		\WPENON\Util\DB::installTable( $this->slug, $this->schema['fields'], $this->schema['primary_field'] );
	}

	public function uninstall() {
		\WPENON\Util\DB::uninstallTable( $this->slug );
	}

	public function import( $file, $charset = WPENON_DEFAULT_CHARSET ) {
		return \WPENON\Util\DB::csvToTable( $this->slug, $file, $charset );
	}

	public function export( $charset = WPENON_DEFAULT_CHARSET ) {
		\WPENON\Util\DB::tableToCSV( $this->slug, $charset );
	}

	private function _getListTable() {
		if ( $this->list_table === null ) {
			$this->list_table = new \WPENON\Model\TableListTable( $this->slug, $this );
		}

		return $this->list_table;
	}

	private function _validateSchema( $schema ) {
		$schema = wp_parse_args( $schema, array(
			'title'             => '',
			'description'       => '',
			'asterisks'         => array(),
			'primary_field'     => '',
			'search_field'      => '',
			'search_before'     => false,
			'items_per_page'    => 50,
			'import_text'       => '',
			'rebuild_on_import' => false,
			'fields'            => array(),
			'filters'           => array(),
		) );

		if ( count( $schema['fields'] ) > 0 ) {
			foreach ( $schema['fields'] as $field_slug => &$field ) {
				$field = wp_parse_args( $field, array(
					'title'          => '',
					'type'           => 'INT',
					'default'        => '',
					'not_null'       => true,
					'auto_increment' => false,
					'format'         => '',
					'sortable'       => true,
					'descending'     => false,
				) );

				if ( empty( $field['format'] ) ) {
					switch ( $field['type'] ) {
						case 'TINYINT':
						case 'SMALLINT':
						case 'INT':
						case 'BIGINT':
							$field['format'] = array( '\WPENON\Util\Format', 'int' );
							break;
						case 'FLOAT':
							$field['format'] = array( '\WPENON\Util\Format', 'float' );
							break;
						case 'FLOAT_LENGTH':
							$field['format'] = array( '\WPENON\Util\Format', 'float_length' );
							break;
						case 'DATE':
							$field['format'] = array( '\WPENON\Util\Format', 'date' );
							break;
						case 'DATETIME':
							$field['format'] = array( '\WPENON\Util\Format', 'datetime' );
							break;
						default:
							$field['format'] = 'esc_html';
							break;
					}
				}
			}
			unset( $field );
		}

		if ( count( $schema['filters'] ) > 0 ) {
			foreach ( $schema['filters'] as $filterslug => &$filter ) {
				$filter = wp_parse_args( $filter, array(
					'type'     => 'rows',
					'callback' => null,
					'title'    => '',
					'default'  => '',
					'options'  => array(),
				) );
			}
			unset( $filter );
		}

		if ( empty( $schema['primary_field'] ) ) {
			reset( $schema['fields'] );
			$schema['primary_field'] = key( $schema['fields'] );
		}

		if ( empty( $schema['import_text'] ) ) {
			$schema['import_text'] = __( 'Sie können den kompletten Tabelleninhalt ersetzen, indem Sie eine CSV-Datei importieren, welche zum Beispiel mit Excel erzeugt werden kann.', 'wpenon' ) . '<br />';
			$schema['import_text'] .= __( 'Bei der Tabelle müssen Sie darauf achten, dass sämtliche Spalten der oben angezeigten Tabelle in genau dieser Reihenfolge existieren. In der CSV-Datei selbst müssen die einzelnen Spalten durch Semekolons <em>;</em> voneinander getrennt werden, die verschiedenen Datensätze durch Zeilenumbrüche.', 'wpenon' ) . '<br>';
			$schema['import_text'] .= __( 'Am einfachsten ist es, wenn Sie sich die CSV-Datei aus der aktuellen Tabelle exportieren und diese entsprechend anpassen.', 'wpenon' );
		}

		return $schema;
	}

	public function refreshSchema() {
		$unprefixed_slug = \WPENON\Util\Format::unprefix( $this->slug );
		if ( file_exists( WPENON_DATA_PATH . '/tables/' . $unprefixed_slug . '.php' ) ) {
			$schema       = require WPENON_DATA_PATH . '/tables/' . $unprefixed_slug . '.php';
			$this->schema = $this->_validateSchema( $schema );
		}
	}
}
