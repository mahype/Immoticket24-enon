<?php
/**
 * The page template file.
 *
 * Template Name: Seite ohne Titel
 *
 * @package immoticketenergieausweis
 */

get_header(); ?>

    <div class="row">

      <main class="primary col-md-<?php echo ( is_active_sidebar( 'primary' ) ? '8' : '12' ); ?>" role="main">

        <?php if( have_posts() ) : ?>
          <?php while( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
              <?php the_content(); ?>
            </article>
          <?php endwhile; ?>
        <?php endif; ?>

      </main>

      <?php if( is_active_sidebar( 'primary' ) ) : ?>
      <aside class="secondary col-md-4" role="complementary">
        <?php get_sidebar( 'primary' ); ?>
      </aside>
      <?php endif; ?>

    </div>

<?php get_footer(); ?>
