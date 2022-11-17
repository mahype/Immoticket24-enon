<?php

register_block_pattern(
    'enon/price-small',
    array(
        'title'       => __( 'Preise (Klein)', 'enon-patterns' ),
        'description' => _x( 'Eine Übersicht über Verbrauchs- und Bedarfsausweis (klein)', 'enon-patterns' ),
        'categories'  => ['content'],
        'content'     => pattern_replace_urls( '<!-- wp:group {"style":{"spacing":{"padding":{"top":"0px"}}},"gradient":"green-neon-to-green-mint","className":"is-style-group-main"} -->
        <div class="wp-block-group is-style-group-main has-green-neon-to-green-mint-gradient-background has-background" style="padding-top:0px"><!-- wp:columns -->
        <div class="wp-block-columns"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:heading -->
        <h2 id="verbrauchsausweis">Verbrauchsausweis</h2>
        <!-- /wp:heading -->
        
        <!-- wp:image {"sizeSlug":"large"} -->
        <figure class="wp-block-image size-large"><img src="/app/themes/jason/assets/img/layout/verbrauchsausweis-symbolbild.jpg" alt=""/></figure>
        <!-- /wp:image -->
        
        <!-- wp:list {"className":"is-style-checkmarks-formatted"} -->
        <ul class="is-style-checkmarks-formatted"><li>rechtsgültiger Verbrauchsausweis nach der aktuellen EnEV 2014</li><li>persönliche Übersichtsseite für Ihren Energieausweis</li><li>PDF-Vorschau Ihres späteren Energieausweises mit den aktuellen Eingabedaten</li><li>Abbildung des Gebäudes im Energieausweis möglich – Einfach Foto hochladen</li></ul>
        <!-- /wp:list -->
        
        <!-- wp:group {"className":"is-style-label-green"} -->
        <div class="wp-block-group is-style-label-green"><!-- wp:paragraph {"className":"is-style-price-label-green"} -->
        <p class="is-style-price-label-green">59,95 €</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group --></div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:heading -->
        <h2 id="bedarfsausweis">Bedarfsausweis</h2>
        <!-- /wp:heading -->
        
        <!-- wp:image {"sizeSlug":"large"} -->
        <figure class="wp-block-image size-large"><img src="/app/themes/jason/assets/img/layout/bedarfsausweis-symbolbild.jpg" alt=""/></figure>
        <!-- /wp:image -->
        
        <!-- wp:list {"className":"is-style-checkmarks-formatted"} -->
        <ul class="is-style-checkmarks-formatted"><li>rechtsgültiger Bedarfsausweis nach der aktuellen EnEV 2014</li><li>persönliche Übersichtsseite für Ihren Energieausweis</li><li>PDF-Vorschau Ihres späteren Energieausweises mit den aktuellen Eingabedaten</li><li>Abbildung des Gebäudes im Energieausweis möglich – Einfach Foto hochladen</li></ul>
        <!-- /wp:list -->
        
        <!-- wp:group {"className":"is-style-label-green"} -->
        <div class="wp-block-group is-style-label-green"><!-- wp:paragraph {"className":"is-style-price-label-green"} -->
        <p class="is-style-price-label-green">109,95 €</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","orientation":"horizontal"},"style":{"spacing":{"margin":{"top":"80px"}}}} -->
        <div class="wp-block-buttons" style="margin-top:80px"><!-- wp:button {"backgroundColor":"green","textColor":"white","style":{"border":{"radius":"5px"}}} -->
        <div class="wp-block-button"><a class="wp-block-button__link has-white-color has-green-background-color has-text-color has-background" href="/energieausweis/" style="border-radius:5px">Jetzt Energieausweis-Erstellung beginnen &gt;&gt;</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons --></div>
        <!-- /wp:group -->' ),
    )
);