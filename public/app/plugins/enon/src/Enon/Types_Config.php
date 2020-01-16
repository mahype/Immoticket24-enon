<?php
/**
 * Types config.
 *
 * @category Class
 * @package  Enon\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Enon;

/**
 * Class Types.
 *
 * @since 1.0.0
 *
 * @package Enon\Enon
 */
class Types_Config extends Config {
	/**
	 * Initiating types.
	 *
	 * @since 1.0.0
	 *
	 * @todo Loading dynamically.
	 */
	protected function initiate() {
		 $this->config_data = array(
			 'vw' => __( 'Verbrauchsausweis für Wohngebäude', 'wpenon' ),
			 'bw' => __( 'Bedarfsausweis für Wohngebäude', 'wpenon' ),
		 );
	}
}
