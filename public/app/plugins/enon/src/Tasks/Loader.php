<?php
/**
 * Tasks loader.
 *
 * @category Class
 * @package  Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Enon\Logger;
use Enon\Task_Loader;
use Enon\Tasks\Emails\Edd_Payment_Emails;
use Enon\Tasks\Scripts\Add_Scripts;
use Enon\Tasks\Scripts\Add_Page_Scripts;
use Enon\Tasks\Scripts\Add_Uptain_Scripts;
use Enon\Tasks\Scripts\Add_Trusted_Shops_Scripts;
use Enon\Tasks\Scripts\Add_Google_Tag_Manager;

use Enon\Tasks\Filter_EDD_Emails;
use Enon\Tasks\EDD_Payments_Download;
use Enon\Tasks\Scripts\Add_Ekomi;

/**
 * Tasks loader.
 *
 * @package Enon\Config
 */
class Loader extends Task_Loader {
	public function __construct() {
        $logger = new Logger('enon');
        parent::__construct( $logger );
    }

	/**
	 * Loading Scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_task( EDD_Payments_Download::class );
		$this->add_task( Config_User::class );
		$this->add_task( Add_Options_General::class, $this->logger() );
		$this->add_task( Add_Page_Fields::class, $this->logger() );
		$this->add_task( Add_Cli_Commands::class );
		$this->add_task( Edd_Payment_Emails::class );
		$this->add_task( Test_Script::class );

		$this->add_task( Mediathek_Thumbnail_Validator::class, $this->logger() );
		$this->add_task( Add_Ekomi::class );

        $this->add_task( Setup_Edd::class, $this->logger() );
		$this->add_task( Filter_EDD_Emails::class );
		$this->add_task( Add_Costum_Fees_EVM::class );

		if ( is_admin() && ! wp_doing_ajax() ) {
			$this->add_admin_tasks();
		} else {
			$this->add_frontend_tasks();
		}

		$this->run_tasks();
	}

	/**
	 * Running admin tasks.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_tasks() {
		// $this->add_task( Fix_Misleaded_Invoices::class );
		$this->add_task( Setup_Edd_Payments_Table::class );
		$this->add_task( Mediathek_Thumbnail_Validator::class );
		$this->add_task( Show_Edd_Log_Post_Type::class );
		$this->add_task( Setup_AffilateWP::class );
	}

	/**
	 * Running frontend tasks.
	 *
	 * @since 1.0.0
	 */
	public function add_frontend_tasks() {
		$this->add_task( Add_Popups::class );

		$this->add_task( Remove_Optimizepress::class );

		$this->add_task( Add_Scripts::class );
		$this->add_task( Add_Page_Scripts::class );
		$this->add_task( Add_Uptain_Scripts::class );
		$this->add_task( Add_Trusted_Shops_Scripts::class );
		$this->add_task( Add_Google_Tag_Manager::class );
		$this->add_task( Filter_Custom_Fees_By_Coupon_Code::class );
	}
}

