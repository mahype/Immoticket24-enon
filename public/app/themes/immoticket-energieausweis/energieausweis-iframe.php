<?php
/**
 * Template for Energieausweis-IFrames.
 *
 * @package immoticketenergieausweis
 */
?><!DOCTYPE html>
<html <?php echo $html_attrs; ?> <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php immoticketenergieausweis_wp_title( '|' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
<style type="text/css">
  .energieausweis-iframe {
    background-color: white;
    background-image: none;
  }

  .energieausweis-iframe .wrapper {
    padding: 0 30px;
  }

  .energieausweis-iframe .primary.col-md-12 {
    padding-right: 30px !important;
    padding-left: 30px !important;
  }
</style>
</head>

<body <?php body_class( 'energieausweis-iframe' ); ?>>

  <div class="wrapper">
    <div class="content">

      <div class="row">

        <main class="primary col-md-12" role="main">
          <?php if( have_posts() ) : ?>
            <?php while( have_posts() ) : the_post(); ?>
              <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h1><?php the_title(); ?></h1>
                <?php the_content(); ?>
              </article>
            <?php endwhile; ?>
          <?php endif; ?>
        </main>

      </div>

    </div>
  </div><!-- .wrapper -->

<?php wp_footer(); ?>

</body>
</html>
