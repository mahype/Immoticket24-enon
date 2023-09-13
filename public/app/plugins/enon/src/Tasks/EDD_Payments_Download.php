<?php
/**
 * Edd payment download.
 *
 * @category Class
 * @package  Enon\Tasks\Filters
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

 namespace Enon\Tasks;

 use Awsm\WP_Wrapper\Interfaces\Actions;
 use Awsm\WP_Wrapper\Interfaces\Task;
 use EDD_Payment;
use WP_Query;

 class Payment_CLI {
     public function pdf($args, $assoc_args) {
         $year = $assoc_args['year'];
         $month = $assoc_args['month'];
 
         if( !isset( $year )) {
             $year = date("Y", strtotime ( '-1 month' , time() )) ;
         }
 
         if( !isset( $month )) {
             $month = date("m", strtotime ( '-1 month' , time() )) ;
         }
 
         if( strlen($month) === 1) {
             $month = '0' . $month;
         }
 
         $this->generate_bills($year, $month);
     }
 
     public function regenerate() {
         $bills_list = EDD_Payments_Download::get_bills_list();
 
         foreach($bills_list as $year => $months) {
             foreach($months as $month => $bill) {
                 if( empty( $bill ) ) {
                     $this->generate_bills( $year, $month );
                 }
             }
         }
     }
 
     private function generate_bills($year, $month) {
         global $wpdb;
 
         $create_file = dirname( ABSPATH ). '/dl/rechnungen/.creating';
 
         if( file_exists( $create_file )) {
             \WP_CLI::error( 'Rechnungen werden gerade erstellt. Bitte warten.' );
         }
 
         $file = fopen( $create_file, 'w' );
         fclose( $file );
 
         $bills_filename = get_bloginfo('url') . '/dl/rechnungen/' . $year . '-' . $month .'.zip' ;
 
         \WP_CLI::line('Starte PDF-Erstellung für ' . $year . '-' . $month);
         \WP_CLI::line( sprintf( 'Rechnungen in PDF Form und CSV-Auflistung kann unter %s heruntergeladen werden.', $bills_filename ) );
 
         $charset = 'UTF-8'; // WPENON_DEFAULT_CHARSET
 
         $sql = $wpdb->prepare("SELECT DISTINCT p.ID FROM {$wpdb->prefix}posts AS p
             INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id
             WHERE p.post_type = 'edd_payment'
             AND p.post_date LIKE %s
             ORDER BY p.post_date DESC",
             $year . '-' . $month . '%'
         );
 
         $ids = $wpdb->get_col($sql);
 
         
         if( empty( $ids )) {
             \WP_CLI::error('Keine Zahlungen gefunden');
         }
 
         $payments = edd_get_payments( [
             'number' => WP_ENV === 'development' ? 50: -1,
             'post__in' => $ids,
         ]);
 
         $this->sequential = edd_get_option('enable_sequential');
 
         $path = dirname( ABSPATH ). '/dl/rechnungen/';
         if( ! is_dir( $path ) ) {
             mkdir( $path, 0777, true );
         }
 
         $path = $path  . $year;
         if( ! is_dir( $path ) ) {
             mkdir( $path, 0777, true );
         }
 
         $path = $path . '/' . $month;
         if( ! is_dir( $path ) ) {
             mkdir( $path, 0777, true );
         }
 
         $csv_filename = $path . '/payments.csv';
 
         $csv_settings = array(
             'terminated' => ';',
             'enclosed'   => '"',
             'escaped'    => '"',
         );
     
         $csv_headings = array(
             'nummer'   => __( 'Nummer', 'wpenon' ),
             'name'     => __( 'Name, Vorname', 'wpenon' ),
             'subtotal' => __( 'Nettobetrag', 'wpenon' ),
             'tax'      => __( 'MwSt.', 'wpenon' ),
             'total'    => __( 'Bruttobetrag', 'wpenon' ),
             'status'   => __( 'Status', 'wpenon' ),
         );
 
         $output = fopen( $csv_filename, 'w' );
         fputcsv( $output, \WPENON\Util\Format::csvEncode( $csv_headings, $charset ), $csv_settings['terminated'], $csv_settings['enclosed'] );
 
         $time_start = microtime(true);
         foreach( $payments as $payment ) {
             $name = get_the_title($payment->ID);
             $receipt = new \WPENON\Model\ReceiptPDF($name);
             $payment_data = $this->get_payment_details($payment->ID);
             $receipt->create($payment_data);
             $receipt->finalize('F', $path );
 
             switch( $payment_data->post_status ) {
                 case 'publish':
                     $status = 'Bezahlt';
                     break;
                 case 'pending':
                     $status = 'Ausstehend';
                     break;
                 case 'refunded':
                     $status = 'Rückerstattet';
                     break;
                 case 'revoked':
                     $status = 'Storniert';
                     break;
                 case 'failed':
                     $status = 'Fehlgeschlagen';
                     break;
                 default:
                     $status = 'Unbekannt';
             }
 
             $result = array(
                 'nummer'   => $name,
                 'name'     => $payment_data->last_name . ', ' . $payment_data->first_name,
                 'subtotal' => $payment_data->total - $payment_data->tax,
                 'tax'      => $payment_data->tax,
                 'total'    => $payment_data->total,
                 'status'   => $status,
             );
     
             fputcsv( $output, \WPENON\Util\Format::csvEncode( $result, $charset ), $csv_settings['terminated'], $csv_settings['enclosed'] );
 
             \WP_CLI::line( 'Rechnung ' . $name . ' abgelegt in ' . $path . '/' . $name . '.pdf' );
         }
 
         \WP_CLI::line( 'CSV Datei erstellt: ' . $csv_filename );
         fclose( $output );
 
         $time_end = microtime(true);
         $execution_time = ($time_end - $time_start);
         \WP_CLI::line( 'Rechnungen wurden erstellt. Benötigte Zeit ' . $execution_time . ' Sekunden' );
 
         $zip = new \ZipArchive();
         $zip->open( dirname( dirname( $path ) ) . '/'. $year .'-' . $month . '.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE );
         $files = new \RecursiveIteratorIterator(
             new \RecursiveDirectoryIterator($path),
             \RecursiveIteratorIterator::LEAVES_ONLY
         );
 
         foreach ($files as $name => $file)
         {
             // Skip directories (they would be added automatically)
             if (!$file->isDir())
             {
                 // Get real and relative path for current file
                 $filePath = $file->getRealPath();
                 $relativePath = substr($filePath, strlen($path) + 1);
 
                 // Add current file to archive
                 $zip->addFile($filePath, $relativePath);
             }
         }
 
         // Zip archive will be created only after closing object
         $zip->close();
 
         EDD_Payments_Download::delTree(dirname($path));
         \WP_CLI::line( 'Temporärer Pfad ' . dirname($path) . ' gelöscht.' );
 
         EDD_Payments_Download::add_bills_zip( $year, $month, $bills_filename );
 
         \WP_CLI::line( sprintf( 'Rechnungen in PDF Form und CSV-Auflistung kann unter %s heruntergeladen werden.', $bills_filename ) );
 
         unlink( $create_file );
     }
 
     private function get_payment_details($payment_id)
     {
         $payment = new EDD_Payment( $payment_id );
 
         $details = new \stdClass();
 
         $details->ID = $payment_id;
         $details->first_name = $payment->first_name;
         $details->last_name = $payment->last_name;
         $details->date = $payment->date;
         $details->post_status = $payment->status;
         $details->total = floatval( $payment->total );
         $details->subtotal = $payment->subtotal;
         $details->tax = $payment->tax;
         $details->fees = $payment->get_fees('all');
         $details->key = $payment->key;
         $details->gateway = $payment->gateway;
         $details->user_info = $payment->user_info;
         $details->cart_details = edd_get_payment_meta_cart_details($payment_id, true);
 
 
         if ($this->sequential) {
             $details->payment_number = $payment->number;
         }
 
         return $details;
     }
 }

 /**
 * Edd payment download.
 *
 * @category Class
 * @package  Enon\Tasks\Filters
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */


