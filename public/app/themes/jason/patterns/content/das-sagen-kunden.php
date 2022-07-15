<?php

register_block_pattern(
    'enon/content-das-sagen-kunden',
    array(
        'title'       => __( 'Das sagen Kunden', 'enon-patterns' ),
        'description' => _x( 'Eine Liste mit Testimonials und Bewertungen', 'enon-patterns' ),
        'categories'  => ['content'],
        'content'     => pattern_replace_urls( '<!-- wp:group {"gradient":"white-green-to-green-white","className":"is-style-main-group is-style-group-main"} -->
        <div class="wp-block-group is-style-main-group is-style-group-main has-white-green-to-green-white-gradient-background has-background"><!-- wp:heading -->
        <h2 id="das-sagen-unsere-kundenzufriedene-kunden-sind-fur-uns-das-wichtigste"><strong>Das sagen unsere Kunden</strong><br>zufriedene Kunden sind für uns das wichtigste!</h2>
        <!-- /wp:heading -->
        
        <!-- wp:awsm/block-trusted-shops-testimonials {"className":"wp-block-awsm-block-trusted-shops-testimonials bts-rating"} /-->
        
        <!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"12px"}}} -->
        <p class="has-text-align-center" style="font-size:12px"><em>Ausgewählte Top-Bewertungen für energieausweis-online-erstellen.de powered by TrustedShops</em></p>
        <!-- /wp:paragraph -->
        
        <!-- wp:buttons {"className":"uppercase","layout":{"type":"flex","justifyContent":"center","orientation":"horizontal"}} -->
        <div class="wp-block-buttons uppercase"><!-- wp:button -->
        <div class="wp-block-button"><a class="wp-block-button__link" href="/energieausweis/">Jetzt Energieausweis erstellen</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons --></div>
        <!-- /wp:group -->' ),
    )
);
