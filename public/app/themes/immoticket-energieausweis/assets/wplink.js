( function( $, wp ) {
  if ( 'undefined' === typeof wpLink ) {
    return;
  }

  var getAttrsOrig = wpLink.getAttrs;

  wpLink.getAttrs = function() {
    var attrs = getAttrsOrig();
    if ( $( '#wp-link-is-button' ).prop( 'checked' ) ) {
      attrs.class = 'btn btn-primary';
    }
    return attrs;
  }
})( jQuery, window.wp );
