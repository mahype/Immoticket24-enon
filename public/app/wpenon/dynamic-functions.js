jQuery( document ).ready( function( $ ) {
  if ( $( '.immoticket24-grundriss-bild' ).length > 0 ) {
    $( '#grundriss_form' ).on( 'change', function() {
      var form = $( this ).val();
      if ( form && form.length ) {
        form = 'grundriss_' + form;
      } else {
        form = 'grundrisse';
      }
      $( '.immoticket24-grundriss-bild' ).each( function() {
        var src = $( this ).attr( 'src' ).replace( /grundrisse|grundriss_(a|b|c|d|e|f|g|h)/, form );
        $( this ).attr( 'src', src );
      });
    });
  }

  if ( $( '.immoticket24-anbau-bild' ).length > 0 ) {
    $( '#anbau_form' ).on( 'change', function() {
      var form = $( this ).val();
      if ( form && form.length ) {
        form = 'anbau_' + form;
      } else {
        form = 'anbauformen';
      }
      $( '.immoticket24-anbau-bild' ).each( function() {
        var src = $( this ).attr( 'src' ).replace( /anbauformen|anbau_(a|b)/, form );
        $( this ).attr( 'src', src );
      });
    });
  }

  if ( $( '#fenster_manuell' ).length > 0 ) {
    var $fenster_manuell = $( '#fenster_manuell' );
    var $fenster_grundriss_wrap = $fenster_manuell.parent().parent().next();

    if ( ! $fenster_manuell.prop( 'checked' ) ) {
      $fenster_grundriss_wrap.hide();
    }

    $fenster_manuell.on( 'change', function() {
      if ( $fenster_manuell.prop( 'checked' ) ) {
        $fenster_grundriss_wrap.show();
      } else {
        $fenster_grundriss_wrap.hide();
      }
    });
  }
});

