<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

$GLOBALS['wpenon_readonly_fields'] = true;
?>

<div class="overview-meta well">

<?php foreach( $data as $tab_slug => $tab ) : ?>

  <p class="lead"><strong><?php echo $tab['title']; ?></strong></p>

  <?php if( !empty( $tab['description'] ) ) : ?>
    <p><?php echo $tab['description']; ?></p>
  <?php endif; ?>

  <?php wpenon_get_view()->displaySubTemplate( 'schemagroups', '', $tab['groups'] ); ?>

<?php endforeach; ?>

</div>
