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
use Enon\Models\Edd\Payment;

use WPENON\Model\Energieausweis;

class Payment_CLI {
    public function pdf($args, $assoc_args) {
        $year = $assoc_args['year'];
        $month = $assoc_args['month'];

        if( ! $year || ! $month ) {
            \WP_CLI::error( 'Please provide a year and month' );
            exit;
        }

        \WP_CLI::line('Starte PDF-Erstellung für ' . $year . '-' . $month);

        $payments = edd_get_payments( [
            'number' => -1,
            'status' => 'publish',
            'month'  => $month,
            'year'  => $year,
        ] );

        $this->sequential = edd_get_option('enable_sequential');

        $path = dirname( dirname( ABSPATH ) ) . '/payments/';
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

        $time_start = microtime(true);
        foreach( $payments as $payment ) {
            $name = get_the_title($payment->ID);
            $receipt = new \WPENON\Model\ReceiptPDF($name);
            $payment_data = $this->get_payment_details($payment->ID);
            $receipt->create($payment_data);
            $receipt->finalize('F', $path );

            \WP_CLI::line( 'Rechnung ' . $name . ' abgelegt in ' . $path . '/' . $name . '.pdf' );
        }
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        \WP_CLI::line( 'Rechnungen wurden erstellt. Benötigte Zeit ' . $execution_time . ' Sekunden' );

        $zip = new \ZipArchive();
        $zip->open( dirname( dirname( $path ) ) . '/rechnungen-'. $year .'-' . $month . '.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE );
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

        \WP_CLI::line( 'Rechnungen wurden gepackt. ' . dirname( dirname( $path ) ) . '/rechnungen-'. $year .'-' . $month . '.zip' );
        exit;
    }

    private function get_payment_details($payment_id)
    {
        $payment = new EDD_Payment( $payment_id );

        $details = new \stdClass();

        $details->ID = $payment_id;
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
        add_action( 'cli_init', [ $this, 'init' ], 5  );
	}

    public function init() {
        \WP_CLI::add_command( 'payments', Payment_CLI::class);
    }
}

