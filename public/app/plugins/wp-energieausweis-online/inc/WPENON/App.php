<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON;

class App
{
  private static $instance;

  public static function instance()
  {
    if( self::$instance === null )
    {
      self::$instance = new self;
    }
    return self::$instance;
  }

  private function __construct()
  {
    $this->_loadConfig();
    $this->_loadCustomFunctions();
    $this->_loadUtil();

    \WPENON\Controller\General::instance();
    if( !is_admin() || defined( 'DOING_AJAX' ) && DOING_AJAX )
    {
      \WPENON\Controller\Frontend::instance();
    }
    if( is_admin() )
    {
      \WPENON\Controller\Admin::instance();
    }

    add_action( 'init', array( '\WPENON\Util\ThumbnailHandler', '_registerStatus' ) );

    register_activation_hook( WPENON_MAINFILE, array( __CLASS__, 'install' ) );
    register_uninstall_hook( WPENON_MAINFILE, array( __CLASS__, 'uninstall' ) );
  }

  public function __clone()
  {
    new \WPENON\Util\Error( 'notice', __METHOD__, __( 'Die Klasse darf nicht geklont werden.', 'wpenon' ), '1.0.0' );
  }

  public function __wakeup()
  {
    new \WPENON\Util\Error( 'notice', __METHOD__, __( 'Die Klasse darf nicht unserialisiert werden.', 'wpenon' ), '1.0.0' );
  }

  private function _loadConfig()
  {
    if( file_exists( WPENON_DATA_PATH . '/config.php' ) )
    {
      require_once WPENON_DATA_PATH . '/config.php';
    }
    require_once WPENON_PATH . '/config.php';
  }

  private function _loadCustomFunctions()
  {
    if( file_exists( WPENON_DATA_PATH . '/functions.php' ) )
    {
      require_once WPENON_DATA_PATH . '/functions.php';
    }
  }

  private function _loadUtil()
  {
    \WPENON\Util\Settings::instance();
    \WPENON\Util\EDDAdjustments::instance();
    \WPENON\Util\PaymentMeta::instance();
    \WPENON\Util\CustomerMeta::instance();
    \WPENON\Util\Emails::instance();
  }

  public static function install()
  {
    if ( ! get_option( 'wpenon_installed' ) ) {
      do_action( 'wpenon_install' );
      update_option( 'wpenon_installed', true );
    }
  }

  public static function uninstall()
  {
    if ( get_option( 'wpenon_installed' ) ) {
      do_action( 'wpenon_uninstall' );
      delete_option( 'wpenon_installed' );
    }
  }
}
