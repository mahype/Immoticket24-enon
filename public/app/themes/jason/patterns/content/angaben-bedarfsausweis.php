<?php

register_block_pattern(
    'enon/content-angaben-bedarfsausweis',
    array(
        'title'       => __( 'Angaben Bedarfsausweis', 'enon-patterns' ),
        'description' => _x( 'Ein Block einer Datei mit Angaben für den Bedarfsausweis.', 'enon-patterns' ),
        'categories'  => ['content'],
        'content'     => pattern_replace_urls( '<!-- wp:group {"gradient":"white-green-to-green-white","className":"is-style-group-main"} -->
        <div class="wp-block-group is-style-group-main has-white-green-to-green-white-gradient-background has-background"><!-- wp:heading {"textAlign":"left"} -->
        <h2 class="has-text-align-left" id="diese-angaben-benotigen-sie-zur-bedarfsausweis-erstellung">Diese Angaben benötigen Sie <br><strong>zur Bedarfsausweis-Erstellung</strong></h2>
        <!-- /wp:heading -->
        
        <!-- wp:heading {"textAlign":"center","level":3} -->
        <h3 class="has-text-align-center" id="downloads">Downloads</h3>
        <!-- /wp:heading -->
        
        <!-- wp:columns -->
        <div class="wp-block-columns"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:image {"align":"center","id":436,"width":47,"height":60,"sizeSlug":"full","linkDestination":"custom"} -->
        <div class="wp-block-image"><figure class="aligncenter size-full is-resized"><a href="/app/themes/jason/assets/downloads/angaben-bedarfsausweis.pdf"><img src="/app/themes/jason/assets/img/icons/dokument-pdf.webp" alt="" class="wp-image-436" width="47" height="60"/></a></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","level":3,"textColor":"green-moss","className":"text-center md:text-left"} -->
        <h3 class="has-text-align-center text-center md:text-left has-green-moss-color has-text-color" id="angaben-bedarfsausweis"><a href="/app/themes/jason/assets/downloads/angaben-bedarfsausweis.pdf">Angaben Bedarfsausweis</a></h3>
        <!-- /wp:heading --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","orientation":"horizontal"}} -->
        <div class="wp-block-buttons"><!-- wp:button {"textColor":"white","style":{"color":{"background":"#3da81d"},"border":{"radius":"5px"}}} -->
        <div class="wp-block-button"><a class="wp-block-button__link has-white-color has-text-color has-background" href="https://enon.test/energieausweis2/bedarfsausweis-wohngebaeude/" style="border-radius:5px;background-color:#3da81d">Bedarfsausweis erstellen &gt;&gt;</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons --></div>
        <!-- /wp:group -->' ),
    )
);
