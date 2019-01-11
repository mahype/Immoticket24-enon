<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class TableListTable extends \WP_List_Table {
	protected $slug = '';

	/**
	 * @var Table
	 */
	protected $table = null;

	public function __construct( $slug, $table ) {
		parent::__construct( array(
			'plural' => $slug,
			'ajax'   => false,
			'screen' => $table->getHook(),
		) );
		$this->slug  = $slug;
		$this->table = $table;
	}

	public function get_columns() {
		$schema = $this->table->getSchema();

		$columns = array();
		foreach ( $schema['fields'] as $fieldslug => $field ) {
			$include = true;
			foreach ( $schema['filters'] as $filterslug => $filter ) {
				if ( $filter['type'] == 'columns' ) {
					$current_value = isset( $_GET[ $filterslug ] ) ? $_GET[ $filterslug ] : $filter['default'];
					if ( ! call_user_func( $filter['callback'], $current_value, $fieldslug ) ) {
						$include = false;
					}
				}
			}
			if ( $include ) {
				$prefixed_slug             = \WPENON\Util\Format::prefix( $fieldslug );
				$columns[ $prefixed_slug ] = $field['title'];
			}
		}

		return $columns;
	}

	public function get_sortable_columns() {
		$schema = $this->table->getSchema();

		$columns = array();
		foreach ( $schema['fields'] as $fieldslug => $field ) {
			if ( $field['sortable'] ) {
				$desc = false;
				if ( $field['descending'] ) {
					$desc = true;
				}
				$prefixed_slug             = \WPENON\Util\Format::prefix( $fieldslug );
				$columns[ $prefixed_slug ] = array( $fieldslug, $desc );
			}
		}

		return $columns;
	}

	protected function setup_columns() {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );
	}

	public function prepare_items() {
		global $wpdb;

		$schema = $this->table->getSchema();

		$this->setup_columns();

		$table_slug = $this->slug;

		$orderby = '';
		if ( isset( $_GET['orderby'] ) && ! empty( $_GET['orderby'] ) ) {
			$orderby = $_GET['orderby'];
		}

		$order = '';
		if ( ! empty( $orderby ) ) {
			$order = ( isset( $schema['fields'][ $orderby ] ) && $schema['fields'][ $orderby ]['descending'] ) ? 'DESC' : 'ASC';
			if ( isset( $_GET['order'] ) && ! empty( $_GET['order'] ) ) {
				$order = strtoupper( $_GET['order'] );
			}
		}

		if ( ! empty( $orderby ) && ! empty( $order ) ) {
			$orderby = " ORDER BY " . $orderby . " " . $order;
		} else {
			$orderby = "";
		}

		$where = "";
		if ( isset( $_GET['s'] ) ) {
			if ( empty( $where ) ) {
				$where .= " WHERE ";
			} else {
				$where .= " AND ";
			}
			$search_field = ! empty( $schema['search_field'] ) ? $schema['search_field'] : $schema['primary_field'];
			$where        .= self::_search_cb( $_GET['s'], $search_field, $schema['search_before'] );
		}
		foreach ( $schema['filters'] as $filterslug => $filter ) {
			if ( $filter['type'] == 'rows' ) {
				$current_value = isset( $_GET[ $filterslug ] ) ? $_GET[ $filterslug ] : $filter['default'];
				if ( empty( $where ) ) {
					$where .= " WHERE ";
				} else {
					$where .= " AND ";
				}
				$where .= call_user_func( $filter['callback'], $current_value );
			}
		}

		$query      = "SELECT COUNT(*) FROM " . $wpdb->$table_slug . $where . ";";
		$totalitems = $wpdb->get_var( $query );

		$paged = 1;
		if ( isset( $_GET['paged'] ) && absint( $_GET['paged'] ) > 1 ) {
			$paged = absint( $_GET['paged'] );
		}

		$totalpages = ceil( $totalitems / $schema['items_per_page'] );

		$offset = ( $paged - 1 ) * $schema['items_per_page'];

		$query .= " LIMIT %d,%d";

		$this->set_pagination_args( array(
			'total_items' => $totalitems,
			'total_pages' => $totalpages,
			'per_page'    => $schema['items_per_page'],
		) );

		$query       = "SELECT * FROM " . $wpdb->$table_slug . $where . $orderby . " LIMIT " . $offset . ", " . $schema['items_per_page'] . ";";
		$this->items = $wpdb->get_results( $query );
	}

	public function display_rows() {
		$schema = $this->table->getSchema();

		list( $columns, $hidden ) = $this->get_column_info();
		$primary_field = $schema['primary_field'];

		foreach ( $this->items as $key => $item ) {
			$class = '';
			if ( $key % 2 == 0 ) {
				$class = ' class="alternate"';
			}
			echo '<tr id="' . $this->slug . '_' . $item->$primary_field . '"' . $class . '>';
			foreach ( $columns as $column_slug => $column_title ) {
				$class = $column_slug . ' column-' . $column_slug;

				$style = '';
				if ( in_array( $column_slug, $hidden ) ) {
					$style = ' style="display:none;"';
				}

				if ( $column_slug == 'cb' ) {
					echo '<th scope="row" class="check-column">';
					echo '<label class="screen-reader-text" for="wpenon-cb-select-' . $item->$primary_field . '">' . __( 'Auswählen', 'wpenon' ) . '</label>';
					echo '<input id="wpenon-cb-select-' . $item->$primary_field . '" type="checkbox" name="' . \WPENON\Util\Format::unprefix( $this->slug ) . '[]" value="' . $item->$primary_field . '" />';
					echo '</th>';
				} else {
					echo '<td class="' . $class . '"' . $style . '>';
					$raw_slug = \WPENON\Util\Format::unprefix( $column_slug );
					if ( isset( $schema['fields'][ $raw_slug ]['format'] ) ) {
						echo call_user_func( $schema['fields'][ $raw_slug ]['format'], $item->$raw_slug );
					}
					echo '</td>';
				}
			}
			echo '</tr>';
		}
	}

	public function no_items() {
		if ( isset( $_GET['s'] ) ) {
			_e( 'Es konnten keine Ergebnisse für Ihre Suche gefunden werden.', 'wpenon' );
		} else {
			_e( 'Diese Tabelle ist im Moment leer.', 'wpenon' );
		}
	}

	public function asterisks() {
		$schema = $this->table->getSchema();

		if ( ! empty( $schema['asterisks'] ) ) {
			echo '<p style="margin-top:-30px;margin-bottom:30px;">';
			$counter = 0;
			foreach ( $schema['asterisks'] as $abbr => $meaning ) {
				$counter ++;
				echo '<sup>' . $counter . '</sup>' . $abbr . ' = ' . esc_html( $meaning ) . '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			echo '</p>';
		}
	}

	public function extra_tablenav( $which ) {
		if ( $which == 'top' ) {
			$this->advanced_filters( __( 'Filtern', 'wpenon' ) );
			$this->search_box( __( 'Suchen', 'wpenon' ), $this->slug . '-search-input' );
		}
	}

	public function advanced_filters( $text ) {
		$schema = $this->table->getSchema();

		if ( ! empty( $schema['filters'] ) && $this->has_items() ) {
			echo '<div class="alignleft actions">';
			foreach ( $schema['filters'] as $filterslug => $filter ) {
				$current_value = isset( $_GET[ $filterslug ] ) ? $_GET[ $filterslug ] : $filter['default'];
				echo '<select id="' . $filterslug . '" name="' . $filterslug . '">';
				foreach ( $filter['options'] as $value => $label ) {
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $current_value, $value, false ) . '>' . esc_html( $label ) . '</option>';
				}
				echo '</select>';
			}
			submit_button( $text, 'button', 'filter', false, array( 'id' => 'wpenon-filter-submit' ) );
			echo '</div>';
		}
	}

	public function search_box( $text, $input_id ) {
		if ( ! empty( $_REQUEST['s'] ) || $this->has_items() ) {
			if ( ! empty( $_REQUEST['orderby'] ) ) {
				echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
			}
			if ( ! empty( $_REQUEST['order'] ) ) {
				echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
			}
			echo '<p class="search-box">';
			echo '<label class="screen-reader-text" for="' . $input_id . '">' . $text . '</label>';
			echo '<input type="search" id="' . $input_id . '" name="s" value="';
			_admin_search_query();
			echo '" />';
			submit_button( $text, 'button', 'search', false, array( 'id' => 'wpenon-search-submit' ) );
			echo '</p>';
		}
	}

	protected function display_tablenav( $which ) {
		if ( 'bottom' == $which ) {
			echo '</div>';
		}

		parent::display_tablenav( $which );

		if ( 'top' == $which ) {
			echo '<div class="wpenon-list-table-wrapper">';
		}
	}

	protected function get_table_classes() {
		$classes   = parent::get_table_classes();
		$fixed_key = array_search( 'fixed', $classes );
		unset( $classes[ $fixed_key ] );

		return array_values( $classes );
	}

	public function table_actions() {
		$schema = $this->table->getSchema();

		wp_nonce_field( $this->slug . '-table-actions', 'wpenon-nonce' );

		do_action( 'wpenon_table_actions_fields_' . $this->slug );

		$encodings = \WPENON\Util\Format::getFileEncodings();

		echo '<h3>' . __( 'Tabelleninhalt ersetzen', 'wpenon' ) . '</h3>';
		if ( ! empty( $schema['import_text'] ) ) {
			echo '<p>' . $schema['import_text'] . '</p>';
		}

		echo '<h4>' . __( 'Export', 'wpenon' ) . '</h4>';
		echo '<p class="table-export">';
		echo '<label class="screen-reader-text" for="wpenon-table-export">' . __( 'CSV-Datei aus Tabelle exportieren', 'wpenon' ) . '</label>';
		echo '<label for="wpenon-table-export-charset">' . __( 'Zeichencodierung der Datei:', 'wpenon' ) . '</label> ';
		echo '<select id="wpenon-table-export-charset" name="csv-export-charset">';
		foreach ( $encodings as $value => $label ) {
			echo '<option value="' . $value . '"' . selected( $value, WPENON_DEFAULT_CHARSET ) . '>' . $label . '</option>';
		}
		echo '</select>';
		submit_button( __( 'CSV-Datei aus Tabelle exportieren', 'wpenon' ), 'button', 'csv-export', false, array( 'id' => 'wpenon-table-export-submit' ) );
		echo '</p>';

		echo '<h4>' . __( 'Import', 'wpenon' ) . '</h4>';
		echo '<p class="table-import">';
		echo '<label class="screen-reader-text" for="wpenon-table-import">' . __( 'CSV-Datei in Tabelle importieren', 'wpenon' ) . '</label>';
		echo '<input type="file" id="wpenon-table-import" name="csv-import" value="" />';
		echo '<label for="wpenon-table-import-charset">' . __( 'Zeichencodierung der Datei:', 'wpenon' ) . '</label> ';
		echo '<select id="wpenon-table-import-charset" name="csv-import-charset">';
		foreach ( $encodings as $value => $label ) {
			echo '<option value="' . $value . '"' . selected( $value, WPENON_DEFAULT_CHARSET ) . '>' . $label . '</option>';
		}
		echo '</select>';
		submit_button( __( 'CSV-Datei in Tabelle importieren', 'wpenon' ), 'button', 'csv-import', false, array( 'id' => 'wpenon-table-import-submit' ) );
		echo '</p>';
	}

	public function process_request() {
		$schema = $this->table->getSchema();

		$action       = $this->current_action();
		$filter       = $this->current_filter();
		$search       = $this->current_search();
		$table_action = $this->current_table_action();

		if ( $action || $filter || $search ) {
			check_admin_referer( 'bulk-' . $this->slug );

			$vars = array( 'page' => $this->slug );
			if ( isset( $_GET['paged'] ) ) {
				$vars['paged'] = $_GET['paged'];
			}
			if ( isset( $_GET['orderby'] ) ) {
				$vars['orderby'] = $_GET['orderby'];
			}
			if ( isset( $_GET['order'] ) ) {
				$vars['order'] = $_GET['order'];
			}

			if ( $action ) {
				$raw_slug = \WPENON\Util\Format::unprefix( $this->slug );
				$ids      = isset( $_GET[ $raw_slug ] ) ? $_GET[ $raw_slug ] : array();

				if ( ! is_array( $ids ) ) {
					$ids = array( $ids );
				}

				switch ( $action ) {
					default:
						break;
				}
			}

			if ( $filter ) {
				$include_search = true;
				foreach ( $schema['filters'] as $filterslug => $filter ) {
					if ( isset( $_GET[ $filterslug ] ) && ! empty( $_GET[ $filterslug ] ) ) {
						$vars[ $filterslug ] = $_GET[ $filterslug ];
						if ( $filter['type'] == 'rows' ) {
							$include_search = false;
						}
					}
				}
				if ( $include_search && isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
					$vars['s'] = $_GET['s'];
				}
			}

			if ( $search ) {
				foreach ( $schema['filters'] as $filterslug => $filter ) {
					if ( $filter['type'] == 'columns' && isset( $_GET[ $filterslug ] ) && ! empty( $_GET[ $filterslug ] ) ) {
						$vars[ $filterslug ] = $_GET[ $filterslug ];
					}
				}
				$vars['s'] = isset( $_GET['s'] ) ? $_GET['s'] : ' ';
			}

			wp_redirect( add_query_arg( $vars, get_admin_url( null, 'admin.php' ) ) );
			exit;
		} elseif ( $table_action ) {
			check_admin_referer( $this->slug . '-table-actions', 'wpenon-nonce' );

			$message_slugs  = array();
			$message_counts = array();

			do_action( 'wpenon_table_actions_process_' . $this->slug, $table_action, $message_slugs, $message_counts );

			switch ( $table_action ) {
				case 'csv-export':
					$charset = WPENON_DEFAULT_CHARSET;
					if ( isset( $_POST['csv-export-charset'] ) ) {
						$charset = $_POST['csv-export-charset'];
					}
					$this->table->export( $charset );
					exit;
					break;
				case 'csv-import':
					if ( ! isset( $_FILES['csv-import'] ) || $_FILES['csv-import']['error'] == 4 ) {
						$message_slugs[]                 = 'emptyfile';
						$message_raw_counts['emptyfile'] = 'yes';
					} else {
						$overrides = array(
							'test_form' => false,
							'test_type' => true,
							'mimes'     => array( 'csv' => 'text/plain' ),
						);
						$file      = wp_handle_upload( $_FILES['csv-import'], $overrides );
						if ( isset( $file['error'] ) ) {
							$message_slugs[]              = 'option';
							$message_raw_counts['option'] = 'yes';
							update_option( 'wpenon-upload-message', $file['error'] );
						} else {
							$url      = $file['url'];
							$type     = $file['type'];
							$file     = $file['file'];
							$filename = basename( $file );

							$id = wp_insert_attachment( array(
								'post_title'     => $filename,
								'post_content'   => $url,
								'post_mime_type' => $type,
								'guid'           => $url,
								'context'        => 'wpenon_import',
								'post_status'    => 'private',
							), $file );

							if ( ! file_exists( $file ) ) {
								$message_slugs[]                = 'notfound';
								$message_raw_counts['notfound'] = 'yes';
							} else {
								$charset = WPENON_DEFAULT_CHARSET;
								if ( isset( $_POST['csv-import-charset'] ) ) {
									$charset = $_POST['csv-import-charset'];
								}

								if ( $schema['rebuild_on_import'] !== false ) {
									$this->table->uninstall();

									if ( is_callable( $schema['rebuild_on_import'] ) ) {
										call_user_func( $schema['rebuild_on_import'], $file, $charset );
										$this->table->refreshSchema();
									}

									$this->table->install();
								}

								list( $status, $message ) = $this->table->import( $file, $charset );

								$message_slugs[]              = 'option';
								$message_raw_counts['option'] = 'yes';
								update_option( 'wpenon-upload-message', $message );
							}

							wp_delete_attachment( $id, true );
						}
					}
					break;
				default:
			}

			foreach ( $message_slugs as $message_slug ) {
				$prefixed_message_slug                    = $table_action . '-' . $message_slug;
				$message_counts[ $prefixed_message_slug ] = $message_raw_counts[ $message_slug ];
			}

			$vars = array_merge( array(
				'page'    => $this->slug,
				'updated' => 'updated',
			), $message_counts );

			wp_redirect( add_query_arg( $vars, get_admin_url( null, 'admin.php' ) ) );
			exit;
		}
	}

	public function current_table_action() {
		$table_actions = apply_filters( 'wpenon_table_actions_' . $this->slug, array( 'csv-export', 'csv-import' ) );
		if ( ! in_array( 'csv-export', $table_actions ) ) {
			$table_actions[] = 'csv-export';
		}
		if ( ! in_array( 'csv-import', $table_actions ) ) {
			$table_actions[] = 'csv-import';
		}
		foreach ( $table_actions as $table_action ) {
			if ( isset( $_POST[ $table_action ] ) ) {
				return $table_action;
			}
		}

		return false;
	}

	public function current_search() {
		if ( isset( $_GET['search'] ) ) {
			return true;
		}

		return false;
	}

	public function current_filter() {
		if ( isset( $_GET['filter'] ) ) {
			return true;
		}

		return false;
	}

	public function get_message( $message_slug, $data = '' ) {
		$message = apply_filters( 'wpenon_table_actions_get_message_' . $this->slug, '', $message_slug, $data );
		if ( ! empty( $message ) ) {
			return $message;
		}

		switch ( $message_slug ) {
			case 'csv-import-success':
				return __( 'Der Tabelleninhalt wurde erfolgreich mit den Inhalten der CSV-Datei ersetzt.', 'wpenon' );
			case 'csv-import-emptyfile':
				return __( 'Entweder wurde keine Datei zum Hochladen ausgewählt, oder die Datei ist leer.', 'wpenon' );
			case 'csv-import-notfound':
				return __( 'Die Datei konnte nicht ins WordPress-Upload-Verzeichnis hochgeladen werden.', 'wpenon' );
			case 'csv-import-option':
				$message = get_option( 'wpenon-upload-message', '' );
				delete_option( 'wpenon-upload-message' );

				return $message;
			default:
				break;
		}

		return false;
	}

	public function messages() {
		if ( isset( $_GET['updated'] ) ) {
			$messages = array();
			foreach ( $_GET as $message_slug => $data ) {
				$message = $this->get_message( $message_slug, $data );
				if ( $message !== false ) {
					$messages[] = $message;
				}
				$_SERVER['REQUEST_URI'] = remove_query_arg( $message_slug, $_SERVER['REQUEST_URI'] );
			}
			if ( $messages ) {
				echo '<div id="message" class="updated"><p>' . join( ' ', $messages ) . '</p></div>';
			}
			$_SERVER['REQUEST_URI'] = remove_query_arg( 'updated', $_SERVER['REQUEST_URI'] );
		}
	}

	private static function _search_cb( $s, $field_name, $before_flexible = true ) {
		return $field_name . " LIKE '" . ( $before_flexible ? "%" : "" ) . $s . "%'";
	}
}
