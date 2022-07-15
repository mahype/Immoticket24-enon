<?php

register_block_pattern(
    'enon/das-ist-ein-verbrauchsausweis',
    array(
        'title'       => __( 'Das ist ein Verbrauchausweis', 'enon-patterns' ),
        'description' => _x( 'Darstellung eines Beispiel-Verbrauchsausweis.', 'enon-patterns' ),
        'categories'  => ['content'],
        'content'     => pattern_replace_urls( '<!-- wp:group {"gradient":"white-green-to-green-white","className":"is-style-group-main","layout":{"inherit":true}} -->
        <div class="wp-block-group is-style-group-main has-white-green-to-green-white-gradient-background has-background"><!-- wp:heading {"textAlign":"left"} -->
        <h2 class="has-text-align-left" id="das-ist-ein-verbrauchsausweis">Das ist ein <strong>Verbrauchsausweis</strong>:</h2>
        <!-- /wp:heading -->
        
        <!-- wp:image {"align":"center","sizeSlug":"large"} -->
        <div class="wp-block-image"><figure class="aligncenter size-large"><img src="https://enon.test/app/uploads/2017/01/verbrauchsausweis_seite1.jpg" alt=""/></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">So sieht der Verbrauchsausweis aus.</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:gallery {"columns":5,"linkTo":"media"} -->
        <figure class="wp-block-gallery has-nested-images columns-5 is-cropped"><!-- wp:image {"id":42031,"sizeSlug":"large","linkDestination":"media"} -->
        <figure class="wp-block-image size-large"><a href="https://enon.test/app/uploads/2017/01/verbrauchsausweis_seite1.jpg"><img src="https://enon.test/app/uploads/2017/01/verbrauchsausweis_seite1.jpg" alt="Verbrauchsausweis Seite 1" class="wp-image-42031"/></a></figure>
        <!-- /wp:image -->
        
        <!-- wp:image {"id":42032,"sizeSlug":"large","linkDestination":"media"} -->
        <figure class="wp-block-image size-large"><a href="https://enon.test/app/uploads/2017/01/verbrauchsausweis_seite2.jpg"><img src="https://enon.test/app/uploads/2017/01/verbrauchsausweis_seite2.jpg" alt="Verbrauchsausweis Seite 2" class="wp-image-42032"/></a></figure>
        <!-- /wp:image -->
        
        <!-- wp:image {"id":42033,"sizeSlug":"large","linkDestination":"media"} -->
        <figure class="wp-block-image size-large"><a href="https://enon.test/app/uploads/2017/01/verbrauchsausweis_seite3.jpg"><img src="https://enon.test/app/uploads/2017/01/verbrauchsausweis_seite3.jpg" alt="Verbrauchsausweis Seite 3" class="wp-image-42033"/></a></figure>
        <!-- /wp:image -->
        
        <!-- wp:image {"id":42034,"sizeSlug":"large","linkDestination":"media"} -->
        <figure class="wp-block-image size-large"><a href="https://enon.test/app/uploads/2017/01/verbrauchsausweis_seite4.jpg"><img src="https://enon.test/app/uploads/2017/01/verbrauchsausweis_seite4.jpg" alt="Verbrauchsausweis Seite 4" class="wp-image-42034"/></a></figure>
        <!-- /wp:image -->
        
        <!-- wp:image {"id":42035,"sizeSlug":"large","linkDestination":"media"} -->
        <figure class="wp-block-image size-large"><a href="https://enon.test/app/uploads/2017/01/verbrauchsausweis_seite5.jpg"><img src="https://enon.test/app/uploads/2017/01/verbrauchsausweis_seite5.jpg" alt="Verbrauchsausweis Seite 5" class="wp-image-42035"/></a></figure>
        <!-- /wp:image --></figure>
        <!-- /wp:gallery -->
        
        <!-- wp:heading {"textAlign":"center","textColor":"green-moss","className":"center"} -->
        <h2 class="has-text-align-center center has-green-moss-color has-text-color" id="downloads-2">Downloads</h2>
        <!-- /wp:heading -->
        
        <!-- wp:columns -->
        <div class="wp-block-columns"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:image {"align":"center","id":436,"width":47,"height":60,"sizeSlug":"full","linkDestination":"custom"} -->
        <div class="wp-block-image"><figure class="aligncenter size-full is-resized"><a href="/app/themes/jason/assets/logos/downloads/muster-verbrauchsausweis.pdf"><img src="/app/themes/jason/assets/img/icons/dokument-pdf.webp" alt="" class="wp-image-436" width="47" height="60"/></a></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","level":3,"textColor":"green-moss","className":"text-center md:text-left"} -->
        <h3 class="has-text-align-center text-center md:text-left has-green-moss-color has-text-color" id="muster-verbrauchsausweis"><a href="/app/themes/jason/assets/logos/downloads/muster-verbrauchsausweis.pdf">Muster Verbrauchsausweis</a></h3>
        <!-- /wp:heading --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
        <div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"green","textColor":"white","style":{"border":{"radius":"5px"}}} -->
        <div class="wp-block-button"><a class="wp-block-button__link has-white-color has-green-background-color has-text-color has-background" href="/energieausweis2/verbrauchsausweis-wohngebaeude/" style="border-radius:5px">Verbrauchsausweis erstellen &gt;&gt;</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons --></div>
        <!-- /wp:group -->' ),
    )
);