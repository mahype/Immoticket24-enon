<?php

namespace awsmug\Enon\Tools;

/**
 * Class Google_Tag_Manager
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Google_Tag_Manager {

	/**
	 * Google Tag manager Company ID.
	 *
	 * @var null
	 */
	protected $company_id = null;

	/**
	 * Loading nesesary properties and functions.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->load_hooks();
		$this->company_id = 'GTM-N2M4CSV';
	}

	/**
	 * Load targeting scripts into hooks.
	 *
	 * @since 1.0.0
	 */
	private function load_hooks() {
		add_action( 'wp_head', array( $this, 'head_script' ), 1 );
		add_action( 'wp_body_open', array( $this, 'body_script' ), 1 );

		add_action( 'edd_payment_receipt_after_table', array( $this, 'edd_purchase_conversions' ), 10, 2 );
	}

	/**
	 * Google Tag Manager head script.
	 *
	 * @since 1.0.0
	 */
	public function head_script() {
		?>
		<!-- Google Tag Manager -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo $this->company_id; ?>');</script>
		<!-- End Google Tag Manager -->
		<?php
	}

	/**
	 * Google Tag Manager body script.
	 *
	 * @since 1.0.0
	 */
	public function body_script() {
		?>
		<!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $this->company_id; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<!-- End Google Tag Manager (noscript) -->
		<?php
	}

	/**
	 * Loads the scripts on the right places for EDD conversions.
	 *
	 * @since 1.0.0
	 */
	public function edd_purchase_conversions() {
		if ( ! isset( $_SESSION['edd']['edd_purchase'] ) ) {
			return;
		}

		$purchase          = json_decode( $_SESSION['edd']['edd_purchase'] );
		$energieausweis_id = $purchase->downloads[0]->id;
		$type              = get_post_meta( $energieausweis_id, 'wpenon_type', true );

		if ( 'bw' === $type ) {
			$this->conversion_bedarfsausweis();
		}

		if ( 'vw' === $type ) {
			$this->conversion_verbrauchsausweis();
		}
	}

	/**
	 * Loads the scripts after a bedarfsausweis had a conversion.
	 *
	 * @since 1.0.0
	 */
	private function conversion_bedarfsausweis() {
		?>
		<script>dataLayer.push({'event':'conversion-bedarfsausweis'});</script>
		<?php
	}

	/**
	 * Loads the scripts after a verbrauchsausweis had a conversion.
	 *
	 * @since 1.0.0
	 */
	private function conversion_verbrauchsausweis() {
		?>
		<script>dataLayer.push({'event':'conversion-verbrauchsausweis'});</script>
		<?php
	}
}
