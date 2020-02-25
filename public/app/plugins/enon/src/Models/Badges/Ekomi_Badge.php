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
			<div id="ekomi-badge-tuev">
				<img src="<?php echo plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'src/Assets/Img/Badges/tuev-saarland-logo.png'; ?>" />
			</div>
			<div id="ekomi-rating">
				<?php
				// phpcs:ignore
				echo $ekomi->get_rating_stars();
				?>
				<div class="stars-description">
					<strong>
						4.80
					</strong>/5.00
				</div>
			</div>
			<div id="ekomi-widget" class="ekomi-widget-container ekomi-widget-sf812665e4a9b6aad5b9"></div>
		</div>
		<?php

		$content = ob_get_clean();

		return $content;
	}

	public function js() : string {
		ob_start();
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				$('#ekomi-wrap').hover( function () {
					if ( document.documentElement.clientWidth < 992 ) {
						return;
					}
					$('#ekomi-widget').fadeIn(50);
				}, function () {
					if ( document.documentElement.clientWidth < 992 ) {
						return;
					}
					$('#ekomi-widget').fadeOut(50);
				});
			});

			(function (w) {
				w['_ekomiWidgetsServerUrl'] = (document.location.protocol == 'https:' ? 'https:' : 'http:') + '//widgets.ekomi.com';
				w['_customerId'] = 81266;
				w['_ekomiDraftMode'] = true;
				w['_language'] = 'de';

				if(typeof(w['_ekomiWidgetTokens']) !== 'undefined'){
					w['_ekomiWidgetTokens'][w['_ekomiWidgetTokens'].length] = 'sf812665e4a9b6aad5b9';
				} else {
					w['_ekomiWidgetTokens'] = new Array('sf812665e4a9b6aad5b9');
				}

				if(typeof(ekomiWidgetJs) == 'undefined') {
					ekomiWidgetJs = true;

					var scr = document.createElement('script');scr.src = 'https://sw-assets.ekomiapps.de/static_resources/widget.js';
					var head = document.getElementsByTagName('head')[0];head.appendChild(scr);
				}
			})(window);
		</script>
		<?php

		$content = ob_get_clean();

		return $content;
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
			.trust .badges #ekomi-badge-tuev,
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
			.trust .badges #ekomi-widget {
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
			.trust .badges #ekomi-widget:before {
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
			.trust .badges #ekomi-widget:after {
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
			.trust .badges #ekomi-widget img {
				display: block;
				margin: 0 auto;
			}
		</style>
		<?php

		$content = ob_get_clean();

		return $content;
	}
}
