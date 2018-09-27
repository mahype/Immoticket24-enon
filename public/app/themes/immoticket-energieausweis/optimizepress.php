<?php
/**
 * The OptimizePress template file.
 *
 * @package immoticketenergieausweis
 */

op_in_body();

get_header(); ?>

<?php op_in_body(); ?>

  <div class="container main-content">
    <?php
    //op_page_header();
    $GLOBALS['op_feature_area']->load_feature();
    if ( class_exists( 'LazyLoad_Images' ) ) {
    	echo LazyLoad_Images::add_image_placeholders( $GLOBALS['op_content_layout'] );
    } else {
    	echo $GLOBALS['op_content_layout'];
    }
    //op_page_footer();
    ?>
  </div>

<?php get_footer(); ?>
