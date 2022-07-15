<?php
/**
 * AffiliateWP Scheduler class
 *
 * @package    AffiliateWP
 * @subpackage Core
 * @copyright  Copyright (c) 2022, Sandhills Development, LLC
 * @license    GPL2+
 * @since      2.9.5
 */

// Exit if accessed directly
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
	 * Setup for action scheduler events
	 *
	 * @since 2.9.5
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'schedule_events' ) );
	}

	/**
	 * Schedules our events
	 *
	 * @since 2.9.5
	 * @return void
	 */
	public function schedule_events() {
		$this->daily_events();
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
		if (
			function_exists( 'as_has_scheduled_action' ) &&
			true === as_has_scheduled_action( 'affwp_daily_scheduled_events' )
		) {
			// Bail if already scheduled and using the more efficient as_has_scheduled_action() function if available.
			return;
		} elseif ( false !== as_next_scheduled_action( 'affwp_daily_scheduled_events' )  ) {
			/*
			 * The function as_next_scheduled_action() returns an integer for an
			 * already pending schedule or true for an in-progress one, and false if
			 * there is no matching action scheduled.
			 *
			 * So we're bailing here if there is a pending or in-progress one already.
			 */
			return;
		}

		as_schedule_recurring_action( strtotime( 'now' ), DAY_IN_SECONDS, 'affwp_daily_scheduled_events' );
	}

}
new Affiliate_WP_Scheduler;
