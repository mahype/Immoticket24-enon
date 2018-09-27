jQuery( document ).ready( function( $ ) {
  var texte;

  if ( $( '#bw_basisdaten' ).length ) {
    texte = [
      {
        next: "Weiter zur Gebäudetopologie"
      },
      {
        prev: "Zurück zu den Basisdaten",
        next: "Weiter zu den Bauteilen"
      },
      {
        prev: "Zurück zur Gebäudetopologie",
        next: "Weiter zur Anlagentechnik"
      },
      {
        prev: "Zurück zu den Bauteilen"
      }
    ];
  } else {
    texte = [
      {
        next: "Weiter zu den Bauteilen"
      },
      {
        prev: "Zurück zu den Basisdaten",
        next: "Weiter zur Anlagentechnik"
      },
      {
        prev: "Zurück zu den Bauteilen",
        next: "Weiter zur Verbrauchserfassung"
      },
      {
        prev: "Zurück zur Anlagentechnik"
      }
    ];
  }

  $( document ).on( 'wpenon.update_active_tab', function( e, index ) {
    if ( texte[ index ] && texte[ index ].prev ) {
      $( '#wpenon-previous-button' ).text( texte[ index ].prev );
    }
    if ( texte[ index ] && texte[ index ].next ) {
      $( '#wpenon-next-button' ).text( texte[ index ].next );
    }
  });

  if ( texte[0] && texte[0].next ) {
    $( '#wpenon-next-button' ).text( texte[0].next );
  }
});