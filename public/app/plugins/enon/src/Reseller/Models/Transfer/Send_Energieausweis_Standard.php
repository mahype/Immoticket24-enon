<?php
/**
 * Class for sending and preparing data to anyone (template for other resellers).
 *
 * @category Class
 * @package  Enon\Reseller\Models\Transfer
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Reseller\Models\Transfer;

/**
 * Class Send_Energieausweis_Standard
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Send
 */
class Send_Energieausweis_Standard extends Send_Energieausweis {
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
