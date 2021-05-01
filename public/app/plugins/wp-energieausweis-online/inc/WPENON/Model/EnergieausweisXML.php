<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

class EnergieausweisXML extends \WPENON\Util\XSDReader {
	protected $output = '';
	protected $sanitized_namespace = '';

	protected $title = '';
	protected $type = 'bw';
	protected $standard = 'enev2013';

	protected $energieausweis = null;
	protected $mode = '';

	protected $namespace = 'ns1';

	public function __construct( $mode, $title, $type, $standard ) {
		$this->title    = $title;
		$this->type     = $type;
		$this->standard = $standard;

		$this->mode = $mode;

		switch ( $this->mode ) {
			case 'zusatzdatenerfassung':
			case 'datenerfassung':
				\WPENON\Model\EnergieausweisManager::loadMappings( 'xml-' . $this->mode, $this->standard );
				$target_namespace = 'mstns';
				if ( $this->mode == 'zusatzdatenerfassung' ) {
					$target_namespace = 'n1';
				}
				$xsd_file = \WPENON\Model\EnergieausweisManager::getXSDFile( $this->mode, $this->standard );
				parent::__construct( $xsd_file, $target_namespace, 'wpenon_xml_template_' . $this->mode . '_' . $this->standard );
				$this->read();
				print_r( $this->template );
				break;
			default:
				new \WPENON\Util\Error( 'fatal', __METHOD__, sprintf( __( 'Der Modus %s ist kein gültiger XML-Modus.', 'wpenon' ), $this->mode ), '1.0.0' );
		}
	}

	public function create( $energieausweis, $raw = false ) {
		if ( empty( $this->output ) ) {
			if ( is_a( $energieausweis, '\WPENON\Model\Energieausweis' ) && $this->template ) {
				$this->energieausweis = $energieausweis;
			}

			$this->sanitized_namespace = $this->namespace;

			$current = $this->template;

			if ( ! $raw ) {
				if ( ! empty( $this->sanitized_namespace ) && strpos( $this->sanitized_namespace, ':' ) !== strlen( $this->sanitized_namespace ) - 1 ) {
					$this->sanitized_namespace .= ':';
				}

				$this->output = $this->getXMLHeader();
			} else {
				$this->sanitized_namespace = '';
			}

			if ( isset( $current['targetNamespace'] ) ) {
				$name = 'xmlns';
				if ( ! empty( $this->sanitized_namespace ) ) {
					$name .= ':' . substr( $this->sanitized_namespace, 0, strlen( $this->sanitized_namespace ) - 1 );
				}

				$current['children'][0]['attributes'] = array(
					array(
						'name'     => $name,
						'type'     => 'string',
						'value'    => $current['targetNamespace'],
						'required' => true,
					),
				);
			}

			$this->outputElement( $current['children'][0], array() );
			exit;
		}
	}

	public function finalize( $output_mode = 'I' ) {
		switch ( $output_mode ) {
			case 'S':
				return $this->output;
			case 'D':
			case 'I':
			default:
				$disposition = 'inline';
				if ( $output_mode == 'D' ) {
					$disposition = 'attachment';
				}
				header( 'Content-Type: text/xml; charset=utf-8' );
				header( 'Content-Disposition: ' . $disposition . '; filename="' . $this->title . '.xml"' );
				echo $this->output;
				exit;
		}

		return $this->output;
	}

	public function getXMLHeader() {
		return '<?xml version="1.0" encoding="UTF-8"?>';
	}

	public function getOccurrencesData( $min_occurs, $max_occurs, $path ) {
		if ( $this->energieausweis !== null ) {
			$context = $this->getPathItem( $path, 0 );
			$index   = 0;
			if ( strpos( $context, '::' ) ) {
				$context = explode( '::', $context );
				$index   = absint( $context[1] );
				$context = $context[0];
			}

			return call_user_func( 'wpenon_get_enev_xml_' . $this->mode . '_data', $context, $index, $this->energieausweis, array(
				'mode' => 'occurrences',
				'min'  => $min_occurs,
				'max'  => $max_occurs
			) );
		}

		return false;
	}

