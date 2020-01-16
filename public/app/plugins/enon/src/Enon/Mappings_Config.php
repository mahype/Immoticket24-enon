<?php
/**
 * Configuration.
 *
 * @category Class
 * @package  Enon\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Enon;

use Enon\Enon\Standards\Schema;

/**
 * Class for managing Standards.
 *
 * @package Enon\Enon
 *
 * @todo Renaming standards?
 */
class Mappings_Config extends Config {

	/**
	 * Initiating types.
	 *
	 * @since 1.0.0
	 *
	 * @todo Loading dynamically.
	 */
	protected function initiate() {
		$this->config_data = array(
			'pdf'                      => __( 'PDF', 'wpenon' ),
			'xml-datenerfassung'       => __( 'XML Datenerfassung', 'wpenon' ),
			'xml-zusatzdatenerfassung' => __( 'XML Zusatzdatenerfassung', 'wpenon' ),
		);
	}
}
