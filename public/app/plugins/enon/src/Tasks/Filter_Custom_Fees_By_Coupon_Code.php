<?php
/**
 * Filter custom fees by coupon code
 *
 * @category Class
 * @package  Enon\Models\Add_Costum_Fees_EVM
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Filter_Custom_Fees_By_Coupon_Code.
 *
 * @since 1.0.0
 */
class Filter_Custom_Fees_By_Coupon_Code implements Filters, Task {

	/**
	 * Premiumbewertung_Popup constructor.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_filters();
	}

	/**
	 * Add filters.
	 */
	public function add_filters() {
        add_filter( 'eddcf_custom_fees', [ $this, 'filter_fees' ] );
    }

    /**
	 * Filter fees.
	 *
	 * @param array $fees Fees.
	 *
	 * @return array Filtered fees.
	 *
	 * @since 1.0.0
	 */
	public function filter_fees( $fees ) {
        $premiumbewertung = true;

        $discounts = EDD()->cart->discounts;

        $coupon_codes =[
            'xLswR42', 'hsGrez27k', '5StFmRgt311'
        ];

        foreach( $discounts as $code ) {
            if (in_array($code, $coupon_codes)) {
                $premiumbewertung = false;
            }
        }

		if ( $premiumbewertung ) {
			return $fees;
		}

        foreach( $fees as $key => $fee ) {
            if ( 'premium_bewertung' === $fee['id'] ) {
                unset($fees[$key]);
            }
        }

		return $fees;
	}
}