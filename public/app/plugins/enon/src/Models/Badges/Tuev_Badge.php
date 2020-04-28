<?php
/**
 * Ekomi badge.
 *
 * @category Class
 * @package  Enon\Models\Badges
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Badges;

/**
 * Class Ekomi_Badge.
 *
 * @since 1.0.0
 */
class Tuev_Badge extends Badge_Component {
	/**
	 * Ekomi Badge.
	 *
	 * @return string Badge HTML.
	 *
	 * @since 1.0.0
	 */
	public function html() : string {
		$ekomi = new Ekomi_Service();

		ob_start();
		?>
		<div id="tuev-wrap">
			<div id="tuev-badge">
				<a href="http://tuev-saar.de/SC44176"><img src="<?php echo plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'src/Assets/Img/Badges/tuev-geprueft.png'; ?>" /></a>
			</div>
		</div>
		<?php

		$content = ob_get_clean();

		return $content;
	}

	public function css() : string {
		ob_start();
		?>
		<style>
			.trust .badges #tuev-wrap {
				padding-left: 8px;
			}
			.trust .badges #tuev-badge {
				display: block;
				float: left;
			}
			.trust .badges #tuev-badge img {
				width: 180px;
			}
		</style>
		<?php

		$content = ob_get_clean();

		return $content;
	}
}
