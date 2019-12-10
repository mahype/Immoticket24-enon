<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class DB {
	private static $_current_schema = null;

	public static function getResults( $table_slug, $rows = array(), $cols = array(), $single = false, $results_mode = OBJECT_K, $orderby = null, $order = 'ASC' ) {
		global $wpdb;

		$table_slug = \WPENON\Util\Format::prefix( $table_slug );

		$query_columns = "*";
		if ( count( $cols ) > 0 ) {
			$query_columns = "";
			foreach ( $cols as $col ) {
				if ( ! empty( $query_columns ) ) {
					$query_columns .= ", ";
				}
				$query_columns .= $col;
			}
		}

		$query_rows = "";
		$row_values = array();
		$relation   = ( isset( $rows['relation'] ) && strtoupper( $rows['relation'] ) == "OR" ) ? "OR" : "AND";
		if ( isset( $rows['relation'] ) ) {
			unset( $rows['relation'] );
		}
		foreach ( $rows as $col_name => $col_data ) {
			if ( ! is_array( $col_data ) ) {
				$col_data = array( 'value' => $col_data, 'compare' => "=" );
			} else {
				$col_data = wp_parse_args( $col_data, array( 'value' => '', 'compare' => "=" ) );
			}

			$value = $col_data['value'];

			$type = "'%s'";
			if ( is_float( $value ) ) {
				$type = "%f";
			} elseif ( is_int( $value ) ) {
				$type = "%d";
			} elseif ( is_array( $value ) ) {
				$type = "";
				foreach ( $value as $v ) {
					if ( ! empty( $type ) ) {
						$type .= ", ";
					}
					if ( is_float( $v ) ) {
						$type .= "%f";
					} elseif ( is_int( $v ) ) {
						$type .= "%d";
					} else {
						$type .= "'%s'";
					}
				}
			}

			$compare     = trim( $col_data['compare'] );
			$compare_end = "";
			if ( is_array( $value ) ) {
				$compare     = "IN (";
				$compare_end = ")";
			} elseif ( ! in_array( $compare, array(
					"=",
					">",
					"<",
					">=",
					"<=",
					"LIKE"
				) ) || ( $compare == "LIKE" && $type != "'%s'" ) ) {
				$compare = "=";
			}

			if ( empty( $query_rows ) ) {
				$query_rows .= " WHERE ";
			} else {
				$query_rows .= " " . $relation . " ";
			}
			$query_rows .= $col_name . " " . $compare . " " . $type . " " . $compare_end;
			if ( is_array( $value ) ) {
				foreach ( $value as $v ) {
					$row_values[] = $v;
				}
			} else {
				$row_values[] = $value;
			}
		}

		$mode = 'results';
		if ( count( $cols ) == 1 ) {
			if ( $single ) {
				$mode = 'var';
			} else {
				$mode = 'col';
			}
		} elseif ( $single ) {
			$mode = 'row';
		}

		$orderby_query = "";
		if ( $orderby ) {
			$order         = 'DESC' === strtoupper( $order ) ? 'DESC' : 'ASC';
			$orderby_query = " ORDER BY $orderby $order";
		}

		$query = "SELECT " . $query_columns . " FROM " . $wpdb->$table_slug . $query_rows . $orderby_query . ";";
		if ( count( $row_values ) > 0 ) {
			$query = $wpdb->prepare( $query, $row_values );
		}

		if ( $mode == 'results' ) {
			return $wpdb->get_results( $query, $results_mode );
		}

		return call_user_func( array( $wpdb, 'get_' . $mode ), $query );
	}

	public static function tableToCSV( $table_slug, $charset = WPENON_DEFAULT_CHARSET ) {
		$columns      = self::getTableColumnNames( $table_slug );
		$results      = self::getResults( \WPENON\Util\Format::unprefix( $table_slug ), array(), array(), false, ARRAY_A );
		$csv_settings = self::_getCSVSettings();

		header( 'Content-Type: text/csv; charset=' . $charset );
		header( 'Content-Disposition: attachment; filename=' . \WPENON\Util\Format::unprefix( $table_slug ) . '.csv' );

		$output = fopen( 'php://output', 'w' );

		fputcsv( $output, \WPENON\Util\Format::csvEncode( $columns, $charset ), $csv_settings['terminated'], $csv_settings['enclosed'] );
		foreach ( $results as $row ) {
			fputcsv( $output, \WPENON\Util\Format::csvEncode( $row, $charset ), $csv_settings['terminated'], $csv_settings['enclosed'] );
		}

		fclose( $output );

		exit;
	}

	public static function csvToTable( $table_slug, $file, $charset = WPENON_DEFAULT_CHARSET ) {
		global $wpdb;

		ini_set( 'auto_detect_line_endings', '1' );

		$csv_settings = self::_getCSVSettings();

		$columns         = self::getTableColumns( $table_slug );
		$required_fields = count( $columns );

		$field_names = array();
		$data_format = array();
		foreach ( $columns as $column ) {
			$field_names[] = $column->Field;

			$type = strtolower( $column->Type );
			if ( strpos( $type, 'int' ) !== false ) {
				$data_format[] = '%d';
			} elseif ( strpos( $type, 'float' ) !== false ) {
				$data_format[] = '%f';
			} else {
				$data_format[] = '%s';
			}
		}

		$queries = array();

		$status  = true;
		$message = '';

		$file_handle  = fopen( $file, 'r' );
		$line_counter = 0;
		while ( ! feof( $file_handle ) ) {
			$raw_values = fgetcsv( $file_handle, 0, $csv_settings['terminated'], $csv_settings['enclosed'], $csv_settings['escaped'] );
			if ( count( $raw_values ) > 1 ) {
				$line_counter ++;
				if ( count( $raw_values ) != $required_fields ) {
					if ( count( $raw_values ) - 1 == $required_fields ) {
						unset( $raw_values[ count( $raw_values ) - 1 ] );
					} else {
						$message = sprintf( __( 'In Zeile %d der CSV-Datei befindet sich eine ungültige Anzahl Spalten.', 'wpenon' ), $line_counter );
						$status  = false;
						break;
					}
				}
				if ( $status === false ) {
					break;
				}
				$field_values = array();
				foreach ( $raw_values as $key => $value ) {
					if ( $data_format[ $key ] == '%d' ) {
						$value = intval( $value );
					} elseif ( $data_format[ $key ] == '%f' ) {
						$value = str_replace( ',', '.', $value );
						$value = floatval( $value );
					} else {
						$value = \WPENON\Util\Format::utf8Encode( $value, $charset );
					}
					$value = str_replace("\n","" , $value);
					$value = str_replace("\r","" , $value);
					$field_values[ $field_names[ $key ] ] = $value;
				}
				if ( $line_counter > 1 ) {
					$queries[] = $field_values;
				}
			}
		}
		fclose( $file_handle );

		$wpdb->show_errors();

		$i = 0;

		if ( $status === true ) {
			$table_name = $wpdb->$table_slug;
			$wpdb->query("TRUNCATE TABLE {$table_name}");

			$message = '';
			foreach ( $queries as $key => $data ) {
				$sql_status = $wpdb->insert( $wpdb->$table_slug, $data, $data_format );
				if ( $sql_status === false ) {
					$message .= sprintf( __( 'Beim Einfügen der Zeile %d in die Tabelle ist ein MySQL-Fehler aufgetreten.', 'wpenon' ), $key + 1 );
					$status  = false;
					$i++;
				}
			}
			if ( $status === true ) {
				$message = sprintf( __( 'Es wurden erfolgreich %d Zeilen in die Tabelle importiert.', 'wpenon' ), $line_counter - 1 );
			}
		}

		return array( $status, $message );
	}

	public static function getTableColumnNames( $table_slug ) {
		$names = array();

		$columns = self::getTableColumns( $table_slug );

		$schema = \WPENON\Model\TableManager::instance()->getTable( $table_slug )->getSchema();

		self::$_current_schema = $schema;

		foreach ( $columns as $column ) {
			if ( isset( $schema['fields'][ $column->Field ] ) ) {
				$names[] = html_entity_decode( preg_replace_callback( '/([A-Za-z]+)<sup>([0-9]+)<\/sup>/', array(
					__CLASS__,
					'_formatColumnName'
				), str_replace( array( '<sub>', '</sub>' ), '', $schema['fields'][ $column->Field ]['title'] ) ) );
			} else {
				$names[] = $column->Field;
			}
		}

		self::$_current_schema = null;

		return $names;
	}

	public static function _formatColumnName( $matches ) {
		if ( isset( $matches[1] ) ) {
			if ( isset( self::$_current_schema['asterisks'][ $matches[1] ] ) ) {
				return self::$_current_schema['asterisks'][ $matches[1] ];
			}

			return $matches[1];
		}

		return $matches[0];
	}

	public static function getTableColumns( $table_slug ) {
		global $wpdb;

		$query   = "DESCRIBE " . $wpdb->$table_slug . ";";
		$results = $wpdb->get_results( $query );

		return $results;
	}

	public static function registerTable( $table_slug ) {
		global $wpdb;

		$wpdb->tables[]    = $table_slug;
		$wpdb->$table_slug = $wpdb->prefix . $table_slug;

		return true;
	}

	public static function installTable( $table_slug, $fields, $primary_field ) {
		global $wpdb;

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		}

		$charset_collate = self::_getCharsetCollate();

		$query = "CREATE TABLE IF NOT EXISTS " . $wpdb->$table_slug . " (";
		foreach ( $fields as $fieldslug => $field ) {
			$default = '';
			if ( ! empty( $field['default'] ) ) {
				$default = " DEFAULT " . $field['default'];
			}
			$not_null = '';
			if ( $field['not_null'] ) {
				$not_null = " NOT NULL";
			}
			$auto_increment = '';
			if ( $field['auto_increment'] ) {
				$auto_increment = " AUTO_INCREMENT";
			}
			$query .= " " . $fieldslug . " " . $field['type'] . $default . $not_null . $auto_increment . " ,";
		}
		$query .= " PRIMARY KEY (" . $primary_field . ")";
		$query .= " ) " . $charset_collate . ";";

		return dbDelta( $query );
	}

	public static function regenerateTable( $table_slug ) {
		global $wpdb;

		if ( self::uninstallTable( $table_slug ) !== false ) {
			$schema = \WPENON\Model\TableManager::instance()->getTable( $table_slug )->getSchema();

			return self::installTable( $table_slug, $schema['fields'], $schema['primary_field'] );
		}

		return false;
	}

	public static function uninstallTable( $table_slug ) {
		global $wpdb;

		return $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->$table_slug . ";" );
	}

	public static function emptyTable( $table_slug ) {
		global $wpdb;

		return $wpdb->query( "TRUNCATE TABLE IF EXISTS " . $wpdb->$table_slug . ";" );
	}

	private static function _getCSVSettings() {
		return array(
			'terminated' => ';',
			'enclosed'   => '"',
			'escaped'    => '"',
		);
	}

	private static function _getCharsetCollate() {
		global $wpdb;

		$charset_collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$charset_collate = "DEFAULT CHARACTER SET " . $wpdb->charset;
			}
			if ( ! empty( $wpdb->collate ) ) {
				$charset_collate .= " COLLATE " . $wpdb->collate;
			}
		}

		return $charset_collate;
	}
}
