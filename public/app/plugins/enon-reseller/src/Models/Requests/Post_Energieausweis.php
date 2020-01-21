<?php
/**
 * Parent class for sending data to reseller.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Transfer
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Requests;

use Awsm\WP_Wrapper\Tools\Logger;

use WPENON\Model\Energieausweis;

use Enon\Models\Request;


/**
 * Class ResellerSend_Energieausweis
 *
 * @since 1.0.0
 */
abstract class Post_Energieausweis extends Request {
	/**
	 * Energieausweis Object.
	 *
	 * @since 1.0.0
	 *
	 * @var Energieausweis
	 */
	protected $energieausweis;

	/**
	 * ResellerSend_Energieausweis constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string         $endpoint       URL to send data.
	 * @param Energieausweis $energieausweis Energieausweis object.
	 * @param Logger         $logger         Logger object.
	 */
	public function __construct( string $endpoint, Energieausweis $energieausweis, Logger $logger ) {
		$this->energieausweis = $energieausweis;

		parent::__construct( $endpoint, $logger );
	}
}
