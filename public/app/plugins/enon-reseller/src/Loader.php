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
use Enon_Reseller\Models\User_Detector;
use Enon_Reseller\Tasks\Add_CPT_Reseller;
use Enon_Reseller\Tasks\Add_Post_Meta;
use Enon_Reseller\Tasks\Config_User;
use Enon_Reseller\Tasks\CSV_Generator;
use Enon_Reseller\Tasks\Filters\Filter_Email_Template;
use Enon_Reseller\Tasks\Filters\Filter_Payment_Fee_Email;
use Enon_Reseller\Tasks\Setup_Enon;

use Enon_Reseller\Tasks\Filters\Filter_General;
use Enon_Reseller\Tasks\Filters\Filter_Confirmation_Email;
use Enon_Reseller\Tasks\Filters\Filter_Bill_Email;
use Enon_Reseller\Tasks\Filters\Filter_Website;
use Enon_Reseller\Tasks\Filters\Filter_Iframe;
use Enon_Reseller\Tasks\Filters\Filter_Schema;
use Enon_Reseller\Tasks\Filters\Filter_Template;

use Enon_Reseller\Tasks\Add_Energy_Certificate_Submission;

use Enon_Reseller\Tasks\Sparkasse\Add_CSV_Export;
use Enon_Reseller\Tasks\Sparkasse\Add_Sparkasse_Discounts;
use Enon_Reseller\Tasks\Sparkasse\Sparkasse_Setup_Edd;

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
		$this->add_task( Config_User::class );
		$this->add_task( CSV_Generator::class );
		$this->add_task( Add_CSV_Export::class );
		$this->add_task( Tasks\Admin\Loader::class, $this->logger() );

		try {
			$reseller = new Reseller( null, $this->logger() );
		} catch ( Exception $exception ) {
			$this->logger()->error( sprintf( $exception->getMessage() ) );
		}

		$this->add_task( Add_Post_Meta::class, $this->logger() );
		$this->add_task( Add_Energy_Certificate_Submission::class, $reseller, $this->logger() );
		$this->add_task( Filter_Payment_Fee_Email::class, $reseller, $this->logger() );

		$this->add_task( Setup_Enon::class );
	}

	/**
	 * Running frontend tasks.
	 *
	 * @since 1.0.0
	 */
	public function add_frontend_tasks() {
        // Only reseller customers get tasks
        if( ! User_Detector::is_reseller() ) {
            return;
        }

        $reseller_id = User_Detector::get_reseller_id();
        $reseller    = new Reseller( $reseller_id, $this->logger() );

        $this->logger()->notice( 'Set reseller.', array( 'company_name', $reseller->data()->general->get_company_name() ) );
        
        $this->add_reseller_tasks( $reseller );
        
        // Only start iframe scripts on iframe based url
        if ( User_Detector::is_iframe() ) {
            $this->add_iframe_tasks( $reseller );
        }
        
        // @todo Move to sparkasse
        $this->add_task( Sparkasse_Setup_Edd::class, $reseller, $this->logger() );
		$this->add_task( Add_Sparkasse_Discounts::class, $reseller, $this->logger() );
    }
    
    public function add_iframe_tasks( Reseller $reseller ) {
        $this->add_task( Filter_Template::class, $reseller, $this->logger() );
        $this->add_task( Filter_Iframe::class, $reseller, $this->logger() );
        $this->add_task( Filter_Website::class, $reseller, $this->logger() );
    }

    public function add_reseller_tasks( Reseller $reseller ) {
        $this->add_task( Setup_Enon::class );

        $this->add_task( Filter_General::class, $reseller, $this->logger() );
        $this->add_task( Filter_Email_Template::class, $reseller, $this->logger() );
        $this->add_task( Filter_Confirmation_Email::class, $reseller, $this->logger() );
        $this->add_task( Filter_Bill_Email::class, $reseller, $this->logger() );
        $this->add_task( Filter_Payment_Fee_Email::class, $reseller, $this->logger() );  
		
        $this->add_task( Filter_Schema::class, $reseller, $this->logger() );
        $this->add_task( Add_Energy_Certificate_Submission::class, $reseller, $this->logger() );
    }
}

