<?php

/**
 * Reseller object.
 *
 * @category Class
 * @package  Enon_Reseller\Models
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 *
 * @todo Replacing old energieausweis class.
 */

namespace Enon_Reseller\Models;

use Enon\Models\Enon\Energieausweis;

/**
 * Class Reseller
 *
 * @package Enon_Reseller
 *
 * @since 1.0.0
 */
class Reseller
{
	/**
	 * Post id.
	 *
	 * @var int $id
	 *
	 * @since 1.0.0
	 */
	private $id;

	/**
	 * Holds loaded reseller data.
	 *
	 * @var Reseller_Data
	 *
	 * @since 1.0.0
	 */
	private $data;

	/**
	 * Reseller constructor.
	 *
	 * @param int    $id    Reseller id.
	 *
	 * @since 1.0.0
	 */
	public function __construct($id)
	{
		$this->set_id($id);
	}

	/**
	 * Get id.
	 *
	 * @return int Post id of reseller.
	 *
	 * @since 1.0.0
	 */
	public function get_id()
	{
		return $this->id;
	}

	/**
	 * Set post id.
	 *
	 * @param int $id Post id.
	 *
	 * @since 1.0.0
	 */
	public function set_id($id)
	{
		$this->id = $id;
		$this->data = new Reseller_Data($id);
	}

	/**
	 * Get reseller values.
	 *
	 * @return Reseller_Data
	 *
	 * @since 1.0.0
	 */
	public function data()
	{
		return $this->data;
	}

	/**
	 * Adds iframe and energeausweis parameters to url.
	 *
	 * @param string $url               URL where parameters have to be added.
	 * @param int    $energieausweis_id ID of energy certificate.
	 *
	 * @return string $url               URL with needed parameters.
	 *
	 * @since 1.0.0
	 */
	public function add_iframe_params($url, $energieausweis_id = null)
	{
		$args = array(
			'iframe_token' => $this->data()->general->get_token(),
		);

		// If there is an existing energy certificate, get slug and access token.
		if (!empty($energieausweis_id)) {
			$post = get_post($energieausweis_id);

			$args['access_token'] = (new Energieausweis($energieausweis_id))->get_access_token();
			$args['slug']         = $post->post_name;
		}

		return add_query_arg($args, $url);
	}

	public function get_iframe_js()
	{
		global $wp;
		$page = $wp->request;

		$post = get_post();
		if( $post->post_type === 'download') {
			$ec = new Energieausweis($post->ID);
			$type = $ec->get_type();
		}elseif( $page === 'energieausweis2/bedarfsausweis-wohngebaeude' ) {
			$type = 'bw';
		}elseif( $page === 'energieausweis2/verbrauchsausweis-wohngebaeude' ) {
			$type = 'vw';
		}

		$js = 'jQuery( document ).ready( function ( $ ) {
			let get_document_height = function () {
			  var body = document.body,
				  html = document.documentElement;
		  
			  var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
			  return height;
			}
		  
			let get_wrapper_height = function() {
			   return $(".wrapper").height() + 100;
			}
		  
			let send_document_height = function () {
			  var height = get_wrapper_height();
			  console.log( \'sending height: \' + height );
			  parent.postMessage( JSON.stringify( {\'frame_height\': height } ), \'*\' );
			}
		  
			send_document_height();
		  
			$( document ).on(\'wpenon.update_active_tab\', function (e) {
			   setTimeout( function(){ send_document_height(); }, 100 );
			});
		  
			$( document ).on(\'edd_gateway_loaded\', function (e) {
			   setTimeout( function(){ send_document_height(); }, 100 );
			});
		  });
		';

		return $js;
	}
}
