<?php // phpcs:disable WordPress.Files.FileName.InvalidClassFileName -- Filename Ok for legacy file.
/**
 * AffiliateWP Scheduler class
 *
 * @package    AffiliateWP
 * @subpackage Core
 * @copyright  Copyright (c) 2022, Sandhills Development, LLC
 * @license    GPL2+
 * @since      2.9.5
 */

// phpcs:disable WordPress.Classes.ClassInstantiation.MissingParenthesis -- Legacy code uses no ().
// phpcs:disable Generic.ControlStructures.InlineControlStructure.NotAllowed -- Legacy code uses this format.

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AffiliateWP Scheduler class
 *
 * This class handles scheduled events.
 *
 * @since 2.9.5
 */
class Affiliate_WP_Scheduler {

	/**
	 * Group (for Action Scheduler).
	 *
	 * @since 2.9.6
	 *
	 * @var string
	 */
	private $group = 'affiliatewp';

	/**
	 * Setup for action scheduler events
	 *
	 * @since 2.9.5
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'schedule_actions' ) );
		register_deactivation_hook( AFFILIATEWP_PLUGIN_FILE, array( $this, 'unschedule_actions' ) );
	}

	/**
	 * Remove schedules on deactivation of AffiliateWP.
	 *
	 * Since schedules will fire actions that are only relevant to AffiliateWP,
	 * once AffiliateWP is deactivated, we will remove scheduled actions.
	 *
	 * @since 2.9.6
	 */
	public function unschedule_actions() {

		foreach ( array(
			'affwp_monthly_email_summaries',
			'affwp_daily_scheduled_events',
		) as $scheduled_action ) {

			if ( ! is_string( $scheduled_action ) ) {
				continue;
			}

			as_unschedule_all_actions( $scheduled_action );
		}
	}

	/**
	 * Schedules our actions
	 *
	 * @since 2.9.5
	 * @return void
	 *
	 * @see self::unschedule_actions() Where you should remove scheduled actions upon deactivation.
	 */
	public function schedule_actions() {

		$this->daily_events();
		$this->monthly_email_summaries();
	}

	/**
	 * Is something already scheduled in action scheduler?
	 *
	 * Just a wrapper for as_has_scheduled_action() or as_next_scheduled_action() for
	 * backwards compat.
	 *
	 * @since 2.9.6
	 * @since 2.9.6.1 See https://github.com/awesomemotive/AffiliateWP/issues/4451
	 *
	 * @param  string $action    The action hook.
	 * @param  array  $arguments Arguments.
	 * @return bool
	 */
	private function as_has_scheduled_action( $action, $arguments = array() ) {

		if ( function_exists( 'as_has_scheduled_action' ) ) {

			// Prefer this, only boolean expected back, easier to work with.
			return as_has_scheduled_action( $action, $arguments, $this->group );
		}

		// The timestamp for the next occurrence of a pending scheduled action, true for an async or in-progress action or false if there is no matching action.
		if (
			function_exists( 'as_next_scheduled_action' ) &&

			// In progress, must be scheduled.
			true === as_next_scheduled_action( $action, $arguments, $this->group ) ||

			// Next occurrence, must be scheduled.
			is_numeric( as_next_scheduled_action( $action, $arguments, $this->group ) )
		) {
			return true; // Must be scheduled.
		}

		return false;
	}

	/**
	 * Every 30 days, starting now.
	 *
	 * @since 2.9.6
	 * @since 2.9.6.1 See https://github.com/awesomemotive/AffiliateWP/issues/4451
	 *
	 * @return void
	 */
	public function monthly_email_summaries() {

		// Only if never added to Action Scheduler before.
		if ( $this->as_has_scheduled_action( 'affwp_monthly_email_summaries', array(), $this->group ) ) {
			return;
		}

		if ( ! function_exists( 'as_schedule_recurring_action' ) ) {
			return; // Can't find Action Scheduler.
		}

		// When we upgrade to 2.9.6.1, we might set this because we cleaned up an issue with email summaries in our upgrade routine.
		$send_now = get_option( 'affwp_email_summary_now', false );

		if ( 'no' === $send_now ) {

			// Now that we know, we don't need this option anymore.
			delete_option( 'affwp_email_summary_now' );
		}

		// Schedule a recurring action in action scheduler.
		as_schedule_recurring_action(
			'no' === $send_now

				// Don't send now, send in 30 days.
				? time() + ( DAY_IN_SECONDS * 30 )

				// Send the email now.
				: time(),
			DAY_IN_SECONDS * 30,
			'affwp_monthly_email_summaries',
			array(),
			$this->group
		);
	}

	/**
	 * Schedule daily events
	 *
	 * Schedule an action with the hook 'affwp_daily_scheduled_events' to run once each day
	 * so that our callback is run then.
	 *
	 * @access private
	 * @since 2.9.5
	 * @return void
	 */
	private function daily_events() {

		if ( $this->as_has_scheduled_action( 'affwp_daily_scheduled_events', array(), $this->group ) ) {
			return;
		}

		if ( ! function_exists( 'as_schedule_recurring_action' ) ) {
			return; // Can't find Action Scheduler.
		}

		as_schedule_recurring_action( strtotime( 'now' ), DAY_IN_SECONDS, 'affwp_daily_scheduled_events', array(), $this->group );
	}
}
new Affiliate_WP_Scheduler;
