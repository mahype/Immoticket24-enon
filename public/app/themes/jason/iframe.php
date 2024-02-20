<?php
/**
 * Template for Energieausweis in IFrames.
 *
 * @package immoticketenergieausweis
 */
?><!DOCTYPE html>
<html <?php  echo ! empty($html_attrs) ? $html_attrs: ''; ?> <?php language_attributes(); ?>>
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
			padding: 0;
		}
		.energieausweis-iframe main.col-md-12 {
			float:none;
			width: auto;
		}
		<?php do_action('enon_iframe_css' ); ?>
	</style>
</head>

<body <?php body_class( 'energieausweis-iframe' ); ?>>

<div class="wrapper">
	<div class="content">

		<div class="row">

			<main class="primary col-md-12" role="main">
				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : the_post(); ?>
							<?php the_content(); ?>
						</article>
					<?php endwhile; ?>
				<?php endif; ?>
			</main>

		</div>

	</div>
</div><!-- .wrapper -->

<?php wp_footer(); ?>

<!-- Start Iframe JS //-->
<script type="text/javascript">
<?php do_action( 'enon_page_js' ); ?>
<?php do_action( 'enon_iframe_js' ); ?>
</script>
<!-- End Enon Iframe JS //-->

</body>
</html>
