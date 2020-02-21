<?php
/**
 * Ekomi service class.
 *
 * @category Class
 * @package  Enon\Models\Badges
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Badges;

/**
 * Class Ekomi_Service.
 *
 * @package Enon\Models\Badges
 *
 * @since 1.0.0
 */
class Ekomi_Service {

	/**
	 * Rating.
	 *
	 * @var float Rating value.
	 *
	 * @since 1.0.0
	 */
	private $rating;

	/**
	 * Rating count.
	 *
	 * @var int Rating count.
	 *
	 * @since 1.0.0
	 */
	private $rating_count;

	/**
	 * Ekomi_Service constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init_rating();
	}

	/**
	 * Initializing data for rating.
	 *
	 * @return bool True if data has been loaded.
	 *
	 * @since 1.0.0
	 */
	private function init_rating() {
		$rating       = get_transient( 'it-ekomi-aggregate-rating' );
		$rating_count = get_transient( 'it-ekomi-rating-count' );

		if ( false === $rating || false === $rating_count ) {
			$rating_updated = $this->request_rating();

			$rating       = $rating_updated['rating'];
			$rating_count = $rating_updated['rating_count'];

			set_transient( 'it-ekomi-aggregate-rating', $rating, DAY_IN_SECONDS );
			set_transient( 'it-ekomi-rating-count', $rating_count, DAY_IN_SECONDS );
		} else {
			$rating = floatval( $rating );
			$rating_count = intval( $rating_count );
		}

		$this->rating = $rating;
		$this->rating_count = $rating_count;

		return true;
	}

	/**
	 * Requesting data on ekomi api.
	 *
	 * @return array|bool Requested values for rating and rating_count or false if failed.
	 *
	 * @since 1.0.0
	 */
	private function request_rating() {
		$rating = 0.0;
		$rating_count = 0;

		$url = add_query_arg( array(
			'auth'          => '81266|bcee523b2de16d3165bc9f8d6',
			'type'          => 'json',
		), 'https://api.ekomi.de/v3/getFeedback' );

		$response = wp_remote_get( $url );

		if ( ! is_wp_error( $response ) ) {
			$reviews = json_decode( wp_remote_retrieve_body( $response ) );
			foreach ( $reviews as $review ) {
				$rating += floatval( $review->rating );
				$rating_count++;
			}
			$rating /= floatval( $rating_count );
		}

		$requested_values = array(
			'rating'       => $rating,
			'rating_count' => $rating_count,
		);

		return $requested_values;
	}

	/**
	 * Get rating.
	 *
	 * @return string Rating value.
	 *
	 * @since 1.0.0
	 */
	public function get_rating() {
		return $this->rating;
	}

	/**
	 * Get rating formatted (with two decimals).
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function get_rating_formatted() {
		return number_format( $this->get_rating(), 2 );
	}

	/**
	 * Get rating count.
	 *
	 * @return int Rating count.
	 *
	 * @since 1.0.0
	 */
	public function get_rating_count() {
		return $this->rating_count;
	}

	/**
	 * Get rating stars.
	 *
	 * @return string Rating stars as HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_rating_stars() {
		$rating = $this->get_rating();
		$star_count = 5;

		$full_count = round( $rating );
		$half_count = ( (float) $rating - (float) $full_count ) > 0.5 ? 1 : 0;
		$empty_count = $star_count - $full_count - $half_count;

		$html = '<div class="stars">';

		for ( $i = 0; $i < $full_count; $i++ ) {
			$html .= '<div class="star-full"></div>';
		}
		for ( $i = 0; $i < $half_count; $i++ ) {
			$html .= '<div class="star-half"></div>';
		}
		for ( $i = 0; $i < $empty_count; $i++ ) {
			$html .= '<div class="star-empty"></div>';
		}

		$html .= '</div>';

		return $html;
	}
}
