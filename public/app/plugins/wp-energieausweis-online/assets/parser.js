if ( typeof Array.prototype.map !== 'function' ) {
  Array.prototype.map = function( func ) {
    var len = this.length;

    if ( typeof func === 'function' ) {
      var res = new Array( len );
      var thisp = arguments[1];

      for ( var i = 0; i < len; i++ ) {
        if ( i in this ) {
          res[ i ] = func.call( thisp, this[ i ], i, this );
        }
      }

      return res;
    }
    return this;
  };
}

if ( typeof _wpenon_data === 'object' ) {
  _wpenon_data.parser = {
    parseInt: function( value ) {
      return parseInt( value, 10 );
    },
    parseFloat: function( value ) {
      if ( ! value ) {
        return 0.0;
      }
      if ( typeof value === 'number' ) {
        return parseFloat( value );
      }
      var decimal_sep = _wpenon_data.decimal_separator || '.';
      var thousands_sep = _wpenon_data.thousands_separator || ',';

      if ( decimal_sep === ',' && value.indexOf( decimal_sep ) > -1 ) {
        if ( ( thousands_sep === '.' || thousands_sep === ' ' ) && value.indexOf( thousands_sep ) > -1 ) {
          value = value.replace( thousands_sep, '' );
        } else if ( thousands_sep === '' && value.indexOf( '.' ) > -1 ) {
          value = value.replace( '.', '' );
        }
        value = value.replace( decimal_sep, '.' );
      } else if ( thousands_sep === ',' && value.indexOf( thousands_sep ) > -1 ) {
        value = value.replace( thousands_sep, '' );
      }

      value = value.replace( /[^0-9\.]/, '' );

      return parseFloat( value );
    },
    parseBoolean: function( value ) {
      if ( typeof value === 'boolean' ) {
        return value;
      }
      if ( typeof value === 'number' ) {
        if ( value > 0.0 ) {
          return true;
        }
        return false;
      }
      if ( typeof value === 'string' ) {
        if ( value.toLowerCase() === 'true' || value === '1' ) {
          return true;
        }
        return false;
      }
      return !!value;
    },
    parseString: function( value ) {
      if ( typeof value === 'object' ) {
        value = value[ Object.keys( value )[0] ];
      }
      return '' + value;
    },
    parseArray: function( value ) {
      if ( typeof value !== 'object' || typeof value.length === 'undefined' ) {
        var ret = [];
        if ( typeof value !== 'undefined' ) {
          ret.push( value );
        }
        return ret;
      }
      return value;
    },
    parseObject: function( value ) {
      if ( typeof value !== 'object' ) {
        return {};
      }
      if ( typeof value[0] !== 'undefined' ) {
        var obj = value.reduce( function( o, v, i ) {
          o[ i ] = v;
          return o;
        }, {});
        return obj;
      }
      return value;
    }
  };
}
