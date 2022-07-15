<?php

register_block_pattern(
    'enon/checklisten-zur-erstellung',
    array(
        'title'       => __( 'Checklisten zur Erstellung', 'enon-patterns' ),
        'description' => _x( 'Checklisten zur Erstellung und Aufnahme der Angaben beim Kunden', 'enon-patterns' ),
        'categories'  => ['content'],
        'content'     => pattern_replace_urls( '<!-- wp:group {"gradient":"yellow-neon-to-green-mint","className":"is-style-group-main"} -->
        <div class="wp-block-group is-style-group-main has-yellow-neon-to-green-mint-gradient-background has-background"><!-- wp:heading -->
        <h2 id="checklisten-zur-erstellungund-aufnahme-der-angaben-beim-kunden"><strong>Checklisten </strong>zur Erstellung<br>und Aufnahme der Angaben beim Kunden</h2>
        <!-- /wp:heading -->
        
        <!-- wp:columns -->
        <div class="wp-block-columns"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:image {"align":"center","id":436,"width":47,"height":60,"sizeSlug":"full","linkDestination":"custom"} -->
        <div class="wp-block-image"><figure class="aligncenter size-full is-resized"><a href="/app/themes/jason/assets/downloads/angaben-bedarfsausweis.pdf"><img src="/app/themes/jason/assets/img/icons/dokument-pdf.webp" alt="" class="wp-image-436" width="47" height="60"/></a></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","level":3,"textColor":"green-moss","className":"text-center md:text-left"} -->
        <h3 class="has-text-align-center text-center md:text-left has-green-moss-color has-text-color" id="checkliste-bedarfsausweis"><a href="/app/themes/jason/assets/downloads/angaben-bedarfsausweis.pdf">Checkliste Bedarfsausweis</a></h3>
        <!-- /wp:heading --></div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:image {"align":"center","id":436,"width":47,"height":60,"sizeSlug":"full","linkDestination":"custom"} -->
        <div class="wp-block-image"><figure class="aligncenter size-full is-resized"><a href="/app/themes/jason/assets/downloads/angaben-verbrauchsausweis.pdf"><img src="/app/themes/jason/assets/img/icons/dokument-pdf.webp" alt="" class="wp-image-436" width="47" height="60"/></a></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","level":3,"textColor":"green-moss","className":"text-center md:text-left"} -->
        <h3 class="has-text-align-center text-center md:text-left has-green-moss-color has-text-color" id="angaben-verbrauchsausweis"><a href="/app/themes/jason/assets/downloads/angaben-verbrauchsausweis.pdf">Checkliste Verbrauchsausweis</a></h3>
        <!-- /wp:heading --></div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:image {"align":"center","id":436,"width":47,"height":60,"sizeSlug":"full","linkDestination":"custom"} -->
        <div class="wp-block-image"><figure class="aligncenter size-full is-resized"><a href="/app/themes/jason/assets/downloads/angaben-nichtwohngebaeude.pdf"><img src="/app/themes/jason/assets/img/icons/dokument-pdf.webp" alt="" class="wp-image-436" width="47" height="60"/></a></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","level":3,"textColor":"green-moss","className":"text-center md:text-left"} -->
        <h3 class="has-text-align-center text-center md:text-left has-green-moss-color has-text-color" id="angaben-verbrauchsausweis"><a href="/app/themes/jason/assets/downloads/angaben-nichtwohngebaeude.pdf">Checkliste Nichtwohngebäude-Energieausweis</a></h3>
        <!-- /wp:heading --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","orientation":"horizontal"}} -->
        <div class="wp-block-buttons"><!-- wp:button -->
        <div class="wp-block-button"><a class="wp-block-button__link" href="/energieausweise-fuer-immobilienmakler-und-hausverwalter/gewerbeschein-senden/">Jetzt Vorteilskonditionen sichern</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons --></div>
        <!-- /wp:group -->' ),
    )
);