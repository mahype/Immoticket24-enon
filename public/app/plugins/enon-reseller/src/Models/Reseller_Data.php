<?php
/**
 * Class for handling reseller data.
 *
 * @category Class
 * @package  Enon_Reseller\Models
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models;

use Enon_Reseller\Models\Data\Post_Meta_General;
use Enon_Reseller\Models\Data\Post_Meta_Billing_Email;
use Enon_Reseller\Models\Data\Post_Meta_Confirmation_Email;
use Enon_Reseller\Models\Data\Post_Meta_Iframe;
use Enon_Reseller\Models\Data\Post_Meta_Schema;
use Enon_Reseller\Models\Data\Post_Meta_Website;
use Enon_Reseller\Models\Data\Post_Meta_Send_Data;

/**
 * Class ACFResellerFiels
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller
 */
class Reseller_Data {
	/**
	 * Post Id.
	 *
	 * @var int
	 *
	 * @since 1.0.0
	 */
	private $post_id = null;

	/**
	 * Company data.
	 *
	 * @var Post_Meta_General
	 *
	 * @since 1.0.0
	 */
	public $general;

	/**
	 * Billing email data.
	 *
	 * @var Post_Meta_Billing_Email
	 *
	 * @since 1.0.0
	 */
	public $billing_email;

	/**
	 * Confirmation email data.
	 *
	 * @var Post_Meta_Confirmation_Email
	 *
	 * @since 1.0.0
	 */
	public $confirmation_email;

	/**
	 * Schema data.
	 *
	 * @var Post_Meta_Schema
	 *
	 * @since 1.0.0
	 */
	public $schema;

	/**
	 * Iframe data.
	 *
	 * @var Post_Meta_Iframe
	 *
	 @since 1.0.0
	 */
	public $iframe;

	/**
	 * Website data.
	 *
	 * @var Post_Meta_Website
	 *
	 * @since 1.0.0
	 */
	public $website;

	/**
	 * Website data.
	 *
	 * @var Post_Meta_Send_Data
	 *
	 * @since 1.0.0
	 */
	public $send_data;

	/**
	 * Reseller_Data constructor.
	 *
	 * @param int $post_id Post id.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $post_id ) {
		$this->post_id = $post_id;

		$this->general            = new Post_Meta_General( $post_id );
		$this->confirmation_email = new Post_Meta_Confirmation_Email( $post_id );
		$this->billing_email      = new Post_Meta_Billing_Email( $post_id );
		$this->iframe             = new Post_Meta_Iframe( $post_id );
		$this->schema             = new Post_Meta_Schema( $post_id );
		$this->website            = new Post_Meta_Website( $post_id );
	}

	/**
	 * Get post id.
	 *
	 * @since 1.0.0
	 *
	 * @return int $post_id Post Id.
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Get admin url.
	 * 
	 * @since 1.0.0
	 * 
	 * @return string $admin_url Admin url.
	 */
	public function get_admin_url() {
		return get_edit_post_link( $this->post_id );
	}

	/**
	 * Get admin link.
	 * 
	 * @since 1.0.0
	 * 
	 * @return string $admin_link Admin link.
	 */
	public function get_admin_link() {
		return '<a href="' . $this->get_admin_url() . '">' . $this->general->get_company_name() . '</a>';
	}

	/**
	 * Get iframe url.
	 *
	 * @since 1.0.0
	 *
	 * @return string iframe url.
	 */
	public function get_iframe_bedarfsausweis_url() {
		return get_home_url() . '/energieausweis2/bedarfsausweis-wohngebaeude/?iframe_token=' . $this->general->get_token();
	}

	/**
	 * Get iframe url.
	 *
	 * @since 1.0.0
	 *
	 * @return string iframe url.
	 */
	public function get_iframe_verbrauchsausweis_url() {
		return get_home_url() . '/energieausweis2/verbrauchsausweis-wohngebaeude/?iframe_token=' . $this->general->get_token();
	}
}
