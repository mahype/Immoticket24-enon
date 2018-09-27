<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class Settings {
  private static $instance;

  public static function instance() {
    if ( self::$instance === null ) {
      self::$instance = new self;
    }
    return self::$instance;
  }

  private $settings = array();
  private $fields = array();

  private function __construct() {
    $this->_loadSettings();

    $this->_registerSettings();
    $this->_adjustEDDSettings();
  }

  public function __get( $setting ) {
    if ( in_array( $setting, $this->fields ) ) {
      $setting = \WPENON\Util\Format::prefix( $setting );
    }
    return edd_get_option( $setting );
  }

  private function _loadSettings() {
    $settings = array(
      'general'             => array(
        array(
          'id'                  => 'allow_changes_after_order',
          'name'                => __( 'Energieausweis-Änderungen nach Bestellung zulassen?', 'wpenon' ),
          'type'                => 'checkbox',
        ),
      ),
      'styles'              => array(
        array(
          'id'                => 'firmenname',
          'name'              => __( 'Firmenname', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'firmenlogo',
          'name'              => __( 'Firmenlogo', 'wpenon' ),
          'desc'              => __( 'Ihr Firmenlogo erscheint in Rechnungen und automatisch generierten Emails.', 'wpenon' ),
          'type'              => 'upload',
        ),
        array(
          'id'                => 'inhaber',
          'name'              => __( 'Name des Inhabers', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'strassenr',
          'name'              => __( 'Straße und Nr.', 'wpenon' ),
          'desc'              => __( 'Straße und Hausnummer Ihres Firmensitzes.', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'plz',
          'name'              => __( 'Postleitzahl', 'wpenon' ),
          'desc'              => __( 'Postleitzahl Ihres Firmensitzes.', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'ort',
          'name'              => __( 'Ort', 'wpenon' ),
          'desc'              => __( 'Ort Ihres Firmensitzes.', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'telefon',
          'name'              => __( 'Telefonnummer', 'wpenon' ),
          'desc'              => __( 'Die Festnetz-Telefonnummer, unter der Ihre Firma erreichbar ist.', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'email',
          'name'              => __( 'Kontakt-Emailadresse', 'wpenon' ),
          'desc'              => __( 'Die Emailadresse, mit der Kunden sich an Ihre Firma wenden können.', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'automail',
          'name'              => __( 'Autoreply-Emailadresse', 'wpenon' ),
          'desc'              => __( 'Diese Emailadresse wird in automatisch generierten Emails als Absender verwendet. Kunden können keine Antworten dorthin senden.', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'bankdaten',
          'name'              => __( 'Bankdaten', 'wpenon' ),
          'desc'              => __( 'Hier können Sie die Kontodaten Ihrer Firma bearbeiten, an die Kunden ihre Überweisungen richten sollen. Achten Sie darauf, dass alle Felder stets ausgefüllt sind.', 'wpenon' ),
          'type'              => 'header',
        ),
        array(
          'id'                => 'kontoinhaber',
          'name'              => __( 'Kontoinhaber', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'kontonummer',
          'name'              => __( 'Kontonummer', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'bankleitzahl',
          'name'              => __( 'Bankleitzahl', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'kreditinstitut',
          'name'              => __( 'Kreditinstitut / Bank', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'iban',
          'name'              => __( 'IBAN', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'bic',
          'name'              => __( 'BIC (SWIFT)', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'prefix',
          'name'              => __( 'Überweisungs-Präfix', 'wpenon' ),
          'desc'              => __( 'Was Sie hier eingeben, müssen Kunden bei einer Überweisung an den Anfang des Verwendungszwecks setzen.', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'legal',
          'name'              => __( 'Rechtliche Informationen', 'wpenon' ),
          'desc'              => __( 'Die folgenden Informationen werden für Rechnungen benötigt.', 'wpenon' ),
          'type'              => 'header',
        ),
        array(
          'id'                => 'amtsgericht',
          'name'              => __( 'Amtsgericht', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'steuernummer',
          'name'              => __( 'Steuernummer', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'ustid',
          'name'              => __( 'USt-Identifikationsnummer', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'glaeubigerid',
          'name'              => __( 'Gläubiger-ID', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'styleheader',
          'name'              => __( 'Stil-Einstellungen', 'wpenon' ),
          'desc'              => __( 'Die folgenden Einstellungen betreffen das optische Erscheinungsbild der Benutzeroberfläche.', 'wpenon' ),
          'type'              => 'header',
        ),
        array(
          'id'                => 'custom_bootstrap_css',
          'name'              => __( 'Eigene Bootstrap CSS-Datei', 'wpenon' ),
          'desc'              => __( 'Das Plugin WP Energieausweis Online verwendet das CSS-Framework Bootstrap für alle öffentlichen Formulare. Wenn Sie stattdessen eine eigene Bootstrap-Version verwenden möchten, geben Sie hier den Namen der entsprechenden CSS Handle ein.', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
        array(
          'id'                => 'custom_bootstrap_js',
          'name'              => __( 'Eigene Bootstrap JavaScript-Datei', 'wpenon' ),
          'desc'              => __( 'Das Plugin WP Energieausweis Online verwendet das CSS-Framework Bootstrap für alle öffentlichen Formulare. Wenn Sie stattdessen eine eigene Bootstrap-Version verwenden möchten, geben Sie hier den Namen der entsprechenden JavaScript Handle ein.', 'wpenon' ),
          'type'              => 'text',
          'size'              => 'regular',
        ),
      ),
      'taxes'               => array(),
    );
    
    if ( WPENON_BW ) {
      $settings['general'][] = array(
        'id'                => 'new_bw_page',
        'name'              => __( 'Seite für Bedarfsausweis für Wohngebäude', 'wpenon' ),
        'desc'              => __( 'Auf dieser Seite kann ein neuer Bedarfsausweis für Wohngebäude erstellt werden.', 'wpenon' ),
        'type'              => 'select',
        'options'           => edd_get_pages(),
      );
      $settings['taxes'][] = array(
        'id'                => 'bw_download_price',
        'name'              => __( 'Bedarfsausweis für Wohngebäude: Download-Preis', 'wpenon' ),
        'desc'              => __( 'Der Preis für Downloads von Bedarfsausweisen für Wohngebäude.', 'wpenon' ),
        'type'              => 'price',
        'size'              => 'small',
      );
      if ( WPENON_POSTAL && ( ! defined( 'WPENON_POSTAL_ADDITIONAL_AMOUNT' ) || ! WPENON_POSTAL_ADDITIONAL_AMOUNT ) ) {
        $settings['taxes'][] = array(
          'id'                => 'bw_postal_price',
          'name'              => __( 'Bedarfsausweis für Wohngebäude: Postversand-Preis', 'wpenon' ),
          'desc'              => __( 'Der Preis für den Postversand von Bedarfsausweisen für Wohngebäude.', 'wpenon' ),
          'type'              => 'price',
          'size'              => 'small',
        );
      }
      if ( WPENON_AUDIT && ( ! defined( 'WPENON_AUDIT_ADDITIONAL_AMOUNT' ) || ! WPENON_AUDIT_ADDITIONAL_AMOUNT ) ) {
        $settings['taxes'][] = array(
          'id'                => 'bw_audit_price',
          'name'              => __( 'Bedarfsausweis für Wohngebäude: Kontroll-Preis', 'wpenon' ),
          'desc'              => __( 'Der Preis für die Kontrolle von Bedarfsausweisen für Wohngebäude.', 'wpenon' ),
          'type'              => 'price',
          'size'              => 'small',
        );
      }
      if ( WPENON_POSTAL && ( ! defined( 'WPENON_POSTAL_ADDITIONAL_AMOUNT' ) || ! WPENON_POSTAL_ADDITIONAL_AMOUNT ) && WPENON_AUDIT && ( ! defined( 'WPENON_AUDIT_ADDITIONAL_AMOUNT' ) || ! WPENON_AUDIT_ADDITIONAL_AMOUNT ) ) {
        $settings['taxes'][] = array(
          'id'                => 'bw_postal_audit_price',
          'name'              => __( 'Bedarfsausweis für Wohngebäude: Postversand- und Kontroll-Preis', 'wpenon' ),
          'desc'              => __( 'Der Preis für den Postverstand und die Kontrolle von Bedarfsausweisen für Wohngebäude.', 'wpenon' ),
          'type'              => 'price',
          'size'              => 'small',
        );
      }
    }
    if ( WPENON_BN )  {
      $settings['general'][] = array(
        'id'                => 'new_bn_page',
        'name'              => __( 'Seite für Bedarfsausweis für Nichtwohngebäude', 'wpenon' ),
        'desc'              => __( 'Auf dieser Seite kann ein neuer Bedarfsausweis für Nichtwohngebäude erstellt werden.', 'wpenon' ),
        'type'              => 'select',
        'options'           => edd_get_pages(),
      );
      $settings['taxes'][] = array(
        'id'                => 'bn_download_price',
        'name'              => __( 'Bedarfsausweis für Nichtwohngebäude: Download-Preis', 'wpenon' ),
        'desc'              => __( 'Der Preis für Downloads von Bedarfsausweisen für Nichtwohngebäude.', 'wpenon' ),
        'type'              => 'price',
        'size'              => 'small',
      );
      if ( WPENON_POSTAL && ( ! defined( 'WPENON_POSTAL_ADDITIONAL_AMOUNT' ) || ! WPENON_POSTAL_ADDITIONAL_AMOUNT ) ) {
        $settings['taxes'][] = array(
          'id'                => 'bn_postal_price',
          'name'              => __( 'Bedarfsausweis für Nichtwohngebäude: Postversand-Preis', 'wpenon' ),
          'desc'              => __( 'Der Preis für den Postversand von Bedarfsausweisen für Nichtwohngebäude.', 'wpenon' ),
          'type'              => 'price',
          'size'              => 'small',
        );
      }
      if ( WPENON_AUDIT && ( ! defined( 'WPENON_AUDIT_ADDITIONAL_AMOUNT' ) || ! WPENON_AUDIT_ADDITIONAL_AMOUNT ) ) {
        $settings['taxes'][] = array(
          'id'                => 'bn_audit_price',
          'name'              => __( 'Bedarfsausweis für Nichtwohngebäude: Kontroll-Preis', 'wpenon' ),
          'desc'              => __( 'Der Preis für die Kontrolle von Bedarfsausweisen für Nichtwohngebäude.', 'wpenon' ),
          'type'              => 'price',
          'size'              => 'small',
        );
      }
      if ( WPENON_POSTAL && ( ! defined( 'WPENON_POSTAL_ADDITIONAL_AMOUNT' ) || ! WPENON_POSTAL_ADDITIONAL_AMOUNT ) && WPENON_AUDIT && ( ! defined( 'WPENON_AUDIT_ADDITIONAL_AMOUNT' ) || ! WPENON_AUDIT_ADDITIONAL_AMOUNT ) ) {
        $settings['taxes'][] = array(
          'id'                => 'bn_postal_audit_price',
          'name'              => __( 'Bedarfsausweis für Nichtwohngebäude: Postversand- und Kontroll-Preis', 'wpenon' ),
          'desc'              => __( 'Der Preis für den Postverstand und die Kontrolle von Bedarfsausweisen für Nichtwohngebäude.', 'wpenon' ),
          'type'              => 'price',
          'size'              => 'small',
        );
      }
    }
    if ( WPENON_VW ) {
      $settings['general'][] = array(
        'id'                => 'new_vw_page',
        'name'              => __( 'Seite für Verbrauchsausweis für Wohngebäude', 'wpenon' ),
        'desc'              => __( 'Auf dieser Seite kann ein neuer Verbrauchsausweis für Wohngebäude erstellt werden.', 'wpenon' ),
        'type'              => 'select',
        'options'           => edd_get_pages(),
      );
      $settings['taxes'][] = array(
        'id'                => 'vw_download_price',
        'name'              => __( 'Verbrauchsausweis für Wohngebäude: Download-Preis', 'wpenon' ),
        'desc'              => __( 'Der Preis für Downloads von Verbrauchsausweisen für Wohngebäude.', 'wpenon' ),
        'type'              => 'price',
        'size'              => 'small',
      );
      if ( WPENON_POSTAL && ( ! defined( 'WPENON_POSTAL_ADDITIONAL_AMOUNT' ) || ! WPENON_POSTAL_ADDITIONAL_AMOUNT ) ) {
        $settings['taxes'][] = array(
          'id'                => 'vw_postal_price',
          'name'              => __( 'Verbrauchsausweis für Wohngebäude: Postversand-Preis', 'wpenon' ),
          'desc'              => __( 'Der Preis für den Postversand von Verbrauchsausweisen für Wohngebäude.', 'wpenon' ),
          'type'              => 'price',
          'size'              => 'small',
        );
      }
      if ( WPENON_AUDIT && ( ! defined( 'WPENON_AUDIT_ADDITIONAL_AMOUNT' ) || ! WPENON_AUDIT_ADDITIONAL_AMOUNT ) ) {
        $settings['taxes'][] = array(
          'id'                => 'vw_audit_price',
          'name'              => __( 'Verbrauchsausweis für Wohngebäude: Kontroll-Preis', 'wpenon' ),
          'desc'              => __( 'Der Preis für die Kontrolle von Verbrauchsausweisen für Wohngebäude.', 'wpenon' ),
          'type'              => 'price',
          'size'              => 'small',
        );
      }
      if ( WPENON_POSTAL && ( ! defined( 'WPENON_POSTAL_ADDITIONAL_AMOUNT' ) || ! WPENON_POSTAL_ADDITIONAL_AMOUNT ) && WPENON_AUDIT && ( ! defined( 'WPENON_AUDIT_ADDITIONAL_AMOUNT' ) || ! WPENON_AUDIT_ADDITIONAL_AMOUNT ) ) {
        $settings['taxes'][] = array(
          'id'                => 'vw_postal_audit_price',
          'name'              => __( 'Verbrauchsausweis für Wohngebäude: Postversand- und Kontroll-Preis', 'wpenon' ),
          'desc'              => __( 'Der Preis für den Postverstand und die Kontrolle von Verbrauchsausweisen für Wohngebäude.', 'wpenon' ),
          'type'              => 'price',
          'size'              => 'small',
        );
      }
    }
    if ( WPENON_VN ) {
      $settings['general'][] = array(
        'id'                => 'new_vn_page',
        'name'              => __( 'Seite für Verbrauchsausweis für Nichtwohngebäude', 'wpenon' ),
        'desc'              => __( 'Auf dieser Seite kann ein neuer Verbrauchsausweis für Nichtwohngebäude erstellt werden.', 'wpenon' ),
        'type'              => 'select',
        'options'           => edd_get_pages(),
      );
      $settings['taxes'][] = array(
        'id'                => 'vn_postal_price',
        'name'              => __( 'Verbrauchsausweis für Nichtwohngebäude: Download-Preis', 'wpenon' ),
        'desc'              => __( 'Der Preis für Downloads von Verbrauchsausweisen für Nichtwohngebäude.', 'wpenon' ),
        'type'              => 'price',
        'size'              => 'small',
      );
      if ( WPENON_POSTAL && ( ! defined( 'WPENON_POSTAL_ADDITIONAL_AMOUNT' ) || ! WPENON_POSTAL_ADDITIONAL_AMOUNT ) ) {
        $settings['taxes'][] = array(
          'id'                => 'vn_postal_price',
          'name'              => __( 'Verbrauchsausweis für Nichtwohngebäude: Postversand-Preis', 'wpenon' ),
          'desc'              => __( 'Der Preis für den Postversand von Verbrauchsausweisen für Nichtwohngebäude.', 'wpenon' ),
          'type'              => 'price',
          'size'              => 'small',
        );
      }
      if ( WPENON_AUDIT && ( ! defined( 'WPENON_AUDIT_ADDITIONAL_AMOUNT' ) || ! WPENON_AUDIT_ADDITIONAL_AMOUNT ) ) {
        $settings['taxes'][] = array(
          'id'                => 'vn_audit_price',
          'name'              => __( 'Verbrauchsausweis für Nichtwohngebäude: Kontroll-Preis', 'wpenon' ),
          'desc'              => __( 'Der Preis für die Kontrolle von Verbrauchsausweisen für Nichtwohngebäude.', 'wpenon' ),
          'type'              => 'price',
          'size'              => 'small',
        );
      }
      if ( WPENON_POSTAL && ( ! defined( 'WPENON_POSTAL_ADDITIONAL_AMOUNT' ) || ! WPENON_POSTAL_ADDITIONAL_AMOUNT ) && WPENON_AUDIT && ( ! defined( 'WPENON_AUDIT_ADDITIONAL_AMOUNT' ) || ! WPENON_AUDIT_ADDITIONAL_AMOUNT ) ) {
        $settings['taxes'][] = array(
          'id'                => 'vn_postal_audit_price',
          'name'              => __( 'Verbrauchsausweis für Nichtwohngebäude: Postversand- und Kontroll-Preis', 'wpenon' ),
          'desc'              => __( 'Der Preis für den Postverstand und die Kontrolle von Verbrauchsausweisen für Nichtwohngebäude.', 'wpenon' ),
          'type'              => 'price',
          'size'              => 'small',
        );
      }
    }

    foreach ( $settings as $slug => $group ) {
      $this->settings[ $slug ] = array();
      foreach ( $group as $field ) {
        if ( isset( $field['id'] ) ) {
          $this->fields[] = $field['id'];
          $field['id'] = \WPENON\Util\Format::prefix( $field['id'] );
          $this->settings[ $slug ][ $field['id'] ] = $field;
        }
      }
    }
  }

  private function _registerSettings() {
    foreach ( edd_get_settings_tabs() as $tab => $title ) {
      add_filter( 'edd_settings_' . $tab, array( $this, '_registerSettingsCallback' ), 10, 1 );
    }
    add_filter( 'edd_get_option', array( $this, '_getOptionCallback' ), 10, 3 );
  }

  public function _registerSettingsCallback( $settings ) {
    $tab = str_replace( 'edd_settings_', '', current_filter() );
    if ( isset( $this->settings[ $tab ] ) ) {
      $settings = array_merge( $this->settings[ $tab ], $settings );
    }

    $configured_settings = array();
    foreach ( $settings as $slug => $field ) {
      $constant_slug = 'WPENON_SETTING_' . strtoupper( \WPENON\Util\Format::unprefix( $slug ) );
      if ( apply_filters( 'wpenon_enable_field', ! defined( $constant_slug ), $slug ) ) {
        if ( defined( $constant_slug . '_DEFAULT' ) ) {
          $field['std'] = constant( $constant_slug . '_DEFAULT' );
        }
        $configured_settings[ $slug ] = $field;
      }
    }

    return $configured_settings;
  }

  public function _getOptionCallback( $value, $key, $default ) {
    $constant_slug = 'WPENON_SETTING_' . strtoupper( \WPENON\Util\Format::unprefix( $key ) );

    if ( defined( $constant_slug ) ) {
      return constant( $constant_slug );
    }

    if ( $value === $default && defined( $constant_slug . '_DEFAULT' ) ) {
      return constant( $constant_slug . '_DEFAULT' );
    }

    return $value;
  }

  private function _adjustEDDSettings() {
    add_filter( 'edd_settings_tabs', array( $this, '_adjustEDDTabHeadlines' ) );
    //TODO: remove settings we do not need (receipt title format, tax rates)
  }

  public function _adjustEDDTabHeadlines( $tabs ) {
    $tabs['styles'] = __( 'Kontaktdaten und Stile', 'wpenon' );
    $tabs['taxes'] = __( 'Preise', 'wpenon' );

    return $tabs;
  }
}
