jQuery(document).ready(function($) {

  $( 'body' ).prepend( '<div id="wpenon-preloader-overlay" class="wpenon-preloader-overlay"><div class="wpenon-preloader"></div></div>' );
  
  $( 'body' ).on( 'wpenon_ajax_start', function() {
    $( '#wpenon-preloader-overlay' ).show();
  });

  $( 'body' ).on( 'wpenon_ajax_end', function() {
    $( '#wpenon-preloader-overlay' ).hide();
  });

  $( '#title' ).prop( 'readonly', true ).on( 'focus', function() {
    if ( ! $( this ).val() ) {
      $( this ).val( $( '#title-prompt-text' ).text() );
    }
  });

});
