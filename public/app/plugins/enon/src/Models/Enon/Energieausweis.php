<?php

/**
 * New Energieasweis class.
 *
 * @category Class
 * @package  Enon\Models\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 *
 * @todo Replacing old energieausweis class.
 */

namespace Enon\Models\Enon;

use WP_Post;

/**
 * Class Energieausweis
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Taks\Plugins
 */
class Energieausweis
{
	/**
	 * Energieausweis id.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $id;

	/**
	 * Post object.
	 *
	 * @since 1.0.0
	 *
	 * @var \WP_Post
	 */
	private $post;

	/**
	 * Post meta values.
	 * 
	 * @var array
	 * 
	 * @since 2022-02-08
	 */
	private $post_meta;

	/**
	 * Energieausweis constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id Energieausweis id.
	 */
	public function __construct(int $id)
	{
		$this->id = $id;

		$this->post = get_post($id);
		$this->post_meta = get_post_meta($id);
	}

	/**
	 * Get option value.
	 * 
	 * @since 1.0.0
	 */
	protected function get_option(string $name)
	{
		if (!array_key_exists($name, $this->post_meta)) {
			return;
		}

		if( count($this->post_meta) > 1 ) {
			$this->post_meta[$name];
		}

		return $this->post_meta[$name][0];
	}

	/**
	 * Get energieausweis id.
	 *
	 * @since 1.0.0
	 *
	 * @return int Energieausweis id.
	 */
	public function get_id(): int
	{
		return $this->id;
	}

	/**
	 * Get post object.
	 *
	 * @return array|\WP_Post|null
	 *
	 * @since 1.0.0
	 */
	public function get_post(): WP_Post
	{
		return $this->post;
	}

	/**
	 * Get the energieausweis title.
	 * 
	 * @return string Energieausweis title.
	 * 
	 * @since 1.0.0
	 */
	public function get_title(): string
	{
		return $this->post->post_title;
	}

	/**
	 * Get energieausweis type.
	 *
	 * @since 1.0.0
	 *
	 * @return string Energieausweis type bw or vw.
	 */
	public function get_type()
	{
		return $this->get_option('wpenon_type');
	}

	/**
	 * Get energieausweis standard.
	 *
	 * @since 1.0.0
	 *
	 * @return string Energieausweis standard.
	 */
	public function get_standard()
	{
		return $this->get_option('wpenon_standard');
	}

	/**
	 * Get payment id.
	 *
	 * @return int Payment id.
	 *
	 * @since 1.0.0
	 */
	public function get_payment_id()
	{
		$payment_ids = $this->get_option('_wpenon_attached_payment_id');

		if (is_array( $payment_ids )) {
			if( count($payment_ids) > 0 ) {
				return false;
			}
			return $payment_ids[0];
		}

		return $payment_ids;
	}

	/**
	 * Get contact email.
	 *
	 * @since 1.0.0
	 *
	 * @return string Contact email.
	 */
	public function get_contact_email()
	{
		return $this->get_option('wpenon_email');
	}

	/**
	 * Get address street nr.
	 *
	 * @since 1.0.0
	 *
	 * @return string Address street nr.
	 */
	public function get_address_street()
	{
		return $this->get_option('addresse_strassenr');
	}

	/**
	 * Get address plz.
	 *
	 * @since 1.0.0
	 *
	 * @return string Address street nr.
	 */
	public function get_address_postcode()
	{
		return $this->get_option('addresse_plz');
	}

	/**
	 * Get address postcode.
	 *
	 * @since 1.0.0
	 *
	 * @return string Address street postcode.
	 */
	public function get_address_city()
	{
		return $this->get_option('addresse_ort');
	}

	/**
	 * Get address state.
	 *
	 * @since 1.0.0
	 *
	 * @return string Address state.
	 */
	public function get_address_state()
	{
		return $this->get_option('addresse_bundesland');
	}

	/**
	 * Get access token for editing page.
	 *
	 * @since 1.0.0
	 *
	 * @return string $access_token      Token to use in URL.
	 */
	public function get_access_token()
	{
		$wpenon_email  = $this->get_option('wpenon_email');
		$wpenon_secret = $this->get_option('wpenon_secret');
		$access_token  = md5($wpenon_email) . '-' . $wpenon_secret;

		return $access_token;
	}

	/**
	 * Get access url.
	 * 
	 * @return string Accessible url for customer.
	 * 
	 * @since 1.0.0
	 */
	public function get_access_url()
	{
		return add_query_arg(
			'access_token',
			$this->get_access_token(),
			$this->get_url()
		);
	}

	public function get_url() {
		return get_permalink($this->id);
	}

	/**
	 * Has the user allowed to contact?
	 * 
	 * @return bool True if contacting is allowed, false if not.
	 */
	public function contacting_allowed(): bool
	{
		return $this->post_meta['contact_acceptance'][0] == 1 ? true : false;
	}
}
