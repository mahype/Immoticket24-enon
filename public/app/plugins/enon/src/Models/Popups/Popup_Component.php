<?php
/**
 * Abstraction component layer for popups.
 *
 * @category Class
 * @package  Enon\Models\Popups
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Popups;

use Enon\Models\Component;

/**
 * Class Badge
 *
 * @since 1.0.0
 */
abstract class Popup_Component extends Component {
	/**
	 * Top margin for popup.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	const MARGIN_TOP = '50px';

	/**
	 * Popup id.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	protected $popup_id;

	/**
	 * Selector for trigger.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	private $trigger_selector;

	/**
	 * Event for trigger.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	private $trigger_event;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $popup_id = null ) {
		if ( ! empty( $popup_id ) ) {
			$this->popup_id = $popup_id;
		} else {
			$this->popup_id = $this->create_popup_id();
		}
	}

	/**
	 * Creates a unique popup id.
	 *
	 * @return string Popup id.
	 *
	 * @since 1.0.0
	 */
	private function create_popup_id() : string {
		return 'modal__' . substr( md5( self::class . microtime() ), 0, 5 );
	}

	/**
	 * Get popup id.
	 *
	 * @return string Popup id.
	 *
	 * @since 1.0.0
	 */
	public function get_popup_id() : string {
		return $this->popup_id;
	}

	/**
	 * Get popup id.
	 *
	 * @return string Popup id.
	 *
	 * @since 1.0.0
	 */
	protected function get_show_popup_function_name() : string {
		return 'show_' . $this->popup_id;
	}

	/**
	 * Set trigger.
	 *
	 * @param string $selector Selector for DOM element.
	 * @param string $event    JS event (click, focus, focusout...).
	 *
	 * @since 1.0.0
	 */
	public function set_trigger( string $selector, string $event ) {
		$this->trigger_selector = $selector;
		$this->trigger_event = $event;
	}

	/**
	 * HTML Popup.
	 *
	 * @param string $title         Popup title.
	 * @param string $content       Popup content.
	 * @param string $text_action   Text for action button.
	 * @param string $text_noaction Text for leave button.
	 * @param string $image         Background image.
	 *
	 * @return string HTML for popup.
	 *
	 * @since 1.0.0
	 */
	public function html_popup( string $title, string $content, string $text_action, string $text_noaction, string $image ): string {
		ob_start();
		?>
		<div id="<?php echo $this->get_popup_id(); ?>" class="modal fade" role="dialog">
			<div class="modal-dialog" style="margin-top:<?php echo self::MARGIN_TOP; ?>">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title"><?php echo $title; ?></h4>
					</div>
					<?php if ( ! empty( $image ) ) : ?>
						<img src="<?php echo $image; ?>" class="modal-image" />
					<?php endif; ?>
					<div class="modal-body">
						<?php echo $content; ?>
					</div>
					<div class="modal-footer">
						<button id="<?php echo $this->popup_id; ?>-action" type="button" class="btn btn-primary"><?php echo $text_action; ?></button>
						<button id="<?php echo $this->popup_id; ?>-noaction" type="button" class="btn btn-default"><?php echo $text_noaction; ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php

		$html = ob_get_clean();
		$html .= $this->js();

		return $html;
	}

	/**
	 * Popup JS.
	 *
	 * @return string Popup HTML/JS.
	 *
	 * @since 1.0.0
	 */
	public function js() : string {
		ob_start();
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function ( $ ) {
				var <?php echo $this->get_popup_id(); ?> = $( '#<?php echo $this->get_popup_id(); ?>' );

				var <?php echo $this->get_show_popup_function_name(); ?> = function() {
					<?php echo $this->get_popup_id(); ?>.modal( 'show' );
				}

				<?php echo $this->get_popup_id(); ?>.modal({
					show: false,
					closeExisting: false
				});

				<?php if ( ! empty( $this->trigger_selector ) ) : ?>
				var trigger_element = $( <?php echo $this->trigger_selector; ?> );
				trigger_element.on( '<?php echo $this->trigger_event; ?>', function(e) {
					<?php echo $this->js_action_on_trigger(); ?>
					<?php echo $this->get_popup_id(); ?>.modal('show');
				});
				<?php endif; ?>

				$('#<?php echo $this->popup_id; ?>-action').on('click', function () {
					<?php echo $this->js_action(); ?>
					<?php echo $this->get_popup_id(); ?>.modal('hide');
				});

				$('#<?php echo $this->popup_id; ?>-noaction').on('click', function () {
					<?php echo $this->js_noaction(); ?>
					<?php echo $this->get_popup_id(); ?>.modal('hide');
				});
			});
		</script>
		<?php

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * JS on action.
	 *
	 * @return mixed
	 *
	 * @since 1.0.0
	 */
	abstract protected function js_action() : string;

	/**
	 * JS on no action.
	 *
	 * @return mixed
	 *
	 * @since 1.0.0
	 */
	protected function js_noaction() : string {
		return '';
	}

	/**
	 * JS if action on trigger.
	 *
	 * @return mixed
	 *
	 * @since 1.0.0
	 */
	protected function js_action_on_trigger() : string {
		return '';
	}

	/**
	 * Enqueueing Scripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		if ( is_admin() ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
	}
}
