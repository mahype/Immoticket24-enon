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
    <thead>
      <tr>
        <?php foreach ( $data['fields'] as &$field ) :
        $field = wp_parse_args( $field, array( 'key' => '', 'headline' => '', 'format' => 'string' ) ); ?>
          <th scope="col">
            <?php echo $field['headline']; ?>
          </th>
        <?php endforeach; unset( $field ); ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ( $data['data'] as $row ) : ?>
        <tr>
          <?php foreach ( $data['fields'] as $field ) : ?>
            <td>
              <?php
              if ( isset( $row[ $field['key'] ] ) ) {
                $value = $row[ $field['key'] ];
                if ( is_callable( array( '\WPENON\Util\Format', $field['format'] ) ) ) {
                  $value = call_user_func( array( '\WPENON\Util\Format', $field['format'] ), $value );
                }
                echo $value;
              }
              ?>
            </td>
          <?php endforeach; unset( $field ); ?>
        </tr>
      <?php endforeach; unset( $row ); ?>
    </tbody>
  </table>
<?php endif; ?>
