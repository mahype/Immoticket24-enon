<?php

use setasign\Fpdi\Fpdi;

if( ! defined( 'ABSPATH' ) ) exit;

add_action( 'rest_api_init', 'wpenon_register_rest_api_endpoint_image_delete' );

function wpenon_register_rest_api_endpoint_image_upload() {
	register_rest_route( 'ec', '/image_upload/', array(
		'methods' => 'POST',
		'callback' => 'wpenon_image_upload',
		'permission_callback' => '__return_true'
	) );
}

add_action( 'rest_api_init', 'wpenon_register_rest_api_endpoint_image_upload' );

function wpenon_register_rest_api_endpoint_image_delete() {
	register_rest_route( 'ec', '/image_delete/', array(
		'methods' => 'POST',
		'callback' => 'wpenon_image_delete',
		'permission_callback' => '__return_true'
	) );
}

add_action( 'rest_api_init', 'wpenon_register_rest_api_endpoint_image_delete' );

function wpenon_image_delete( \WP_REST_Request $request )
{
	$ecId  = $request->get_param( 'ecId' );
	$field = $request->get_param( 'field' );

	$postMetaUrl      = $field. '_image';
	$postMetaFileName = $field. '_image_file';

	$fileName = get_post_meta( $ecId, $postMetaFileName, true );
	@unlink( $fileName );

	delete_post_meta( $ecId, $postMetaUrl );
	delete_post_meta( $ecId, $postMetaFileName );
}

/**
 * AJAX uploads in frontend
 */
function wpenon_image_upload( \WP_REST_Request $request ) {
    require_once( ABSPATH . 'wp-admin/includes/file.php');

	$ecId  = $request->get_param( 'ecId' );
	$field = $request->get_param( 'field' );
	$postMetaName = $field. '_image';

	$file  = $request->get_file_params();
	$oldFile = get_post_meta( $ecId, $postMetaName, true );

	if( $file['file']['size'] === 0 ) {
		echo json_encode( ['error' => 'Datei hat keinen Inhalt'] );
		exit;
	}

	if( $file['file']['type'] !== 'image/png' &&  $file['file']['type'] !== 'image/jpeg'  ) {		
		echo json_encode( ['error' => 'Falscher Dateityp'] );
		exit;
	}

	$type = $file['file']['type'] === 'image/jpeg' ? 'jpg': 'png' ;

	try {
		$pdf = new Fpdi();
		$pdf->AddPage();
		$pdf->Image($file['file']['tmp_name'], null, null, 0, 0, $type );
	}catch(Exception $e){
		echo json_encode( ['error' => 'Bild kann nicht gelesen werden. Bitte laden Sie das Bild in einem anderen Format hoch.' ] );
		exit;
	}

	$uploadedFile = wp_handle_upload( $file['file'], [ 'test_form' => FALSE ] );
	
	if( ! isset( $uploadedFile['file'] ) ) {		
		echo json_encode( ['error' => 'Datei konnte nicht verschoben werden'] );
		exit;
	}
	
	$imageEditor = wp_get_image_editor( $uploadedFile['file'] );	
	if( is_wp_error( $imageEditor ) ) {		
		echo json_encode( ['error' => $imageEditor->get_error_message() ] );
		exit;
	}

	$imageEditor->maybe_exif_rotate();
	$setQuality = $imageEditor->set_quality( 35 );

	$size = $imageEditor->get_size();
	if( $size[0] > 800 || $size[1] > 800 || ( isset( $size['width'] ) && $size['width'] > 800 ) || ( isset( $size['height'] ) && $size['width'] > 800 ) )
	{
		$resized = $imageEditor->resize( 800, 800 );	
	}	

	if( is_wp_error( $resized ) ) {
		echo json_encode( ['error' => $resized->get_error_message() ] );
		exit;
	}

    $resizedFile = $imageEditor->save();

	if( is_wp_error( $resizedFile ) ) {
		echo json_encode( ['error' => $resizedFile->get_error_message() ] );
		exit;
	}

	$upload_dir = wp_upload_dir();

	$randInt = random_int ( PHP_INT_MIN, PHP_INT_MAX );
	$prefix = $field . '_' . md5( $randInt );
	$suffix = $file['file']['type'] === 'image/jpeg' ? 'jpg': 'png' ;
		
	$filename = $prefix . '.' . $suffix;
		
	rename( $resizedFile['path'], trailingslashit( $upload_dir['path'] ) . $filename );	
	unlink( $uploadedFile['file']);
	
	$fileUrl =  trailingslashit( $upload_dir['url'] ) . basename( $filename );

	if( 'undefined' == $fileUrl || 'undefined' == trailingslashit( $upload_dir['path'] ) ) {
		echo json_encode( ['error' => 'Probleme bei Dateiupload, bitte laden Sie eine andere Datei hoch.' ] );
		exit;
	}

	update_post_meta( $ecId, $postMetaName . '_file', trailingslashit( $upload_dir['path'] ) . $filename  );
	update_post_meta( $ecId, $postMetaName, $fileUrl );
	update_post_meta( $ecId, $field, $fileUrl );

	if( ! empty( $oldFile ) && file_exists( $oldFile ) ) {
		@unlink( $oldFile );
	}

	echo json_encode( ['url' => $fileUrl ] );
	exit;
}