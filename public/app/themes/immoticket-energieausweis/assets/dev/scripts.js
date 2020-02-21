+function ($) {
  'use strict';

  var theme_config = {
    load_fancybox: true,
    load_tooltips: false,
    load_popovers: false,
    enable_navbar: false,
    wrap_embeds: true
  };

  function lazy_load_image( img ) {
    var $img = $( img ),
      src = $img.attr( 'data-lazy-src' );

    if ( ! src || 'undefined' === typeof( src ) )
      return;

    $img.unbind( 'scrollin' ) // remove event binding
      .hide()
      .removeAttr( 'data-lazy-src' )
      .attr( 'data-lazy-loaded', 'true' );

    img.src = src;
    $img.fadeIn();
  }

  $( '.flexslider, .comparison-table-content' ).each( function() {
    $( this ).find( 'img[data-lazy-src]' ).each( function() {
      lazy_load_image( this );
    } );
  });

  $(document).ready(function($) {

    if(theme_config.load_fancybox && $.fn.fancybox) {
      var fancybox_args = {
        maxWidth: 1280,
        maxHeight: 720,
        width: '90%',
        height: '90%',
        openEffect: 'elastic',
        closeEffect: 'elastic',
        nextEffect: 'elastic',
        prevEffect: 'elastic'
      };
      $('a[href$=".jpg"],a[href$=".jpeg"],a[href$=".png"],a[href$=".gif"]').each(function() {
        var $this = $(this);
        var $wrap = $this.parents('.gallery');
        if($wrap.length !== 0) {
          $this.attr('rel', $wrap.attr('id')).fancybox(fancybox_args);
        }
        else {
          $this.fancybox(fancybox_args);
        }
      });
    }

    if(theme_config.load_tooltips && $.fn.tooltip) {
      $('[rel="tooltip"]').tooltip();
    }

    if(theme_config.load_popovers && $.fn.popover) {
      $('[rel="popover"]').popover();
    }

    if(theme_config.wrap_embeds) {
      $('.wrapper iframe, .wrapper embed, .wrapper object').each(function() {
        var $this = $(this);
        if($this.parents('.embed-responsive').length === 0 && ! $this.hasClass( 'do-not-wrap' ) ) {
          $this.wrap('<div class="embed-responsive embed-responsive-16by9"></div>');
        }
      });
    }

    var $navbar_wrapper = $( '.navigation-bar-wrapper:first' );
    if ( $navbar_wrapper.length ) {
      var $navbar = $( '.navigation-bar:first' );
      var check_offset = function() {
        var offset = 0;
        if ( $( 'body' ).hasClass( 'admin-bar' ) ) {
          if ( document.documentElement.clientWidth <= 782 ) {
            offset = 46;
          } else {
            offset = 32;
          }
        }
        if ( $( window ).scrollTop() + offset >= $navbar_wrapper.offset().top ) {
          $navbar.addClass( 'navigation-bar-fixed' );
        } else {
          $navbar.removeClass( 'navigation-bar-fixed' );
        }
      }
      check_offset();
      $( window ).on( 'scroll', check_offset );
    }

    $( '.navigation-bar-toggle-button' ).on( 'click', function( e ) {
      $( '.navigation-menu:first' ).toggleClass( 'is-collapsed' );

      e.preventDefault();
    });

  });

}(jQuery);
