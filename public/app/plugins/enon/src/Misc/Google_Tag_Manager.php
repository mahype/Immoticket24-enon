<?php

namespace Enon\Misc;
use Awsm\WP_Plugin\Building_Plans\Hooks_Actions;
use Awsm\WP_Plugin\Building_Plans\Service;
use Awsm\WP_Plugin\Loaders\Hooks_Loader;
use Awsm\WP_Plugin\Loaders\Loader;

/**
 * Class Google_Tag_Manager
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Google_Tag_Manager implements Hooks_Actions, Service {
	use Loader, Hooks_Loader;

	/**
	 * Google Tag manager Company ID.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected static $company_id = 'GTM-N2M4CSV';

	/**
	 * Load targeting scripts into hooks.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'wp_head', array( __CLASS__, 'head_script' ), 1 );
		add_action( 'wp_body_open', array( __CLASS__, 'body_script' ), 1 );
		add_action( 'edd_payment_receipt_after_table', array( __CLASS__, 'edd_purchase_conversions' ), 10, 2 );
	}

	/**
	 * Google Tag Manager head script.
	 *
	 * @since 1.0.0
	 */
	public static function head_script() {
		?>
		<!-- Google Tag Manager -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo self::$company_id; ?>');</script>
		<!-- End Google Tag Manager -->
		<?php
	}

	/**
	 * Google Tag Manager body script.
	 *
	 * @since 1.0.0
	 */
	public static function body_script() {
		?>
		<!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo self::$company_id; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<!-- End Google Tag Manager (noscript) -->
		<?php
	}

	/**
	 * Loads the scripts on the right places for EDD conversions.
	 *
	 * @since 1.0.0
	 */
	public static function edd_purchase_conversions() {
		if ( ! isset( $_SESSION['edd']['edd_purchase'] ) ) {
			return;
		}
		$purchase          = json_decode( $_SESSION['edd']['edd_purchase'] );
		$energieausweis_id = $purchase->downloads[0]->id;
		$type              = get_post_meta( $energieausweis_id, 'wpenon_type', true );
		if ( 'bw' === $type ) {
			self::conversion_bedarfsausweis();
		}
		if ( 'vw' === $type ) {
			self::conversion_verbrauchsausweis();
		}
	}

	/**
	 * Loads the scripts after a bedarfsausweis had a conversion.
	 *
	 * @since 1.0.0
	 */
	private static function conversion_bedarfsausweis() {
		?>
		<script>dataLayer.push({'event':'conversion-bedarfsausweis'});</script>
		<?php
	}

	/**
	 * Loads the scripts after a verbrauchsausweis had a conversion.
	 *
	 * @since 1.0.0
	 */
	private static function conversion_verbrauchsausweis() {
		?>
		<script>dataLayer.push({'event':'conversion-verbrauchsausweis'});</script>
		<?php
	}
}
