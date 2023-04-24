<div id="affwp-affiliate-dashboard-creatives" class="affwp-tab-content">

	<h4><?php _e( 'Creatives', 'affiliate-wp' ); ?></h4>

	<?php

	$per_page  = 30;
	$page      = affwp_get_current_page_number();
	$pages     = absint( ceil( affiliate_wp()->creatives->count(

		/** This filter is documented below. -- Get all active creatives we can show to build pagination. */
		apply_filters( 'affwp_affiliate_dashboard_creatives_args', array( 'status' => 'active' ) )

	) / $per_page ) );

	/**
	 * Filter arguments used to show creatives on the affiliate area creatives tab.
	 *
	 * @since 2.13.0
	 *
	 * @param array $args Arguments.
	 */
	$args = apply_filters( 'affwp_affiliate_dashboard_creatives_args', array(
		'number' => $per_page,
		'offset' => $per_page * ( $page - 1 ),
		'status' => 'active',
	) );

	$creatives = affiliate_wp()->creative->affiliate_creatives( $args );

	?>

	<?php if ( $creatives ) : ?>

		<?php
		/**
		 * Fires immediately before creatives in the creatives tab of the affiliate area.
		 *
		 * @since 1.0
		 */
		do_action( 'affwp_before_creatives' );
		?>

		<?php echo $creatives; ?>

		<?php if ( $pages > 1 ) : ?>

			<p class="affwp-pagination">
				<?php
				echo paginate_links(
					array(
						'current'  => $page,
						'total'    => $pages,
						'add_args' => array(
							'tab' => 'creatives',
						),
					)
				);
				?>
			</p>

		<?php endif; ?>

		<?php
		/**
		 * Fires immediately after creatives in the creatives tab of the affiliate area.
		 *
		 * @since 1.0
		 */
		do_action( 'affwp_after_creatives' );
		?>

	<?php else : ?>

		<?php

		/**
		 * Fires immediately before creatives in the creatives tab of the affiliate area when there are no results.
		 *
		 * @since 2.12.0
		 */
		do_action( 'affwp_before_creatives_no_results' );
		?>

		<p class="affwp-no-results"><?php esc_html_e( 'Sorry, there are currently no creatives available.', 'affiliate-wp' ); ?></p>

		<?php

		/**
		 * Fires immediately after creatives in the creatives tab of the affiliate area when there are no results.
		 *
		 * @since 2.12.0
		 */
		do_action( 'affwp_after_creatives_no_results' );
		?>

	<?php endif; ?>

</div>
