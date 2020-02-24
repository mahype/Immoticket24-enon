<?php
/**
 * CO2 Badge.
 *
 * @category Class
 * @package  Enon\Models\Badges
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Badges;

/**
 * Class Distributor_Energy_Certificate
 *
 * @since 1.0.0
 */
class CO2_Badge extends Badge_Component {
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
		<div id="co2-neutral">
			<a href="https://www.co2neutralwebsite.de/crt/dispcust/c/4127/l/4" target="_blank">
				<img src=<?php echo plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'src/Assets/Img/Badges/co2-neutrale-website.png'; ?>">
			</a>
		</div>
		<?php

		$content = ob_get_clean();

		return $content;
	}
}
