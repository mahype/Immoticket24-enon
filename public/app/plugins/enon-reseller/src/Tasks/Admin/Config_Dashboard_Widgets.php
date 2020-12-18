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
        $this->extended_export();

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
        $first_day_of_previous_month = date( 'Y-m-d', strtotime( 'first day of previous month' ) );
        $last_day_of_previous_month   = date( 'Y-m-d', strtotime( 'last day of previous month' ) );
        
        $first_day_of_this_month = date( 'Y-m-d', strtotime( 'first day of this month' ) );
        $today = date( 'Y-m-d' );
        
        ?>
        <style>
        .wpenon-admin-fieldset {
            border: 2px dotted #1C6EA4; 
            padding: 10px; 
            margin-bottom: 20px;
        }

        .wpenon-admin-fieldset input,
        .wpenon-admin-fieldset select,
        .wpenon-admin-fieldset textarea {
            width:100%;
        }

        .wpenon-admin-fieldset label {
            margin-top: 10px;
            display: block;
        }

        .wpenon-admin-fieldset input[type=text] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            box-sizing: border-box;
        }

        .button-group {
            display: block;
            margin-top: 10px;
        }

        .button-group .button {
            margin: 0 5px 5px 0;
        }

        #export {
            margin-top:10px;
        }
        </style>
        <fieldset class="wpenon-admin-fieldset">
            <legend>Export</legend>

            <div class="button-group">
                <h3>Voreinstellungen</h3>
                <a id="filter_all" class="button">Gesamter Zeitraum</a>
                <a id="filter_last_month" class="button">Letzter Monat</a>
                <a id="filter_this_month" class="button">Dieser Monat</a>                
            </div>

            <?php do_action( 'enon_widget_lead_export_after_fast_setting' ); ?>

            <label for="filter_ec_type">Ausweistyp</label> 
            <select id="filter_ec_type">
                <option value="all" selected>Alle</option>
                <option value="bw">Bedarfsausweis</option>
                <option value="vw">Verbrauchsausweis</option>
            </select>

            <?php do_action( 'enon_widget_lead_export_after_ec_type' ); ?>
            
            <label for="filter_date_start" style="display:inline-block; width:100px;">Startdatum:</label>
            <input type="date" id="filter_date_start" value="<?php echo $first_day_of_this_month; ?>">

            <label for="filter_date_end" style="display:inline-block; width:100px;">Enddatum:</label>
            <input type="date" id="filter_date_end" value="<?php echo date('Y-m-d'); ?>">  
            
            <?php do_action( 'enon_widget_lead_export_after_date' ); ?>

            <label for="filter_postcodes">Postleitzahlen (eine PLZ pro Zeile)</label>    
            <textarea id="filter_postcodes" rows="10"></textarea>

            <label for="filter_postcodes_direction">Oben genannte Postleitzahlen</label>
            <select id="filter_postcodes_direction">
                <option value="include">einschließen</option>
                <option value="exclude">ausschließen</option>
            </select>

            <?php do_action( 'enon_widget_lead_export_after_postcode' ); ?>                            

            <input type="button" class="button button-primary" id="export" value="Exportieren" />
        </fieldset>

        <script type="text/javascript">
            document.getElementById( 'filter_all' ).addEventListener('click', function() {
                document.getElementById('filter_date_start').value = '2010-01-01';
                document.getElementById('filter_date_end').value = '<?php echo date('Y-m-d'); ?>';
            });

            document.getElementById( 'filter_last_month' ).addEventListener('click', function() {
                document.getElementById('filter_date_start').value = '<?php echo $first_day_of_previous_month; ?>';
                document.getElementById('filter_date_end').value = '<?php echo $last_day_of_previous_month; ?>';
            });

            document.getElementById( 'filter_this_month' ).addEventListener('click', function() {
                document.getElementById('filter_date_start').value = '<?php echo $first_day_of_this_month; ?>';
                document.getElementById('filter_date_end').value = '<?php echo $today; ?>';
            });

            document.getElementById( 'export' ).addEventListener('click', function () {
                var admin_url = '<?php echo admin_url(); ?>';

                var filter_ec_type = document.getElementById('filter_ec_type').value;

                var filter_date_start = document.getElementById('filter_date_start').value;
                var filter_date_end = document.getElementById('filter_date_end').value;

                var filter_postcodes = document.getElementById('filter_postcodes').value.replace(/(\r\n|\n|\r)/gm,",");
                var filter_postcodes_direction = document.getElementById('filter_postcodes_direction').value;

                admin_url += '?reseller_leads_date_range=' + filter_date_start + '|' + filter_date_end + '&reseller_leads_ec_type=' + filter_ec_type + '&reseller_leads_postcodes=' + filter_postcodes + '&reseller_leads_postcodes_direction=' + filter_postcodes_direction;

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