<?php
/**
 * XSD config.
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
 * Class for managing XSD Config.
 *
 * @package Enon\Enon
 *
 * @todo Renaming standards?
 */
class XSD_Config extends Config {

	/**
	 * Initiating types.
	 *
	 * @since 1.0.0
	 *
	 * @todo Loading dynamically.
	 */
	protected function initiate() {
		 $this->config_data = array(
			 'datenerfassung' => __( 'Datenerfassung', 'wpenon' ),
			 'zusatzdatenerfassung' => __( 'Zusatzdatenerfassung', 'wpenon' ),
		 );
	}
}
