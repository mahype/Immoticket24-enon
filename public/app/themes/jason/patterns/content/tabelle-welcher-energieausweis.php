<?php

register_block_pattern(
    'enon/tabelle-welcher-energieausweis',
    array(
        'title'       => __( 'Tabelle - Welcher Energieausweis', 'enon-patterns' ),
        'description' => _x( 'Eine Tabelle mit der Übersicht, wann ein Verbrauchs- und wann ein Bedarfsausweis benötigt wird.', 'enon-patterns' ),
        'categories'  => ['content'],
        'content'     => pattern_replace_urls( '<!-- wp:columns {"className":"col-table"} -->
        <div class="wp-block-columns col-table"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:spacer {"height":80} -->
        <div style="height:80px" aria-hidden="true" class="wp-block-spacer"></div>
        <!-- /wp:spacer -->
        
        <!-- wp:paragraph {"className":"hide-on-mobile"} -->
        <p class="hide-on-mobile"><strong>Bis zu 4 Wohneinheiten</strong></p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"hide-on-mobile"} -->
        <p class="hide-on-mobile"><strong>Ab 5 Wohneinheiten</strong></p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:heading {"textAlign":"center","backgroundColor":"black-20","textColor":"white"} -->
        <h2 class="has-text-align-center has-white-color has-black-20-background-color has-text-color has-background" id="baujahr-bis-1977">Baujahr Bis 1977</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center","className":"hide-on-screen"} -->
        <p class="has-text-align-center hide-on-screen"><strong>Bis zu 4 Wohneinheiten</strong></p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">Bedarfsausweis</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"align":"center","className":"hide-on-screen"} -->
        <p class="has-text-align-center hide-on-screen"><strong>Ab 5 Wohneinheiten</strong></p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">Freie Wahl</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:heading {"textAlign":"center","backgroundColor":"black-20","textColor":"white"} -->
        <h2 class="has-text-align-center has-white-color has-black-20-background-color has-text-color has-background" id="baujahr-ab-1978">Baujahr ab 1978</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center","className":"hide-on-screen"} -->
        <p class="has-text-align-center hide-on-screen"><strong>Bis zu 4 Wohneinheiten</strong></p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">Freie Wahl</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"align":"center","className":"hide-on-screen"} -->
        <p class="has-text-align-center hide-on-screen"><strong>Ab 5 Wohneinheiten</strong></p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">Freie Wahl</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:heading {"textAlign":"center","backgroundColor":"black-20","textColor":"white"} -->
        <h2 class="has-text-align-center has-white-color has-black-20-background-color has-text-color has-background" id="neubau">Neubau</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center","className":"hide-on-screen"} -->
        <p class="has-text-align-center hide-on-screen"><strong>Bis zu 4 Wohneinheiten</strong></p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">Bedarfsausweis</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"align":"center","className":"hide-on-screen"} -->
        <p class="has-text-align-center hide-on-screen"><strong>Ab 5 Wohneinheiten</strong></p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">Bedarfsausweis</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->' ),
    )
);


