<?php
/**
 * Trusted shops badge.
 *
 * @category Class
 * @package  Enon\Models\Badges
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Badges;

/**
 * Class Trusted_Shops_Badge.
 *
 * @since 1.0.0
 */
class Trusted_Shops_Badge extends Badge_Component {
	/**
	 * Ekomi Badge.
	 *
	 * @return string Badge HTML.
	 *
	 * @since 1.0.0
	 */
	public function html() : string {
		ob_start();
		?>
		<div id="trusted-shops-badge"></div>
		<?php

		$content = ob_get_clean();

		return $content;
	}
}
