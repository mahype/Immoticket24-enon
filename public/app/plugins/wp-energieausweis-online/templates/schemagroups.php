<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */
?>

<?php foreach( $data as $group_slug => $group ) : ?>

  <div id="group-<?php echo $group_slug; ?>" class="well">
    <fieldset id="<?php echo $group_slug; ?>">

      <?php if( !empty( $group['title'] ) ) : ?>
        <legend><?php echo $group['title']; ?></legend>
      <?php endif; ?>

      <?php if( !empty( $group['description'] ) ) : ?>
        <p><?php echo $group['description']; ?></p>
      <?php endif; ?>

      <?php do_action( 'wpenon_form_group_' . $group_slug . '_before', $group ); ?>

      <?php wpenon_get_view()->displaySubTemplate( 'schemafields', '', $group['fields'] ); ?>

      <?php do_action( 'wpenon_form_group_' . $group_slug . '_after', $group ); ?>

    </fieldset>
  </div>

<?php endforeach; ?>
