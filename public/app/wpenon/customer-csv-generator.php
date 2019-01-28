<?php

class WPENON_Immoticket24_Customer_CSV_Generator {
  private static $instance = null;

  public static function instance() {
    if ( null === self::$instance ) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  private function __construct() {
    // Empty constructor.
  }

  public function add_menu_page() {
    $hook_suffix = add_submenu_page( 'edit.php?post_type=download', __( 'CSV-Generator', 'wpenon' ), __( 'CSV-Generator', 'wpenon' ), 'export_shop_reports', 'wpenon_immoticket24_csv_generator', array( $this, 'render_menu_page' ) );

    add_action( 'load-' . $hook_suffix, array( $this, 'listen' ) );
  }

  public function render_menu_page() {
    ?>
    <script type="text/javascript">
      document.addEventListener( 'DOMContentLoaded', function() {
        var $show = document.getElementById( 'show' );

        $show.addEventListener( 'change', function() {
          var value = $show.value;

          switch ( value ) {
            case 'all':
              document.getElementById( 'start-all-label' ).style.display = 'inline';
              document.getElementById( 'start-all-description' ).style.display = 'inline';
              document.getElementById( 'end-all-label' ).style.display = 'inline';
              document.getElementById( 'end-all-description' ).style.display = 'inline';
              document.getElementById( 'start-almost-label' ).style.display = 'none';
              document.getElementById( 'start-almost-description' ).style.display = 'none';
              document.getElementById( 'end-almost-label' ).style.display = 'none';
              document.getElementById( 'end-almost-description' ).style.display = 'none';
              break;
            case 'almost':
              document.getElementById( 'start-all-label' ).style.display = 'none';
              document.getElementById( 'start-all-description' ).style.display = 'none';
              document.getElementById( 'end-all-label' ).style.display = 'none';
              document.getElementById( 'end-all-description' ).style.display = 'none';
              document.getElementById( 'start-almost-label' ).style.display = 'inline';
              document.getElementById( 'start-almost-description' ).style.display = 'inline';
              document.getElementById( 'end-almost-label' ).style.display = 'inline';
              document.getElementById( 'end-almost-description' ).style.display = 'inline';
              break;
          }
        });
      });
    </script>

    <div class="wrap">
      <h1><?php _e( 'CSV-Generator', 'wpenon' ); ?></h1>

      <p class="description">
        <?php _e( 'Hier können Sie sich eine CSV-Datei Ihrer Kunden erzeugen lassen.', 'wpenon' ); ?>
        <br />
        <?php _e( 'Diese Datei können Sie zum Beispiel zum Import der Kunden nach Mailchimp verwenden.', 'wpenon' ); ?>    
      </p>

      <form method="get">
        <table class="form-table">
          <tr>
            <th scope="row">
              <label for="show"><?php _e( 'Art der Kunden', 'wpenon' ); ?></label>
            </th>
            <td>
              <select id="show" name="show">
                <option value="all"><?php _e( 'Kunden, die mindestens einen Ausweis bezahlt haben', 'wpenon' ); ?></option>
                <option value="almost"><?php _e( 'Kunden, die mindestens einen Ausweis erstellt haben', 'wpenon' ); ?></option>
              </select>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="start">
                <span id="start-all-label"><?php _e( 'Start-Kundennummer', 'wpenon' ); ?></span>
                <span id="start-almost-label" style="display:none;"><?php _e( 'Start-Energieausweisnummer', 'wpenon' ); ?></span>
              </label>
            </th>
            <td>
              <input type="text" id="start" name="start" value="" aria-describedby="start-description" />
              <span id="start-description" class="description">
                <span id="start-all-description"><?php _e( 'Geben Sie die Kundennummer ein, bei der die Auflistung beginnen soll.', 'wpenon' ); ?></span>
                <span id="start-almost-description" style="display:none;"><?php _e( 'Geben Sie die Energieausweisnummer ein, bei der die Auflistung beginnen soll.', 'wpenon' ); ?></span>
              </span>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="end">
                <span id="end-all-label"><?php _e( 'End-Kundennummer', 'wpenon' ); ?></span>
                <span id="end-almost-label" style="display:none;"><?php _e( 'End-Energieausweisnummer', 'wpenon' ); ?></span>
              </label>
            </th>
            <td>
              <input type="text" id="end" name="end" value="" aria-describedby="end-description" />
              <span id="end-description" class="description">
                <span id="end-all-description"><?php _e( 'Geben Sie die Kundennummer ein, bei der die Auflistung enden soll.', 'wpenon' ); ?></span>
                <span id="end-almost-description" style="display:none;"><?php _e( 'Geben Sie die Energieausweisnummer ein, bei der die Auflistung enden soll.', 'wpenon' ); ?></span>
              </span>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="number"><?php _e( 'Anzahl anzuzeigender Kunden', 'wpenon' ); ?></label>
            </th>
            <td>
              <input type="number" id="number" name="number" value="20" aria-describedby="number-description" />
              <span id="number-description" class="description"><?php _e( 'Das Eingeben von 0 hebt die Beschränkung auf.', 'wpenon' ); ?></span>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="offset"><?php _e( 'Offset anzuzeigender Kunden', 'wpenon' ); ?></label>
            </th>
            <td>
              <input type="number" id="offset" name="offset" value="0" />
            </td>
          </tr>
        </table>

        <input type="hidden" name="post_type" value="download" />
        <input type="hidden" name="page" value="wpenon_immoticket24_csv_generator" />

        <?php submit_button( __( 'CSV generieren', 'wpenon' ) ); ?>
      </form>
    </div>
    <?php
  }

  public function listen() {
    if ( ! function_exists( 'EDD' ) || ! class_exists( 'WPENON\Util\Format' ) ) {
      return;
    }

    if ( ! isset( $_GET['page'] ) || 'wpenon_immoticket24_csv_generator' !== $_GET['page'] ) {
      return;
    }

    if ( ! isset( $_GET['show'] ) ) {
      return;
    }

    $customers = $_GET['show'];

    if ( ! current_user_can( 'edit_shop_payments' ) ) {
      wp_die( __( 'Sie haben keine Rechte, diese Informationen zu sehen.', 'wpenon' ) );
    }

    $start  = isset( $_GET['start'] ) ? trim( wp_unslash( $_GET['start'] ) ) : '';
    $end    = isset( $_GET['end'] ) ? trim( wp_unslash( $_GET['end'] ) ) : '';
    $number = isset( $_GET['number'] ) ? absint( $_GET['number'] ) : 20;
    $offset = isset( $_GET['offset'] ) ? absint( $_GET['offset'] ) : 0;

    $start_id = $end_id = 0;

    $filename = 'null.csv';
    $results = array();

    switch ( $customers ) {
      case 'all':
        if ( 0 === strpos( $start, '#' ) ) {
          $start = substr( $start, 1 );
        }
        if ( 0 === strpos( $end, '#' ) ) {
          $end = substr( $end, 1 );
        }

        $start_id = absint( $start );
        $end_id   = absint( $end );

        $filename = __( 'energieausweis-kunden.csv', 'wpenon' );
        $results = $this->get_customers( array(
          'start_id' => $start_id,
          'end_id'   => $end_id,
          'number'   => $number,
          'offset'   => $offset,
        ) );
        break;
      case 'almost':
        $args = array(
          'posts_per_page'         => 1,
          'post_type'              => 'download',
          'post_status'            => 'publish',
          'fields'                 => 'ids',
          'no_found_rows'          => true,
          'update_post_meta_cache' => false,
          'update_post_term_cache' => false,
        );

        if ( ! empty( $start ) ) {
          $start_ids = get_posts( array_merge( $args, array( 'title' => $start ) ) );
          if ( ! empty( $start_ids ) ) {
            $start_id = $start_ids[0];
          }
        }
        if ( ! empty( $end ) ) {
          $end_ids = get_posts( array_merge( $args, array( 'title' => $end ) ) );
          if ( ! empty( $end_ids ) ) {
            $end_id = $end_ids[0];
          }
        }

        $filename = __( 'energieausweis-ersteller.csv', 'wpenon' );
        $results = $this->get_almost_customers( array(
          'start_id' => $start_id,
          'end_id'   => $end_id,
          'number'   => $number,
          'offset'   => $offset,
        ) );
        break;
    }

    $csv_settings = array(
      'terminated'  => ';',
      'enclosed'    => '"',
      'escaped'     => '"',
    );

    $headings = array(
      'email'      => 'Email Address',
      'first_name' => 'First Name',
      'last_name'  => 'Last Name',
    );

    header( 'Content-Type: text/csv; charset=' . WPENON_DEFAULT_CHARSET );
    header( 'Content-Disposition: inline; filename=' . $filename );
    //header( 'Content-Disposition: attachment; filename=' . $filename );

    $output = fopen( 'php://output', 'w' );

    fputcsv( $output, \WPENON\Util\Format::csvEncode( $headings, WPENON_DEFAULT_CHARSET ), $csv_settings['terminated'], $csv_settings['enclosed'] );

    foreach ( $results as $result ) {
      fputcsv( $output, \WPENON\Util\Format::csvEncode( $result, WPENON_DEFAULT_CHARSET ), $csv_settings['terminated'], $csv_settings['enclosed'] );
    }

    fclose( $output );
    exit;
  }

  private function get_customers( $args = array() ) {
    global $wpdb;

    $args = wp_parse_args( $args, array(
      'number'     => 20,
      'offset'     => 0,
      'start_id'   => 0,
      'end_id'     => 0,
    ) );

    if ( $args['number'] < 1 ) {
      $args['number'] = 999999999999;
    }

    $where = 'WHERE 1=1';
    if ( $args['start_id'] > 0 ) {
      $start_id = absint( $args['start_id'] );
      $where .= " AND `id` >= {$start_id}";
    }
    if ( $args['end_id'] > 0 ) {
      $end_id = absint( $args['end_id'] );
      $where .= " AND `id` <= {$end_id}";
    }

    $table_name = $wpdb->prefix . 'edd_customers';

    $query = "SELECT * FROM $table_name $where GROUP BY id ORDER BY id DESC LIMIT %d,%d;";

    $customers = $wpdb->get_results( $wpdb->prepare( $query, absint( $args['offset'] ), absint( $args['number'] ) ) );
    if ( ! $customers ) {
      return array();
    }

    $results = array();

    foreach ( $customers as $customer ) {
      $first_name = $last_name = '';

      if ( false !== strpos( $customer->name, ' ' ) ) {
        list( $first_name, $last_name ) = explode( ' ', $customer->name, 2 );
      } else {
        $first_name = $customer->name;
      }

      $results[] = array(
        'email'      => $customer->email,
        'first_name' => $first_name,
        'last_name'  => $last_name,
      );
    }

    return $results;
  }

  private function get_almost_customers( $args = array() ) {
    global $wpdb;

    $args = wp_parse_args( $args, array(
      'number' => 20,
      'offset' => 0,
      'start_id'   => 0,
      'end_id'     => 0,
    ) );

    if ( $args['number'] < 1 ) {
      $args['number'] = 999999999999;
    }

    $where = '';
    if ( $args['start_id'] > 0 ) {
      $start_id = absint( $args['start_id'] );
      $where .= " AND pm1.post_id >= {$start_id}";
    }
    if ( $args['end_id'] > 0 ) {
      $end_id = absint( $args['end_id'] );
      $where .= " AND pm1.post_id <= {$end_id}";
    }

    $query = "SELECT DISTINCT pm1.meta_value FROM $wpdb->postmeta AS pm1 WHERE pm1.meta_key = %s $where AND NOT EXISTS(
    SELECT pm2.meta_id FROM $wpdb->postmeta AS pm2 WHERE pm2.meta_key = %s AND pm1.post_id = pm2.post_id
) ORDER BY pm1.post_id DESC LIMIT %d,%d;";

    $prepared_query = $wpdb->prepare( $query, 'wpenon_email', '_wpenon_attached_payment_id', $args['offset'], $args['number'] );
    
    $emails = $wpdb->get_col( $prepared_query );
    if ( ! $emails ) {
      return array();
    }

    $results = array();

    foreach ( $emails as $email ) {
      $results[] = array(
        'email'      => $email,
        'first_name' => '',
        'last_name'  => '',
      );
    }

    return $results;
  }
}
