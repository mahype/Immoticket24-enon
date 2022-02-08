<?php
/**
 * Add scripts.
 *
 * @category Class
 * @package  Enon\Misc\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks\Scripts;

use Enon\Models\Enon\Energieausweis;
use Enon\Models\Enon\Enon_Location;
use Enon\Models\Scripts\Script_Loader;

/**
 * Class Google_Tag_Manager
 *
 * @package awsmug\Enon\Tools
 *
 * @since 2022-02-07
 */
class Add_Trusted_Shops_Scripts extends Script_Loader {
	/**
	 * Funnel page.
	 *
	 * @since 2022-02-07
	 */
	protected function ec_funnel_contacting_allowed() {
		echo $this->base_script();
	}

	/**
	 * Script
	 * 
	 * @since 2022-02-07
	 */
	protected function script() : string {
        return "(function () { 
          var _tsid = 'X509FCF5891E8A90932F2A46F02AD28DE'; 
          _tsConfig = { 
            'yOffset': '0', /* offset from page bottom */
            'variant': 'reviews', /* default, reviews, custom, custom_reviews */
            'customElementId': '', /* required for variants custom and custom_reviews */
            'trustcardDirection': '', /* for custom variants: topRight, topLeft, bottomRight, bottomLeft */
            'customBadgeWidth': '', /* for custom variants: 40 - 90 (in pixels) */
            'customBadgeHeight': '', /* for custom variants: 40 - 90 (in pixels) */
            'disableResponsive': 'false', /* deactivate responsive behaviour */
            'disableTrustbadge': 'false' /* deactivate trustbadge */
          };
          var _ts = document.createElement('script');
          _ts.type = 'text/javascript'; 
          _ts.charset = 'utf-8'; 
          _ts.async = true; 
          _ts.src = '//widgets.trustedshops.com/js/' + _tsid + '.js'; 
          var __ts = document.getElementsByTagName('script')[0];
          __ts.parentNode.insertBefore(_ts, __ts);
        })();";
	}

	/**
	 * Create uptain data tag.
	 * 
	 * @param array $values Values to add to tag.
	 * @return string Data tag HTML.
	 * 
	 * @since 2022-02-07
	 */
	public function success() {
    $id = $this->ec()->ID;
    $ec = new Energieausweis( $id );

    if( ! $ec->contacting_allowed() ) {
      return;
    }

    $tag = '<div id="trustedShopsCheckout" style="display: none;">';
    $tag.= '<span id="tsCheckoutOrderNr">' . $this->ec_name() . '</span>';
    $tag.= '<span id="tsCheckoutBuyerEmail">' . $this->ec_mail() . '</span>';
    $tag.= '<span id="tsCheckoutOrderAmount">' . $this->ec_price() . '</span>';
    $tag.= '<span id="tsCheckoutOrderCurrency">EUR</span>';
    $tag.= '<span id="tsCheckoutOrderPaymentType">WIRE TRANSFER</span>';
    $tag.= '<span id="tsCheckoutOrderEstDeliveryDate">' . $this->ec_date() . '</span>';
    $tag.= '</div>';

    echo $tag;
	}
}
