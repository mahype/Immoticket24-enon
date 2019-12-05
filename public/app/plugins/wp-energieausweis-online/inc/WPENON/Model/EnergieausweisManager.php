<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

use Enon\Enon\Standard;
use Enon\Enon\Standards;

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
		$energieausweis = edd_get_download_by($field, $value);

		return self::_postToEnergieausweis($energieausweis);
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
		$energieausweis = self::_getPost($energieausweis);

		return self::_postToEnergieausweis($energieausweis);
	}

	public function getReferenceDate( $format = 'Y-m-d', $energieausweis = null )
	{
		$date = '';
		if ( $energieausweis !== null && is_a($energieausweis, '\WPENON\Model\Energieausweis') ) {
			$date = $energieausweis->ausstellungsdatum;
		} else {
			$post = self::_getPost();
			if ( is_object($post) && in_array($post->post_type, self::$post_types) ) {
				$date = get_post_meta($post->ID, 'ausstellungsdatum', true);
			}
		}

		if ( !empty($date) ) {
			if ( $format == 'timestamp' ) {
				return strtotime($date);
			}

			return date($format, strtotime($date));
		}

		return current_time($format);
	}

	public function getExpirationDate( $format = 'Y-m-d', $energieausweis = null )
	{
		$date = $this->getReferenceDate('timestamp', $energieausweis);
		$date = strtotime('+10 years', $date);
		$date = strtotime('-1 days', $date);

		if ( $format == 'timestamp' ) {
			return $date;
		}

		return date($format, $date);
	}

	public function getStandardDate( $format = 'Y-m-d', $energieausweis = null )
	{
		$standard_dates = self::getAvailableStandards('dates');
		$standard = '';
		if ( $energieausweis !== null ) {
			if ( is_a($energieausweis, '\WPENON\Model\Energieausweis') ) {
				$standard = $energieausweis->wpenon_standard;
			} elseif ( is_string($energieausweis) ) {
				$standard = $energieausweis;
			}
		} else {
			$post = self::_getPost();
			if ( is_object($post) && in_array($post->post_type, self::$post_types) ) {
				$standard = get_post_meta($post->ID, 'wpenon_standard', true);
			}
		}

		$date = '';
		if ( isset($standard_dates[ $standard ]) ) {
			$date = $standard_dates[ $standard ];
		} else {
			$date = array_shift($standard_dates);
		}

		if ( $format == 'timestamp' ) {
			return strtotime($date);
		}

		return date($format, strtotime($date));
	}

	public function getCreatePage()
	{
		if ( is_singular('page') ) {
			$post = self::_getPost();
			$settings = \WPENON\Util\Settings::instance();

			$types = self::getAvailableTypes();
			foreach ( $types as $slug => $title ) {
				$setting_name = 'new_' . $slug . '_page';
				if ( $post->ID === absint($settings->$setting_name) ) {
					return $slug;
				}
			}
		}

		return false;
	}

	public function create( $type, $standard = '', $custom_meta = array() )
	{
		$types = self::getAvailableTypes();

		if ( !isset($types[ $type ]) ) {
			new \WPENON\Util\Error('notice', __METHOD__, __('Ungültiger Typ für Energieausweis angegeben.', 'wpenon'), '1.0.0');
			return null;
		}

		if ( empty($standard) ) {
			$standard = ( new Standards() )->getCurrentStandard();
		} else {
			$standard = new Standard($standard, ( new Standards() ));
		}

		if ( empty($standard) ) {
			new \WPENON\Util\Error('notice', __METHOD__, __('Ungültiger Standard für Energieausweis angegeben.', 'wpenon'), '1.0.0');
			return null;
		}

		$args = array(
			'post_type' => self::$post_types[ 0 ],
			'post_status' => 'publish',
			'post_title' => self::_generateTitle(null, false),
			'post_content' => '',
		);

		$energieausweis = wp_insert_post($args);

		if ( !is_numeric($energieausweis) ) {
			new \WPENON\Util\Error('notice', __METHOD__, __('Der Energieausweis konnte nicht erzeugt werden.', 'wpenon'), '1.0.0');
			return null;
		}

		update_post_meta($energieausweis, 'wpenon_type', $type);
		update_post_meta($energieausweis, 'wpenon_standard', $standard);

		$energieausweis = self::_postToEnergieausweis($energieausweis);

		$meta = array(
			'ausstellungsdatum' => '',
			'registriernummer' => '',
			'wpenon_secret' => md5(microtime()),
		);
		$meta = array_merge($meta, $custom_meta);
		foreach ( $meta as $key => $value ) {
			$energieausweis->$key = $value;
		}

		do_action('wpenon_energieausweis_create', $energieausweis);

		return $energieausweis;
	}

	public static function getAvailableTypes()
	{
		$types = array();
		if ( WPENON_BW ) {
			$types[ 'bw' ] = __('Bedarfsausweis für Wohngebäude', 'wpenon');
		}
		if ( WPENON_BN ) {
			$types[ 'bn' ] = __('Bedarfsausweis für Nichtwohngebäude', 'wpenon');
		}
		if ( WPENON_VW ) {
			$types[ 'vw' ] = __('Verbrauchsausweis für Wohngebäude', 'wpenon');
		}
		if ( WPENON_VN ) {
			$types[ 'vn' ] = __('Verbrauchsausweis für Nichtwohngebäude', 'wpenon');
		}

		return $types;
	}

	public static function getAvailableStandards( $mode = 'names' )
	{
		$_standards = unserialize(WPENON_STANDARDS);

		$index = 0;
		if ( $mode == 'dates' ) {
			$index = 1;
		} elseif ( $mode === 'startdates' ) {
			$index = 2;
		}
		$standards = array();
		foreach ( $_standards as $key => $value ) {
			$standards[ $key ] = $value[ $index ];
		}

		return $standards;
	}

	public static function getAvailableClasses( $type )
	{
		$classes = array();
		if ( substr($type, 1, 1) == 'w' ) {
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
				'label' => __('Ihre Email-Adresse', 'wpenon'),
				'required' => true,
				'set_once' => true,
			),
			'wpenon_email2' => array(
				'type' => 'email',
				'label' => __('Email wiederholen', 'wpenon'),
				'required' => true,
				'set_once' => true,
				'validate' => array( __CLASS__, 'validate_email_2' )
			),
			'wpenon_secret' => array(
				'type' => 'text',
				'label' => __('Zugriffsschlüssel', 'wpenon'),
				'required' => true,
			),
			'wpenon_type' => array(
				'type' => 'select',
				'label' => __('Energieausweis-Typ', 'wpenon'),
				'options' => $types,
				'default' => !empty($type) ? $type : key($types),
				'required' => true,
			),
			'wpenon_standard' => array(
				'type' => 'select',
				'label' => __('EnEV: Standard', 'wpenon'),
				'options' => $standards,
				'default' => !empty($standard) ? $standard : key($standards),
				'required' => true,
			),
			'ausstellungsdatum' => array(
				'type' => 'date',
				'label' => __('Ausstellungsdatum', 'wpenon'),
				'default' => '',
			),
			'ausstellungszeit' => array(
				'type' => 'time',
				'label' => __('Ausstellungszeit', 'wpenon'),
				'default' => '',
			),
			'registriernummer' => array(
				'type' => 'text',
				'label' => __('Registriernummer', 'wpenon'),
				'column' => true,
			),
			'adresse_headline' => array(
				'type' => 'headline',
				'label' => __('Adresse des Gebäudes', 'wpenon'),
				'description' => __('Machen Sie hier Angaben zum Gebäude, für das Sie den Energieausweis erstellen möchten.', 'wpenon'),
				'set_once' => true,
			),
			'adresse_strassenr' => array(
				'type' => 'text',
				'label' => __('Straße und Hausnummer', 'wpenon'),
				'required' => true,
				'set_once' => true,
			),
			'adresse_plz' => array(
				'type' => 'zip',
				'label' => __('Postleitzahl', 'wpenon'),
				'required' => true,
				'set_once' => true,
			),
			'adresse_ort' => array(
				'type' => 'text',
				'label' => __('Ort', 'wpenon'),
				'value' => array(
					'callback' => 'wpenon_get_location_by_plz',
					'callback_args' => array( 'field::adresse_plz', 'ort' ),
				),
				'required' => true,
				'set_once' => true,
			),
			'adresse_bundesland' => array(
				'type' => 'text',
				'label' => __('Bundesland', 'wpenon'),
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
				if ( isset($field[ 'set_once' ]) && $field[ 'set_once' ] ) {
					unset($field[ 'set_once' ]);
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
	 * @return \WPENON\Model\Schema Schema
	 */
	public static function loadSchema( $type, $standard )
	{
		$schema = array();

		$types = self::getAvailableTypes();
		if ( !empty($type) && isset($types[ $type ]) ) {
			$type = array( $type );
			$types = array_intersect_key($types, array_flip($type));
		}
		$types = array_keys($types);

		$standards = self::getAvailableStandards();
		if ( empty($standard) || !isset($standards[ $standard ]) ) {
			$standard = key($standards);
		}

		foreach ( $types as $type ) {
			$schema_file = WPENON_DATA_PATH . '/' . $standard . '/schema/' . $type . '.php';
			$schema_file = apply_filters('wpenon_schema_file', $schema_file, $standard, $type);

			if ( file_exists($schema_file) ) {
				$data = require $schema_file;
				$schema = array_merge_recursive($schema, $data);
			} else {
				new \WPENON\Util\Error('fatal', __METHOD__, sprintf(__('Die geforderte Schema-Datei %s existiert nicht.', 'wpenon'), '<code>' . $schema_file . '</code>'), '1.0.0');
			}
		}

		$private_fields = array(
			'private' => array(
				'title' => __('Energieausweis-Metadaten', 'wpenon'),
				'groups' => array(
					'basisdaten' => array(
						'title' => __('Energieausweis-Metadaten', 'wpenon'),
						'fields' => self::getPrivateFields($type, $standard),
					),
				),
			),
		);
		$schema = array_merge_recursive($schema, $private_fields);

		return new \WPENON\Model\Schema($schema);
	}

	public static function loadCalculations( $energieausweis )
	{
		$calculations = array();

		$type = $energieausweis->wpenon_type;
		$standard = $energieausweis->wpenon_standard;

		$types = self::getAvailableTypes();
		if ( empty($type) || !isset($types[ $type ]) ) {
			$type = key($types);
		}
		unset($types);

		$standards = self::getAvailableStandards();
		if ( empty($standard) || !isset($standards[ $standard ]) ) {
			$standard = key($standards);
		}
		unset($standards);

		if ( file_exists(WPENON_DATA_PATH . '/' . $standard . '/calculations/' . $type . '.php') ) {
			$calculations = require WPENON_DATA_PATH . '/' . $standard . '/calculations/' . $type . '.php';
		} else {
			new \WPENON\Util\Error('fatal', __METHOD__, sprintf(__('Die geforderte Berechnungs-Datei %s existiert nicht.', 'wpenon'), '<code>' . '/' . $standard . '/schema/' . $type . '.php' . '</code>'), '1.0.0');
		}

		return $calculations;
	}

	public static function loadMappings( $mode, $standard )
	{
		$funcmode = str_replace(array( '-', ' ' ), '_', $mode);

		if ( !function_exists('wpenon_get_' . $standard . '_' . $funcmode . '_data') ) {
			if ( file_exists(WPENON_DATA_PATH . '/' . $standard . '/' . $mode . '-mappings.php') ) {
				require_once WPENON_DATA_PATH . '/' . $standard . '/' . $mode . '-mappings.php';
				if ( !function_exists('wpenon_get_' . $standard . '_' . $funcmode . '_data') ) {
					new \WPENON\Util\Error('fatal', __METHOD__, sprintf(__('Die geforderte Mapping-Methode %s konnte nicht gefunden werden.', 'wpenon'), '<code>' . 'wpenon_get_' . $standard . '_' . $mode . '_data' . '</code>'), '1.0.0');
				}
			} else {
				new \WPENON\Util\Error('fatal', __METHOD__, sprintf(__('Die geforderte Mapping-Datei %s existiert nicht.', 'wpenon'), '<code>' . '/' . $standard . '/' . $mode . '-mappings.php' . '</code>'), '1.0.0');
			}
		}
	}

	public static function findXSDFile( $mode, $standard )
	{
		$mode = str_replace(array( 'xml-', 'xml_' ), '', $mode);

		$file = WPENON_DATA_PATH . '/' . $standard . '/' . $mode . '.xsd';
		if ( !file_exists($file) ) {
			$file = str_replace('/' . $standard, '', $file);
			if ( !file_exists($file) ) {
				new \WPENON\Util\Error('fatal', __METHOD__, sprintf(__('Die geforderte XSD-Datei %s existiert nicht.', 'wpenon'), '<code>' . '/' . $standard . '/' . $mode . '.xsd' . '</code>'), '1.0.0');
			}
		}

		return $file;
	}

	public static function getVerifiedPermalink( $post_id, $action = '' )
	{
		if ( get_post_type($post_id) !== 'download' ) {
			return '';
		}

		$query_args = array(
			'access_token' => md5(get_post_meta($post_id, 'wpenon_email', true)) . '-' . get_post_meta($post_id, 'wpenon_secret', true),
		);
		if ( !empty($action) ) {
			$query_args[ 'action' ] = $action;
		}

		return add_query_arg($query_args, get_permalink($post_id));
	}

	public static function _getPost( $post = null )
	{
		if ( $post === null && is_admin() ) {
			if ( isset($_GET[ 'post' ]) ) {
				$post = absint($_GET[ 'post' ]);
			}
		}

		return get_post($post);
	}

	public static function _postToEnergieausweis( $post )
	{
		if ( is_numeric($post) ) {
			$post = get_post($post);
		}
		if ( is_a($post, '\WP_Post') && in_array($post->post_type, self::$post_types) ) {
			if ( !isset(self::$energieausweise[ $post->ID ]) ) {
				self::$energieausweise[ $post->ID ] = new \WPENON\Model\Energieausweis($post->ID);
			}

			return self::$energieausweise[ $post->ID ];
		}

		return null;
	}

	public static function _generateTitle( $id = null, $after_publish = false )
	{
		return \WPENON\Util\Format::generateTitle(WPENON_ENERGIEAUSWEIS_TITLE_STRUCTURE, self::$post_types, $id, $after_publish);
	}
}
