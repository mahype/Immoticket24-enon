<?php
/**
 * Admin: New Referral View
 *
 * @package    AffiliateWP
 * @subpackage Admin/Referrals
 * @copyright  Copyright (c) 2014, Sandhills Development, LLC
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.2
 */
?>
<div class="wrap">

	<h2><?php _e( 'New Referral', 'affiliate-wp' ); ?></h2>

	<form method="post" id="affwp_add_referral">

		<?php
		/**
		 * Fires at the top of the new-referral admin screen.
		 *
		 * @since 1.0
		 */
		do_action( 'affwp_new_referral_top' );
		?>

		<p><?php _e( 'Use this screen to manually create a new referral record for an affiliate.', 'affiliate-wp' ); ?></p>

		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<label for="user_name"><?php _e( 'Affiliate', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<span class="affwp-ajax-search-wrap">
						<input type="text" name="user_name" id="user_name" class="affwp-user-search" data-affwp-status="active" autocomplete="off" />
					</span>
					<p class="description"><?php _e( 'Enter the name of the affiliate or enter a partial name or email to perform a search.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="amount"><?php _e( 'Amount', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="amount" id="amount" />
					<p class="description"><?php _e( 'The amount of the referral, such as 15.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="referral-types"><?php _e( 'Referral Type', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<select class="affwp-use-select2" name="type" id="referral-types">
						<?php foreach( affwp_get_referral_types() as $type_id => $type ) : ?>
							<option value="<?php echo esc_attr( $type_id ); ?>"><?php echo esc_html( $type['label'] ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php _e( 'Select the type of the referral.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="date"><?php _e( 'Date', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="date" id="date" class="affwp-datepicker" autocomplete="off" placeholder="<?php echo esc_attr( affwp_date_i18n( strtotime( 'today' ), 'm/d/y' ) ); ?>"/>
					<p class="description"><?php _e( 'Select or enter a date for this referral.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="description"><?php _e( 'Description', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="description" id="description" />
					<p class="description"><?php _e( 'Enter a description for this referral.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="reference"><?php _e( 'Reference', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="reference" id="reference" />
					<p class="description"><?php _e( 'Enter a reference for this referral (optional). Usually this would be the transaction ID of the associated purchase.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="context"><?php _e( 'Context', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="context" id="context" />
					<p class="description"><?php _e( 'Enter a context for this referral (optional). Usually this is used to help identify the payment system that was used for the transaction.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="custom"><?php _e( 'Custom', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="custom" id="custom" />
					<p class="description"><?php _e( 'Any custom data that should be stored with the referral (optional).', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="status"><?php _e( 'Status', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<?php $statuses = affwp_get_referral_statuses(); ?>
					<select class="affwp-use-select2" name="status" id="status">
						<?php
						foreach( $statuses as $status => $label ) :
							// Ensure Unpaid is selected by default.
							$selected = 'unpaid' === $status ? selected( 'unpaid', $status, false ) : '';
							?>
							<option value="<?php echo esc_attr( $status ); ?>"<?php echo $selected; ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php _e( 'Select the status of the referral.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

		</table>

		<?php
		/**
		 * Fires at the bottom of the new-referral admin screen.
		 *
		 * @since 1.0
		 */
		do_action( 'affwp_new_referral_bottom' );
		?>

		<?php echo wp_nonce_field( 'affwp_add_referral_nonce', 'affwp_add_referral_nonce' ); ?>
		<input type="hidden" name="affwp_action" value="add_referral" />

		<?php submit_button( __( 'Add Referral', 'affiliate-wp' ) ); ?>

	</form>

</div>
