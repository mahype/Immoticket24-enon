<?php

namespace awsmug\Enon\Modules\Targeting;

use awsmug\Enon\Modules\Hooks_Submodule_Interface;
use awsmug\Enon\Modules\Hooks_Submodule_Trait;

/**
 * Class Tag_Manager
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Tag_Manager extends Targeting implements Hooks_Submodule_Interface {
	use Hooks_Submodule_Trait;

	/**
	 * Google Tag manager Company ID.
	 *
	 * @var null
	 */
	protected $company_id = 'GTM-N2M4CSV';

	/**
	 * Bootstraps the submodule by setting properties.
	 *
	 * @since 1.0.0
	 */
	protected function bootstrap() {
		$this->slug  = 'tag-manager';
		$this->title = __( 'Tag Manager', 'enon' );
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

	/**
	 * Sets up all action and filter hooks for the service.
	 *
	 * @since 1.0.0
	 */
	protected function setup_hooks() {
		parent::setup_hooks();
		$this->actions[] = array(
			'name'     => 'wp_head',
			'callback' => array( $this, 'head_script' ),
			'priority' => 1,
		);
		$this->actions[] = array(
			'name'     => 'wp_body_open',
			'callback' => array( $this, 'body_script' ),
			'priority' => 1,
		);
		$this->actions[] = array(
			'name'     => 'edd_payment_receipt_after_table',
			'callback' => array( $this, 'edd_purchase_conversions' ),
			'priority' => 10,
			'num_args' => 2,
		);
	}
}
