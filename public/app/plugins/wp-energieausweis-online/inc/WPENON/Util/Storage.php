<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class Storage {
  public static function getErrors( $id ) {
    return self::_getHelper( $id, 'errors', array(), true );
  }

  public static function getWarnings( $id ) {
    return self::_getHelper( $id, 'warnings', array(), true );
  }

  public static function storeErrors( $id, $errors ) {
    return self::_storeHelper( $id, 'errors', $errors );
  }

  public static function storeWarnings( $id, $warnings ) {
    return self::_storeHelper( $id, 'warnings', $warnings );
  }

  private static function _storeHelper( $id, $mode, $data ) {
    $data = json_encode( $data );
    return set_transient( 'wpenon_energieausweis_' . $id . '_' . $mode, $data );
  }

  private static function _getHelper( $id, $mode, $default = false, $one_time = false ) {
    $data = get_transient( 'wpenon_energieausweis_' . $id . '_' . $mode );
    if ( $data !== false ) {
      $data = json_decode( $data, true );
      if ( $one_time ) {
        delete_transient( 'wpenon_energieausweis_' . $id . '_' . $mode );
      }
    } else {
      $data = $default;
    }
    return $data;
  }
}
