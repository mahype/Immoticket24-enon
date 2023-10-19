<?php
/**
 * Google tag manager tasks.
 *
 * @category Class
 * @package  Enon\Misc\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks\Scripts;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;
use WPENON\Model\Energieausweis;

/**
 * Class Google_Tag_Manager
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Add_Google_Tag_Manager implements Actions, Task {
	/**
	 * Google Tag manager Company ID.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected static $company_id = 'GTM-N2M4CSV';

	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->add_actions();
	}

	/**
	 * Load targeting scripts into hooks.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		// Commented scripts are done with Borlabs
		add_action( 'init',  array( __CLASS__, 'add_shortcode' ) );
	}

	/**
	 * Add shortcode.
	 * 
	 * @since 1.0.0
	 */
	public static function add_shortcode() {
		add_shortcode( 'google_conversion', array( __CLASS__, 'shortcode' ) );
	}

	/**
	 * Shortcode function.
	 * 
	 * @since 1.0.0
	 */
	public static function shortcode() {
		ob_start();
		self::edd_purchase_conversions();
		return ob_get_clean();
	}

	/**
	 * Google Tag Manager head script.
	 *
	 * @since 1.0.0
	 */
	public static function head_script() {
		/**
		 * Set if tag manager have to be shown.
		 *
		 * @param bool True if it have to be shown.
		 *
		 * @since 1.0.0
		 */
		$show_tag_manager = apply_filters( 'wpenon_show_tag_manager_scripts', true );

		if ( ! $show_tag_manager ) {
			return;
		}

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
		/**
		 * Set if tag manager have to be shown.
		 *
		 * @param bool True if it have to be shown.
		 *
		 * @since 1.0.0
		 */
		$show_tag_manager = apply_filters( 'wpenon_show_tag_manager_scripts', true );

		if ( ! $show_tag_manager ) {
			return;
		}

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
		$energieausweis    = new Energieausweis( $energieausweis_id );		
		$type              = get_post_meta( $energieausweis_id, 'wpenon_type', true );
		
		if ( 'bw' === $type ) {
			self::conversion_bedarfsausweis( $energieausweis, $purchase );
		}
		if ( 'vw' === $type ) {
			self::conversion_verbrauchsausweis( $energieausweis, $purchase );
		}
	}

	/**
	 * Loads the scripts after a bedarfsausweis had a conversion.
	 * 
	 * @param Energieausweis $energieausweis
	 * @param stdObject $purchase
	 *
	 * @since 1.0.0
	 */
	private static function conversion_bedarfsausweis( $energieausweis, $purchase ) {
		echo '<!--';
		print_r($purchase);
		echo '-->';
		?>
		<script>dataLayer.push({'event':'conversion-bedarfsausweis', 'bestellnummer': '<?php echo $purchase->purchase_key; ?>', 'price': '<?php echo $purchase->price; ?>', 'product': 'bedarfsausweis', 'email': '<?php echo $energieausweis->wpenon_email; ?>'});</script>
		<?php
	}

	/**
	 * Loads the scripts after a verbrauchsausweis had a conversion.
	 * 
	 * @param Energieausweis $energieausweis
	 * @param stdObject $purchase 
	 *
	 * @since 1.0.0
	 */
	private static function conversion_verbrauchsausweis( $energieausweis, $purchase ) {
		echo '<!--';
		print_r($purchase);
		echo '-->';
		?>
		<script>dataLayer.push({'event':'conversion-verbrauchsausweis', 'bestellnummer': '<?php echo $purchase->purchase_key; ?>', 'price': '<?php echo $purchase->price; ?>', 'product': 'verbrauchsausweis', 'email': '<?php echo $energieausweis->wpenon_email; ?>'});</script>
		<?php
	}
}
