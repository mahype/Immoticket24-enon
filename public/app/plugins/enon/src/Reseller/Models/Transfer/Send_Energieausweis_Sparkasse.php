<?php
/**
 * Class for sending and preparing data to sparkasse.
 *
 * @category Class
 * @package  Enon\Reseller\Models\Transfer
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Reseller\Models\Transfer;

/**
 * Class Send_Energieausweis_Sparkasse
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Send
 */
class Send_Energieausweis_Sparkasse extends Send_Energieausweis {
	/**
	 * Get body to send to endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	protected function get_body() {
		$fields = $this->energieausweis->getSchema()->getFields();

		$data = array();

		foreach ( $fields as  $key => $field ) {
			$data[ $key ] = $this->energieausweis->$key;
		}

		$data['sender'] = 'immoticket24';

		return $data;
	}
}
