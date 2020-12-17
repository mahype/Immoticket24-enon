<?php
/**
 * Class for configuring dashboard widgets.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Admin
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Admin;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;
use Enon_Reseller\Models\Data\Post_Meta_Iframe;

/**
 * Class Config_Dashboard_Widgets
 *
 * @package Enon_Reseller\Tasks\Admin
 *
 * @since 1.0.0
 */
class Config_Dashboard_Widgets implements Task, Actions {
	/**
	 * Run actions.
	 *
	 * @return mixed|void
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_actions();
	}

	/**
	 * Add actions.
	 *
	 * @return mixed|void
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'wp_dashboard_setup', [ $this, 'remove' ], 10000 );
		add_action( 'wp_dashboard_setup', [ $this, 'add' ] );
	}

	/**
	 * Remove existing meta boxes.
	 *
	 * @since 1.0.0
	 */
	public function remove() {
		global $wp_meta_boxes;

		unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] );
		unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
		unset( $wp_meta_boxes['dashboard']['normal']['core']['op_optin_stats_widget'] );
	}

	/**
	 * Add own meta boxes.
	 *
	 * @since 1.0.0
	 */
	public function add() {
		wp_add_dashboard_widget( 'lead_export', 'Lead export', [ $this, 'widget_lead_export' ] );
		wp_add_dashboard_widget( 'iframe_html', 'iFrame HTML', [ $this, 'widget_iframe_code' ] );
	}

	/**
	 * Widget for lead export.
	 *
	 * @since 1.0.0
	 */
	public function widget_lead_export() {
        $this->buttons();        
        $this->date_field();

        do_action( 'enon_widget_lead_export_end' );
    }

    private function buttons() {
        $csv_all = admin_url( '?reseller_leads_download' );

		$date_start_last_month = date( 'Y-m-d', strtotime( 'first day of previous month' ) );
		$date_end_last_month   = date( 'Y-m-d', strtotime( 'last day of previous month' ) );

		$csv_last_month = admin_url( '?reseller_leads_date_range=' . $date_start_last_month . '|' . $date_end_last_month );

		$date_start_this_month = date( 'Y-m-d', strtotime( 'first day of this month' ) );
		$date_end_this_month   = date( 'Y-m-d', time() );

		$csv_this_month = admin_url( '?reseller_leads_date_range=' . $date_start_this_month . '|' . $date_end_this_month );
        ?>
        <fieldset style="border: 2px dotted #1C6EA4; padding: 10px; margin-bottom: 20px;">
            <legend>Schnellexport</legend>
            <a href ="<?php echo $csv_all; ?>" class="button" style="margin: 0 5px 5px 0;">Alle</a>
            <a href ="<?php echo $csv_last_month; ?>" class="button" style="margin: 0 5px 5px 0;">Letzter Monat</a>
            <a href ="<?php echo $csv_this_month; ?>" class="button" style="margin: 0 5px 5px 0;">Dieser Monat</a>
            <?php do_action( 'enon_widget_lead_export_buttons_end' ); ?>
        </fieldset>

        <script type="text/javascript">
            document.getElementById( 'export-by-date' ).addEventListener('click', function () {
                var admin_url = '<?php echo admin_url(); ?>';
                var date_start = document.getElementById('export-date-start').value;
                var date_end = document.getElementById('export-date-end').value;

                admin_url += '?reseller_leads_date_range=' + date_start + '|' + date_end;
                console.log( admin_url );
                
                document.location.href = admin_url;
            });
        </script>
        <?php
    }

    private function extended_export() {
        ?>
        <fieldset style="border: 2px dotted #1C6EA4; padding: 10px; margin-bottom: 20px;">
            <legend>Export</legend>
            <h3>Typ</h3>
            <div style="display:block; margin-bottom:20px;">
                <label>
                    <input type="radio" name="ec_type" value="all" checked>
                    Alle
                </label>
                <label>
                    <input type="radio" name="ec_type" value="vw">
                    Verbrauchsausweise
                </label>
                <label>
                    <input type="radio" name="ec_type" value="bw">
                    Bedarfsausweise
                </label>
            </div>

            <h3>Zeitraum</h3>
            <div style="display:block; margin-bottom:20px;">
                <label>
                    <input type="radio" name="range" value="all" checked>
                    Gesamter Zeitraum
                </label>
                <label>
                    <input type="radio" name="range" value="vw">
                    Letzter Monat
                </label>
                <label>
                    <input type="radio" name="range" value="bw">
                    Dieser Monat
                </label>
            </div>

            <div style="display:block; margin-bottom:20px;">
                <input type="button" class="button button-primary" id="export" value="Exportieren" />
            </div>
            

            <a href ="<?php echo $csv_all; ?>" class="button" style="margin: 0 5px 5px 0;">Alle</a>
            <a href ="<?php echo $csv_last_month; ?>" class="button" style="margin: 0 5px 5px 0;">Letzter Monat</a>
            <a href ="<?php echo $csv_this_month; ?>" class="button" style="margin: 0 5px 5px 0;">Dieser Monat</a>
            <?php do_action( 'enon_widget_lead_export_buttons_end' ); ?>
        </fieldset>

        <script type="text/javascript">
            document.getElementById( 'export-by-date' ).addEventListener('click', function () {
                var admin_url = '<?php echo admin_url(); ?>';
                var date_start = document.getElementById('export-date-start').value;
                var date_end = document.getElementById('export-date-end').value;

                admin_url += '?reseller_leads_date_range=' + date_start + '|' + date_end;
                console.log( admin_url );
                
                document.location.href = admin_url;
            });
        </script>
        <?php
    }
    
    private function date_field() {
        $admin_url = admin_url();
        ?>
        <fieldset style="border: 2px dotted #1C6EA4; padding: 10px;">
                <legend>Export nach Datum</legend>
                
                <div>
                    <label for="export-date-start" style="display:inline-block; width:100px;">Startdatum:</label>
                    <input type="date" id="export-date-start" name="export-date-start" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <label for="export-date-end" style="display:inline-block; width:100px;">Enddatum:</label>
                <input type="date" id="export-date-end" name="export-date-end" value="<?php echo date('Y-m-d'); ?>">

                <input type="button" class="button button-primary" id="export-by-date" value="Exportieren" />
        </fieldset>
        <script type="text/javascript">
            document.getElementById( 'export-by-date' ).addEventListener('click', function () {
                var admin_url = '<?php echo admin_url(); ?>';
                var date_start = document.getElementById('export-date-start').value;
                var date_end = document.getElementById('export-date-end').value;

                admin_url += '?reseller_leads_date_range=' + date_start + '|' + date_end;
                console.log( admin_url );
                
                document.location.href = admin_url;
            });
        </script>
        <?php
    }

	/**
	 * Widget for iframe code.
	 *
	 * @since 1.0.0
	 */
	public function widget_iframe_code() {
		$user = wp_get_current_user();
		$reseller_id = get_user_meta( $user->ID, 'reseller_id', true );
		$iframe = new Post_Meta_Iframe( $reseller_id );

		echo '<p>Iframe Bedarfsausweis code</p>';
		echo '<pre><code style="width:95%; display: block; overflow: scroll; font-size: 12px;">' . htmlentities( $iframe->get_iframe_bw_html() ) . '</code></pre>';

		echo '<p>Iframe Verbrauchsausweis code</p>';
		echo '<pre><code style="width:95%; display: block; overflow: scroll; font-size: 12px;">' . htmlentities( $iframe->get_iframe_vw_html() ) . '</code></pre>';
	}
}