if ( typeof _wpenon_data === 'object' ) {

  _wpenon_data.dynamic_functions.get_scroll_offset = function() {
    if ( jQuery( '.navigation-bar-fixed' ).length > 0 && jQuery( '.navigation-bar-fixed' ).is( ':visible' ) ) {
      return jQuery( '.navigation-bar-fixed' ).height();
    }
    return 0;
  };

  _wpenon_data.dynamic_functions.wpenon_immoticket24_show_wand = function( grundriss, wand, nachbar ) {
    if ( typeof nachbar === 'undefined' ) {
      nachbar = false;
    }
    nachbar = _wpenon_data.parser.parseBoolean( nachbar );
    if ( ! nachbar ) {
      var formen = _wpenon_data.grundriss_formen;
      if ( typeof formen[ grundriss ] !== 'undefined' && typeof formen[ grundriss ][ wand ] !== 'undefined' ) {
        return true;
      }
    }
    return false;
  };

  _wpenon_data.dynamic_functions.wpenon_immoticket24_show_anbauwand = function( grundriss, wand, anbau ) {
    if ( typeof anbau === 'undefined' ) {
      anbau = false;
    }
    anbau = _wpenon_data.parser.parseBoolean( anbau );
    if ( anbau ) {
      var formen = _wpenon_data.anbau_formen;
      if ( typeof formen[ grundriss ] !== 'undefined' && typeof formen[ grundriss ][ wand ] !== 'undefined' ) {
        return true;
      }
    }
    return false;
  };

  _wpenon_data.dynamic_functions.wpenon_immoticket24_show_wand_porenbeton_bedarf = function( grundriss, a, b, c, d, e, f, g, h ) {
    var daemmungen = [ a, b, c, d, e, f, g, h ];
    var waende     = [ 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h' ];

    var formen = _wpenon_data.grundriss_formen;
    if ( typeof formen[ grundriss ] === 'undefined' ) {
      return false;
    }

    var form = formen[ grundriss ];
    for ( var i in waende ) {
      if ( typeof form[ waende[ i ] ] !== 'undefined' && parseInt( daemmungen[ i ], 10 ) > 0 ) {
        return false;
      }
    }

    return true;
  };

  _wpenon_data.dynamic_functions.wpenon_immoticket24_show_wand_porenbeton_verbrauch = function( daemmung ) {
    if ( parseInt( daemmung, 10 ) === 0 ) {
      return true;
    }

    return false;
  };

  _wpenon_data.dynamic_functions.wpenon_immoticket24_calculate_wand = function( grundriss, wand, a, b, c, d, e, f, g, h ) {
    if ( typeof wand === 'undefined' ) {
      wand = '';
    }
    var waende = {
      a: a,
      b: b,
      c: c,
      d: d,
      e: e,
      f: f,
      g: g,
      h: h
    };
    var formen = _wpenon_data.grundriss_formen;
    if ( typeof formen[ grundriss ] !== 'undefined' && typeof formen[ grundriss ][ wand ] !== 'undefined' ) {
      var formel = formen[ grundriss ][ wand ][0];
      if ( formel !== true ) {
        formel = formel.split( ' ' );
        var rechnung = 0.0;
        var current_operator = '+';
        for ( var i in formel ) {
          var formel_part = formel[ i ];
          switch ( formel_part ) {
            case '+':
            case '-':
              current_operator = formel_part;
              break;
            default:
              switch ( current_operator ) {
                case '+':
                  rechnung += _wpenon_data.parser.parseFloat( waende[ formel_part ] );
                  break;
                case '-':
                  rechnung -= _wpenon_data.parser.parseFloat( waende[ formel_part ] );
                  break;
                default:
              }
          }
        }
        if ( rechnung < 0.0 ) {
          rechnung = 0.0;
        }
        return rechnung;
      }
    }
    return null;
  };

  _wpenon_data.dynamic_functions.wpenon_immoticket24_calculate_anbauwand = function( grundriss, wand, b, t, s1, s2 ) {
    if ( typeof wand === 'undefined' ) {
      wand = '';
    }
    var waende = {
      b: b,
      t: t,
      s1: s1,
      s2: s2
    };
    var formen = _wpenon_data.anbau_formen;
    if ( typeof formen[ grundriss ] !== 'undefined' && typeof formen[ grundriss ][ wand ] !== 'undefined' ) {
      var formel = formen[ grundriss ][ wand ][0];
      if ( formel !== true ) {
        formel = formel.split( ' ' );
        var rechnung = 0.0;
        var current_operator = '+';
        for ( var i in formel ) {
          var formel_part = formel[ i ];
          switch ( formel_part ) {
            case '+':
            case '-':
              current_operator = formel_part;
              break;
            default:
              switch ( current_operator ) {
                case '+':
                  rechnung += _wpenon_data.parser.parseFloat( waende[ formel_part ] );
                  break;
                case '-':
                  rechnung -= _wpenon_data.parser.parseFloat( waende[ formel_part ] );
                  break;
                default:
              }
          }
        }
        if ( rechnung < 0.0 ) {
          rechnung = 0.0;
        }
        return rechnung;
      }
    }
    return null;
  };

  _wpenon_data.dynamic_functions.wpenon_immoticket24_show_fenster = function( grundriss, fenster, nachbar, flaeche ) {
    if ( typeof flaeche === 'undefined' ) {
      flaeche = 1.0;
    } else {
      flaeche = _wpenon_data.parser.parseFloat( flaeche );
    }
    if ( flaeche > 0.0 ) {
      return _wpenon_data.dynamic_functions.wpenon_immoticket24_show_wand( grundriss, fenster, nachbar );
    }
    return false;
  };

  _wpenon_data.dynamic_functions.wpenon_immoticket24_show_dachfenster = function( dach, flaeche ) {
    if ( typeof flaeche === 'undefined' ) {
      flaeche = 1.0;
    } else {
      flaeche = _wpenon_data.parser.parseFloat( flaeche );
    }
    if ( dach == 'beheizt' && flaeche > 0.0 ) {
      return true;
    }
    return false;
  };

  _wpenon_data.dynamic_functions.wpenon_immoticket24_show_specific_fenster = function( fenster_manuell, grundriss, fenster, nachbar, flaeche ) {
    if ( fenster_manuell ) {
      if ( typeof flaeche === 'undefined' ) {
        flaeche = 1.0;
      } else {
        flaeche = _wpenon_data.parser.parseFloat( flaeche );
      }
      if ( flaeche > 0.0 ) {
        return _wpenon_data.dynamic_functions.wpenon_immoticket24_show_wand( grundriss, fenster, nachbar );
      }
    }
    return false;
  };

  _wpenon_data.dynamic_functions.wpenon_immoticket24_show_specific_dachfenster = function( fenster_manuell, dach, flaeche ) {
    if ( fenster_manuell ) {
      if ( typeof flaeche === 'undefined' ) {
        flaeche = 1.0;
      } else {
        flaeche = _wpenon_data.parser.parseFloat( flaeche );
      }
      if ( dach == 'beheizt' && flaeche > 0.0 ) {
        return true;
      }
    }
    return false;
  };

  _wpenon_data.dynamic_functions.wpenon_immoticket24_show_specific_anbaufenster = function( fenster_manuell, anbau, grundriss, fenster, flaeche ) {
    if ( fenster_manuell && anbau ) {
      if ( typeof flaeche === 'undefined' ) {
        flaeche = 1.0;
      } else {
        flaeche = _wpenon_data.parser.parseFloat( flaeche );
      }
      if ( flaeche > 0.0 ) {
        return _wpenon_data.dynamic_functions.wpenon_immoticket24_show_anbauwand( grundriss, fenster, anbau );
      }
    }
    return false;
  };

}
