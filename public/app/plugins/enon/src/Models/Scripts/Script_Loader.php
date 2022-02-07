<?php 
/**
 * Script Loader.
 *
 * @category Class
 * @package  Enon\Models\Scripts
 * @author   Sven Wagener
 * @link     https://awesome.ug
 */

namespace Enon\Models\Scripts;

use ReflectionClass;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;
use Enon\Models\Edd\Payment;
use Enon\Models\Enon\Enon_Location;
use Enon\Models\Exceptions\Exception;
use WPENON\Model\EnergieausweisManager;
use WPENON\Model\Energieausweis;

/**
 * Class Script_Loader.
 * 
 * @since 2020-09-11.
 */
abstract class Script_Loader implements Actions, Task {
    /**
     * Run scripts.
     * 
     * @since 2020-09-11
     */
    public function run() {
        $this->add_actions();
    }

    /**
     * Add actions.
     * 
     * @since 2020-09-11
     */
    public function add_actions() {
        add_action( 'wp_footer', [ $this, 'controller' ] );
    }

    /**
     * Controller.
     * 
     * @since 2020-09-11
     */
    public function controller() {
        if ( Enon_Location::cart() ) {
            $this->cart();
        }

        if ( Enon_Location::ec_funnel() ) {
            $this->ec_funnel();
        }

        if ( Enon_Location::ec_funnel_started() ) {
            $this->ec_funnel_editing_started();
        }

        if ( Enon_Location::ec_funnel_started() && $this->contacting_allowed() ) {
            $this->ec_funnel_contacting_allowed();
        }

        if ( Enon_Location::ec_registration() ) {
            $this->ec_registration();
        }

        if ( Enon_Location::ec_edit() ) {
            $this->ec_edit();
        }

        if ( Enon_Location::success() ) {
            $this->success();
        }

        if ( Enon_Location::failed() ) {
            $this->failed();
        }

        echo $this->base_script();
    }

    /**
     * Base script to load in Borlabs.
     */
    protected function base_script() {
        $reflect = new ReflectionClass( $this );

        $function_name = strtolower( $reflect->getShortName() );
        $js_files      = '';
        $js            = '';

        if ( count( $this->script_files() ) > 0 ) {            
            foreach( $this->script_files()  AS $file ) {
                $js_files.= sprintf( 'var %s = document.createElement("script");', $function_name );
                $js_files.= sprintf( '%s.src = \'%s\';', $function_name, $file );
                $js_files.= sprintf( 'document.body.appendChild(%s);', $function_name );
                $js_files.= sprintf( 'console.log("Loaded script: %s");', $function_name );
            }
        }

        if ( ! empty( $this->script() ) || count( $this->script_files() ) > 0 ) {
            $js = sprintf( '<script>window.load_%s=function(){%s}</script>', $function_name, $this->script() . $js_files );
        }

        $js = '<!-- Start scripts for ' . get_class( $this ) . ' //-->' . $js . '<!-- End scripts for ' . get_class( $this ) . ' //-->';
        
        return $js;
    }

    /**
     * JavaScript functionality.
     * 
     * @since 2020-09-11.
     */
    protected function script() : string {
        return '';
    }

    /**
     * JavaScript scripts to load.
     * 
     * @since 2020-09-11.
     */
    protected function script_files() : array {
        return array();
    }

    /**
     * Scripts for whole fronend.
     * 
     * @since 2020-09-11.
     */
    protected function frontend() {}

    /**
     * Funnel page scripts if contacting is allowed.
     * 
     * @since 2020-09-11.
     */
    protected function ec_funnel_contacting_allowed() {}

    /**
     * Funnel page scripts if energy certificate is started.
     * 
     * @since 2020-09-11.
     */
    protected function ec_funnel_editing_started() {}

    /**
     * Funnel page scripts.
     * 
     * @since 2020-09-11.
     */
    protected function ec_funnel() {}

    /**
     * Registration page.
     * 
     * @since 2020-09-11.
     */
    protected function ec_registration() {}

    /**
     * Energy certificate edit page.
     * 
     * @since 2020-09-11.
     */
    protected function ec_edit() {}

