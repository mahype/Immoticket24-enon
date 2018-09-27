if ( typeof _wpenon_data === 'object' ) {

  // dynamic functions
  if ( typeof _wpenon_data.dynamic_functions.wpenon_show_on_bool_compare === 'undefined' ) {
    _wpenon_data.dynamic_functions.wpenon_show_on_bool_compare = function( value, required_values, relation ) {
      if ( typeof relation === 'undefined' ) {
        relation = 'AND';
      }

      value = _wpenon_data.parser.parseArray( value );
      required_values = _wpenon_data.parser.parseArray( required_values );

      value = value.map( _wpenon_data.parser.parseBoolean );
      required_values = required_values.map( _wpenon_data.parser.parseBoolean );

      var results = [];
      for ( var key in value ) {
        if ( typeof required_values[ key ] !== 'undefined' && value[ key ] === required_values[ key ] ) {
          results.push( value[ key ] );
        }
      }

      if ( relation.toUpperCase() === 'OR' ) {
        if ( results.length > 0 ) {
          return true;
        }
        return false;
      } else {
        if ( results.length === value.length ) {
          return true;
        }
        return false;
      }
    }
  }
  if ( typeof _wpenon_data.dynamic_functions.wpenon_show_on_number_higher === 'undefined' ) {
    _wpenon_data.dynamic_functions.wpenon_show_on_number_higher = function( value, number_to_compare, allow_equal ) {
      if ( typeof allow_equal === 'undefined' ) {
        allow_equal = true;
      }

      value = _wpenon_data.parser.parseFloat( value );
      number_to_compare = _wpenon_data.parser.parseFloat( number_to_compare );

      if ( value > number_to_compare ) {
        return true;
      } else if ( value === number_to_compare && allow_equal ) {
        return true;
      }
      return false;
    }
  }
  if ( typeof _wpenon_data.dynamic_functions.wpenon_show_on_number_lower === 'undefined' ) {
    _wpenon_data.dynamic_functions.wpenon_show_on_number_lower = function( value, number_to_compare, allow_equal ) {
      if ( typeof allow_equal === 'undefined' ) {
        allow_equal = true;
      }

      value = _wpenon_data.parser.parseFloat( value );
      number_to_compare = _wpenon_data.parser.parseFloat( number_to_compare );

      if ( value < number_to_compare ) {
        return true;
      } else if ( value === number_to_compare && allow_equal ) {
        return true;
      }
      return false;
    }
  }
  if ( typeof _wpenon_data.dynamic_functions.wpenon_show_on_array_whitelist === 'undefined' ) {
    _wpenon_data.dynamic_functions.wpenon_show_on_array_whitelist = function( value, whitelist ) {
      whitelist = _wpenon_data.parser.parseArray( whitelist );

      if ( whitelist.indexOf( value ) > -1 ) {
        return true;
      }
      return false;
    }
  }
  if ( typeof _wpenon_data.dynamic_functions.wpenon_show_on_array_blacklist === 'undefined' ) {
    _wpenon_data.dynamic_functions.wpenon_show_on_array_blacklist = function( value, blacklist ) {
      blacklist = _wpenon_data.parser.parseArray( blacklist );

      if ( blacklist.indexOf( value ) < 0 ) {
        return true;
      }
      return false;
    }
  }
  if ( typeof _wpenon_data.dynamic_functions.wpenon_show_on_array_dynamic_whitelist === 'undefined' ) {
    _wpenon_data.dynamic_functions.wpenon_show_on_array_dynamic_whitelist = function( value, dependency, whitelists ) {
      if ( typeof whitelists[ dependency ] !== 'undefined' ) {
        var whitelist = _wpenon_data.parser.parseArray( whitelists[ dependency ] );

        if ( whitelist.indexOf( value ) > -1 ) {
          return true;
        }
      }
      return false;
    }
  }
  if ( typeof _wpenon_data.dynamic_functions.wpenon_show_on_array_dynamic_blacklist === 'undefined' ) {
    _wpenon_data.dynamic_functions.wpenon_show_on_array_dynamic_blacklist = function( value, dependency, blacklists ) {
      if ( typeof blacklists[ dependency ] !== 'undefined' ) {
        var blacklist = _wpenon_data.parser.parseArray( blacklists[ dependency ] );

        if ( blacklist.indexOf( value ) < 0 ) {
          return true;
        }
        return false;
      }
      return true;
    }
  }
  if ( typeof _wpenon_data.dynamic_functions.wpenon_get_value_by_field === 'undefined' ) {
    _wpenon_data.dynamic_functions.wpenon_get_value_by_field = function( value, parse_type ) {
      if ( typeof parse_type === 'undefined' ) {
        parse_type = 'string';
      }
      parse_type = 'parse' + parse_type.charAt(0).toUpperCase() + parse_type.slice( 1 );
      if ( typeof _wpenon_data.parser[ parse_type ] === 'function' ) {
        return _wpenon_data.parser[ parse_type ].apply( null, [ value ] );
      }
      return value;
    }
  }

  if ( typeof _wpenon_data.dynamic_functions.wpenon_get_value_by_whitelist === 'undefined' ) {
    _wpenon_data.dynamic_functions.wpenon_get_value_by_whitelist = function( value, whitelist, parse_type ) {
      if ( typeof parse_type === 'undefined' ) {
        parse_type = 'string';
      }
      parse_type = 'parse' + parse_type.charAt(0).toUpperCase() + parse_type.slice( 1 );
      whitelist = _wpenon_data.parser.parseObject( whitelist );
      if ( typeof whitelist[ value ] !== 'undefined' ) {
        if ( typeof _wpenon_data.parser[ parse_type ] === 'function' ) {
          return _wpenon_data.parser[ parse_type ].apply( null, [ whitelist[ value ] ] );
        }
        return whitelist[ value ];
      }
      return null;
    }
  }

  if ( typeof _wpenon_data.dynamic_functions.wpenon_get_value_by_dynamic_whitelist === 'undefined' ) {
    _wpenon_data.dynamic_functions.wpenon_get_value_by_dynamic_whitelist = function( value, dependency, whitelists, parse_type ) {
      if ( typeof parse_type === 'undefined' ) {
        parse_type = 'string';
      }
      parse_type = 'parse' + parse_type.charAt(0).toUpperCase() + parse_type.slice( 1 );
      if ( typeof whitelists[ dependency ] !== 'undefined' ) {
        var whitelist = _wpenon_data.parser.parseObject( whitelists[ dependency ] );

        if ( typeof whitelist[ value ] !== 'undefined' ) {
          if ( typeof _wpenon_data.parser[ parse_type ] === 'function' ) {
            return _wpenon_data.parser[ parse_type ].apply( null, [ whitelist[ value ] ] );
          }
          return whitelist[ value ];
        }
      }
      return null;
    }
  }

  if ( typeof _wpenon_data.dynamic_functions.wpenon_get_value_by_sum === 'undefined' ) {
    _wpenon_data.dynamic_functions.wpenon_get_value_by_sum = function( sum, dependency_values, dependency_statuses, cancel ) {
      if ( typeof dependency_values !== 'object' ) {
        dependency_values = {};
      }
      if ( typeof dependency_statuses !== 'object' ) {
        dependency_statuses = {};
      }
      if ( typeof cancel === 'undefined' ) {
        cancel = false;
      } else {
        cancel = _wpenon_data.parser.parseBoolean( cancel );
      }

      var parse_type = 'parseInt';
      if ( sum % 1 !== 0 ) {
        parse_type = 'parseFloat';
      }
      var zero = _wpenon_data.parser[ parse_type ].apply( null, [ 0.0 ] );
      var properties = Object.keys( dependency_values );

      for ( var i in properties ) {
        if ( typeof dependency_statuses[ properties[ i ] ] === 'undefined' || _wpenon_data.parser.parseBoolean( dependency_statuses[ properties[ i ] ] ) ) {
          sum -= _wpenon_data.parser[ parse_type ].apply( null, [ dependency_values[ properties[ i ] ] ] );
        } else if ( cancel ) {
          break;
        }
      }
      
      if ( sum < zero ) {
        return zero;
      }
      return sum;
    }
  }

  // allows to override the default scroll offset (might be needed by specific themes)
  if ( typeof _wpenon_data.dynamic_functions.get_scroll_offset === 'undefined' ) {
    _wpenon_data.dynamic_functions.get_scroll_offset = function() {
      return 0;
    };
  }

}