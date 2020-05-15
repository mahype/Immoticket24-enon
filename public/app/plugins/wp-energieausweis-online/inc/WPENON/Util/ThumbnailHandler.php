<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class ThumbnailHandler {
	public static function set( $energieausweis, $thumbnail_id ) {
		$old_thumbnail_id = 0;
		if ( has_post_thumbnail( $energieausweis->id ) ) {
			$old_thumbnail_id = get_post_thumbnail_id( $energieausweis->id );
		}

		if ( $thumbnail_id != $old_thumbnail_id ) {
			$changed = false;
			if ( $thumbnail_id > 0 && get_post_type( $thumbnail_id ) == 'attachment' ) {
				$changed = true;
				set_post_thumbnail( $energieausweis->id, $thumbnail_id );
				wp_update_post( array(
					'ID'          => $thumbnail_id,
					'post_title'  => get_the_title( $energieausweis->id ),
					'post_parent' => $energieausweis->id,
				) );
			} elseif ( $thumbnail_id == 0 && $old_thumbnail_id > 0 ) {
				$changed = true;
				delete_post_thumbnail( $energieausweis->id );
			}

			if ( $changed && $old_thumbnail_id > 0 ) {
				self::delete( $old_thumbnail_id );
			}
		}
	}

	public static function get( $energieausweis ) {
		if ( has_post_thumbnail( $energieausweis->id ) ) {
			return get_post_thumbnail_id( $energieausweis->id );
		}

		return false;
	}

	public static function getImageURL( $attachment_id, $size = 'thumbnail' ) {
		$image = wp_get_attachment_image_src( $attachment_id, $size, false );
		if ( $image && is_array( $image ) ) {
			$image = $image[0];

			return $image;
		}

		return $image;
	}

	public static function getImagePath( $attachment_id, $size = 'thumbnail' ) {
		$image = self::getImageURL( $attachment_id, $size );
		if ( ! empty( $image ) ) {
			if ( function_exists( 'get_imagify_backup_dir_path' ) && 'full' === $size ) {
				$upload_dir = wp_upload_dir();
				$backup_dir = get_imagify_backup_dir_path();

				$file = str_replace( $upload_dir['baseurl'], $backup_dir, $image );
				if ( file_exists( $file ) ) {
					return $file;
				}
			}

			return self::urlToPath( $image );
		}

		return $image;
	}

	public static function urlToPath( $url ) {
		$upload_dir = wp_upload_dir();

		return str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $url );
	}

	public static function pathToUrl( $path ) {
		$upload_dir = wp_upload_dir();

		return str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $path );
	}

	public static function upload( $field_slug ) {
		if ( isset( $_FILES[ $field_slug ] ) ) {
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			if ( ! function_exists( 'wp_read_image_metadata' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/image.php' );
			}
			if ( ! function_exists( 'media_handle_upload' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/media.php' );
			}


			if(!empty($_POST['energieausweis_id'])){
				array( 'error' => __( 'Energieausweis-ID fehlt', 'wpenon' ) );
			}

			$post_array = array(
				'post_title'  => __( 'Temporäres Energieausweis-Bild', 'wpenon' ),
				'post_name'  => "temporaeres-energieausweis-bild-" . $_POST['energieausweis_id'],
				'post_status' => 'wpenon-thumbnail',
			);

			$overrides = array(
				'test_form' => false,
				'test_type' => true,
				'mimes'     => array(
					'jpg'  => 'image/jpeg',
					'jpe'  => 'image/jpeg',
					'jpeg' => 'image/jpeg',
					'png'  => 'image/png',
				),
			);

			add_filter( 'wp_unique_filename', function($filename, $ext){
				$filename = substr(md5($filename . $_POST['energieausweis_id']), 0 ,12) . $ext;
				return $filename;
			}, 10, 2 );

			$id = media_handle_upload( $field_slug, 0, $post_array, $overrides );

			if ( is_a( $id, '\WP_Error' ) ) {
				return array( 'error' => sprintf( __( 'Beim Upload ist folgender Fehler aufgetreten: %s', 'wpenon' ), $id->get_error_message() ) );
			} elseif ( $id < 1 ) {
				return array( 'error' => __( 'Beim Upload ist ein unbekannter Fehler aufgetreten.', 'wpenon' ) );
			}


			set_post_thumbnail(get_post($_POST['energieausweis_id']), $id);
			return $id;
		}

		return array( 'error' => __( 'Es wurde keine Datei zum Upload angegeben.', 'wpenon' ) );
	}

	public static function delete( $attachment_id ) {
		wp_delete_attachment( $attachment_id, true );
	}

	public static function _registerStatus() {
		register_post_status( 'wpenon-thumbnail', array(
			'label'                     => __( 'Energieausweis-Thumbnail', 'wpenon' ),
			'public'                    => false,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Energieausweis-Thumbnail (%s)', 'Energieausweis-Thumbnails (%s)', 'wpenon' ),
		) );
	}
}
