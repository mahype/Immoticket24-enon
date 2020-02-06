<?php
/**
 * Task which loads reseller scripts.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Awsm\WP_Wrapper\Tools\Logger_Trait;
use Awsm\WP_Wrapper\Tools\Logger;

use Enon_Reseller\Models\Reseller;

use WPENON\Model\Energieausweis;

/**
 * Class Setup_Enon.
 *
 * Running enon scripts
 *
 * @package Enon_Reseller\WordPress
 */
class Setup_Enon implements Task, Actions, Filters {

	use Logger_Trait;

	/**
	 * Reseller object.
	 *
	 * @since 1.0.0
	 * @var Reseller;
	 */
	private $reseller;

	/**
	 * Wpenon constructor.
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
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'wpenon_energieausweis_create', array( $this, 'update_reseller_id' ) );
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'wpenon_show_tag_manager_scripts', '__return_false' );
		add_filter( 'wpenon_show_uptain_scripts', '__return_false' );
		add_filter( 'wpenon_show_adcell_scripts', '__return_false' );
	}

	/**
	 * Updating reseller id.
	 *
	 * @since 1.0.0
	 *
	 * @param Energieausweis $energieausweis Energieausweis object.
	 */
	public function update_reseller_id( $energieausweis ) {
		update_post_meta( $energieausweis->id, 'reseller_id', $this->get_reseller_id( $energieausweis ) );
	}

	/**
	 * Get reseller id.
	 *
	 * @param Energieausweis $energieausweis Energieausweis object.
	 *
	 * @return int Reseller id.
	 *
	 * @since 1.0.0
	 */
	private function get_reseller_id( $energieausweis ) {
		$reseller_id = get_post_meta( $energieausweis->id, 'reseller_id', true );

		if ( ! empty( $reseller_id ) ) {
			return $reseller_id;
		}

		return $this->reseller->data()->get_post_id();
	}
}