class Purge_CLI {
    public function all($args, $assoc_args) {
        global $wpdb;

        $clean_post_types = [
            'download', 
            'edd_payment', 
            'edd_discount', 
            'edd_log',
            'revision'
        ];
        // The Query
        $query = new WP_Query( $args );

        // Delete all post types from $clean_post_Types older than 1 day
        echo 'Deleting all post types from $clean_post_Types older than 1 day...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_posts WHERE post_type IN ("' . implode('","', $clean_post_types) . '")' );

        // Delete images from WordPress db with post_name beginning with 'temporaeres-energieausweis-bild'
        echo 'Deleting images from WordPress db with post_name beginning with "temporaeres-energieausweis-bild"...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_posts WHERE post_name LIKE "temporaeres-energieausweis-bild%"' );
        
        // Delete posts with parent_id is not 0 and non existing parent_id
        echo 'Deleting posts with parent_id != 0 and non existing parent_id...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_posts WHERE post_parent != 0 AND post_parent NOT IN (SELECT id FROM wpit24_posts)' );
        
        // Delete orphaned post_meta data
        echo 'Deleting orphaned post meta data...' . "\n";         
        $wpdb->query( 'DELETE FROM wpit24_postmeta WHERE post_id NOT IN (SELECT id FROM wpit24_posts)' );

        // Delete all edd customers
        echo 'Deleting all edd customers...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_edd_customers' );

        // Delete WP Affiliate data
        echo 'Deleting WP Affiliate data...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_affiliatemeta' );
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_affiliates' );
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_campaigns' );
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_connections' );
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_custom_links' );
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_customers' );
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_lifetime_customers' );
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_customermeta' );
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_notifications' );
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_clicks' );
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_commissions' );        
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_referrals' );
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_sales' );                
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_payouts' );

        // Delete Gravity Forms data
        echo 'Deleting Gravity Forms data...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_gf_entry' );
        $wpdb->query( 'DELETE FROM wpit24_gf_entry_meta' );
        $wpdb->query( 'DELETE FROM wpit24_gf_entry_notes' );
        $wpdb->query( 'DELETE FROM wpit24_gf_form_view' );

        // Delete all customermeta
        echo 'Deleting all customermeta...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_edd_customermeta' );
         
        // Delete comments and their meta data
        echo 'Deleting comments and their meta data...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_comments' );
        $wpdb->query( 'DELETE FROM wpit24_commentmeta' ); 

        // Delete Email logs from WP Mail SMTP
        echo 'Deleting WP Mail SMTP Data...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_wpmailsmtp_emails_log' ); 
        $wpdb->query( 'DELETE FROM wpit24_wpmailsmtp_attachment_files' ); 


        // Delete Security logs
        echo 'Deleting Security logs...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_icwp_wpsf_req_logs' );
        $wpdb->query( 'DELETE FROM wpit24_icwp_wpsf_audit_trail' );
        $wpdb->query( 'DELETE FROM wpit24_icwp_wpsf_botsignal' );
        $wpdb->query( 'DELETE FROM wpit24_icwp_wpsf_ips' );
        $wpdb->query( 'DELETE FROM wpit24_icwp_wpsf_at_meta' );
        $wpdb->query( 'DELETE FROM wpit24_icwp_wpsf_resultitem_meta' );

        // Delete WP Securitry Audit Log data
        echo 'Deleting WP Securitry Audit Log data...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_wsal_metadata' );
        $wpdb->query( 'DELETE FROM wpit24_wsal_occurrences' );
        $wpdb->query( 'DELETE FROM wpit24_wsal_options' );

        // Delete Wordfence data
        echo 'Deleting Wordfence data...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_wfknownfilelist' );
        $wpdb->query( 'DELETE FROM wpit24_wfstatus' );
        $wpdb->query( 'DELETE FROM wpit24_wflogins' );
        $wpdb->query( 'DELETE FROM wpit24_wffilemods' );
        $wpdb->query( 'DELETE FROM wpit24_wfcrawlers' );
        $wpdb->query( 'DELETE FROM wpit24_wfnotifications' );
        $wpdb->query( 'DELETE FROM wpit24_wfhits' );

        // Delete borlabs cookie data
        echo 'Deleting Borlabs cookie consent logs...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_borlabs_cookie_statistics' );
        $wpdb->query( 'DELETE FROM wpit24_borlabs_cookie_consent_log' );

        // Deleting visits from Affliate WP
        echo 'Deleting visits from Affliate WP...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_affiliate_wp_visits' );

        // Deleting yoast data
        echo 'Deleting yoast data...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_yoast_indexable' );
        $wpdb->query( 'DELETE FROM wpit24_yoast_indexable_hierarchy' );
        $wpdb->query( 'DELETE FROM wpit24_yoast_seo_links' );
        $wpdb->query( 'DELETE FROM wpit24_yoast_seo_meta' );
        $wpdb->query( 'DELETE FROM wpit24_yoast_migrations' );

        // Deleting WP Rocket data
        echo 'Deleting WP Rocket data...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_wpr_rocket_cache' );
        $wpdb->query( 'DELETE FROM wpit24_options WHERE option_name LIKE "%_transient_wpr_cache%"' );

        // Deleting Customermeta
        echo 'Deleting Customermeta...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_it24ea_customermeta' );

        // Dropping optimizepress data
        echo 'Dropping unused plugins...' . "\n";
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_optimizepress_assets' );
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_optimizepress_launchfunnels' );
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_optimizepress_launchfunnels_pages' );
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_optimizepress_layout_categories' );
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_optimizepress_pb_products' );
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_optimizepress_post_layouts' );
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_optimizepress_predefined_layouts' );
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_optimizepress_presets' );

        // Delete WP All Export data
        echo 'Deleting WP All Export data...' . "\n";
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_pmxe_exports' );        
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_pmxe_google_cats' );
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_pmxe_posts' );
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_pmxe_templates' );

        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_bdp_archives' );
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_bdp_single_layouts' );
        
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_login_fails' );
        $wpdb->query( 'DROP TABLE IF EXISTS wpit24_lockdowns' );

        // Deleting orphaned term relationships
        echo 'Deleting orphaned term relationships...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_term_relationships WHERE object_id NOT IN (SELECT id FROM wpit24_posts)' );

        // Whatever it is, delete it...
        echo 'Deleting whatever it is...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_tcb_api_error_log' );

        // Delete all users except base users
        echo 'Deleting all users except base users...' . "\n";
        $wpdb->query( 'DELETE FROM wpit24_users' );        
        $wpdb->query( 'DELETE FROM wpit24_usermeta' );

        // Add dev users, admins, editors, authors, contributors, subscribers
        echo 'Adding dev admin' . "\n";

        wp_insert_user( [
            'user_login' => 'admin',
            'user_pass' => 'V,n<2Oj]U0%Y\<',
            'user_email' => 'admin@enon.test',
            'role' => 'administrator'
        ] );

        // echo 'Found entries: ' . $query->found_posts . "\n";      

        // if ( $query->have_posts() ) {
        //     $progress = \WP_CLI\Utils\make_progress_bar( 'Cleaning up DB: ', $query->found_posts );

        //     while ( $query->have_posts() ) {
        //         $query->the_post();
                
        //         // wp_trash_post( get_the_ID() );  use this function if you have custom post type
        //         wp_delete_post(get_the_ID(),true); //use this function if you are working with default posts

        //         $progress->tick();
        //     }           

        //     $progress->finish();
        // } else {
        //     // no posts found
        //     return false;

        // }
        // die();
        // // Restore original Post Data
        // wp_reset_postdata();
    }
}

