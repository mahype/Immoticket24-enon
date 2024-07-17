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
use Enon\Models\Edd\Payment;
use Enon\Models\Enon\Energieausweis;
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
use Enon_Reseller\Tasks\EVM\Add_Discounts as EVM_Discounts;
use Enon_Reseller\Tasks\VNR\Add_Discounts as VNR_Discounts;

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

        if( wp_doing_ajax() ) {
            return;
        }

        add_action('edd_update_payment_status', [ $this, 'update_payment_status' ], 1, 3);

		if ( is_admin() ) {            
            $this->add_backend_tasks();
            $this->run_tasks();            
		} else {            
            add_action('init', [ $this, 'add_frontend_tasks_by_iframe'], 1, 0);
            add_action('template_redirect', [ $this, 'add_frontend_tasks_by_page'], 1, 0);                        
        }
	}

    /**
     * Update payment status.
     * 
     * @param mixed $payment_id 
     * @param mixed $new_status 
     * @param mixed $old_status 
     * @return void 
     */
    public function update_payment_status( $payment_id, $new_status, $old_status ) {       
        // Do nothing if status is not changed.
        if( $new_status === $old_status) {
            return;
        }

        // Do nothing if status is not changed to publish.
        if( $new_status !== 'publish' ) {
            return;
        }
        
        $payment = new Payment( $payment_id ); 
        $reseller_id = get_post_meta( $payment->get_energieausweis_id(), 'reseller_id', true);
        
        // We only need to update if reseller id is set.
        if( empty( $reseller_id ) ) {
            return;
        }

        $this->reseller = new Reseller( $reseller_id );
        $this->logger->notice( 'Updating payment status.', array( 'energy_cerificate_id', $payment->get_energieausweis_id(), 'reseller_id', $reseller_id ) );
        
        // Load reseller scritps for email filters etc.
        $this->load_reseller_scripts();
        
        if( $this->maybe_add_referal( $payment ) !== false ) {
            $this->logger->notice( 'Referal added.', array( 'reference', $payment->get_id() ) );
        } else {
            $this->logger->notice( 'Referal not added.', array( 'reference', $payment->get_id() ) );
        }
    }

    /**
     * Add referal to affiliate WP.
     * 
     * @param Payment $payment
     * 
     * @return void
     */
    protected function maybe_add_referal( Payment $payment ) {
        global $wpdb;

        $affiliate_id = $this->reseller->data()->general->get_affiliate_id();
        $energieausweis = $payment->get_energieausweis();
        $amount = $payment->get_amount();        

        // Check Affiliate WP Referal table for existing entries by using reference.
        $referral = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}affiliate_wp_referrals WHERE reference = %s", $payment->get_id() ) );

        // Is there any entry?
        if( ! empty( $referral ) ) {
            $this->logger->notice( 'Referal already exists.', array( 'reference', $payment->get_id() ) );
            return;
        }

        $amount 	 = $amount > 0 ? affwp_calc_referral_amount( $amount, $affiliate_id ) : 0;
        $description = $energieausweis->get_title();
        $context     = 'edd';
        $campaign    = '';
        $reference   = $payment->get_id();
        $type        = 'sale';
        $visit_id    = 0; 

        // Create a new referral
        return  affiliate_wp()->referrals->add( apply_filters( 'affwp_insert_pending_referral', array(
                'affiliate_id' => $affiliate_id,
                'amount'       => $amount,
                'status'       => 'pending',
                'description'  => $description,
                'context'      => $context,
                'campaign'     => $campaign,
                'reference'    => $reference,
                'type'         => $type,
                'visit_id'     => $visit_id,
        ), $amount, $reference, $description, $affiliate_id, $visit_id, array(), $context ) );
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
        $this->add_task( VNR_Discounts::class, $this->logger );
    }

    public function add_frontend_tasks_by_iframe() {
        if ( ! Detector::is_reseller_iframe() ) {
            return;
        }

        $reseller = Detector::get_reseller_by_iframe();

        if( empty( $reseller ) ) {
            $this->logger->alert('Reseller not found for iframe token.', array( 'iframe_token' => Detector::get_iframe_token() ) );    
            return;
        }

        $this->reseller = $reseller;
        $this->set_affiliate_by_reseller( $this->reseller );

        $this->load_iframe_scripts();
        $this->load_reseller_scripts();

         // Sparkasse specific tasks.
        if ( 321587 === $this->reseller->get_id() ) {
            $this->add_task( Sparkasse_Discounts::class, $this->logger );
        }

        $this->run_tasks();
    }

    public function add_frontend_tasks_by_page() {
        if ( ! Detector::is_reseller_ec_page() ) {
            return;
        }

        $this->reseller = Detector::get_reseller_by_page();
        $this->set_affiliate_by_reseller( $this->reseller );
        $this->load_reseller_scripts();

         // Sparkasse specific tasks.
        if ( 321587 !== $this->reseller->get_id() ) {
            $this->add_task( Sparkasse_Discounts::class, $this->logger );
        }

        $this->run_tasks();
    }

    private function load_reseller_scripts() {
        $this->add_task( Filter_General::class, $this->reseller, $this->logger );
        $this->add_task( Filter_Email_Template::class, $this->reseller, $this->logger );
        $this->add_task( Filter_Confirmation_Email::class, $this->reseller, $this->logger );
        $this->add_task( Filter_Bill_Email::class, $this->reseller, $this->logger );
        $this->add_task( Filter_Schema::class, $this->reseller, $this->logger );

        $this->run_tasks();
    }

    private function load_iframe_scripts() {
        $this->add_task( Filter_Template::class, $this->reseller, $this->logger );
        $this->add_task( Filter_Iframe::class, $this->reseller, $this->logger );
        $this->add_task( Filter_Website::class, $this->reseller, $this->logger );

        $this->run_tasks();
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
            $this->logger->notice( 'Set affiliate id for reseller.', array( 'company_name', $reseller->data()->general->get_company_name(), 'affiliate_id', $affiliate_id ) );
        } else {
            $this->logger->notice( 'No affiliate id found.', array( 'reseller_id', $reseller->get_id() ) );
        }
    }
}