	public function getAttributeData( $attribute, $path ) {
		if ( $this->energieausweis !== null ) {
			$context = $this->getPathItem( $path, 0 );
			$index   = 0;
			if ( strpos( $context, '::' ) ) {
				$context = explode( '::', $context );
				$index   = absint( $context[1] );
				$context = $context[0];
			}

			return call_user_func( 'wpenon_get_enev_xml_' . $this->mode . '_data', $context, $index, $this->energieausweis, array(
				'mode'      => 'attribute',
				'attribute' => $attribute
			) );
		}

		return false;
	}

	public function getChoiceData( $choices, $path ) {
		if ( $this->energieausweis !== null ) {
			$context = $this->getPathItem( $path, 0 );
			$index   = 0;
			if ( strpos( $context, '::' ) ) {
				$context = explode( '::', $context );
				$index   = absint( $context[1] );
				$context = $context[0];
			}

			return call_user_func( 'wpenon_get_enev_xml_' . $this->mode . '_data', $context, $index, $this->energieausweis, array(
				'mode'    => 'choice',
				'choices' => $choices
			) );
		}

		return false;
	}

	public function getValueData( $item, $path ) {
		if ( $this->energieausweis !== null ) {
			$context = $this->getPathItem( $path, 0 );
			$index   = 0;
			if ( strpos( $context, '::' ) ) {
				$context = explode( '::', $context );
				$index   = absint( $context[1] );
				$context = $context[0];
			}
			$value = call_user_func( 'wpenon_get_enev_xml_' . $this->mode . '_data', $context, $index, $this->energieausweis, array(
				'mode' => 'value',
				'item' => $item
			) );
			if ( $value === false ) {
				$context = $this->getPathItem( $path, 1 ) . '_' . $context;
				$value   = call_user_func( 'wpenon_get_enev_xml_' . $this->mode . '_data', $context, $index, $this->energieausweis, array(
					'mode' => 'value',
					'item' => $item
				) );
				if ( $value === false ) {
					$context = $this->getPathItem( $path, 2 ) . '_' . $context;
					$value   = call_user_func( 'wpenon_get_enev_xml_' . $this->mode . '_data', $context, $index, $this->energieausweis, array(
						'mode' => 'value',
						'item' => $item
					) );
				}
			}

			if ( $value === false ) {
				$value = $this->getDefaultValue( $item );
			} else {
				$value = $this->validateValue( $value, $item, $context );
			}

			return $value;
		}

		return false;
	}

	protected function getPathItem( $path = array(), $ancestor = 0 ) {
		$item = null;

		for ( $i = 0; $i <= $ancestor; $i ++ ) {
			if ( count( $path ) < 1 ) {
				return null;
			}
			if ( $path[ count( $path ) - 1 ] == 'CHOICE' ) {
				unset( $path[ count( $path ) - 1 ] );
				if ( count( $path ) < 1 ) {
					return null;
				}
			}
			$item = $path[ count( $path ) - 1 ];
			unset( $path[ count( $path ) - 1 ] );
		}

		return $item;
	}

