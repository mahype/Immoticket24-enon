<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */
?>

<?php if( count( $data ) > 1 ) : ?>

  <ul class="wpenon-nav-tabs nav nav-tabs" role="tablist">
    <?php foreach( $data as $tab_slug => $tab ) : ?>

      <li<?php echo $tab['active'] ? ' class="active"' : ''; ?>>
        <a href="#<?php echo $tab_slug; ?>" role="tab" data-toggle="tab"><?php echo $tab['title']; ?></a>
      </li>

    <?php endforeach; ?>
  </ul>

  <div class="wpenon-tab-content tab-content">
    <?php foreach( $data as $tab_slug => $tab ) : ?>

      <div class="wpenon-tab-pane tab-pane<?php echo $tab['active'] ? ' active' : ''; ?>" id="<?php echo $tab_slug; ?>">

        <?php if( !empty( $tab['description'] ) ) : ?>
          <p><?php echo $tab['description']; ?></p>
        <?php endif; ?>

        <?php wpenon_get_view()->displaySubTemplate( 'schemagroups', '', $tab['groups'] ); ?>

      </div>
      
    <?php endforeach; ?>
  </div>

  <div id="wpenon-tab-navigation-buttons" class="row">
    <div class="col-sm-6">
      <p class="text-left">
        <button type="button" id="wpenon-previous-button" class="btn btn-default"><?php _e( 'Zurück', 'wpenon' ); ?></button>
      </p>
    </div>
    <div class="col-sm-6">
      <p class="text-right">
        <button type="button" id="wpenon-next-button" class="btn btn-default"><?php _e( 'Weiter', 'wpenon' ); ?></button>
      </p>
    </div>
  </div>

<?php else : ?>

  <?php foreach( $data as $tab_slug => $tab ) : ?>

    <?php if( !empty( $tab['description'] ) ) : ?>
      <p><?php echo $tab['description']; ?></p>
    <?php endif; ?>

    <?php wpenon_get_view()->displaySubTemplate( 'schemagroups', '', $tab['groups'] ); ?>

  <?php endforeach; ?>

<?php endif; ?>
