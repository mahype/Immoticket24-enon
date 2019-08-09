<?php

namespace EA\Tools\Targeting;

/**
 * Bing targeting scripts.
 *
 * @since 1.1.0
 */
class Bing extends Service {
	/**
	 * Loads the base script on every site to the header.
	 *
	 * @since 1.1.0
	 */
	protected static function base_script() {
		?>
		<script>(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:"5475474"};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");</script>
		<?php
	}

	/**
	 * Loads the scripts after a bedarfsausweis had a conversion.
	 *
	 * @since 1.1.0
	 */
	protected static function conversion_bedarfsausweis() {
		echo self::custom_event_script( 'conversion', 'bedarfsausweis', 89.95 );
	}

	/**
	 * Loads the scripts after a verbrauchsausweis had a conversion.
	 *
	 * @since 1.1.0
	 */
	protected static function conversion_verbrauchsausweis() {
		echo self::custom_event_script( 'conversion', 'verbrauchsausweis', 39.95 );
	}

	/**
	 * Inserts the custom Event script;
	 */
	protected static function custom_event_script( $action, $category, $value ) {
		ob_start();
		?>
		<script>
			window.uetq = window.uetq || [];
			window.uetq.push ('event', '<?php echo $action; ?>', {'event_category': '<?php echo $category; ?>', 'event_label': '<?php echo $action . ' ' . $category; ?>', 'event_value': '<?php echo $value; ?>' );		}
		</script>
		<?php

		return ob_get_clean();
	}
}
