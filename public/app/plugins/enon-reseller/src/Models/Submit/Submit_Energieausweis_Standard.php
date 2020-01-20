<?php
/**
 * Class for sending and preparing data to anyone (template for other resellers).
 *
 * @category Class
 * @package  Enon_Reseller\Models
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Submit;

/**
 * Class Submit_Energieausweis_Standard
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\Send
 */
class Submit_Energieausweis_Standard extends Submit_Energieausweis {
	/**
	 * Get body to send to endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	protected function get_body() {
		$data = $this->energieausweis;
		return $data;
	}
}
