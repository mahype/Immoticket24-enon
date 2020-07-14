<?php
/**
 * Task which loads schema filters
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Filters;

use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Tools\Logger_Trait;

use Enon_Reseller\Models\Reseller;
use WPENON\Model\Energieausweis;

/**
 * Class Filter_Schema.
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\Tasks\Enon
 */
class Filter_Schema implements Task, Filters {
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
		$this->add_filters();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'wpenon_schema_file', array( $this, 'filter_schema_file' ), 10, 3 );
	}

	/**
	 * Filtering schema file.
	 *
	 * @since 1.0.0
	 *
	 * @param string         $file           Path to file.
	 * @param string         $standard       Standard.
	 * @param Energieausweis $energieausweis Energieausweis object.
	 *
	 * @return string Filtered schema file.
	 */
	public function filter_schema_file( $file, $standard, $energieausweis ) {
		$reseller_id = $this->get_energieausweis_reseller_id( $energieausweis );
		$type        = get_post_meta( $energieausweis->id, 'wpenon_type', true );

		if ( empty( $reseller_id ) ) {
			return $file;
		}

		switch ( $type ) {
			case 'bw':
				$schema_file = trim( $this->reseller->data()->schema->get_bw_schema_file() );
				break;
			case 'vw':
				$schema_file = trim( $this->reseller->data()->schema->get_vw_schema_file() );
				break;
		}

		if ( empty( $schema_file ) ) {
			return $file;
		}

		$schema_file = WPENON_DATA_PATH . '/' . $standard . '/schema/' . $schema_file;

		$this->logger()->notice('Filtering schema file.', array( 'energy_certificate_id' => $energieausweis->id, 'reseller_id' => $reseller_id, 'schema_file' => $schema_file ) );

		return $schema_file;
	}

	/**
	 * Get reseller id.
	 *
	 * @param Energieausweis $energieausweis Energieausweis object.
	 *
	 * @return int|bool Reseller id or false.
	 *
	 * @since 1.0.0
	 */
	private function get_energieausweis_reseller_id( $energieausweis ) {
		$reseller_id = get_post_meta( $energieausweis->id, 'reseller_id', true );

		if ( ! empty( $reseller_id ) ) {
			return (int) $reseller_id;
		}

		return false;
	}
}
