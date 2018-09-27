<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

if ( isset( $data['class'] ) ) {
  if ( ! is_array( $data['class'] ) ) {
    if ( $data['class'] ) {
      $data['class'] = array( $data['class'] );
    } else {
      $data['class'] = array();
    }
  }
  array_unshift( $data['class'], 'table' );
} else {
  $data['class'] = array( 'table' );
}

?>

<?php if ( count( $data['data'] ) > 0 ) : ?>
  <table class="<?php echo implode( ' ', $data['class'] ); ?>">
    <?php if ( ! empty( $data['caption'] ) ) : ?>
      <caption><?php echo $data['caption']; ?></caption>
    <?php endif; ?>
    <tbody>
      <?php foreach ( $data['fields'] as &$field ) :
      $field = wp_parse_args( $field, array( 'key' => '', 'headline' => '', 'format' => 'string' ) ); ?>
        <tr>
          <th scope="row">
            <?php echo $field['headline']; ?>
          </th>
          <?php foreach ( $data['data'] as $col ) : ?>
            <td>
              <?php
              if ( isset( $col[ $field['key'] ] ) ) {
                $value = $col[ $field['key'] ];
                if ( is_callable( array( '\WPENON\Util\Format', $field['format'] ) ) ) {
                  $value = call_user_func( array( '\WPENON\Util\Format', $field['format'] ), $value );
                }
                echo $value;
              }
              ?>
            </td>
          <?php endforeach; unset( $col ); ?>
        </tr>
      <?php endforeach; unset( $field ); ?>
    </tbody>
  </table>
<?php endif; ?>
