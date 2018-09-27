<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

foreach ( $data as $energy_bar ) {

  $energy_bar['offset_mpk'] = 90.0 / ( $energy_bar['reference'] * 2 );

  $energy_bar['value_top_offset'] = false;
  if ( isset( $energy_bar['value_top'] ) && $energy_bar['value_top'] !== false ) {
    $energy_bar['value_top_offset'] = $energy_bar['value_top'];
    if ( $energy_bar['value_top'] > $energy_bar['reference'] * 2 ) {
      $energy_bar['value_top_offset'] = $energy_bar['reference'] * 2;
    }
  }

  $energy_bar['value_bottom_offset'] = false;
  if ( isset( $energy_bar['value_bottom'] ) && $energy_bar['value_bottom'] !== false ) {
    $energy_bar['value_bottom_offset'] = $energy_bar['value_bottom'];
    if ( $energy_bar['value_bottom'] > $energy_bar['reference'] * 2 ) {
      $energy_bar['value_bottom_offset'] = $energy_bar['reference'] * 2;
    }
  }

  $energy_bar['number_range'] = $energy_bar['reference'] / 5;
  ?>

  <div class="energy-bar">

    <?php if ( isset( $energy_bar['value_top'] ) && $energy_bar['value_top'] !== false ) : ?>
      <div class="top">
        <div class="arrow down" style="margin-left:<?php echo round( $energy_bar['value_top_offset'] * $energy_bar['offset_mpk'], 8 ); ?>%;">
          <p class="description<?php echo $energy_bar['value_top'] > $energy_bar['reference'] ? ' right-align' : ' left-align'; ?>">
            <?php
            if ( $energy_bar['mode'] == 'bs' ) {
              _e( 'Endenergiebedarf Strom:', 'wpenon' );
            } elseif ( $energy_bar['mode'] == 'vs' ) {
              _e( 'Endenergieverbrauch Strom:', 'wpenon' );
            } elseif ( $energy_bar['mode'] == 'b' ) {
              _e( 'Endenergiebedarf dieses Gebäudes:', 'wpenon' );
            } else {
              _e( 'Endenergieverbrauch dieses Gebäudes:', 'wpenon' );
            }
            ?>
            <br><?php printf( __( '%s kWh/m&sup2;', 'wpenon' ), '<strong>' . \WPENON\Util\Format::float( $energy_bar['value_top'] ) . '</strong>' ); ?>
          </p>
        </div>
      </div>
    <?php else : ?>
      <div class="top no-value"></div>
    <?php endif; ?>

    <div class="core">
      <p class="numbers">
        <?php for ( $i = 0; $i < 11; $i++ ) : ?>
          <span><?php echo absint( $i * $energy_bar['number_range'] ); ?></span>
        <?php endfor; ?>
      </p>
    </div>

    <?php if ( ! in_array( $energy_bar['mode'], array( 'bs', 'vs' ) ) && isset( $energy_bar['value_bottom'] ) && $energy_bar['value_bottom'] !== false ) : ?>
      <div class="bottom">
        <div class="arrow up" style="margin-left:<?php echo round( $energy_bar['value_bottom_offset'] * $energy_bar['offset_mpk'], 8 ); ?>%;">
          <p class="description<?php echo $energy_bar['value_bottom'] > $energy_bar['reference'] ? ' right-align' : ' left-align'; ?>">
            <?php
            if ( $energy_bar['mode'] == 'b' ) {
              _e( 'Primärenergiebedarf dieses Gebäudes:', 'wpenon' );
            } else {
              _e( 'Primärenergieverbrauch dieses Gebäudes:', 'wpenon' );
            }
            ?>
            <br><?php printf( __( '%s kWh/m&sup2;', 'wpenon' ), '<strong>' . \WPENON\Util\Format::float( $energy_bar['value_bottom'] ) . '</strong>' ); ?>
          </p>
        </div>
      </div>
    <?php else : ?>
      <div class="bottom no-value"></div>
    <?php endif; ?>

  </div>
<?php

}
