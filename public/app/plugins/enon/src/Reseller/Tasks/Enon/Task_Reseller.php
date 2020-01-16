<?php
/**
 * Task which loads reseller scripts.
 *
 * @category Class
 * @package  Enon\Reseller\Tasks\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Reseller\Tasks\Enon;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Enon\Traits\Logger as Logger_Trait;
use Enon\Logger;
use Enon\Reseller\Models\Reseller;
use WPENON\Model\Energieausweis;

/**
 * Class Wpenon.
 *
 * Running WordPress scripts
 *
 * @package Enon\Reseller\WordPress
 */
class Task_Reseller implements Task, Actions, Filters {

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
	 * @param Reseller $reseller
	 * @param Logger   $logger
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
		add_action( 'wpenon_energieausweis_create', array( $this, 'updatereseller_id' ) );
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'wpenon_schema_file', array( $this, 'filterSchemafile' ), 10, 3 );
	}

	private function getreseller_id( $energieausweis ) {
		$reseller_id = get_post_meta( $energieausweis->id, 'reseller_id', true );

		if ( ! empty( $reseller_id ) ) {
			return $reseller_id;
		}

		return $this->reseller->data()->get_post_id();
	}

	/**
	 * Updating reseller id.
	 *
	 * @since 1.0.0
	 *
	 * @param Energieausweis $energieausweis Energieausweis object.
	 */
	public function updatereseller_id( $energieausweis ) {
		update_post_meta( $energieausweis->id, 'reseller_id', $this->getreseller_id( $energieausweis ) );
	}

	/**
	 * Filtering schema file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Path to file.
	 * @param string $type Schema typ.
	 *
	 * @return string Filtered schema file.
	 */
	public function filterSchemafile( $file, $standard, $energieausweis ) {
		$reseller_id = $this->getreseller_id( $energieausweis );
		$type = get_post_meta( $energieausweis->id, 'wpenon_type', true );

		if ( empty( $reseller_id ) ) {
			return $file;
		}

		$this->reseller->data()->set_post_id( $reseller_id );

		switch ( $type ) {
			case 'bw':
				$schema_file = trim( $this->reseller->data()->get_bw_schema_file() );
				break;
			case 'vw':
				$schema_file = trim( $this->reseller->data()->get_vw_schema_file() );
				break;
		}

		if ( empty( $schema_file ) ) {
			return $file;
		}

		$schema_file = WPENON_DATA_PATH . '/' . $standard . '/schema/' . $schema_file;

		return $schema_file;
	}
}
