<?php

register_block_pattern(
    'enon/drei-guetesiegel',
    array(
        'title'       => __( '3 Gütesiegel', 'enon-patterns' ),
        'description' => _x( '3 Gütesiegel, die für energieausweis.de sprechen:', 'enon-patterns' ),
        'categories'  => ['content'],
        'content'     => pattern_replace_urls( '<!-- wp:group {"style":{"spacing":{"padding":{"top":"0px"}}},"gradient":"green-neon-to-green-mint","className":"is-style-group-main"} -->
        <div class="wp-block-group is-style-group-main has-green-neon-to-green-mint-gradient-background has-background" style="padding-top:0px"><!-- wp:heading {"textAlign":"left"} -->
        <h2 class="has-text-align-left" id="3-gutesiegel-die-fur-energieausweis-online-erstellen-de-sprechen"><strong>3 Gütesiegel</strong> <br>die für energieausweis.de sprechen</h2>
        <!-- /wp:heading -->
        
        <!-- wp:columns {"className":"feature-block-style-2"} -->
        <div class="wp-block-columns feature-block-style-2"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:image {"align":"center","width":100,"height":100,"sizeSlug":"large"} -->
        <div class="wp-block-image"><figure class="aligncenter size-large is-resized"><img src="/app/themes/jason/assets/img/logos/trusted-shops.webp" alt="Trusted Shops Icon" width="100" height="100"/></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","level":3} -->
        <h3 class="has-text-align-center" id="trustedshops-zertifiziert">TrustedShops-zertifiziert</h3>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">Damit ist Ihr Einkauf bei uns mit dem Käuferschutz abgesichert</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:image {"align":"center","width":100,"height":100,"sizeSlug":"large"} -->
        <div class="wp-block-image"><figure class="aligncenter size-large is-resized"><img src="/app/themes/jason/assets/img/logos/ekomi-silber.webp" alt="Ekomi Kundenauszeichnung Icon" width="100" height="100"/></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","level":3} -->
        <h3 class="has-text-align-center" id="97-kundenzufriedenheit">97% Kundenzufriedenheit</h3>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">Bei der Bewertungsplattform eKomi erzielen wir 97% Kundenzufriedenheit.</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:image {"align":"center","sizeSlug":"large"} -->
        <div class="wp-block-image"><figure class="aligncenter size-large"><img src="/app/themes/jason/assets/img/logos/tuev-geprueft-100.webp" alt="TÜV geprüft Icon"/></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","level":3} -->
        <h3 class="has-text-align-center" id="tuv-gepruftes-onlineportal">TÜV-geprüftes Onlineportal</h3>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">Unsere Website ist vom TÜV Saarland zertifiziert.</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","orientation":"horizontal"}} -->
        <div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"green","textColor":"white","style":{"border":{"radius":"5px"}}} -->
        <div class="wp-block-button"><a class="wp-block-button__link has-white-color has-green-background-color has-text-color has-background" href="https://enon.test/energieausweis2/bedarfsausweis-wohngebaeude/" style="border-radius:5px">Energieausweis-Erstellung beginnen &gt;&gt;</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons --></div>
        <!-- /wp:group -->' ),
    )
);