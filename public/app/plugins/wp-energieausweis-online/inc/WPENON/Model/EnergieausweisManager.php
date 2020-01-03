<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

use Enon\Enon\Standards\Mapping;
use Enon\Enon\Standards\XSD;
use Enon\Enon\StandardsConfig;
use Enon\Enon\TypesConfig;
use Enon\Enon\Standards\Calculation;
use Enon\Enon\Standards\Schema;


class EnergieausweisManager
{
	private static $instance;

	public static function instance()
	{
		if ( self::$instance === null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private static $post_types = array( 'download' );

	private static $energieausweise = array();

	private $available_types = array();

	private function __construct()
	{
	}

	public static function getEnergieausweisBy( $field = '', $value = '' )
	{
		$energieausweis = edd_get_download_by( $field, $value );

		return self::_postToEnergieausweis( $energieausweis );
	}

	/**
	 * Get Energieausweis
	 *
	 * @param null $energieausweis
	 *
	 * @return \WPENON\Model|Energieausweis Energieausweis object.
	 */
	public static function getEnergieausweis( $energieausweis = null )
	{
		$energieausweis = self::_getPost( $energieausweis );

		return self::_postToEnergieausweis( $energieausweis );
	}

	public function getReferenceDate( $format = 'Y-m-d', $energieausweis = null )
	{
		$date = '';
		if ( $energieausweis !== null && is_a( $energieausweis, '\WPENON\Model\Energieausweis' ) ) {
			$date = $energieausweis->ausstellungsdatum;
		} else {
			$post = self::_getPost();
			if ( is_object( $post ) && in_array( $post->post_type, self::$post_types ) ) {
				$date = get_post_meta( $post->ID, 'ausstellungsdatum', true );
			}
		}

		if ( !empty( $date ) ) {
			if ( $format == 'timestamp' ) {
				return strtotime( $date );
			}

			return date( $format, strtotime( $date ) );
		}

		return current_time( $format );
	}

	public function getExpirationDate( $format = 'Y-m-d', $energieausweis = null )
	{
		$date = $this->getReferenceDate( 'timestamp', $energieausweis );
		$date = strtotime( '+10 years', $date );
		$date = strtotime( '-1 days', $date );

		if ( $format == 'timestamp' ) {
			return $date;
		}

		return date( $format, $date );
	}

	public function getCreatePage()
	{
		if ( is_singular( 'page' ) ) {
			$post = self::_getPost();
			$settings = \WPENON\Util\Settings::instance();

			$types = self::getAvailableTypes();
			foreach ( $types as $key => $title ) {
				$setting_name = 'new_' . $key . '_page';
				if ( $post->ID === absint( $settings->$setting_name ) ) {
					return $key;
				}
			}
		}

		return false;
	}

	public function create( $type, $standard = '', $custom_meta = array() )
	{
		$standardsConfig = new StandardsConfig();
		$typesConfig = new TypesConfig();

		if ( empty( $type ) ) {
			new \WPENON\Util\Error( 'fatal', __METHOD__, __( 'Type must not be empty.', 'wpenon' ), '1.0.0' );
			return null;
		}

		if ( ! $typesConfig->keyExists( $type ) ) {
			new \WPENON\Util\Error( 'notice', __METHOD__, __( 'Ungültiger Typ für Energieausweis angegeben.', 'wpenon' ), '1.0.0' );
			return null;
		}

		if ( ! empty( $standard ) && ! $standardsConfig->keyExists( $standard ) ) {
			new \WPENON\Util\Error( 'notice', __METHOD__, __( 'Ungültiger Standard für Energieausweis angegeben.', 'wpenon' ), '1.0.0' );
			return null;
		}

		if ( empty( $standard ) ) {
			$standard = $standardsConfig->getCurrent();
		}

		$args = array(
			'post_type' => self::$post_types[ 0 ],
			'post_status' => 'publish',
			'post_title' => self::_generateTitle( null, false ),
			'post_content' => '',
		);

		$energieausweis_id = wp_insert_post( $args );

		if ( !is_numeric( $energieausweis_id ) ) {
			new \WPENON\Util\Error( 'notice', __METHOD__, __( 'Der Energieausweis konnte nicht erzeugt werden.', 'wpenon' ), '1.0.0' );
			return null;
		}

		update_post_meta( $energieausweis_id, 'wpenon_type', $type );
		update_post_meta( $energieausweis_id, 'wpenon_standard', $standard );

		$energieausweis = self::_postToEnergieausweis( $energieausweis_id );

		$meta = array(
			'ausstellungsdatum' => '',
			'registriernummer' => '',
			'wpenon_secret' => md5( microtime() ),
		);
		$meta = array_merge( $meta, $custom_meta );
		foreach ( $meta as $key => $value ) {
			$energieausweis->$key = $value;
		}

		do_action( 'wpenon_energieausweis_create', $energieausweis, $type, $standard );

		return $energieausweis;
	}

	public static function getAvailableTypes()
	{
		return (new TypesConfig())->get();
	}

	public static function getAvailableStandards()
	{
		$standardValues = (new StandardsConfig())->get();

		$standards = array();
		foreach( $standardValues AS $key => $standardValue ) {
			$standards[$key] = $standardValue['name'];
		}

		return $standards;
	}

	public static function getAvailableClasses( $type )
	{
		$classes = array();
		if ( substr( $type, 1, 1 ) == 'w' ) {
			$classes = array(
				'A+' => 30,
				'A' => 50,
				'B' => 75,
				'C' => 100,
				'D' => 130,
				'E' => 160,
				'F' => 200,
				'G' => 250,
				'H' => 250000,
			);
		}

		return $classes;
	}

	public static function validate_email_2( $value, $field )
	{
		$result = array();
		$result[ 'value' ] = $value;

		if ( $value !== $_POST[ 'wpenon_email' ] ) {
			$result[ 'error' ] = 'Die Email-Adresse stimmt nicht überein.';
		}

		return $result;
	}

	public static function getPrivateFields( $type = '', $standard = '', $set_once = false )
	{
		$types = self::getAvailableTypes();
		$standards = self::getAvailableStandards();

		$private_fields = array(
			'wpenon_email' => array(
				'type' => 'email',
				'label' => __( 'Ihre Email-Adresse', 'wpenon' ),
				'required' => true,
				'set_once' => true,
			),
			'wpenon_email2' => array(
				'type' => 'email',
				'label' => __( 'Email wiederholen', 'wpenon' ),
				'required' => true,
				'set_once' => true,
				'validate' => array( __CLASS__, 'validate_email_2' )
			),
			'wpenon_secret' => array(
				'type' => 'text',
				'label' => __( 'Zugriffsschlüssel', 'wpenon' ),
				'required' => true,
			),
			'wpenon_type' => array(
				'type' => 'select',
				'label' => __( 'Energieausweis-Typ', 'wpenon' ),
				'options' => $types,
				'default' => !empty( $type ) ? $type : key( $types ),
				'required' => true,
			),
			'wpenon_standard' => array(
				'type' => 'select',
				'label' => __( 'EnEV: Standard', 'wpenon' ),
				'options' => $standards,
				'default' => !empty( $standard ) ? $standard : key( $standards ),
				'required' => true,
			),
			'ausstellungsdatum' => array(
				'type' => 'date',
				'label' => __( 'Ausstellungsdatum', 'wpenon' ),
				'default' => '',
			),
			'ausstellungszeit' => array(
				'type' => 'time',
				'label' => __( 'Ausstellungszeit', 'wpenon' ),
				'default' => '',
			),
			'registriernummer' => array(
				'type' => 'text',
				'label' => __( 'Registriernummer', 'wpenon' ),
				'column' => true,
			),
			'adresse_headline' => array(
				'type' => 'headline',
				'label' => __( 'Adresse des Gebäudes', 'wpenon' ),
				'description' => __( 'Machen Sie hier Angaben zum Gebäude, für das Sie den Energieausweis erstellen möchten.', 'wpenon' ),
				'set_once' => true,
			),
			'adresse_strassenr' => array(
				'type' => 'text',
				'label' => __( 'Straße und Hausnummer', 'wpenon' ),
				'required' => true,
				'set_once' => true,
			),
			'adresse_plz' => array(
				'type' => 'zip',
				'label' => __( 'Postleitzahl', 'wpenon' ),
				'required' => true,
				'set_once' => true,
			),
			'adresse_ort' => array(
				'type' => 'text',
				'label' => __( 'Ort', 'wpenon' ),
				'value' => array(
					'callback' => 'wpenon_get_location_by_plz',
					'callback_args' => array( 'field::adresse_plz', 'ort' ),
				),
				'required' => true,
				'set_once' => true,
			),
			'adresse_bundesland' => array(
				'type' => 'text',
				'label' => __( 'Bundesland', 'wpenon' ),
				'value' => array(
					'callback' => 'wpenon_get_location_by_plz',
					'callback_args' => array( 'field::adresse_plz', 'land' ),
					'callback_hard' => true,
				),
				'required' => true,
				'set_once' => true,
			),
		);

		if ( $set_once ) {
			$set_once_fields = array();
			foreach ( $private_fields as $slug => $field ) {
				if ( isset( $field[ 'set_once' ] ) && $field[ 'set_once' ] ) {
					unset( $field[ 'set_once' ] );
					$set_once_fields[ $slug ] = $field;
				}
			}

			return $set_once_fields;
		}

		return $private_fields;
	}

	/**
	 * Loading Schema
	 *
	 * @param $type
	 * @param $standard
	 *
	 * @return \WPENON\Model\Energieausweis Schema
	 */
	public static function loadSchema( $energieausweis )
	{
		$schema = new Schema( $energieausweis->standard );
		$schema = $schema->load( $energieausweis );

		$private_fields = array(
			'private' => array(
				'title' => __( 'Energieausweis-Metadaten', 'wpenon' ),
				'groups' => array(
					'basisdaten' => array(
						'title' => __( 'Energieausweis-Metadaten', 'wpenon' ),
						'fields' => self::getPrivateFields( $energieausweis->type, $energieausweis->standard ),
					),
				),
			),
		);
		$schema = array_merge_recursive( $schema, $private_fields );

		return new \WPENON\Model\Schema( $schema );
	}

	public static function loadCalculations( $energieausweis )
	{
		$calculation = new Calculation( $energieausweis->wpenon_standard );
		$calculations = $calculation->load( $energieausweis->wpenon_type, array( 'energieausweis' => $energieausweis ) );

		return $calculations;
	}

	public static function loadMappings( $mode, $standard )
	{
		$mapping = new Mapping( $standard );
		$mapping->load( $mode );
	}

	public static function getXSDFile( $mode, $standard )
	{
		$xsd = new XSD( $standard );
		$file = $xsd->getFile( $mode );

		return $file;
	}

	public static function getVerifiedPermalink( $post_id, $action = '' )
	{
		if ( get_post_type( $post_id ) !== 'download' ) {
			return '';
		}

		$query_args = array(
			'access_token' => md5( get_post_meta( $post_id, 'wpenon_email', true ) ) . '-' . get_post_meta( $post_id, 'wpenon_secret', true ),
		);
		if ( !empty( $action ) ) {
			$query_args[ 'action' ] = $action;
		}

		return add_query_arg( $query_args, get_permalink( $post_id ) );
	}

	public static function _getPost( $post = null )
	{
		if ( $post === null && is_admin() ) {
			if ( isset( $_GET[ 'post' ] ) ) {
				$post = absint( $_GET[ 'post' ] );
			}
		}

		return get_post( $post );
	}

	public static function _postToEnergieausweis( $post )
	{
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}
		if ( is_a( $post, '\WP_Post' ) && in_array( $post->post_type, self::$post_types ) ) {
			if ( !isset( self::$energieausweise[ $post->ID ] ) ) {
				self::$energieausweise[ $post->ID ] = new \WPENON\Model\Energieausweis( $post->ID );
			}

			return self::$energieausweise[ $post->ID ];
		}

		return null;
	}

	public static function _generateTitle( $id = null, $after_publish = false )
	{
		return \WPENON\Util\Format::generateTitle( WPENON_ENERGIEAUSWEIS_TITLE_STRUCTURE, self::$post_types, $id, $after_publish );
	}
}
