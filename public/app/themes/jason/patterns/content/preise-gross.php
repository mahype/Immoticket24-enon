<?php

register_block_pattern(
    'enon/price-big',
    array(
        'title'       => __( 'Preise (Gross)', 'enon-patterns' ),
        'description' => _x( 'Eine Übersicht über Verbrauchs- und Bedarfsausweis (klein)', 'enon-patterns' ),
        'categories'  => ['content'],
        'content'     => pattern_replace_urls( '<!-- wp:group {"style":{"spacing":{"padding":{"top":"0px"}}},"gradient":"yellow-neon-to-green-mint","className":"wave2 is-style-group-main"} -->
        <div class="wp-block-group wave2 is-style-group-main has-yellow-neon-to-green-mint-gradient-background has-background" style="padding-top:0px"><!-- wp:columns {"className":"price-list"} -->
        <div class="wp-block-columns price-list"><!-- wp:column {"width":""} -->
        <div class="wp-block-column"><!-- wp:group {"className":"price-list-head"} -->
        <div class="wp-block-group price-list-head"><!-- wp:image {"align":"center","id":690,"sizeSlug":"full","linkDestination":"none"} -->
        <div class="wp-block-image"><figure class="aligncenter size-full"><img src="/app/themes/jason/assets/img/logos/bestpreis-garantie.webp" alt="" class="wp-image-690"/></figure></div>
        <!-- /wp:image --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"className":"price-list-descriptions border-gradient-white-bottom-to-top"} -->
        <div class="wp-block-group price-list-descriptions border-gradient-white-bottom-to-top"><!-- wp:paragraph {"className":"price-list-cell"} -->
        <p class="price-list-cell">Rechtsgültiger Energieausweis nach der aktuellen EnEV</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-cell"} -->
        <p class="price-list-cell">PDF-Vorschau Ihres Energieausweises</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-cell"} -->
        <p class="price-list-cell">Abbildung des Gebäudes im Energieausweis<br>möglich – Einfach Foto hochladen</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-cell"} -->
        <p class="price-list-cell">Inklusive Registrierung des<br>Energieausweises beim DIBt</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-cell"} -->
        <p class="price-list-cell">Inklusive Auflistung Verbesserungsmaßnahmen<br>im Energieausweis auf Seite 4</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-cell"} -->
        <p class="price-list-cell">Inklusive Telefonischem Support bei Fragen</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group --></div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"className":"price-list-head"} -->
        <div class="wp-block-group price-list-head"><!-- wp:image {"align":"center","id":15,"sizeSlug":"full","linkDestination":"none"} -->
        <div class="wp-block-image"><figure class="aligncenter size-full"><img src="/app/themes/jason/assets/img/icons/haus-temperatur.webp" alt="" class="wp-image-15"/></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","textColor":"green-moss"} -->
        <h2 class="has-text-align-center has-green-moss-color has-text-color" id="bedarfsausweis-1">Bedarfsausweis</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center","className":"label-green shadow rotate-2 font-bold"} -->
        <p class="has-text-align-center label-green shadow rotate-2 font-bold">99,95 Euro</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"className":"price-list-checkmarks border-bottom-white"} -->
        <div class="wp-block-group price-list-checkmarks border-bottom-white"><!-- wp:group {"className":"price-list-cell"} -->
        <div class="wp-block-group price-list-cell"><!-- wp:paragraph {"className":"price-list-text"} -->
        <p class="price-list-text"><meta charset="utf-8">Rechtsgültiger Energieausweis nach der aktuellen EnEV</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-checkmark"} -->
        <p class="price-list-checkmark"><meta charset="utf-8">✓</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"className":"price-list-cell"} -->
        <div class="wp-block-group price-list-cell"><!-- wp:paragraph {"className":"price-list-text"} -->
        <p class="price-list-text"><meta charset="utf-8">PDF-Vorschau Ihres Energieausweises</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-checkmark"} -->
        <p class="price-list-checkmark"><meta charset="utf-8">✓</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"className":"price-list-cell"} -->
        <div class="wp-block-group price-list-cell"><!-- wp:paragraph {"className":"price-list-text"} -->
        <p class="price-list-text"><meta charset="utf-8">Abbildung des Gebäudes im Energieausweis<br>möglich – Einfach Foto hochladen</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-checkmark"} -->
        <p class="price-list-checkmark"><meta charset="utf-8">✓</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"className":"price-list-cell"} -->
        <div class="wp-block-group price-list-cell"><!-- wp:paragraph {"className":"price-list-text"} -->
        <p class="price-list-text"><meta charset="utf-8">Inklusive Registrierung des<br>Energieausweises beim DIBt</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-checkmark"} -->
        <p class="price-list-checkmark"><meta charset="utf-8">✓</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"className":"price-list-cell"} -->
        <div class="wp-block-group price-list-cell"><!-- wp:paragraph {"className":"price-list-text"} -->
        <p class="price-list-text"><meta charset="utf-8">Inklusive Auflistung Verbesserungsmaßnahmen<br>im Energieausweis auf Seite 4</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-checkmark"} -->
        <p class="price-list-checkmark"><meta charset="utf-8">✓</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"className":"price-list-cell"} -->
        <div class="wp-block-group price-list-cell"><!-- wp:paragraph {"className":"price-list-text"} -->
        <p class="price-list-text"><meta charset="utf-8">Inklusive Telefonischem Support bei Fragen</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-checkmark"} -->
        <p class="price-list-checkmark"><meta charset="utf-8">✓</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"style":{"spacing":{"padding":{"top":"40px","right":"0px","bottom":"0px","left":"0px"}}},"className":"text-center p-4"} -->
        <div class="wp-block-group text-center p-4" style="padding-top:40px;padding-right:0px;padding-bottom:0px;padding-left:0px"><!-- wp:heading {"textAlign":"center","level":3,"textColor":"green-moss"} -->
        <h3 class="has-text-align-center has-green-moss-color has-text-color" id="bedarfsausweis">Bedarfsausweis</h3>
        <!-- /wp:heading -->
        
        <!-- wp:image {"align":"center","id":761,"sizeSlug":"full","linkDestination":"custom"} -->
        <div id="shadow" class="wp-block-image"><figure class="aligncenter size-full"><a href="/app/themes/Jason/assets/downloads/muster-bedarfsausweis.pdf"><img src="/app/themes/jason/assets/img/layout/bedarfsausweis-klein.webp" alt="" class="wp-image-761"/></a></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
        <div class="wp-block-buttons"><!-- wp:button {"className":"is-style-button-white"} -->
        <div class="wp-block-button is-style-button-white"><a class="wp-block-button__link" href="/app/themes/Jason/assets/downloads/muster-bedarfsausweis.pdf" target="_blank" rel="noreferrer noopener">Beispiel ansehen</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","orientation":"horizontal"}} -->
        <div class="wp-block-buttons"><!-- wp:button -->
        <div class="wp-block-button"><a class="wp-block-button__link" href="/energieausweis2/bedarfsausweis-wohngebaeude/">Bedarfsausweis erstellen</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons --></div>
        <!-- /wp:group --></div>
        <!-- /wp:column -->
        
        <!-- wp:column -->
        <div class="wp-block-column"><!-- wp:group {"className":"price-list-head"} -->
        <div class="wp-block-group price-list-head"><!-- wp:image {"align":"center","id":15,"sizeSlug":"full","linkDestination":"none"} -->
        <div class="wp-block-image"><figure class="aligncenter size-full"><img src="/app/themes/jason/assets/img/icons/haus-temperatur.webp" alt="" class="wp-image-15"/></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","textColor":"green-moss"} -->
        <h2 class="has-text-align-center has-green-moss-color has-text-color" id="verbrauchsausweis-1"><meta charset="utf-8">Verbrauchsausweis</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center","className":"label-green shadow rotate-2 font-bold"} -->
        <p class="has-text-align-center label-green shadow rotate-2 font-bold">49,95 Euro</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"className":"price-list-checkmarks border-gradient-white-bottom-to-top"} -->
        <div class="wp-block-group price-list-checkmarks border-gradient-white-bottom-to-top"><!-- wp:group {"className":"price-list-cell"} -->
        <div class="wp-block-group price-list-cell"><!-- wp:paragraph {"className":"price-list-text"} -->
        <p class="price-list-text"><meta charset="utf-8">Rechtsgültiger Energieausweis nach der aktuellen EnEV</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-checkmark"} -->
        <p class="price-list-checkmark"><meta charset="utf-8">✓</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"className":"price-list-cell"} -->
        <div class="wp-block-group price-list-cell"><!-- wp:paragraph {"className":"price-list-text"} -->
        <p class="price-list-text"><meta charset="utf-8">PDF-Vorschau Ihres Energieausweises</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-checkmark"} -->
        <p class="price-list-checkmark"><meta charset="utf-8">✓</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"className":"price-list-cell"} -->
        <div class="wp-block-group price-list-cell"><!-- wp:paragraph {"className":"price-list-text"} -->
        <p class="price-list-text"><meta charset="utf-8">Abbildung des Gebäudes im Energieausweis<br>möglich – Einfach Foto hochladen</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-checkmark"} -->
        <p class="price-list-checkmark"><meta charset="utf-8">✓</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"className":"price-list-cell"} -->
        <div class="wp-block-group price-list-cell"><!-- wp:paragraph {"className":"price-list-text"} -->
        <p class="price-list-text"><meta charset="utf-8">Inklusive Registrierung des<br>Energieausweises beim DIBt</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-checkmark"} -->
        <p class="price-list-checkmark"><meta charset="utf-8">✓</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"className":"price-list-cell"} -->
        <div class="wp-block-group price-list-cell"><!-- wp:paragraph {"className":"price-list-text"} -->
        <p class="price-list-text"><meta charset="utf-8">Inklusive Auflistung Verbesserungsmaßnahmen<br>im Energieausweis auf Seite 4</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-checkmark"} -->
        <p class="price-list-checkmark"><meta charset="utf-8">✓</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"className":"price-list-cell"} -->
        <div class="wp-block-group price-list-cell"><!-- wp:paragraph {"className":"price-list-text"} -->
        <p class="price-list-text"><meta charset="utf-8">Inklusive Telefonischem Support bei Fragen</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:paragraph {"className":"price-list-checkmark"} -->
        <p class="price-list-checkmark"><meta charset="utf-8">✓</p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group --></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"style":{"spacing":{"padding":{"top":"40px","right":"0px","bottom":"0px","left":"0px"}}},"className":"text-center p-4"} -->
        <div class="wp-block-group text-center p-4" style="padding-top:40px;padding-right:0px;padding-bottom:0px;padding-left:0px"><!-- wp:heading {"textAlign":"center","level":3,"textColor":"green-moss"} -->
        <h3 class="has-text-align-center has-green-moss-color has-text-color" id="verbrauchsausweis">Verbrauchsausweis</h3>
        <!-- /wp:heading -->
        
        <!-- wp:image {"align":"center","id":762,"sizeSlug":"full","linkDestination":"custom"} -->
        <div class="wp-block-image"><figure class="aligncenter size-full"><a href="/app/themes/Jason/assets/downloads/muster-verbrauchsausweis.pdf"><img src="/app/themes/jason/assets/img/layout/verbrauchsausweis-klein.webp" alt="" class="wp-image-762"/></a></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
        <div class="wp-block-buttons"><!-- wp:button {"className":"is-style-button-white"} -->
        <div class="wp-block-button is-style-button-white"><a class="wp-block-button__link" href="/app/themes/Jason/assets/downloads/muster-verbrauchsausweis.pdf" target="_blank" rel="noreferrer noopener">Beispiel ansehen</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","orientation":"horizontal"}} -->
        <div class="wp-block-buttons"><!-- wp:button -->
        <div class="wp-block-button"><a class="wp-block-button__link" href="/energieausweis2/verbrauchsausweis-wohngebaeude/">Verbauchsausweis erstellen</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons --></div>
        <!-- /wp:group --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->
        
        <!-- wp:paragraph -->
        <p></p>
        <!-- /wp:paragraph --></div>
        <!-- /wp:group -->' ),
    )
);