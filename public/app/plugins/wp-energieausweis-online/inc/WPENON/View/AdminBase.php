<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\View;

class AdminBase extends TemplateBase {
	/**
	 * Add meta boxes
	 *
	 * @param \WPENON\Model|Energieausweis $energieausweis
	 */
	public function addMetaBoxes( $energieausweis ) {
		$schema = $energieausweis->getSchema();

		$boxes = $schema->get( $energieausweis, false, true, true );

		foreach ( $boxes as $box_slug => $box ) {
			$context = 'normal';
			if ( $box_slug == 'private' ) {
				$context = 'side';
			}
			add_meta_box( $box_slug, $box['title'], array(
				$this,
				'displayMetaBox'
			), 'download', $context, 'high', $box );
			add_filter( 'postbox_classes_download_' . $box_slug, array( $this, '_addPluginMetaBoxClass' ), 10, 1 );
		}
	}

	public function displayMetaBox( $post = null, $fields = array() ) {
		$box = $fields['args'];

		if ( ! empty( $box['description'] ) ) {
			echo '<p class="description">' . $box['description'] . '</p>';
		}
		$this->displaySubTemplate( 'schemagroups', '', $box['groups'] );
	}

	public function saveMetaBoxes( $energieausweis ) {
		$schema = $energieausweis->getSchema();
		$schema->validateFields( $_POST, $energieausweis );

		do_action( 'wpenon_save_meta_boxes', $_POST, $energieausweis );

		if ( count( $energieausweis->errors ) > 0 ) {
			\WPENON\Util\Storage::storeErrors( $energieausweis->ID, $energieausweis->errors );
		}

		if ( count( $energieausweis->warnings ) > 0 ) {
			\WPENON\Util\Storage::storeWarnings( $energieausweis->ID, $energieausweis->warnings );
		}
	}

	public function displayNotices( $energieausweis ) {
		$schema   = $energieausweis->getSchema();
		$errors   = $schema->getErrors( $energieausweis );
		$warnings = $schema->getWarnings( $energieausweis );

		$this->displaySubTemplate( 'message-error', '', $errors );
		$this->displaySubTemplate( 'message-warning', '', $warnings );
	}

	public function _addPluginMetaBoxClass( $classes = array() ) {
		$classes[] = 'wpenon-metabox';

		return $classes;
	}

	public function getColumns( $columns = array() ) {
		return array(
			'cb'                => '<input type="checkbox"/>',
			'title'             => __( 'Name', 'easy-digital-downloads' ),
			'wpenon_type'       => __( 'Typ', 'wpenon' ),
			'wpenon_standard'   => __( 'Standard', 'wpenon' ),
			'wpenon_owner'      => __( 'Eigentümer', 'wpenon' ),
			'price'             => __( 'Price', 'easy-digital-downloads' ),
			'ausstellungsdatum' => __( 'Ausstellungsdatum', 'wpenon' ),
			'registriernummer'  => __( 'Registriernummer', 'wpenon' ),
			'date'              => __( 'Date', 'easy-digital-downloads' )
		);
	}

	public function getSortableColumns( $columns = array() ) {
		$columns['wpenon_type']       = 'wpenon_type';
		$columns['wpenon_standard']   = 'wpenon_standard';
		$columns['wpenon_owner']      = 'wpenon_email';
		$columns['ausstellungsdatum'] = 'ausstellungsdatum';
		$columns['registriernummer']  = 'registriernummer';

		return $columns;
	}

	public function renderColumn( $column_name, $energieausweis ) {
		switch ( $column_name ) {
			case 'wpenon_owner':
				$email      = $energieausweis->wpenon_email;
				$owner_data = $energieausweis->getOwnerData();
				$link       = 'mailto:' . $email;
				if ( isset( $owner_data['id'] ) ) {
					$link = add_query_arg( array(
						'post_type' => 'download',
						'page'      => 'edd-customers',
						'view'      => 'overview',
						'id'        => $owner_data['id'],
					), admin_url( 'edit.php' ) );
				}
				echo '<a href="' . $link . '">' . $email . '</a>';
				break;
			case 'wpenon_type':
			case 'wpenon_standard':
			case 'ausstellungsdatum':
			case 'registriernummer':
				$key    = 'formatted_' . $column_name;
				$output = $energieausweis->$key;
				$output = trim( $output );
				if ( ! $output ) {
					$output = '-';
				}
				echo $output;
				break;
		}
	}

