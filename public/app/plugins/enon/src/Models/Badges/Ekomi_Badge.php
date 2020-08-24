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
class Ekomi_Badge extends Badge_Component {
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
		<div id="ekomi-wrap">
			<div id="ekomi-badge">
				<img src="<?php echo plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'src/Assets/Img/Badges/ekomi-badge.png'; ?>" />
			</div>
			<div id="ekomi-rating">
				<?php
				// phpcs:ignore
				echo $ekomi->get_rating_stars();
				?>
				<div class="stars-description">
					<strong>
						4.8
					</strong>/5
				</div>
			</div>
			<div id="eKomiWidget_default"></div>
		</div>
		<?php

		$content = ob_get_clean();

		return $content;
	}

	public function js() : string {
		return '';
	}

	public function css() : string {
		ob_start();
		?>
		<style>
			.trust .badges #ekomi-wrap {
				padding-left: 8px;
				background-color: #fafafa;
				border: 1px solid #dddddd;
				border-radius: 8px;
			}
			.trust .badges #ekomi-badge {
				margin-top:8px;
			}
			.trust .badges #ekomi-badge,
			.trust .badges #ekomi-rating {
				display: block;
				float: left;
			}
			.trust .badges #ekomi-badge-tuev img {
				width: 54px;
				margin-top: 8px;
			}
			.trust .badges #ekomi-rating {
				margin-top: 15px;
				padding: 5px;
				text-align: center;
			}
			.trust .badges #eKomiWidget_default {
				position: absolute;
				top: 93px;
				left: -135px;
				z-index: 10000;
				display: none;
				width: 290px;
				padding: 20px;
				background: none repeat scroll 0 0 #ffffff;
				border: 1px solid #aaaaaa;
			}
			.trust .badges #eKomiWidget_default:before {
				content: "";
				border-bottom: 12px solid #ffffff;
				border-right: 12px solid transparent;
				border-left: 12px solid transparent;
				position: absolute;
				top: -12px;
				right: 10%;
				margin-left: -12px;
				z-index: 9999999999999999999;
			}
			.trust .badges #eKomiWidget_default:after {
				content: "";
				border-bottom: 12px solid #aaaaaa;
				border-right: 12px solid transparent;
				border-left: 12px solid transparent;
				position: absolute;
				top: -13px;
				right: 10%;
				margin-left: -12px;
				z-index: -10000;
			}
			.trust .badges #eKomiWidget_default img {
				display: block;
				margin: 0 auto;
			}
		</style>
		<?php

		$content = ob_get_clean();

		return $content;
	}
}
