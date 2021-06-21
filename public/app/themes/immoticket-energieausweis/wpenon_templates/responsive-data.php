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
  <div class="<?php echo implode( ' ', $data['class'] ); ?>">
    <?php if ( ! empty( $data['caption'] ) ) : ?>
      <caption><?php echo $data['caption']; ?></caption>
    <?php endif; ?>
      <?php $i=0; ?>
      <?php foreach ( $data['fields'] as &$field ) :
      $field = wp_parse_args( $field, array( 'key' => '', 'headline' => '', 'format' => 'string' ) ); ?>
        <div style="display:flex;flex-wrap: wrap; border-top: 1px solid #ddd;">          
          <div style="flex-basis: 50%; font-weight:bold; padding: 8px"><?php echo $field['headline']; ?></div>
          
          <?php foreach ( $data['data'] as $col ) : ?>            
            <div style="word-wrap: break-word; padding: 8px;">
              <?php
              if ( isset( $col[ $field['key'] ] ) ) {
                $value = $col[ $field['key'] ];
                if ( is_callable( array( '\WPENON\Util\Format', $field['format'] ) ) ) {
                  $value = call_user_func( array( '\WPENON\Util\Format', $field['format'] ), $value );
                }
                echo $value;
              }
              ?>
            </div>
            <?php $i++; ?>
          <?php endforeach; unset( $col ); ?>
        </div>
        <?php $i=0; ?>
      <?php endforeach; unset( $field ); ?>
  </div>
<?php endif; ?>
