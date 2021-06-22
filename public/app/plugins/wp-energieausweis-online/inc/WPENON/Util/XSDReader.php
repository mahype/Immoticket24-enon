<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class XSDReader {
	protected $template = array();

	protected $file = '';
	protected $target_namespace = '';
	protected $transient_name = '';

	public function __construct( $file, $namespace, $transient_name ) {
		$this->file             = $file;
		$this->target_namespace = $namespace;
		$this->transient_name   = $transient_name;
	}

	public function read() {
		if ( ! $this->template ) {
			$xsd_arr = get_transient( $this->transient_name );
			$xsd_arr = false;

			if ( $xsd_arr !== false ) {
				$this->template = json_decode( $xsd_arr, true );
				
			} else {
				$this->template = self::parseFile( $this->file, $this->target_namespace );

				if ( $this->template ) {
					set_transient( $this->transient_name, json_encode( $this->template ), 4 * WEEK_IN_SECONDS );
				}
			}
		}

		return $this->template;
	}

	public static function parseFile( $file, $namespace ) {
		$file = str_replace( array( '.xsd', '.xml' ), '', $file );
		if ( ! file_exists( $file . '.xml' ) ) {
			if ( ! file_exists( $file . '.xsd' ) ) {
				return array();
			}
			$doc                     = new \DomDocument();
			$doc->preserveWhiteSpace = true;
			$doc->load( $file . '.xsd' );
			$doc->save( $file . '.xml' );
			if ( ! file_exists( $file . '.xml' ) ) {
				return array();
			}
		}

		$source_namespace = self::detectNamespace( $file . '.xml' );

		return self::parseXSDFile( $file . '.xml', $namespace, $source_namespace );
	}

	protected static function parseXSDFile( $file, $target_namespace = '', $source_namespace = 'xs' ) {
		$source = file_get_contents( $file );
		if ( ! empty( $source_namespace ) ) {
			$source = str_replace( $source_namespace . ':', '', $source );
		}

		$xsd = json_decode( json_encode( simplexml_load_string( $source ) ), true );

		// adjust choice elements
		$doc                     = new \DomDocument();
		$doc->preserveWhiteSpace = false;
		$doc->loadXML( $source );

		$choices = $doc->getElementsByTagName( 'choice' );

		$choices_before = array();

		foreach ( $choices as $choice ) {
			$parent       = $choice->parentNode;
			$children     = $parent->childNodes;
			$current_name = '';
			foreach ( $children as $key => $child ) {
				if ( is_a( $child, 'DOMElement' ) ) {
					if ( $child->tagName == 'choice' ) {
						$childnodes    = $child->childNodes;
						$first_element = '';
						foreach ( $childnodes as $childnode ) {
							if ( $childnode->tagName == 'element' ) {
								$first_element = $childnode->getAttribute( 'name' );
								break;
							}
						}
						$choices_before[ $first_element ][] = $current_name;
						break;
					}
					$current_name = $child->getAttribute( 'name' );
				}
			}
		}

		unset( $doc );

		return self::parseXSDComplexType( $xsd, $xsd, $choices_before, $target_namespace );
	}

	protected static function parseXSDComplexType( $current, $xsd, $choices_before = array(), $target_namespace = '' ) {
		$arr = array();

		$type = false;

		if ( isset( $current['@attributes'] ) ) {
			$arr = $current['@attributes'];

			if ( ! empty( $target_namespace ) && isset( $arr['type'] ) && substr( $arr['type'], 0, strlen( $target_namespace ) + 1 ) == ( $target_namespace . ':' ) ) {
				$complex_type = substr( $arr['type'], strlen( $target_namespace ) + 1 );
				$arr['type']  = '';

				$type = self::searchXSDByName( $complex_type, $xsd );
				if ( $type ) {
					if ( $type['@attributes']['type'] == 'simpleType' ) {
						$arr = self::parseXSDSimpleType( $arr, $type );
					} else {
						$arr['type'] = 'complexType';
						$arr['def']  = self::parseXSDComplexType( $type, $xsd, $choices_before, $target_namespace );
					}
				}
			}
		}

		if ( $type === false ) {
			/**
			 * Simple element
			 */
			if ( isset( $current['element'] ) ) {
				$arr['children']   = array();
				$arr['children'][] = self::parseXSDComplexType( $current['element'], $xsd, $choices_before, $target_namespace );
			/**
			 * Choice
			 */
			}
			} elseif ( isset( $current['choice'] ) && isset( $current['choice']['element'] ) ) {
				$arr['children'] = array();
				$choice_arr      = array();
				if ( isset( $current['choice']['element'][0] ) ) {
					foreach ( $current['choice']['element'] as $element ) {
						$choice                        = self::parseXSDComplexType( $element, $xsd, $choices_before, $target_namespace );
						$choice_arr[ $choice['name'] ] = $choice;
					}
				} else {
					$choice                        = self::parseXSDComplexType( $current['choice']['element'], $xsd, $choices_before, $target_namespace );
					$choice_arr[ $choice['name'] ] = $choice;
				}
				$first_element = key( $choice_arr );

				$arr['children'][] = array(
					'type' => 'choice',
					'def'  => $choice_arr,
				);
			/**
			 * Sequence
			 */				
			} elseif ( isset( $current['sequence'] ) ) {
				$arr['children'] = array();

				/**
				 * Element in sequence
				 */
				if ( isset( $current['sequence']['element'] ) ) {
					if ( isset( $current['sequence']['element'][0] ) ) {
						foreach ( $current['sequence']['element'] as $element ) {
							$arr['children'][] = self::parseXSDComplexType( $element, $xsd, $choices_before, $target_namespace );
						}
					} else {
						$arr['children'][] = self::parseXSDComplexType( $current['sequence']['element'], $xsd, $choices_before, $target_namespace );
					}
				}

				/**
				 * Multiple choices in sequence
				 */
				if( isset( $current['sequence']['choice'][0] ) ) {
					
					foreach ( $current['sequence']['choice'] AS $choiceSelection )
					{
						$choice_arr = array();

						/**
						 * Multiple Elements in choice
						 */
						if ( isset( $choiceSelection['element'][0] ) ) {
							foreach ( $choiceSelection['element'] as $element ) {
								$choice                        = self::parseXSDComplexType( $element, $xsd, $choices_before, $target_namespace );
								$choice_arr[ $choice['name'] ] = $choice;
							}
						/**
						 * Single Element in choice
						 */
						} elseif( isset( $choiceSelection['element'] ) ) {
							$choice                        = self::parseXSDComplexType( $choiceSelection['element'], $xsd, $choices_before, $target_namespace );
							$choice_arr[ $choice['name'] ] = $choice;
						}

						/**
						 * Sequence
						 * 
						 * $current['sequence']['choice'][$i]['sequence']
						 */
						if ( isset( $choiceSelection['sequence'] ) ) {
							$sequence_arr = array();
							foreach ( $choiceSelection['sequence'] as $subSequenceElement ) {
								if( isset( $subSequenceElement[0] ) )
								{
									foreach ( $subSequenceElement as $element ) 
									{
										$sequence                        = self::parseXSDComplexType( $element, $xsd, $choices_before, $target_namespace );
										$sequence_arr[ $sequence['name'] ] = $sequence;
									}
								} else {
									$sequence                        = self::parseXSDComplexType( $subSequenceElement['element'], $xsd, $choices_before, $target_namespace );
									$sequence_arr[ $sequence['name'] ] = $sequence;
								}
							}

							$choice_arr[] = array(
								'type' => 'complexType',
								'def'  => array(
									'children' => $sequence_arr
								)
							);
						}
						
						$first_element = key( $choice_arr );

						if( isset( $choices_before[ $first_element ] ) ) {
							$key = self::searchXSDElements( $arr['children'], $choices_before[ $first_element ], true );
							if ( $key !== false ) {
								$choice_arr = array(
									'type' => 'choice',
									'def'  => $choice_arr,
								);
								array_splice( $arr['children'], $key, 0, array( $choice_arr ) );
								$arr = $arr;
							}
						}
					}
				/**
				 * Single Choice in sequence
				 */
				} elseif ( isset( $current['sequence']['choice'] ) ) {
					$choice_arr = array();
					/**
					 * Multiple Elements
					 */
					if ( isset( $current['sequence']['choice']['element'][0] ) ) {
						foreach ( $current['sequence']['choice']['element'] as $element ) {
							$choice                        = self::parseXSDComplexType( $element, $xsd, $choices_before, $target_namespace );
							$choice_arr[ $choice['name'] ] = $choice;
						}
					/**
					 * Single Element
					 */
					} elseif( isset( $current['sequence']['choice']['element'] ) ) {
						$choice                        = self::parseXSDComplexType( $current['sequence']['choice']['element'], $xsd, $choices_before, $target_namespace );
						$choice_arr[ $choice['name'] ] = $choice;
					}
					$first_element = key( $choice_arr );

					$key = self::searchXSDElements( $arr['children'], $choices_before[ $first_element ], true );
					if ( $key !== false ) {
						$choice_arr = array(
							'type' => 'choice',
							'def'  => $choice_arr,
						);
						array_splice( $arr['children'], $key, 0, array( $choice_arr ) );
					}
				}
			} elseif ( isset( $current['complexType'] ) && ( ! isset( $current['complexType']['@attributes'] ) || ! isset( $current['complexType']['@attributes']['name'] ) ) ) {
				$arr['type'] = 'complexType';
				$arr['def']  = self::parseXSDComplexType( $current['complexType'], $xsd, $choices_before, $target_namespace );
			} elseif ( isset( $current['simpleType'] ) && ( ! isset( $current['simpleType']['@attributes'] ) || ! isset( $current['simpleType']['@attributes']['name'] ) ) ) {
				$arr['type'] = '';
				$arr         = self::parseXSDSimpleType( $arr, $current['simpleType'] );
			}
		}

		$arr['attributes'] = array();
		if ( isset( $current['attribute'] ) ) {
			if ( isset( $current['attribute'][0] ) ) {
				foreach ( $current['attribute'] as $attribute ) {
					$arr['attributes'][] = self::parseXSDAttribute( $attribute );
				}
			} else {
				$arr['attributes'][] = self::parseXSDAttribute( $current['attribute'] );
			}
		}

		return $arr;
	}

	protected static function parseXSDSimpleType( $arr, $simple_type ) {
		if ( isset( $simple_type['restriction'] ) ) {
			if ( isset( $simple_type['restriction']['enumeration'] ) ) {
				$arr['type']    = 'enum';
				$arr['options'] = array();
				foreach ( $simple_type['restriction']['enumeration'] as $item ) {
					if ( isset( $item['@attributes'] ) && isset( $item['@attributes']['value'] ) ) {
						$arr['options'][] = $item['@attributes']['value'];
					}
				}
			} elseif ( isset( $simple_type['restriction']['@attributes'] ) && isset( $simple_type['restriction']['@attributes']['base'] ) ) {
				switch ( $simple_type['restriction']['@attributes']['base'] ) {
					case 'decimal':
						$arr['type'] = 'decimal';
						if ( isset( $simple_type['restriction']['fractionDigits'] ) && isset( $simple_type['restriction']['fractionDigits']['@attributes'] ) && isset( $simple_type['restriction']['fractionDigits']['@attributes']['value'] ) ) {
							$arr['digits'] = $simple_type['restriction']['fractionDigits']['@attributes']['value'];
						}
						if ( isset( $simple_type['restriction']['minInclusive'] ) && isset( $simple_type['restriction']['minInclusive']['@attributes'] ) && isset( $simple_type['restriction']['minInclusive']['@attributes']['value'] ) ) {
							$arr['min'] = $simple_type['restriction']['minInclusive']['@attributes']['value'];
						}
						if ( isset( $simple_type['restriction']['maxInclusive'] ) && isset( $simple_type['restriction']['maxInclusive']['@attributes'] ) && isset( $simple_type['restriction']['maxInclusive']['@attributes']['value'] ) ) {
							$arr['max'] = $simple_type['restriction']['maxInclusive']['@attributes']['value'];
						}
						break;
					case 'int':
						$arr['type'] = 'int';
						if ( isset( $simple_type['restriction']['minInclusive'] ) && isset( $simple_type['restriction']['minInclusive']['@attributes'] ) && isset( $simple_type['restriction']['minInclusive']['@attributes']['value'] ) ) {
							$arr['min'] = $simple_type['restriction']['minInclusive']['@attributes']['value'];
						}
						if ( isset( $simple_type['restriction']['maxInclusive'] ) && isset( $simple_type['restriction']['maxInclusive']['@attributes'] ) && isset( $simple_type['restriction']['maxInclusive']['@attributes']['value'] ) ) {
							$arr['max'] = $simple_type['restriction']['maxInclusive']['@attributes']['value'];
						}
						break;
					case 'date':
						$arr['type'] = 'date';
						if ( isset( $simple_type['restriction']['minInclusive'] ) && isset( $simple_type['restriction']['minInclusive']['@attributes'] ) && isset( $simple_type['restriction']['minInclusive']['@attributes']['value'] ) ) {
							$arr['min'] = $simple_type['restriction']['minInclusive']['@attributes']['value'];
						}
						if ( isset( $simple_type['restriction']['maxInclusive'] ) && isset( $simple_type['restriction']['maxInclusive']['@attributes'] ) && isset( $simple_type['restriction']['maxInclusive']['@attributes']['value'] ) ) {
							$arr['max'] = $simple_type['restriction']['maxInclusive']['@attributes']['value'];
						}
						break;
					case 'string':
					default:
						$arr['type'] = 'string';
						if ( isset( $simple_type['restriction']['pattern'] ) && isset( $simple_type['restriction']['pattern']['@attributes'] ) && isset( $simple_type['restriction']['pattern']['@attributes']['value'] ) ) {
							$arr['pattern'] = $simple_type['restriction']['pattern']['@attributes']['value'];
						}
						if ( isset( $simple_type['restriction']['maxLength'] ) && isset( $simple_type['restriction']['maxLength']['@attributes'] ) && isset( $simple_type['restriction']['maxLength']['@attributes']['value'] ) ) {
							$arr['maxlength'] = $simple_type['restriction']['maxLength']['@attributes']['value'];
						}
				}
			}
		}

		return $arr;
	}

	protected static function parseXSDAttribute( $attribute ) {
		$arr = $attribute['@attributes'];

		if ( isset( $arr['use'] ) && $arr['use'] == 'required' ) {
			unset( $arr['use'] );
			$arr['required'] = true;
		} else {
			$arr['required'] = false;
		}

		return $arr;
	}

	protected static function searchXSDByName( $name, $xsd ) {
		if ( isset( $xsd['complexType'] ) ) {
			foreach ( $xsd['complexType'] as $key => $complex_type ) {
				if ( isset( $complex_type['@attributes'] ) && isset( $complex_type['@attributes']['name'] ) && $complex_type['@attributes']['name'] == $name ) {
					$complex_type['@attributes']['type'] = 'complexType';

					return $complex_type;
				}
			}
		}
		if ( isset( $xsd['simpleType'] ) ) {
			foreach ( $xsd['simpleType'] as $key => $simple_type ) {
				if ( isset( $simple_type['@attributes'] ) && isset( $simple_type['@attributes']['name'] ) && $simple_type['@attributes']['name'] == $name ) {
					$simple_type['@attributes']['type'] = 'simpleType';

					return $simple_type;
				}
			}
		}

		return false;
	}

	protected static function searchXSDElements( $elements, $search = array(), $prefer_last = false ) {
		$non_empty_search = array_values( array_filter( $search ) );
		$has_empty        = count( $non_empty_search ) !== count( $search );

		if ( ! empty( $non_empty_search ) ) {
			if ( $prefer_last ) {
				foreach ( $elements as $key => $element ) {
					for ( $i = count( $non_empty_search ) - 1; $i >= 0; $i -- ) {
						if ( $element['name'] === $non_empty_search[ $i ] ) {
							return $key + 1;
						}
					}
				}
			} else {
				foreach ( $elements as $key => $element ) {
					for ( $i = 0; $i <= count( $non_empty_search ) - 1; $i ++ ) {
						if ( $element['name'] == $non_empty_search[ $i ] ) {
							return $key + 1;
						}
					}
				}
			}
		}

		if ( $has_empty ) {
			return 0;
		}

		return false;
	}

	protected static function detectNamespace( $file ) {
		//TODO: actually detect the namespace from file
		return 'xs';
	}
}
