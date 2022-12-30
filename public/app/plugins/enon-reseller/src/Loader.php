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

use Enon\Logger;
use Enon\Task_Loader;
use Enon\Tasks\Config_User;
use Enon_Reseller\Models\Detector;
use Enon_Reseller\Models\Reseller;
use Enon_Reseller\Tasks\Add_CPT_Reseller;
use Enon_Reseller\Tasks\Add_Post_Meta;
use Enon_Reseller\Tasks\Admin\Config_Dashboard;
use Enon_Reseller\Tasks\Admin\Config_Dashboard_Widgets;
use Enon_Reseller\Tasks\CSV_Generator;
use Enon_Reseller\Tasks\Filters\Filter_Email_Template;
use Enon_Reseller\Tasks\Filters\Filter_Payment_Fee_Email;

use Enon_Reseller\Tasks\Filters\Filter_General;
use Enon_Reseller\Tasks\Filters\Filter_Confirmation_Email;
use Enon_Reseller\Tasks\Filters\Filter_Bill_Email;
use Enon_Reseller\Tasks\Filters\Filter_Website;
use Enon_Reseller\Tasks\Filters\Filter_Iframe;
use Enon_Reseller\Tasks\Filters\Filter_Schema;
use Enon_Reseller\Tasks\Filters\Filter_Template;

use Enon_Reseller\Tasks\Setup_Edd;

use Enon_Reseller\Tasks\Sparkasse\Add_CSV_Export as Sparkasse_CSV_Export;
use Enon_Reseller\Tasks\EVM\Add_Discounts as EVM_Discounts;
use Enon_Reseller\Tasks\Sparkasse\Add_Discounts as Sparkasse_Discounts;

/**
 * Whitelabel loader.
 *
 * @package Enon\Config
 */
class Loader extends Task_Loader {
    private $reseller;

    public function __construct() {
        $logger = new Logger('enon-reseller');
        parent::__construct( $logger );
    }

	/**
	 * Loading Scripts.
	 *
	 * @since 1.0.0
     * 
     * @todo Clean up! How to handle add_actions here.
	 */
	public function run() {
		$this->add_task( Add_CPT_Reseller::class );
        $this->add_task( Filter_Payment_Fee_Email::class );
        $this->add_task( EVM_Discounts::class, $this->logger );
        $this->add_task( Setup_Edd::class, $this->logger );

		if ( is_admin() && ! wp_doing_ajax() ) {            
            $this->add_backend_tasks();
            $this->run_tasks();
		} else {            
            add_action('init', [ $this, 'add_frontend_tasks'], 1, 0);
            add_action('init', [ $this, 'run_tasks'], 2, 0);
        }
	}

	/**
	 * Running admin tasks.
	 *
	 * @since 1.0.0
	 */
	public function add_backend_tasks() {
        $this->add_task( Add_Post_Meta::class );
        $this->add_task( Config_Dashboard::class );
        $this->add_task( Config_Dashboard_Widgets::class );
        $this->add_task( Config_User::class );        
        $this->add_task( CSV_Generator::class );
        
        // Sparkasse specific tasks.
        $this->add_task( Sparkasse_CSV_Export::class );
        $this->add_task( Sparkasse_Discounts::class, $this->logger );
    }

    /**
     * Running frontend tasks.
     * 
     *  @since 1.0.0
     */
    public function add_frontend_tasks() {
        if ( Detector::is_reseller_iframe() ) {
            $this->reseller = Detector::get_reseller_by_iframe();

            $this->add_task( Filter_Template::class, $this->reseller, $this->logger );
            $this->add_task( Filter_Iframe::class, $this->reseller, $this->logger );
            $this->add_task( Filter_Website::class, $this->reseller, $this->logger );   
        }elseif( Detector::is_reseller_ec_page() ) {
            $this->reseller = Detector::get_reseller_by_page();
        }

        if( ! $this->reseller instanceof Reseller ) {
            return;
        } 

        $this->set_affiliate_by_reseller( $this->reseller );

        $this->add_task( Filter_General::class, $this->reseller, $this->logger );
        $this->add_task( Filter_Email_Template::class, $this->reseller, $this->logger );
        $this->add_task( Filter_Confirmation_Email::class, $this->reseller, $this->logger );
        $this->add_task( Filter_Bill_Email::class, $this->reseller, $this->logger );
        $this->add_task( Filter_Schema::class, $this->reseller, $this->logger );

        $this->add_task( Setup_Edd::class, $this->logger );

         // Sparkasse specific tasks.
        if ( 321587 !== $this->reseller->get_id() ) {
            $this->add_task( Sparkasse_Discounts::class, $this->logger );
        }
    }

    /**
     * Set affiliate wp affiliate.
     * 
     * @param Reseller $reseller Reseller object.
     * 
     * @since 1.0.0
     */
    private function set_affiliate_by_reseller( Reseller $reseller ) {
        $this->logger->notice( 'Set reseller.', array( 'company_name', $reseller->data()->general->get_company_name() ) );

        $affiliate_id = $reseller->data()->general->get_affiliate_id();

        if ( ! empty( $affiliate_id ) ) {
            affiliate_wp()->tracking->referral = $affiliate_id;
            affiliate_wp()->tracking->set_affiliate_id( $affiliate_id );
        } else {
            $this->logger->notice( 'No affiliate id found.', array( 'reseller_id', $reseller->get_id() ) );
        }
    }
}

