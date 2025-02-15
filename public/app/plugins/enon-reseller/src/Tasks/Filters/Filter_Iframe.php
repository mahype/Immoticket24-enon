<?php
/**
 * Task which loads iframe filters.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Filters;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use Enon\Logger;

use Enon_Reseller\Models\Reseller;

/**
 * Class Filter_Iframe.
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\Tasks\Enon
 */
class Filter_Iframe implements Task, Actions, Filters {

	/**
	 * Reseller object.
	 *
	 * @since 1.0.0
	 * @var Reseller;
	 */
	private $reseller;

	private $inserted = false;

	/**
	 * Wpenon constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Reseller $reseller Reseller object.
	 * @param Logger   $logger   Logger object.
	 */
	public function __construct( Reseller $reseller, Logger $logger ) {
		$this->reseller = $reseller;
		$this->logger   = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'wpenon_create_show_title', array( $this, 'filter_title' ) );
		add_filter( 'wpenon_create_show_description', array( $this, 'filter_description' ) );
        add_filter( 'immoticketenergieausweis_checkout_terms_checkboxes', array( $this, 'filter_newsletter_terms' ) );
		add_filter( 'borlabsCookie/settings', array( $this, 'switch_off_borlabs' ) );
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'enon_iframe_css', array( $this, 'add_css' ) );
		add_action( 'enon_iframe_js', array( $this, 'add_js' ) );
		add_action( 'edd_checkout_form_top', array( $this, 'setup_coupon_code_field' ), -2 );
	}

	/**
	 * Filtering if title will be shown on energieausweis creation.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function filter_title() {
		$show = $this->reseller->data()->iframe->isset_element_title();
		return $show;
	}

	/**
	 * Filtering if description will be shown on energieausweis creation.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function filter_description() {
		$show = $this->reseller->data()->iframe->isset_element_description();
		return $show;
	}

	/**
	 * Filtering if newsletter terms will be shown on energieausweis creation.
	 *
	 * @param array $terms_checkboxes Terms checkboxes.
	 *
	 * @return array Filtered Terms checkboxes.
	 *
	 * @since 1.0.0
	 */
	public function filter_newsletter_terms( $terms_checkboxes ) {
		if ( ! $this->reseller->data()->iframe->isset_element_newsletter_terms() ) {
			unset( $terms_checkboxes['newsletter_terms'] );
        } else {

            $newsletter_terms = $this->reseller->data()->iframe->get_newsletter_terms();
            
            if ( ! empty ( $newsletter_terms ) ) {
                $terms_checkboxes['newsletter_terms']['label'] = $newsletter_terms;
            }
        }

		return $terms_checkboxes;
    }

	/**
	 * Filtering if coupin code field will be shown on energieausweis creation.
	 *
	 * @since 2022-03-23
	 */
	public function setup_coupon_code_field() {
		if ( ! $this->reseller->data()->iframe->isset_element_coupon_code_field() ) {
			remove_action( 'edd_checkout_form_top', 'edd_discount_field', -1 );
        }
    }
    
    /**
     * Hiding cookie box.
     * 
     * @param  array $config Borlabs cookie settings.
     * @return array Filtered borlabs cookie settings.
     * 
     * @since 1.0.0
     */
    public function switch_off_borlabs( $config ) {
        $config['showCookieBox'] = false;
        return $config;
    }

	/**
	 * Add reseller CSS.
	 *
	 * @since 1.0.0
	 */
	public function add_css() {
		if ( defined ('RESELLER_CSS_LOADED') ) {
			return;
		}

		define( 'RESELLER_CSS_LOADED', true );

		$extra_css = $this->reseller->data()->iframe->get_extra_css();

		if ( empty( $extra_css ) ) {
			return;
		}

		echo $extra_css;
	}

	/**
	 * Add reseller JS.
	 *
	 * @since 1.0.0
	 */
	public function add_js() {
		if ( defined ('RESELLER_JS_LOADED') ) {
			return;
		}

		define( 'RESELLER_JS_LOADED', true );

		$js = $this->reseller->get_iframe_js() . chr(13);
		$js.= $this->reseller->data()->iframe->get_extra_js();
		
		if ( empty( $js ) ) {
			return;
		}		
		
		echo $js;		
	}
}