	protected function outputElement( $current, $path = array() ) {
		$occurs = 1;

		print_r( $current['name'] . "\n" );

		if ( isset( $current['minOccurs'] ) || isset( $current['maxOccurs'] ) ) {
			$min_occurs = null;
			$max_occurs = null;

			if ( isset( $current['minOccurs'] ) ) {
				$min_occurs = intval( $current['minOccurs'] );
				if ( $min_occurs < 1 ) {
					$occurs = 0;
				} elseif ( $min_occurs > 1 ) {
					$occurs = $min_occurs;
				}
			}
			if ( isset( $current['maxOccurs'] ) ) {
				$max_occurs = intval( $current['maxOccurs'] );
			}

			$path_for_filter = $path;
			if ( isset( $current['name'] ) ) {
				$path_for_filter[] = $current['name'];
			} else {
				$path_for_filter[] = 'unknown';
			}

			$func_occurs = $this->getOccurrencesData( $min_occurs, $max_occurs, $path_for_filter );
			if ( $func_occurs !== false ) {
				$occurs = absint( $func_occurs );
			}
		}

		$attributes = '';
		if ( isset( $current['attributes'] ) ) {
			$path_for_filter = $path;
			if ( isset( $current['name'] ) ) {
				$path_for_filter[] = $current['name'];
			} else {
				$path_for_filter[] = 'unknown';
			}
			foreach ( $current['attributes'] as $attribute ) {
				if ( $attribute['required'] ) {
					$value = '';
					if ( isset( $attribute['value'] ) ) {
						$value = $attribute['value'];
					}
					$func_value = $this->getAttributeData( $attribute, $path_for_filter );
					if ( $func_value !== false ) {
						$value = $func_value;
					}
					$attributes .= ' ' . $attribute['name'] . '="' . $value . '"';
				}
			}
		}
		if ( isset( $current['def'] ) && isset( $current['def']['attributes'] ) ) {
			$path_for_filter = $path;
			if ( isset( $current['name'] ) ) {
				$path_for_filter[] = $current['name'];
			} else {
				$path_for_filter[] = 'unknown';
			}
			foreach ( $current['def']['attributes'] as $attribute ) {
				if ( $attribute['required'] ) {
					$value = '';
					if ( isset( $attribute['value'] ) ) {
						$value = $attribute['value'];
					}
					$func_value = $this->getAttributeData( $attribute, $path_for_filter );
					if ( $func_value !== false ) {
						$value = $func_value;
					}
					$attributes .= ' ' . $attribute['name'] . '="' . $value . '"';
				}
			}
		}

		$current_identifier = '';

		for ( $i = 0; $i < $occurs; $i ++ ) {
			if ( $current['type'] == 'choice' ) {
				$current_identifier = 'CHOICE';

				$path[] = $current_identifier;

				$choice = $this->getChoiceData( array_keys( $current['def'] ), $path );
				if ( $choice === false || ! isset( $current['def'][ $choice ] ) ) {
					reset( $current['def'] );
					$choice = key( $current['def'] ); // choose the first option
				}

				$this->outputElement( $current['def'][ $choice ], $path );
			} elseif ( $current['type'] == 'complexType' ) {
				$current_identifier = $current['name'] . '::' . $i;
				if ( $i > 0 ) {
					array_pop( $path );
				}

				$this->output .= '<' . $this->sanitized_namespace . $current['name'] . $attributes . '>';
				$path[]       = $current_identifier;
				if ( ! empty( $current['def']['children'] ) ) {
					foreach ( $current['def']['children'] as $child ) {
						$this->outputElement( $child, $path );
					}
				}
				$this->output .= '</' . $this->sanitized_namespace . $current['name'] . '>';
			} else {
				$current_identifier = $current['name'] . '::' . $i;
				if ( $i > 0 ) {
					array_pop( $path );
				}

				$path[] = $current_identifier;

				$value        = $this->getValueData( $current, $path );
				$this->output .= '<' . $this->sanitized_namespace . $current['name'] . $attributes . '>' . $value . '</' . $this->sanitized_namespace . $current['name'] . '>';
			}
		}
	}

