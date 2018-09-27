/*!
 * WP Energieausweis online - Version 1.0.0
 * 
 * Felix Arntz <felix-arntz@leaves-webdesign.com>
 */
if ( typeof _wpenon_data === 'object' ) {
  _wpenon_data.formatter = {
    formatInt: function( value ) {
      return parseInt( value, 10 );
    },
    formatFloat: function( value ) {
      var decimal_sep = _wpenon_data.decimal_separator || '.';
      return parseFloat( value ).toString().replace( '.', decimal_sep );
    },
    formatBoolean: function( value ) {
      if ( value ) {
        return 'true';
      }
      return 'false';
    },
    formatArray: function( value ) {
      return value.split( '|' );
    },
    formatUnit: function( unit, html_entity ) {
      if ( typeof html_entity === 'undefined' ) {
        html_entity = true;
      }
      var replacements = {
        '1': ( html_entity ? '&sup1;' : '¹' ),
        '2': ( html_entity ? '&sup2;' : '²' ),
        '3': ( html_entity ? '&sup3;' : '³' )
      };
      var properties = Object.keys( replacements );
      for ( var i in properties ) {
        if ( unit.indexOf( properties[ i ] ) > -1 ) {
          if ( unit.indexOf( replacements[ properties[ i ] ] ) < 0 ) {
            return unit.replace( properties[ i ], replacements[ properties[ i ] ] );
          }
        }
      }
      return unit;
    }
  };
}