/**
 * Class Filter_Mails_For_Postcodes.
 *
 * @since 1.0.0
 */
class EDD_Payments_Download implements Task, Actions {
    private $sequential;

    /**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_actions();
	}

    /**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
        add_action( 'admin_init', [ $this, 'receive_params' ], 5  );
        add_action( 'cli_init', [ $this, 'add_cli_command' ], 5  );
        add_action( 'wp_dashboard_setup', [$this, 'dashboard_widget'] );
	}

    public function add_cli_command() {
        \WP_CLI::add_command( 'payments', Payment_CLI::class);
        \WP_CLI::add_command( 'enon-purge', Purge_CLI::class);
    }

    public function receive_params() {
        if( isset( $_GET['regenerate_bills'] ) ) {
            if( $_GET['regenerate_bills'] == 'true' ) {
                self::regenerate_bill($_GET['year'], $_GET['month']);
            }
            wp_redirect( admin_url( 'index.php' ) );
            exit;
        }
    }

    public function dashboard_widget() {
        if( ! current_user_can('administrator')) {
            return; 
        }     
        wp_add_dashboard_widget(
            'edd_payments_download',
            'Rechnungen herunterladen',
            [ $this, 'dashboard_widget_content' ]
        );
    }

    public function dashboard_widget_content() {
        
        $bills_list = get_option( 'enon_bills_list' );
        $regenerate_link_base = admin_url('index.php');
        $regenerate_link_base = add_query_arg( 'regenerate_bills', 'true', $regenerate_link_base );

        if ( empty($bills_list) ) {
            echo 'Keine Rechnungen vorhanden';
            return;
        }
       
        foreach( $bills_list as $year => $bills_months ) {
            echo '<h3>Jahr ' . $year . '</h3>';
            echo '<ul>';
            foreach( $bills_months as $month => $bill ) {
                $regenerate_link = add_query_arg( 'year', $year, $regenerate_link_base );
                $regenerate_link = add_query_arg( 'month', $month, $regenerate_link );

                if( empty( $bill ) ) {
                    echo '<li>Rechnung ' . $month . '/' . $year . ' - Warten auf Neugenerierung</li>';
                } else {
                    echo '<li>Rechnung ' . $month . '/' . $year . ' - <a href="' . $bill . '">herunterladen</a> | <a href="' . $regenerate_link . '">neu generieren</a></li>';
                }
                
            }
            echo '</ul>';
        }
    }

    public static function regenerate_bill($year, $month) {
        $bills_list = get_option( 'enon_bills_list' );
        $file = $bills_list[$year][$month];
        if( !empty( $file ) ) {
            $file = dirname( ABSPATH ). '/dl/rechnungen/' .  basename($file);
            unlink( $file );
        }
        $bills_list[$year][$month] = '';
        update_option( 'enon_bills_list', $bills_list );
    }

    public static function add_bills_zip($year, $month, $file) {
        $bills_list = get_option( 'enon_bills_list' );
        $bills_list[$year][$month] = $file;
        update_option( 'enon_bills_list', $bills_list );
    }

    public static function get_bills_zip($year, $month) {
        $bills_list = get_option( 'enon_bills_list' );

        if (isset($bills_list[$year][$month])) {
            return $bills_list[$year][$month];
        }
    }

    public static function get_bills_list() {
        return get_option( 'enon_bills_list' );
    }

    public static function delTree( $dir ) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}
