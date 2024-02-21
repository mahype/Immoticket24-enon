<?php

register_block_pattern(
    'enon/das-ist-ein-bedarfsausweis',
    array(
        'title'       => __( 'Das ist ein Bedarfsausweis', 'enon-patterns' ),
        'description' => _x( 'Darstellung eines Beispiel-Bedarfsausweis.', 'enon-patterns' ),
        'categories'  => ['content'],
        'content'     => pattern_replace_urls( '<!-- wp:group {"gradient":"white-green-to-green-white","className":"is-style-group-main"} -->
        <div class="wp-block-group is-style-group-main has-white-green-to-green-white-gradient-background has-background"><!-- wp:heading {"textAlign":"left"} -->
        <h2 class="has-text-align-left" id="das-ist-ein-bedarfsausweis">Das ist ein <strong>Bedarfsausweis</strong></h2>
        <!-- /wp:heading -->
        
        <!-- wp:image {"align":"center","sizeSlug":"large"} -->
        <div class="wp-block-image"><figure class="aligncenter size-large"><img src="https://energieausweis.de/app/uploads/2017/01/bedarfsausweis_seite1-1.jpg" alt=""/></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center">So sieht der Bedarfsausweis aus.</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:gallery {"columns":5,"linkTo":"media"} -->
        <figure class="wp-block-gallery has-nested-images columns-5 is-cropped"><!-- wp:image {"id":42011,"sizeSlug":"large","linkDestination":"media"} -->
        <figure class="wp-block-image size-large"><a href="https://energieausweis.de/app/uploads/2017/01/bedarfsausweis_seite1-1.jpg"><img src="https://energieausweis.de/app/uploads/2017/01/bedarfsausweis_seite1-1.jpg" alt="Bedarfsausweis Seite 1" class="wp-image-42011"/></a></figure>
        <!-- /wp:image -->
        
        <!-- wp:image {"id":42004,"sizeSlug":"large","linkDestination":"media"} -->
        <figure class="wp-block-image size-large"><a href="https://energieausweis.det/app/uploads/2017/01/bedarfsausweis_seite2.jpg"><img src="https://energieausweis.de/app/uploads/2017/01/bedarfsausweis_seite2.jpg" alt="Bedarfsausweis Seite 2" class="wp-image-42004"/></a></figure>
        <!-- /wp:image -->
        
        <!-- wp:image {"id":42006,"sizeSlug":"large","linkDestination":"media"} -->
        <figure class="wp-block-image size-large"><a href="https://energieausweis.de/app/uploads/2017/01/bedarfsausweis_seite3.jpg"><img src="https://energieausweis.de/app/uploads/2017/01/bedarfsausweis_seite3.jpg" alt="Bedarfsausweis Seite 3" class="wp-image-42006"/></a></figure>
        <!-- /wp:image -->
        
        <!-- wp:image {"id":42007,"sizeSlug":"large","linkDestination":"media"} -->
        <figure class="wp-block-image size-large"><a href="https://energieausweis.de/app/uploads/2017/01/bedarfsausweis_seite4.jpg"><img src="https://energieausweis.de/app/uploads/2017/01/bedarfsausweis_seite4.jpg" alt="Bedarfsausweis Seite 4" class="wp-image-42007"/></a></figure>
        <!-- /wp:image -->
        
        <!-- wp:image {"id":42008,"sizeSlug":"large","linkDestination":"media"} -->
        <figure class="wp-block-image size-large"><a href="https://energieausweis.de/app/uploads/2017/01/bedarfsausweis_seite5.jpg"><img src="https://energieausweis.de/app/uploads/2017/01/bedarfsausweis_seite5.jpg" alt="Bedarfsausweis Seite 5" class="wp-image-42008"/></a></figure>
        <!-- /wp:image --></figure>
        <!-- /wp:gallery -->
        
        <!-- wp:heading {"textAlign":"center","level":3} -->
        <h3 class="has-text-align-center" id="downloads">Downloads</h3>
        <!-- /wp:heading -->
        
        <!-- wp:columns -->
        <div class="wp-block-columns"><!-- wp:column -->
        <div class="wp-block-column"><!-- wp:image {"align":"center","id":436,"width":47,"height":60,"sizeSlug":"full","linkDestination":"custom"} -->
        <div class="wp-block-image"><figure class="aligncenter size-full is-resized"><a href="/app/themes/jason/assets/downloads/muster-bedarfsausweis.pdf"><img src="/app/themes/jason/assets/img/icons/dokument-pdf.webp" alt="" class="wp-image-436" width="47" height="60"/></a></figure></div>
        <!-- /wp:image -->
        
        <!-- wp:heading {"textAlign":"center","level":3,"textColor":"green-moss","className":"text-center md:text-left"} -->
        <h3 class="has-text-align-center text-center md:text-left has-green-moss-color has-text-color" id="muster-bedarfsausweis"><a href="/app/themes/jason/assets/downloads/muster-bedarfsausweis.pdf">Muster Bedarfsausweis</a></h3>
        <!-- /wp:heading --></div>
        <!-- /wp:column --></div>
        <!-- /wp:columns -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","orientation":"horizontal"}} -->
        <div class="wp-block-buttons"><!-- wp:button {"textColor":"white","style":{"color":{"background":"#3da81d"},"border":{"radius":"5px"}}} -->
        <div class="wp-block-button"><a class="wp-block-button__link has-white-color has-text-color has-background" href="https://energieausweis.de/energieausweis2/bedarfsausweis-wohngebaeude/" style="border-radius:5px;background-color:#3da81d">Bedarfsausweis erstellen &gt;&gt;</a></div>
        <!-- /wp:button --></div>
        <!-- /wp:buttons --></div>
        <!-- /wp:group -->' ),
    )
);