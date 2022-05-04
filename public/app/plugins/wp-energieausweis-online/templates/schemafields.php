<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

?>
<?php foreach( $data as $field_slug => $field ) : ?>
  <?php if( $field['type'] != 'hidden' ) : ?>

    <div id="<?php echo $field_slug; ?>-wrap" class="form-group<?php echo ( !empty( $field['unit'] ) ? ' has-unit' : '' ) . ( !empty( $field['warning'] ) ? ' has-warning' : '' ) . ( !empty( $field['error'] ) ? ' has-error' : '' ) . ( $field['done'] ? ' is-done' : '' ); ?>"<?php echo $field['display'] ? '' : ' style="display:none;"'; ?>>

      <?php do_action( 'wpenon_form_field_' . $field_slug . '_wrap_before', $field ); ?>

      <?php if( !empty( $field['label'] ) && $field['type'] != 'checkbox' ) : ?>
        <?php if( $field['type'] == 'headline' ) : ?>
          <label class="form-headline col-md-4 col-sm-4 col-xs-12 control-label"><?php echo $field['label']; ?></label>
        <?php else : ?>
          <label for="<?php echo $field_slug; ?>" class="col-md-4 col-sm-4 col-xs-12 control-label">
            <?php echo $field['label']; ?>
            <?php if( $field['required'] ) : ?>
              <span class="required">*</span>
            <?php endif; ?>
          </label>
        <?php endif; ?>
      <?php endif; ?>

      <div class="col-md-7 col-sm-7 col-xs-11<?php echo ( empty( $field['label'] ) || $field['type'] == 'checkbox' ) ? 'col-md-offset-4 col-sm-offset-4' : ''; ?>">

        <?php do_action( 'wpenon_form_field_' . $field_slug . '_before', $field ); ?>

        <?php if ( isset( $GLOBALS['wpenon_readonly_fields'] ) && $GLOBALS['wpenon_readonly_fields'] ) : ?>
          <?php switch( $field['type'] ) :
            case 'headline':
              break; ?>
            <?php case 'select':
            case 'radio': ?>

              <p class="form-control-static"><?php echo isset( $field['options'][ $field['value'] ] ) ? $field['options'][ $field['value'] ] : ''; ?></p>

              <?php break; ?>
              <?php case 'multiselect':
              case 'multibox': ?>
                <p class="form-control-static">
                  <?php foreach ( $field['value'] as $k => $v ) : ?>
                    <?php
                    if ( isset( $field['options'][ $v ] ) ) :
                      if ( $k > 0 ) :
                        echo ', ';
                      endif;
                      echo $field['options'][ $v ];
                    endif;
                    ?>
                  <?php endforeach; ?>
                </p>

              <?php break; ?>
              <?php case 'checkbox': ?>

                <p class="form-control-static">
                  <?php echo $field['label'] . ' - ' . ( $field['value'] ? __( 'Ja', 'wpenon' ) : __( 'Nein', 'wpenon' ) ); ?>
                </p>

              <?php break; ?>
              <?php default: ?>

                <p class="form-control-static">
                  <?php echo $field['value']; ?>
                  <?php if( !empty( $field['unit'] ) ) : ?>
                    <span id="<?php echo $field_slug; ?>-unit" class="unit"><?php echo str_replace( 'm3', 'm&sup3;', $field['unit'] ); ?></span>
                  <?php endif; ?>
                </p>

              <?php break; ?>
          <?php endswitch; ?>
        <?php else : ?>
          <?php switch( $field['type'] ) :
            case 'headline':
              break; ?>
            <?php case 'select': ?>

              <select id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" class="form-control">
                <option value=""><?php _e( 'Bitte wählen...', 'wpenon' ); ?></option>
                <?php foreach( $field['options'] as $value => $label ) : ?>
                  <option value="<?php echo $value; ?>"<?php echo $value == $field['value'] ? ' selected' : ''; ?>><?php echo str_replace( array( 'm2', 'm3' ), array( 'm&sup2;', 'm&sup3;' ), $label ); ?></option>
                <?php endforeach; ?>
                <?php if ( ! empty( $field['disabled_options'] ) ) : ?>
                  <?php foreach( $field['disabled_options'] as $value => $label ) : ?>
                    <option value="<?php echo $value; ?>" disabled><?php echo str_replace( array( 'm2', 'm3' ), array( 'm&sup2;', 'm&sup3;' ), $label ); ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>

            <?php break; ?>
            <?php case 'multiselect': ?>

              <select id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>[]" class="form-control" multiple>
                <?php foreach( $field['options'] as $value => $label ) : ?>
                  <option value="<?php echo $value; ?>"<?php echo in_array( $value, $field['value'] ) ? ' selected' : ''; ?>><?php echo str_replace( array( 'm2', 'm3' ), array( 'm&sup2;', 'm&sup3;' ), $label ); ?></option>
                <?php endforeach; ?>
                <?php if ( ! empty( $field['disabled_options'] ) ) : ?>
                  <?php foreach( $field['disabled_options'] as $value => $label ) : ?>
                    <option value="<?php echo $value; ?>" disabled><?php echo str_replace( array( 'm2', 'm3' ), array( 'm&sup2;', 'm&sup3;' ), $label ); ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>

            <?php break; ?>
            <?php case 'radio': ?>

              <?php foreach( $field['options'] as $value => $label ) : ?>
                <label class="radio-inline">
                  <input type="radio" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" value="<?php echo $value; ?>"<?php echo $value == $field['value'] ? ' checked' : ''; ?>>
                  <?php echo str_replace( array( 'm2', 'm3' ), array( 'm&sup2;', 'm&sup3;' ), $label ); ?>
                </label>
              <?php endforeach; ?>

            <?php break; ?>
            <?php case 'multibox': ?>

              <?php foreach( $field['options'] as $value => $label ) : ?>
                <label class="checkbox-inline">
                  <input type="checkbox" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>[]" value="<?php echo $value; ?>"<?php echo in_array( $value, $field['value'] ) ? ' checked' : ''; ?>>
                  <?php echo str_replace( array( 'm2', 'm3' ), array( 'm&sup2;', 'm&sup3;' ), $label ); ?>
                </label>
              <?php endforeach; ?>

            <?php break; ?>
            <?php case 'checkbox': ?>

              <div class="checkbox">
                <label>
                  <input type="checkbox" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" value="1"<?php echo $field['value'] ? ' checked' : ''; ?>>
                  <?php echo $field['label']; ?>
                </label>
              </div>

            <?php break; ?>
            <?php case 'textarea': ?>

              <textarea id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" class="form-control" rows="3"<?php echo ( $field['readonly'] ? ' readonly' : '' ) . ( $field['required'] ? ' required' : '' ); ?>>
                <?php echo $field['value']; ?>
              </textarea>

            <?php break; ?>
            <?php case 'int': ?>

              <input type="number" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" class="form-control int-control" value="<?php echo $field['value']; ?>" step="1"<?php echo ( $field['min'] !== false ? ' min="' . $field['min'] . '"' : '' ) . ( $field['max'] !== false ? ' max="' . $field['max'] . '"' : '' ) . ( $field['readonly'] ? ' readonly' : '' ) . ( $field['required'] ? ' required' : '' ); ?>>

            <?php break; ?>
            <?php case 'float': ?>

              <input type="text" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" class="form-control float-control" value="<?php echo $field['value']; ?>"<?php echo ( $field['readonly'] ? ' readonly' : '' ) . ( $field['required'] ? ' required' : '' ); ?>>

            <?php break; ?>
            <?php case 'float_length':
            	  case 'float_length_wall': ?>

              <input type="text" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" class="form-control float-control" value="<?php echo $field['value']; ?>"<?php echo ( $field['readonly'] ? ' readonly' : '' ) . ( $field['required'] ? ' required' : '' ); ?>>

            <?php break; ?>
            <?php case 'zip': ?>

              <input type="text" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" class="form-control" value="<?php echo $field['value']; ?>" pattern="[0-9]{5}"<?php echo ( $field['readonly'] ? ' readonly' : '' ) . ( $field['required'] ? ' required' : '' ); ?>>

            <?php break; ?>
            <?php case 'image': ?>
            <div id="<?php echo $field_slug; ?>_image">
              <?php if( ! empty( $field['value'] ) ): ?>
                <img src="<?php echo $field['value']; ?>" /><br /><br />
              <?php endif; ?>
            </div>
            
            <button id="file-delete-<?php echo $field_slug; ?>" class="file-delete <?php echo empty( $field['value'] ) ? ' hidden': ''; ?>" data-image_name="<?php echo $field_slug; ?>" style="margin:20px 0;">Bild entfernen</button>

            <?php 
                if ( isset( $field['filetypes'] ) ) {
                  $filetypes = ' accept="' . implode( ', ', $field['filetypes'] ) . '"';
                }
            ?>
            <input type="hidden" id="<?php echo $field_slug; ?>_field" name="<?php echo $field_slug; ?>"  value="<?php echo $field['value']; ?>" />
            <input type="file" id="<?php echo $field_slug; ?>" class="file-control" value="<?php echo $field['value']; ?>" <?php echo ( $field['readonly'] ? ' readonly' : '' ) . ( $field['required'] ? ' required' : '' ) . $filetypes; ?>>

            <div class="percentage" style="display:none; margin-top: 10px; height: 10px; line-height: 10px; background-color:grey;">
    	        <div class="percentage-bar" style="width:50%; height: 10px; background-color: #3da81d;"></div>
	          </div>

            <?php break; ?>
            <?php default: ?>
           
              <input type="<?php echo $field['type']; ?>" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" class="form-control" value="<?php echo $field['value']; ?>"<?php echo ( $field['readonly'] ? ' readonly' : '' ) . ( $field['required'] ? ' required' : '' ); ?><?php if( ! empty( $field['placeholder'] ) ) echo ' placeholder="' . $field['placeholder'] . '"'; ?>>

            <?php break; ?>
          <?php endswitch; ?>

          <?php if( !empty( $field['unit'] ) ) : ?>
            <span id="<?php echo $field_slug; ?>-unit" class="unit"><?php echo str_replace( 'm3', 'm&sup3;', $field['unit'] ); ?></span>
          <?php endif; ?>

          <?php if( !empty( $field['text'] ) ) : ?>
            <span class="input-text"><?php echo $field['text']; ?></span>
          <?php endif; ?>

        <?php endif; ?>

        <?php if( !empty( $field['error'] ) ) : ?>
          <span class="help-block"><?php echo $field['error']; ?></span>
        <?php endif; ?>

        <?php if( !empty( $field['warning'] ) ) : ?>
          <span class="help-block"><?php echo $field['warning']; ?></span>
        <?php endif; ?>

        <?php do_action( 'wpenon_form_field_' . $field_slug . '_after', $field, $field_slug ); ?>

      </div>

      <?php if ( $field['type'] == 'headline' ) : ?>
        <?php if( ! empty( $field['description'] ) ) : ?>
          <div class="col-md-10 col-sm-10 col-xs-11 col-md-offset-1 col-sm-offset-1">
            <p class="form-headline-description"><?php echo $field['description']; ?></p>
          </div>
        <?php endif; ?>
      <?php else : ?>
        <div class="field-info col-md-1 col-sm-1 col-xs-1">
            <span class="is-done-checkmark label label-success">&#10004;</span>
            <?php if( ! empty( $field['description'] ) ) : ?>
              <span class="label label-info" data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="<?php echo $field['description']; ?>"></span>
            <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php do_action( 'wpenon_form_field_' . $field_slug . '_wrap_after', $field, $field_slug ); ?>

    </div>

  <?php else : ?>

    <input type="hidden" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" value="<?php echo $field['value']; ?>">

  <?php endif; ?>

<?php endforeach; ?>