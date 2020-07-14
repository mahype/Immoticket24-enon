<?php
/**
 * Class for loading frontend scripts.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Core
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Filters;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use Awsm\WP_Wrapper\Tools\Logger_Trait;

use Enon_Reseller\Logger;
use Enon_Reseller\Models\Reseller;

/**
 * Class Task_Frontend.
 *
 * @package Enon_Reseller\WordPress
 *
 * @since 1.0.0
 */
class Filter_Email_Template implements Task, Actions, Filters {
	use Logger_Trait;

	/**
	 * Reseller object.
	 *
	 * @since 1.0.0
	 * @var Reseller;
	 */
	private $reseller;

	/**
	 * Task_Frontend constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Reseller $reseller Reseller object.
	 * @param Logger   $logger   Logger object.
	 */
	public function __construct( Reseller $reseller, Logger $logger ) {
		$this->reseller = $reseller;
		$this->logger = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'edd_get_template_part', array( $this, 'add_template_part' ), 10, 3 );
	}

	/**
	 * Adding template part for reseller.
	 *
	 * @param string $templates The email template.
	 * @param string $slug The email template.
	 * @param string $name The email template.
	 *
	 * @return string $template Filtered template.
	 *
	 * @since 1.0.0
	 */
	public function add_template_part( $templates, $slug, $name ) {
		if ( count( $templates ) === 0 ) {
			return $templates;
		}

		$default_templates = array(
			'emails/header-default.php',
			'emails/body-default.php',
			'emails/footer-default.php',
		);

		if ( ! in_array( $templates[0], $default_templates ) ) {
			return $templates;
		}

		$company_id = $this->reseller->data()->general->get_company_id();

		if ( empty( $company_id ) ) {
			return $templates;
		}

		switch ( $templates[0] ) {
			case 'emails/header-default.php':
				$additional_template = "emails/header-{$company_id}.php";
				break;
			case 'emails/body-default.php':
				$additional_template = "emails/body-{$company_id}.php";
				break;
			case 'emails/footer-default.php':
				$additional_template = "emails/footer-{$company_id}.php";
				break;
		}

		array_unshift( $templates, $additional_template );

		return $templates;
	}


}
