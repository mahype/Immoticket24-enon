<?php
/**
 * User_Detector object.
 *
 * @category Class
 * @package  Enon_Reseller\Detector
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models;

/**
 * Class Reseller
 *
 * @package Enon_Reseller
 *
 * @since 1.0.0
 */
class Detector {
    /**
     * Check if it is iframe.
     * 
     * @return bool True if is iframe, false if not.
     * 
     * @since 1.0.0
     */
    public static function is_reseller_iframe() {
        if ( ! isset ( $_GET['iframe_token' ] ) ) {
            return false;
        }

        return true;
    }

    public static function is_reseller_ec_page() {
        $post = get_post();

        if ( empty( $post ) ) {
            return false;
        }

        if ( ! is_object( $post ) ) {
            return false;
        }

        if ( 'WP_Post' !== get_class( $post ) ) {
            return false;
        }

        if ( $post->post_type !== 'download' ) {
            return false;
        }

        $reseller_id = get_post_meta( $post->ID, 'reseller_id', true );

        if ( empty( $reseller_id ) ) {
            return false;
        }        

        return true;
    }

    /**
     * Get iframe token.
     * 
     * @return bool|int Iframe token if exits, false if not.
     * 
     * @since 1.0.0
     */
    public static function get_iframe_token() {
        if ( ! self::is_reseller_iframe() ) {
            return false;
        }

        return $_GET['iframe_token' ];
    }

    /**
     * Get reseller id by page (Energy certificate form page).
     * 
     * @return Reseller|bool Reseller object, false if not.
     * 
     * @since 1.0.0
     */
    public static function get_reseller_by_page () {
        if ( ! self::is_reseller_ec_page() ) {
            return false;
        }

        $post        = get_post();
        $reseller_id = get_post_meta( $post->ID, 'reseller_id', true );

        if ( empty( $reseller_id ) ) {
            return false;
        }

        return new Reseller( $reseller_id );
    }

    /**
     * Get reseller id by iframe.
     * 
     * @return Reseller|bool Reseller object if found, false if not.
     * 
     * @since 1.0.0
     */
    public static function get_reseller_by_iframe () {
        $iframe_token = self::get_iframe_token();

        if ( ! empty( $iframe_token ) ) {
            $reseller_id = self::get_reseller_id_by_iframe_token( $iframe_token );

            if ( ! empty( $reseller_id ) ) {
                return new Reseller( $reseller_id );
            }
        }

        return false;
    }

    /**
     * Get reseller id by iframe token.
     * 
     * @param string $iframe_token Iframe token.
     * 
     * @return Reseller|bool Reseller object if found, false if not.
     * 
     * @since 1.0.0
     */
    private static function get_reseller_id_by_iframe_token( string $iframe_token ) {
        $args = array(
			'post_type'  => 'reseller',
			'meta_query' => array(
				array(
					'key'   => 'token',
					'value' => $iframe_token,
				),
			),
		);

		$resellers = get_posts( $args );

		foreach ( $resellers as $reseller ) {
			return $reseller->ID; 
		}

		return false;
    }
}