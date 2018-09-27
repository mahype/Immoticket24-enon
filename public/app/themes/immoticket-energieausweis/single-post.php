<?php
/**
 * The post template file.
 *
 * @package immoticketenergieausweis
 */

get_header(); ?>

    <div class="row">

      <main class="primary col-md-<?php echo ( is_active_sidebar( 'blog' ) ? '8' : '12' ); ?>" role="main">

        <?php if( have_posts() ) : ?>
          <?php while( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
              <h1><?php the_title(); ?></h1>
              <p><small><?php the_date( '', __( 'Veröffentlicht am ', 'immoticketenergieausweis' ), sprintf( __( ' von %s', 'immoticketenergieausweis' ), get_the_author() ) ); ?></small></p>
              
              <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail(); ?>
              <?php endif; ?>

              <?php the_content(); ?>

              <div class="row">
                <div class="col-sm-6 text-left">
                  <?php previous_post_link(); ?>
                </div>
                <div class="col-sm-6 text-right">
                  <?php next_post_link(); ?>
                </div>
              </div>
              
              <?php comments_template(); ?>
            </article>
          <?php endwhile; ?>
        <?php endif; ?>
      
      </main>

      <?php if( is_active_sidebar( 'blog' ) ) : ?>
      <aside class="secondary col-md-4" role="complementary">
        <?php get_sidebar( 'blog' ); ?>
      </aside>
      <?php endif; ?>

    </div>

<?php get_footer(); ?>