	protected function validateValue( $value, $item, $context ) {
		switch ( $item['type'] ) {
			case 'date':
				if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
					new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'Fehler im Kontext %1$s: Der Wert %2$s ist kein gültiges XML-Datum.', 'wpenon' ), $context, $value ), '1.0.0' );
					$value = date( 'Y-m-d' );
				}
				if ( isset( $item['min'] ) && strtotime( $value ) < strtotime( $item['min'] ) ) {
					new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'Fehler im Kontext %1$s: Der Wert %2$s ist kleiner als der minimal erlaubte XML-Wert.', 'wpenon' ), $context, $value ), '1.0.0' );
					$value = $item['min'];
				}
				if ( isset( $item['max'] ) && strtotime( $value ) > strtotime( $item['max'] ) ) {
					new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'Fehler im Kontext %1$s: Der Wert %2$s ist größer als der maximal erlaubte XML-Wert.', 'wpenon' ), $context, $value ), '1.0.0' );
					$value = $item['max'];
				}
				break;
			case 'enum':
				if ( ! in_array( $value, $item['options'] ) ) {
					new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'Fehler im Kontext %1$s: Der Wert %2$s ist nicht in der XML-Auswahl vorhanden.', 'wpenon' ), $context, $value ), '1.0.0' );
					$value = $item['options'][0];
				}
				break;
			case 'decimal':
				$value = floatval( str_replace( ',', '', $value ) );
				if ( isset( $item['min'] ) && $value < floatval( $item['min'] ) ) {
					new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'Fehler im Kontext %1$s: Der Wert %2$s ist kleiner als der minimal erlaubte XML-Wert.', 'wpenon' ), $context, $value ), '1.0.0' );
					$value = floatval( $item['min'] );
				} elseif ( isset( $item['max'] ) && $value > floatval( $item['max'] ) ) {
					new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'Fehler im Kontext %1$s: Der Wert %2$s ist größer als der maximal erlaubte XML-Wert.', 'wpenon' ), $context, $value ), '1.0.0' );
					$value = floatval( $item['max'] );
				}
				$digits = isset( $item['digits'] ) ? intval( $item['digits'] ) : 0;
				$value  = number_format( $value, $digits, '.', '' );
				break;
			case 'int':
				$value = intval( str_replace( ',', '', $value ) );
				if ( isset( $item['min'] ) && $value < intval( $item['min'] ) ) {
					new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'Fehler im Kontext %1$s: Der Wert %2$s ist kleiner als der minimal erlaubte XML-Wert.', 'wpenon' ), $context, $value ), '1.0.0' );
					$value = intval( $item['min'] );
				} elseif ( isset( $item['max'] ) && $value > intval( $item['max'] ) ) {
					new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'Fehler im Kontext %1$s: Der Wert %2$s ist größer als der maximal erlaubte XML-Wert.', 'wpenon' ), $context, $value ), '1.0.0' );
					$value = intval( $item['max'] );
				}
				$value = number_format( $value, 0, '.', '' );
				break;
			case 'boolean':
				if ( strtolower( $value ) == 'true' ) {
					$value = 'true';
				} elseif ( strtolower( $value ) == 'false' ) {
					$value = 'false';
				} else {
					new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'Fehler im Kontext %1$s: Der Wert %2$s ist kein gültiger XML-Boolean.', 'wpenon' ), $context, $value ), '1.0.0' );
					$value = 'false';
				}
				break;
			case 'string':
			default:
				$value = html_entity_decode( $value );
				if ( isset( $item['pattern'] ) ) {
					if ( ! preg_match( '/^' . $item['pattern'] . '$/', $value ) ) {
						new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'Fehler im Kontext %1$s: Der Wert %2$s entspricht nicht dem nötigen XML-Pattern.', 'wpenon' ), $context, $value ), '1.0.0' );
						$value = 'INVALID';
					}
					/*$subpatterns = explode( '|', $item['pattern'] );
						  if ( count( $subpatterns ) > 0 ) {
							 $pattern = $subpatterns[ count( $subpatterns ) - 1 ];
							 $pattern = trim( $pattern, '()' );
							 if ( empty( $pattern ) ) {
								$value = '';
							 }
						  }*/
				}
				if ( isset( $item['maxlength'] ) && strlen( $value ) > intval( $item['maxlength'] ) ) {
					$value = substr( $value, 0, intval( $item['maxlength'] ) );
				}
		}

		return $value;
	}

	protected function getDefaultValue( $item ) {
		$value = '';

		switch ( $item['type'] ) {
			case 'date':
				$value = date( 'Y-m-d' );
				break;
			case 'enum':
				$value = $item['options'][0];
				break;
			case 'decimal':
				$value = 215.6233;
				if ( isset( $item['min'] ) && $value < floatval( $item['min'] ) ) {
					$value = floatval( $item['min'] );
				}
				if ( isset( $item['max'] ) && $value > floatval( $item['max'] ) ) {
					$value = floatval( $item['max'] );
				}
				if ( isset( $item['digits'] ) ) {
					$value = number_format( $value, intval( $item['digits'] ), '.', '' );
				}
				break;
			case 'boolean':
				$value = 'false';
				break;
			case 'string':
			default:
				$value = 'DEFAULT';
				if ( isset( $item['maxlength'] ) && strlen( $value ) > intval( $item['maxlength'] ) ) {
					$value = substr( $value, 0, intval( $item['maxlength'] ) );
				}
		}

		return $value;
	}
}
