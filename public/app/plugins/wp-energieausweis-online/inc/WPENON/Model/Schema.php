<?php
/**
 * @version 1.0.2
 *
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

class Schema {
	private $schema = array();
	private $fields = array();

	private $validated = array();
	private $errors = array();
	private $warnings = array();

	private $recursion_callers = array();

	public function __construct( $schema ) {
		$this->schema = self::parseSchema( $schema );

		foreach ( $this->schema as $metabox_slug => $metabox ) {
			$private = false;
			if ( $metabox_slug == 'private' ) {
				$private = true;
			}
			foreach ( $metabox['groups'] as $group_slug => $group ) {
				foreach ( $group['fields'] as $field_slug => $field ) {
					$this->fields[ $field_slug ] = array_merge( $field, array( 'private' => $private ) );
				}
			}
		}
	}

	/**
	 * Getting Schema.
	 *
	 * @param null \WPENON\Model\Energieausweis
	 * @param bool $active_tab
	 * @param bool $formatted
	 * @param bool $include_private
	 *
	 * @return array
	 */
	public function get( $energieausweis = null, $active_tab = false, $formatted = false, $include_private = false ) {
		$processed_schema = $this->schema;

		if ( ! $include_private && isset( $processed_schema['private'] ) ) {
			unset( $processed_schema['private'] );
		}

		if ( $active_tab && isset( $processed_schema[ $active_tab ] ) ) {
			$processed_schema[ $active_tab ]['active'] = true;
		} elseif ( count( $processed_schema ) > 0 ) {
			reset( $processed_schema );
			$processed_schema[ key( $processed_schema ) ]['active'] = true;
		}

		if ( $energieausweis !== null ) {
			foreach ( $processed_schema as $metabox_slug => &$metabox ) {
				foreach ( $metabox['groups'] as $group_slug => &$group ) {
					foreach ( $group['fields'] as $field_slug => &$field ) {
						$field = $this->getField( $field_slug, $energieausweis, $formatted );
					}
				}
			}
		}

		return $processed_schema;
	}

	public function getFields( $energieausweis = null, $formatted = false, $include_private = false ) {
		$processed_fields = $this->fields;

		if ( ! $include_private ) {
			$processed_fields = array_filter( $processed_fields, array( $this, 'isPublicField' ) );
		}

		if ( $energieausweis !== null ) {
			foreach ( $processed_fields as $field_slug => &$field ) {
				$field = $this->getField( $field_slug, $energieausweis, $formatted );
			}
		}

		return $processed_fields;
	}

	public function getField( $field_slug, $energieausweis = null, $formatted = false ) {
		if ( isset( $this->fields[ $field_slug ] ) ) {
			$processed_field = $this->fields[ $field_slug ];

			if ( $energieausweis !== null ) {
				$cb_params = self::_getFieldParams( true );
				foreach ( $cb_params as $param => $default ) {
					if ( $this->_isCallback( $processed_field[ $param ] ) ) {
						$cb_value = $this->_executeCallback( $processed_field[ $param ], $energieausweis );
						if ( gettype( $cb_value ) !== gettype( $default ) && $param !== 'value' ) {
							new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'Die Callback-Funktion für das dynamische Feld %s existiert nicht oder gibt einen ungültigen Wert zurück.', 'wpenon' ), '<code>' . $field_slug . ':' . $param . '</code>' ), '1.0.0' );
							$cb_value = $default;
						}
						if ( $param == 'unit' ) {
							$cb_value = \WPENON\Util\Format::unit( $cb_value );
						} elseif ( $param == 'value' && $cb_value !== null ) {
							if ( isset( $processed_field[ $param ]['callback_hard'] ) && $processed_field[ $param ]['callback_hard'] ) {
								$processed_field['readonly'] = true;
							}
						}
						$processed_field[ $param ] = $cb_value;
					}
				}

				if ( $processed_field['value'] === null || ! ( isset( $processed_field[ $param ]['callback_hard'] ) && $processed_field[ $param ]['callback_hard'] ) || $processed_field['value'] === $processed_field['default'] ) {
					$value = isset( $energieausweis->$field_slug ) ? $energieausweis->$field_slug : null;
					if ( $value !== null ) {
						$processed_field['value'] = $value;
					}
				}

				if ( $formatted ) {
					$processed_field['value'] = $this->formatBasedOnType( $processed_field['value'], $processed_field['type'] );
				}

				$processed_field['done']    = is_a( $energieausweis, 'WPENON\Model\Energieausweis' ) && $energieausweis->is_progressed( $field_slug );
				$processed_field['error']   = isset( $energieausweis->errors[ $field_slug ] ) ? $energieausweis->errors[ $field_slug ] : '';
				$processed_field['warning'] = isset( $energieausweis->warnings[ $field_slug ] ) ? $energieausweis->warnings[ $field_slug ] : '';
			}

			return $processed_field;
		}

		return false;
	}

	public function getFormattedFieldValue( $field_slug, $energieausweis = null ) {
		if ( isset( $this->fields[ $field_slug ] ) ) {
			$value = '';
			if ( is_a( $energieausweis, '\WPENON\Model\Energieausweis' ) ) {
				$value = $energieausweis->$field_slug;
			} elseif ( $energieausweis !== null ) {
				$value = $energieausweis;
			}
			if ( $value !== null ) {
				switch ( $this->fields[ $field_slug ]['type'] ) {
					case 'select':
					case 'radio':
						if ( isset( $this->fields[ $field_slug ]['options'][ $value ] ) ) {
							$value = $this->fields[ $field_slug ]['options'][ $value ];
						}
						break;
					case 'multiselect':
					case 'multibox':
						$f = array();
						foreach ( $value as $v ) {
							if ( isset( $this->fields[ $field_slug ]['options'][ $v ] ) ) {
								$f[] = $this->fields[ $field_slug ]['options'][ $v ];
							} else {
								$f[] = $v;
							}
						}
						$value = $f;
						break;
					default:
						$value = $this->formatBasedOnType( $value, $this->fields[ $field_slug ]['type'], 'output' );
				}

				return $value;
			}
		}

		return false;
	}

	public function isField( $field_slug ) {
		if ( isset( $this->fields[ $field_slug ] ) ) {
			return true;
		}

		return false;
	}

	public function isPrivateField( $field ) {
		if ( is_string( $field ) ) {
			if ( isset( $this->fields[ $field ] ) ) {
				return $this->fields[ $field ]['private'];
			}
		} elseif ( is_array( $field ) ) {
			return $field['private'];
		}

		return false;
	}

	public function isPublicField( $field ) {
		return ! $this->isPrivateField( $field );
	}

	public function getErrors( $energieausweis ) {
		$errors = array();

		foreach ( $energieausweis->errors as $field_slug => $error ) {
			$errors[ $field_slug ] = array(
				'label'   => $this->fields[ $field_slug ]['label'],
				'message' => $error,
			);
		}

		return $errors;
	}

	public function getWarnings( $energieausweis ) {
		$warnings = array();

		foreach ( $energieausweis->warnings as $field_slug => $warning ) {
			$warnings[ $field_slug ] = array(
				'label'   => $this->fields[ $field_slug ]['label'],
				'message' => $warning,
			);
		}

		return $warnings;
	}

	public function validateFields( $raw, $energieausweis, $include_private = false ) {
		$processed_fields = $this->fields;

		if ( ! $include_private ) {
			$processed_fields = array_filter( $processed_fields, array( $this, 'isPublicField' ) );
		}

		foreach ( $processed_fields as $field_slug => $field ) {
			if ( $field['type'] != 'headline' ) {
				$this->_validateField( $raw, $energieausweis, $field_slug, $field );
			}
		}

		$results = array(
			'validated' => $this->validated,
			'errors'    => $this->errors,
			'warnings'  => $this->warnings,
		);

		$results = apply_filters( 'wpenon_validation_results', $results, $energieausweis );

		$this->validated = $results['validated'];
		$this->errors    = $results['errors'];
		$this->warnings  = $results['warnings'];

		foreach ( $this->validated as $field_slug => $value ) {
			$energieausweis->$field_slug = $value;
		}

		if ( is_a( $energieausweis, '\WPENON\Model\Energieausweis' ) ) {
			$energieausweis->checkValidationErrors( $this->errors, $this->warnings );
		} else {
			$energieausweis->errors   = $this->errors;
			$energieausweis->warnings = $this->warnings;
		}
	}

	private function _validateField( $raw, $energieausweis, $field_slug, $field ) {
		if ( ! isset( $this->validated[ $field_slug ] ) ) {
			if ( ! isset( $raw[ $field_slug ] ) ) {
				// make sure empty values are set too
				if ( in_array( $field['type'], array( 'multiselect', 'multibox' ) ) ) {
					$raw[ $field_slug ] = array();
				} elseif ( $field['type'] == 'checkbox' ) {
					$raw[ $field_slug ] = false;
				} else {
					$raw[ $field_slug ] = '';
				}
			}

			array_push( $this->recursion_callers, $field_slug );

			$cb_params = self::_getFieldParams( true, true );
			foreach ( $cb_params as $param => $default ) {
				if ( $this->_isCallback( $field[ $param ] ) ) {
					$dependencies = $this->_getCallbackDependencies( $field[ $param ] );
					$dependencies = array_merge( $dependencies, $field['validate_dependencies'] );

					// recursively validate values of the dependencies
					foreach ( $dependencies as $dep_field_slug ) {
						if ( array_search( $dep_field_slug, $this->recursion_callers ) === false ) {
							$this->_validateField( $raw, $energieausweis, $dep_field_slug, $this->fields[ $dep_field_slug ] );
						} else {
							new \WPENON\Util\Error( 'fatal', __METHOD__, sprintf( __( 'Es liegt eine wechselseitige Abhängigkeit im Feld %s vor, welche zu einer unendlichen Rekursion führt.', 'wpenon' ), '<code>' . $dep_field_slug . '</code>' ), '1.0.0' );
						}
					}

					if ( $param != 'value' || ( isset( $processed_field[ $param ]['callback_hard'] ) && $processed_field[ $param ]['callback_hard'] ) ) {
						$cb_value = $this->_executeCallback( $field[ $param ], $this->validated );
						if ( gettype( $cb_value ) !== gettype( $default ) ) {
							new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'Die Callback-Funktion für das dynamische Feld %s existiert nicht oder gibt einen ungültigen Wert zurück.', 'wpenon' ), '<code>' . $field_slug . ':' . $param . '</code>' ), '1.0.0' );
							$cb_value = $default;
						}
						$field[ $param ] = $cb_value;
					}
				}
			}

			array_pop( $this->recursion_callers );

			foreach ( $field['validate_dependencies'] as &$dep_field_slug ) {
				if ( isset( $this->validated[ $dep_field_slug ] ) ) {
					$dep_field_slug = $this->validated[ $dep_field_slug ];
				} else {
					$dep_field_slug = false;
				}
			}
			unset( $dep_field_slug );

			$validated = array();

			$field['value'] = $field['default'];
			if ( isset( $energieausweis->$field_slug ) ) {
				$field['value'] = $energieausweis->$field_slug;
			}

			$validated = \WPENON\Util\Validate::callback( $raw[ $field_slug ], $field );

			if ( $field['display'] ) {
				if ( $field['required'] && ! isset ( $validated['error'] ) ) {
					$_validated = \WPENON\Util\Validate::notempty( $validated['value'], $field );
					if ( isset( $_validated['error'] ) ) {
						$validated = $_validated;
					}
				}

				if ( isset( $validated['error'] ) ) {
					$this->errors[ $field_slug ] = $validated['error'];
				}
				if ( isset( $validated['warning'] ) ) {
					$this->warnings[ $field_slug ] = $validated['warning'];
				}
			}

			if ( is_a( $energieausweis, 'WPENON\Model\Energieausweis' ) && isset( $raw['_wpenon_progress'] ) ) {
				if ( ! is_array( $raw['_wpenon_progress'] ) ) {
					$raw['_wpenon_progress'] = explode( ',', $raw['_wpenon_progress'] );
				}

				if ( in_array( $field_slug, $raw['_wpenon_progress'], true ) && ! isset( $validated['error'] ) ) {
					$energieausweis->add_to_progress( $field_slug );
				} else {
					$energieausweis->remove_from_progress( $field_slug );
				}
			}

			$this->validated[ $field_slug ] = $validated['value'];
		}
	}

	public function getDynamicFields( $include_private = false ) {
		$processed_fields = $this->fields;

		if ( ! $include_private ) {
			$processed_fields = array_filter( $processed_fields, array( $this, 'isPublicField' ) );
		}

		$dynamic_fields = array();

		$cb_params = self::_getFieldParams( true );
		foreach ( $processed_fields as $field_slug => $field ) {
			foreach ( $cb_params as $param => $default ) {
				if ( $this->_isCallback( $field[ $param ] ) ) {
					$dependencies = $this->_getCallbackDependencies( $field[ $param ] );
					foreach ( $dependencies as $key => $dep ) {
						if ( ! isset( $dynamic_fields[ $dep ] ) ) {
							$dynamic_fields[ $dep ] = array();
						}
						$dynamic_fields[ $dep ][] = array(
							'mode'            => $param,
							'callback'        => $field[ $param ]['callback'],
							'callback_args'   => $field[ $param ]['callback_args'],
							'callback_deps'   => $dependencies,
							'callback_hard'   => isset( $field[ $param ]['callback_hard'] ) ? $field[ $param ]['callback_hard'] : false,
							'target_slug'     => $field_slug,
							'target_type'     => $field['type'],
							'target_required' => $field['required'],
						);
					}
				}
			}
		}

		return $dynamic_fields;
	}

	private function _isCallback( $field_param ) {
		return is_array( $field_param ) && isset( $field_param['callback'] ) && isset( $field_param['callback_args'] );
	}

	private function _getCallbackDependencies( $field_param ) {
		$dependencies = array();

		foreach ( $field_param['callback_args'] as $arg ) {
			if ( is_string( $arg ) && strpos( $arg, 'field::' ) === 0 ) {
				$dep = str_replace( 'field::', '', $arg );
				if ( isset( $this->fields[ $dep ] ) ) {
					$dependencies[] = $dep;
				}
			} elseif ( is_array( $arg ) ) {
				foreach ( $arg as $a ) {
					if ( is_string( $a ) && strpos( $a, 'field::' ) === 0 ) {
						$dep = str_replace( 'field::', '', $a );
						if ( isset( $this->fields[ $dep ] ) ) {
							$dependencies[] = $dep;
						}
					}
				}
			}
		}

		return $dependencies;
	}

	private function _executeCallback( $field_param, $values ) {
		if ( is_callable( $field_param['callback'] ) ) {
			$args = $field_param['callback_args'];

			foreach ( $args as &$arg ) {
				if ( is_string( $arg ) && strpos( $arg, 'field::' ) === 0 ) {
					$fslug = str_replace( 'field::', '', $arg );
					$fval  = false;
					if ( isset( $this->fields[ $fslug ] ) ) {
						$fval = $this->fields[ $fslug ]['default'];
						if ( is_object( $values ) && isset( $values->$fslug ) ) {
							$fval = $values->$fslug;
						} elseif ( is_array( $values ) && isset( $values[ $fslug ] ) ) {
							$fval = $values[ $fslug ];
						}
					}
					$arg = $fval;
				} elseif ( is_array( $arg ) ) {
					foreach ( $arg as $key => &$a ) {
						if ( is_string( $a ) && strpos( $a, 'field::' ) === 0 ) {
							$fslug = str_replace( 'field::', '', $a );
							$fval  = false;
							if ( isset( $this->fields[ $fslug ] ) ) {
								$fval = $this->fields[ $fslug ]['default'];
								if ( is_object( $values ) && isset( $values->$fslug ) ) {
									$fval = $values->$fslug;
								} elseif ( is_array( $values ) && isset( $values[ $fslug ] ) ) {
									$fval = $values[ $fslug ];
								}
							}
							$a = $fval;
						}
					}
				}
			}

			return call_user_func_array( $field_param['callback'], $args );
		}

		return null;
	}

	private function formatBasedOnType( $value, $type, $mode = 'input' ) {
		switch ( $type ) {
			case 'int':
				return \WPENON\Util\Format::int( $value );
			case 'float':
				return \WPENON\Util\Format::float( $value );
			case 'float_length':
			case 'float_length_wall':
				return \WPENON\Util\Format::float_length( $value );
			case 'date':
				if ( $mode == 'output' ) {
					return \WPENON\Util\Format::date( $value );
				}

				return $value;
			default:
				return $value;
		}
	}

	public static function parseSchema( $schema ) {
		foreach ( $schema as $metabox_slug => &$metabox ) {
			$metabox = wp_parse_args( $metabox, array(
				'title'       => '',
				'description' => '',
				'active'      => false,
				'groups'      => array(),
			) );

			foreach ( $metabox['groups'] as $group_slug => &$group ) {
				$group = wp_parse_args( $group, array(
					'title'       => '',
					'description' => '',
					'fields'      => array(),
				) );

				$group['fields'] = self::parseFields( $group['fields'] );
			}
		}

		return $schema;
	}

	public static function parseFields( $fields ) {
		foreach ( $fields as $field_slug => &$field ) {
			$field = wp_parse_args( $field, self::_getFieldParams() );

			if ( $field['type'] != 'headline' ) {
				if ( $field['min'] !== false && $field['min'] < 0 ) {
					$field['min'] = 0;
				}

				switch ( $field['type'] ) {
					case 'select':
					case 'radio':
						$default_validate = '\WPENON\Util\Validate::select';
						/*if ( count( $field['options'] ) > 0 ) {
						  $default_default = key( $field['options'] );
						} else {
						  $default_default = '';
						}*/
						$default_default = '';
						break;
					case 'multiselect':
					case 'multibox':
						$default_validate = '\WPENON\Util\Validate::multiselect';
						$default_default  = array();
						break;
					case 'checkbox':
						$default_validate = '\WPENON\Util\Validate::checkbox';
						$default_default  = false;
						break;
					case 'int':
						$default_validate = '\WPENON\Util\Validate::int';
						$default_default  = $field['min'] ? $field['min'] : 0;
						break;
					case 'float':
						$default_validate = '\WPENON\Util\Validate::float';
						$default_default  = $field['min'] ? $field['min'] : 0.0;
						break;
					case 'float_length':
						$default_validate = '\WPENON\Util\Validate::float_length';
						$default_default  = $field['min'] ? $field['min'] : 0.0;
						break;
					case 'float_length_wall':
						$default_validate = '\WPENON\Util\Validate::float_length_wall';
						$default_default  = $field['min'] ? $field['min'] : 0.0;
						break;
					case 'zip':
						$default_validate = '\WPENON\Util\Validate::zip';
						$default_default  = '';
						break;
					case 'email':
						$default_validate = '\WPENON\Util\Validate::email';
						$default_default  = '';
						break;
					default:
						$default_validate = '\WPENON\Util\Validate::text';
						$default_default  = '';
						break;
				}

				if ( empty( $field['validate'] ) ) {
					$field['validate'] = $default_validate;
				}
				if ( empty( $field['default'] ) ) {
					$field['default'] = $default_default;
				}

				if ( ! $field['value'] ) {
					$field['value'] = $field['default'];
				}
			}
		}

		return $fields;
	}

	private static function _getFieldParams( $callback_support = false, $validation = false ) {
		$params = array(
			'type'                  => 'text',
			'label'                 => '',
			'description'           => '',
			'value'                 => '',
			'default'               => '',
			'options'               => array(),
			'disabled_options'      => array(),
			'validate'              => '',
			'validate_dependencies' => array(),
			'required'              => false,
			'display'               => true,
			'readonly'              => false,
			'unit'                  => '',
			'min'                   => false,
			'max'                   => false,
		);

		if ( $callback_support ) {
			$cb_support = array( 'label', 'description', 'value', 'options', 'display', 'unit' );
			if ( $validation ) {
				$cb_support = array( 'options', 'display' );
			}
			$params = array_intersect_key( $params, array_flip( $cb_support ) );
		}

		return $params;
	}
}