	public function getActions( $actions, $energieausweis ) {
		$links = array();

		if ( isset( $actions['inline hide-if-no-js'] ) ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		$links['duplicate']               = __( 'Duplizieren', 'wpenon' );
		$links['confirmation-email-send'] = __( 'Bestätiungs-Email erneut zusenden', 'wpenon' );
		if ( $energieausweis->isFinalized() ) {
			if ( $energieausweis->isPaid() ) {
				if ( ! $energieausweis->isRegistered() ) {
					$links['xml-datenerfassung-send'] = __( 'Registriernummer zuweisen', 'wpenon' );
				} elseif ( ! $energieausweis->isDataSent() ) {
					$links['xml-zusatzdatenerfassung-send'] = __( 'Daten an DiBT senden', 'wpenon' );
				}
			}
			$links['xml-datenerfassung']       = array( __( 'Registrierungs-XML ansehen', 'wpenon' ), true );
			$links['xml-zusatzdatenerfassung'] = array( __( 'Daten-XML ansehen', 'wpenon' ), true );
			$links['data-pdf-view']            = array( __( 'Daten-PDF ansehen', 'wpenon' ), true );
			$links['pdf-view']                 = array( __( 'PDF ansehen', 'wpenon' ), true );
		}

		$permalink = $energieausweis->permalink;

		foreach ( $links as $action => $data ) {
			$anchor  = $data;
			$new_tab = false;
			if ( is_array( $data ) ) {
				$anchor = $data[0];
				if ( isset( $data[1] ) ) {
					$new_tab = $data[1];
				}
			}
			$actions[ $action ] = '<a href="' . add_query_arg( 'action', $action, $permalink ) . '"' . ( $new_tab ? ' target="_blank"' : '' ) . '>' . $anchor . '</a>';
		}

		return $actions;
	}

	public function getSortQueryVars( $orderby, $order = 'asc' ) {
		switch ( $orderby ) {
			case 'wpenon_email':
			case 'wpenon_type':
			case 'wpenon_standard':
			case 'ausstellungsdatum':
			case 'registriernummer':
				return array(
					'meta_key' => $orderby,
					'orderby'  => 'meta_value',
				);
			default:
		}

		return array();
	}

	public function getSearchQueryVars( $search ) {
		// Match a ZIP code.
		if ( preg_match( '/^([0-9]{5})$/', (string) $search ) ) {
			add_filter( 'posts_search', array( $this, '_overrideSearch' ), 10, 2 );

			return array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => 'adresse_plz',
						'value'   => (string) $search,
						'compare' => '=',
					),
				),
			);
		}

		if ( ! is_numeric( $search ) ) {
			add_filter( 'posts_search', array( $this, '_overrideSearch' ), 10, 2 );

			// Match a full address.
			if ( preg_match( '/^([^,]+), ([0-9]{5}) (.*)$/', $search, $matches ) ) {
				return array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'     => 'adresse_strassenr',
							'value'   => $matches[1],
							'compare' => '=',
						),
						array(
							'key'     => 'adresse_plz',
							'value'   => $matches[2],
							'compare' => '=',
						),
						array(
							'key'     => 'adresse_ort',
							'value'   => $matches[3],
							'compare' => '=',
						),
					),
				);
			}

			// Match a registry number.
			if ( preg_match( '/^([A-Z]{2})\-20([0-9]{2})\-([0-9]+)$/', $search ) ) {
				return array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'     => 'registriernummer',
							'value'   => $search,
							'compare' => 'LIKE',
						),
					),
				);
			}

			// Match an email address.
			if ( false !== strpos( $search, '@' ) ) {
				return array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'     => 'wpenon_email',
							'value'   => $search,
							'compare' => 'LIKE',
						),
					),
				);
			}
		}

		return array();
	}

	public function _overrideSearch( $search, $query ) {
		return '';
	}
}
