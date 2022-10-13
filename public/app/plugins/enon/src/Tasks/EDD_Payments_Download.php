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

        $bills_filename = get_bloginfo('url') . '/dl/rechnungen/' . $year . '-' . $month .'.zip' ;

        \WP_CLI::line('Starte PDF-Erstellung für ' . $year . '-' . $month);
        \WP_CLI::line( sprintf( 'Rechnungen in PDF Form und CSV-Auflistung kann unter %s heruntergeladen werden.', $bills_filename ) );

        $charset = 'UTF-8'; // WPENON_DEFAULT_CHARSET

        $sql = $wpdb->prepare("SELECT DISTINCT p.ID FROM {$wpdb->prefix}posts AS p
            INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id
            WHERE p.post_type = 'edd_payment'
            AND p.post_status = 'publish'
            AND p.post_date LIKE %s
            ORDER BY p.post_date DESC",
            $year . '-' . $month . '%'
        );

        $ids = $wpdb->get_col($sql);

        
        if( empty( $ids )) {
            \WP_CLI::error('Keine Zahlungen gefunden');
        }

        $payments = edd_get_payments( [
            'number' => -1,
            'status' => 'publish',
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
            'nummer'     => __( 'Nummer', 'wpenon' ),
            'name'     => __( 'Name, Vorname', 'wpenon' ),
            'subtotal' => __( 'Nettobetrag', 'wpenon' ),
            'tax'      => __( 'MwSt.', 'wpenon' ),
            'total'    => __( 'Bruttobetrag', 'wpenon' ),
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

            $result = array(
                'nummer'   => $name,
                'name'     => $payment_data->last_name . ', ' . $payment_data->first_name,
                'subtotal' => $payment_data->total - $payment_data->tax,
                'tax'      => $payment_data->tax,
                'total'    => $payment_data->total,
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

        EDD_Payments_Download::delTree($path);
        \WP_CLI::line( 'Temporärer Pfad ' . $execution_time . ' gelöscht.' );

        EDD_Payments_Download::add_bills_zip( $year, $month, $bills_filename );

        \WP_CLI::line( sprintf( 'Rechnungen in PDF Form und CSV-Auflistung kann unter %s heruntergeladen werden.', $bills_filename ) );
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

