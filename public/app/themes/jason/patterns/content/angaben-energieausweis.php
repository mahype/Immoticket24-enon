<?php

register_block_pattern(
    'enon/content-angaben-energieausweis',
    array(
        'title'       => __( 'Angaben Energieausweis', 'enon-patterns' ),
        'description' => _x( 'Ein Block mit zwei PDF Dateien mit Angaben für den Energieausweis', 'enon-patterns' ),
        'categories'  => ['content'],
        'content'     => pattern_replace_urls( '<!-- wp:group {"gradient":"white-green-to-green-white","className":"is-style-main-group is-style-group-main"} -->
        <div class="wp-block-group is-style-main-group is-style-group-main has-white-green-to-green-white-gradient-background has-background"><!-- wp:heading {"textAlign":"left","textColor":"green-moss","className":"center"} -->
        <h2 class="has-text-align-left center has-green-moss-color has-text-color" id="diese-angaben-benotigen-siezur-erstellung-des-energieausweises"><strong>Diese Angaben benötigen Sie<br></strong>zur Erstellung des Energieausweises</h2>
        <!-- /wp:heading -->
        
        <!-- wp:columns -->
        <div class="wp-block-columns"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:image {"align":"center","sizeSlug":"large","linkDestination":"custom"} -->
        <div class="wp-block-image"><figure class="aligncenter size-large"><a href="/app/themes/jason/assets/downloads/angaben-bedarfsausweis.pdf" target="_blank" rel="noopener"><img src="/app/themes/jason/assets/img/icons/dokument-pdf.webp" alt="PDF Dokument"/></a></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","level":3,"textColor":"green-moss","className":"text-center md:text-left"} -->
        <h3 class="has-text-align-center text-center md:text-left has-green-moss-color has-text-color" id="angaben-bedarfsausweis">Angaben Bedarfsausweis</h3>
        <!-- /wp:heading --></div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:image {"align":"center","sizeSlug":"large","linkDestination":"custom"} -->
        <div class="wp-block-image"><figure class="aligncenter size-large"><a href="/app/themes/jason/assets/downloads/angaben-verbrauchsausweis.pdf" target="_blank" rel="noopener"><img src="/app/themes/jason/assets/img/icons/dokument-pdf.webp" alt="PDF Dokument"/></a></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","level":3,"textColor":"green-moss","className":"text-center md:text-left"} -->
        <h3 class="has-text-align-center text-center md:text-left has-green-moss-color has-text-color" id="angaben-verbrauchsausweis">Angaben Verbrauchsausweis</h3>
        <!-- /wp:heading --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","orientation":"horizontal"}} -->
        <div class="wp-block-buttons"><!-- wp:button -->
        <div class="wp-block-button"><a class="wp-block-button__link" href="/energieausweis/">Jetzt Energieausweis beginnen</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons --></div>
        <!-- /wp:group -->' ),
    )
);
