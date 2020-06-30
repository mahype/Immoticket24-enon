<?php
/**
 * Reseller object.
 *
 * @category Class
 * @package  Enon_Reseller\Models
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 *
 * @todo Replacing old energieausweis class.
 */

namespace Enon_Reseller\Models;

use Enon\Models\Edd\Payment;
use Enon\Models\Exceptions\Exception;
use WPENON\Model\Energieausweis as Energieausweis_Old;

/**
 * Class Reseller
 *
 * @package Enon_Reseller
 *
 * @since 1.0.0
 */
class Reseller_Payment extends Payment {
	/**
	 * Reseller id.
	 *
	 * @var int
	 *
	 * @since 1.0.0
	 */
	private $reseller_id;

	/**
	 * Energy certificate id.
	 *
	 * @var int
	 *
	 * @since 1.0.0
	 */
	private $energy_certificate_id;

	/**
	 * Reseller_Payment constructor.
	 *
	 * @param int $payment_id Payment Id.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $payment_id ) {
		parent::__construct( $payment_id );
	}

	/**
	 * Returns reseller id.
	 *
	 * @return int Reseller id or 0 if not found.
	 *
	 * @since 1.0.0
	 */
	public function get_reseller_id() {
		$this->energy_certificate_id = $this->get_energieausweis_id();
		$this->energy_certificate    = new Energieausweis_Old( $this->energy_certificate_id );

		if ( empty( $this->energy_certificate->reseller_id ) ) {
			return 0;
		}

		return $this->energy_certificate->reseller_id;
	}
}
