<?php
/**
 * Reseller loader.
 *
 * @category Class
 * @package  Enon_Reseller
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller;

use Enon\Task_Loader;
use Enon\Models\Exceptions\Exception;

use Enon_Reseller\Models\Token;
use Enon_Reseller\Models\Reseller;

use Enon_Reseller\Tasks\Add_CPT_Reseller;

use Enon_Reseller\Tasks\Add_Post_Meta;
use Enon_Reseller\Tasks\Filters\Filter_Email_Template;
use Enon_Reseller\Tasks\Setup_Enon;

use Enon_Reseller\Tasks\Filters\Filter_General;
use Enon_Reseller\Tasks\Filters\Filter_Confirmation_Email;
use Enon_Reseller\Tasks\Filters\Filter_Bill_Email;
use Enon_Reseller\Tasks\Filters\Filter_Website;
use Enon_Reseller\Tasks\Filters\Filter_Iframe;
use Enon_Reseller\Tasks\Filters\Filter_Schema;
use Enon_Reseller\Tasks\Filters\Filter_Template;

use Enon_Reseller\Tasks\Add_Energy_Certificate_Submission;
use Enon_Reseller\Tasks\Add_Sparkasse_Discounts;

/**
 * Whitelabel loader.
 *
 * @package Enon\Config
 */
class Loader extends Task_Loader {
	/**
	 * Loading Scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_task( Add_CPT_Reseller::class );

		if ( is_admin() && ! wp_doing_ajax() ) {
			$this->add_admin_tasks();
		} else {
			$this->add_frontend_tasks();
		}

		$this->run_tasks();
	}

	/**
	 * Running admin tasks.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_tasks() {
		try {
			$reseller = new Reseller( null, $this->logger() );
		} catch ( Exception $exception ) {
			$this->logger()->error( sprintf( $exception->getMessage() ) );
		}

		$this->add_task( Add_Post_Meta::class, $this->logger() );
		$this->add_task( Add_Energy_Certificate_Submission::class, $reseller, $this->logger() );

		$this->add_task( Setup_Enon::class, $reseller, $this->logger() );
	}

	/**
	 * Running frontend tasks.
	 *
	 * @since 1.0.0
	 */
	public function add_frontend_tasks() {
		$token = new Token();

		// No token, no action.
		if ( empty( $token->get() ) ) {
			return;
		}

		// phpcs:ignore
		$this->logger()->notice('Got reseller token.', array( 'token', $token->get() ) );

		try {
			$reseller = new Reseller( $token, $this->logger() );
		} catch ( Exception $exception ) {
			// phpcs:ignore
			$this->logger()->error( 'Exception caught', array( 'exception' => $exception ) );
		}

		$this->logger()->notice( 'Set reseller.', array( 'company_name', $reseller->data()->general->get_company_name() ) );

		$this->add_task( Setup_Enon::class, $reseller, $this->logger() );

		$this->add_task( Filter_Template::class, $reseller, $this->logger() );
		$this->add_task( Filter_Email_Template::class, $reseller, $this->logger() );
		$this->add_task( Filter_General::class, $reseller, $this->logger() );
		$this->add_task( Filter_Confirmation_Email::class, $reseller, $this->logger() );
		$this->add_task( Filter_Bill_Email::class, $reseller, $this->logger() );
		$this->add_task( Filter_Website::class, $reseller, $this->logger() );
		$this->add_task( Filter_Iframe::class, $reseller, $this->logger() );
		$this->add_task( Filter_Schema::class, $reseller, $this->logger() );

		$this->add_task( Add_Energy_Certificate_Submission::class, $reseller, $this->logger() );
		$this->add_task( Add_Sparkasse_Discounts::class, $reseller, $this->logger() );
	}
}

