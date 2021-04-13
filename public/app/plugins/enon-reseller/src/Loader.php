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
use Enon_Reseller\Tasks\Sparkasse\Add_Discounts as Sparkasse_Discounts;

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
     * 
     * @todo Clean up! How to handle add_actions here.
	 */
	public function run() {
		$this->add_task( Add_CPT_Reseller::class );
        $this->add_task( Filter_Payment_Fee_Email::class );  

		if ( is_admin() && ! wp_doing_ajax() ) {
            $this->add_backend_tasks();
            $this->run_tasks();
		} else {
            $this->add_frontend_tasks_by_iframe();
            $this->run_tasks();

            // @todo How do we use tasks here? Clean up!
            add_action( 'template_redirect', array( $this, 'add_frontend_tasks_by_page' ), 1 );            
        }
	}

	/**
	 * Running admin tasks.
	 *
	 * @since 1.0.0
     * 
     * @todo Clean up!
	 */
	public function add_backend_tasks() {
        $this->add_task( Add_Post_Meta::class );

        $this->add_task( Config_Dashboard::class );
        $this->add_task( Config_Dashboard_Widgets::class );
        $this->add_task( Config_User::class );        
        
        $this->add_task( CSV_Generator::class );
        
        $this->add_task( Setup_Edd::class, $this->logger() );
        
        $this->add_sparkasse_backend_tasks();
    }

    /**
     * Add sparkasse backend tasks.
     * 
     * @since 1.0.0
     */
    private function add_sparkasse_backend_tasks() {
        $this->add_task( Sparkasse_CSV_Export::class );
        $this->add_task( Sparkasse_Discounts::class, $this->logger() );
    }

	/**
	 * Running frontend tasks.
	 *
	 * @since 1.0.0
     * 
     * @todo Clean up energy certificate submission. Move loggers to exception handled logs.
	 */
	public function add_frontend_tasks_by_iframe() {
        $reseller = '';
        
        if ( ! Detector::is_reseller_iframe() ) {
            return;
        }        

        $reseller = Detector::get_reseller_by_iframe();

        if( ! $reseller instanceof Reseller ) {
            wp_die( 'Reseller not found');
        }
        
        $this->add_reseller_frontend_tasks( $reseller );              
    }

    /**
	 * Running frontend tasks.
	 *
	 * @since 1.0.0
	 */
	public function add_frontend_tasks_by_page() {
        $reseller = '';

        if ( ! Detector::is_reseller_ec_page() ) {
            return;
        }             
        
        $reseller = Detector::get_reseller_by_page();
        
        if( ! $reseller instanceof Reseller ) {
            wp_die( 'Reseller not found');
        }

        $this->add_reseller_frontend_tasks( $reseller );

        add_action( 'template_redirect', array( $this, 'run_tasks' ), 5 );
    }

    /**
     * Add reseller scripts.
     * 
     * @param Reseller $reseller Reseller object.
     * 
     * @since 1.0.0
     */
    private function add_reseller_frontend_tasks( Reseller $reseller ) {
        $this->logger()->notice( 'Set reseller.', array( 'company_name', $reseller->data()->general->get_company_name() ) );

        $this->add_sparkasse_frontend_tasks( $reseller );

        if ( Detector::is_reseller_iframe() ) {
            $this->add_task( Filter_Template::class, $reseller, $this->logger() );
            $this->add_task( Filter_Iframe::class, $reseller, $this->logger() );
            $this->add_task( Filter_Website::class, $reseller, $this->logger() );
        }
        
        $affiliate_id = $reseller->data()->general->get_affiliate_id();

        if ( ! empty( $affiliate_id ) ) {
            affiliate_wp()->tracking->referral = $affiliate_id;
            affiliate_wp()->tracking->set_affiliate_id( $affiliate_id );
        }

        $this->add_task( Filter_General::class, $reseller, $this->logger() );
        $this->add_task( Filter_Email_Template::class, $reseller, $this->logger() );
        $this->add_task( Filter_Confirmation_Email::class, $reseller, $this->logger() );
        $this->add_task( Filter_Bill_Email::class, $reseller, $this->logger() );
        $this->add_task( Filter_Schema::class, $reseller, $this->logger() );

            // @todo Clean up!
        $this->add_task( Setup_Edd::class, $this->logger() );
    }

    /**
     * Add sparkasse frontend tasks.
     * 
     * @param Reseller $reseller Reseller object.
     * 
     * @since 1.0.0
     */
    private function add_sparkasse_frontend_tasks( Reseller $reseller ) {
        if ( 321587 !== $reseller->get_id() ) {
			return;
        }

        $this->add_task( Sparkasse_Discounts::class, $this->logger() );
    }
}

