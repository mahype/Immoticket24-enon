<?php

register_block_pattern(
    'enon/banner',
    array(
        'title'       => __( 'Banner', 'enon-patterns' ),
        'description' => _x( 'Ein Gruppenblock mit Banner', 'enon-patterns' ),
        'categories'  => ['content'],
        'content'     => pattern_replace_urls( '<!-- wp:group {"backgroundColor":"green-lime","className":"banner"} -->
        <div class="wp-block-group banner has-green-lime-background-color has-background"><!-- wp:paragraph {"className":"banner-content-top"} -->
        <p class="banner-content-top">30 tage<br>geld-zurück-garantie</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:heading {"textColor":"white"} -->
        <h2 class="has-white-color has-text-color" id="geld-zuruck-garantieund-unser-bestpreis-versprechen-fur-sie"><strong>Geld-zurück-Garantie</strong><br>und unser Bestpreis-Versprechen für Sie</h2>
        <!-- /wp:heading -->
        
        <!-- wp:columns -->
        <div class="wp-block-columns"><!-- wp:column {"width":"169px"} -->
        <div class="wp-block-column" style="flex-basis:169px"><!-- wp:image {"align":"center","id":421,"sizeSlug":"full","linkDestination":"none"} -->
        <div class="wp-block-image"><figure class="aligncenter size-full"><img src="/app/themes/jason/assets/img/layout/christian-esch-kopf-rand.webp" alt="" class="wp-image-421"/></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:paragraph -->
        <p></p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:paragraph {"textColor":"white"} -->
        <p class="has-white-color has-text-color">Um Ihnen höchste Sicherheit zum bestmöglichen Preis zu bieten, erhalten Sie bei uns neben einer 30 Tage Geld-zurück-Garantie zusätzlich noch unser Bestpreis-Versprechen:</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"textColor":"white"} -->
        <p class="has-white-color has-text-color">Wenn Sie irgendwo im Internet eine Webseite finden, bei der Sie einen Energieausweis in gleicher Qualität zu einem günstigeren Preis finden, kontaktieren Sie uns! Wir schreiben Ihnen dann den Differenzbetrag gut.</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}},"textColor":"white"} -->
        <p class="has-white-color has-text-color" style="font-size:14px"><em>Christian Esch<br>Geschäsftsführer</em></p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","orientation":"horizontal"}} -->
        <div class="wp-block-buttons"><!-- wp:button -->
        <div class="wp-block-button"><a class="wp-block-button__link" href="/energieausweis/">Hier Energieausweis beginnen</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons -->
        
        <!-- wp:paragraph {"className":"banner-content-bottom"} -->
        <p class="banner-content-bottom">unser<br>bestpreis-versprechen</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group -->' ),
    )
);