    /**
     * Cart page.
     * 
     * @since 2020-09-11.
     */
    protected function cart() {}

    /**
     * Success page.
     * 
     * @since 2020-09-11.
     */
    protected function success() {}

    /**
     * Failed page.
     * 
     * @since 2020-09-11.
     */
    protected function failed() {}
    
    /**
    * Everywhere.
    * 
    * @since 2020-09-11.
    */
   protected function whole_page() {}

    /**
	 * Get current ec (works on funnel pages)
	 * 
	 * @return Energieausweis Energy certificate object.
	 * 
	 * @since 2020-09-11
	 */
	protected function ec() {
		if( ! Enon_Location::ec_funnel_started() ) {
			throw new Exception('ec() functions cannot be used outside funnel.');
        }
        
        if ( Enon_Location::cart() ) {
            $ec = $this->get_ec_in_cart();
        } else if ( Enon_Location::success() ) {
            $ec = $this->get_ec_in_success_page();
        } else {
            $ec_manager = EnergieausweisManager::instance();
            $ec = $ec_manager::getEnergieausweis();
        }

		if ( ! $ec ) {
			return false;
		}

		return $ec;
    }

    /**
     * Get ec in cart.
     * 
     * @return Energieausweis
     * 
     * @since 2020-09-16
     */
    public function get_ec_in_cart() {
        $cart_items = EDD()->cart->get_contents();

        if( count( $cart_items ) === 0 ) {
            return false;
        }

        $ec_id = $cart_items[0]['id'];
        return  new Energieausweis( $ec_id );
    }

    /**
     * Get ec in success page.
     * 
     * @return Energieausweis
     * 
     * @since 2020-09-16
     */
    public function get_ec_in_success_page() {
        global $edd_receipt_args;

        $payment_id = $edd_receipt_args['id'];

        if( empty( $payment_id ) ) {
            return;
        }

        $payment = new Payment( $payment_id );
        $ec_id = $payment->get_energieausweis_id();

        return new Energieausweis( $ec_id );
    }	

    /**
	 * Success page
	 * 
	 * @since 2022-02-07
	 */
	protected function is_success() {
		global $edd_receipt_args;

		if( ! Enon_Location::success() || ! array_key_exists( 'id', $edd_receipt_args ) ) {
			return false;
		}

		$payment = get_post( $edd_receipt_args['id'] );
		$status = edd_get_payment_status( $payment );

		switch( $status ) {
			case 'publish':
			case 'pending':
				return true;
				break;
			case 'failed':
			case 'abandoned':
			case 'revoked':
			case 'processing':
			default:
				return false;
				break;
		}
	}

	/**
	 * Get price of ec.
	 * 
	 * @return float Price
	 * 
	 * @since 2020-09-11
	 */
	protected function ec_mail() : string {
		return $this->ec()->wpenon_email;
    }

    /**
	 * Get order date of ec.
	 * 
	 * @return float Price
	 * 
	 * @since 2022-02-07
	 */
    protected function ec_date() {
        return $this->ec()->post_date;
    }

    /**
	 * Get name of ec.
	 * 
	 * @return float Price
	 * 
	 * @since 2022-02-07
	 */
    protected function ec_name() {
        return $this->ec()->post_title;
    }

    /**
	 * Get price of ec.
	 * 
	 * @return float Price
	 * 
	 * @since 2020-09-11
	 */
	protected function ec_price( ) : float {
		switch( $this->ec()->type ) {
			case 'bw':
				return 99.95;
				break;
			case 'vw':
				return 49.95;
				break;
			default:
				return 0;
		}
    }

	/**
	 * Checks if user wants to be contacted.
	 * 
	 * @param Energieausweis $ec Energy certificate object.
	 * @return Bool True if user wants to be contacted, false if not.
	 * 
	 * @since 2020-09-10
	 */
	protected function contacting_allowed() : bool {
        $ec_id = $this->ec()->ID;

        if ( ! $ec_id ) {
            return false;
        }
       
		return (bool) get_post_meta( $ec_id, 'contact_acceptance', '1' );
	}
}