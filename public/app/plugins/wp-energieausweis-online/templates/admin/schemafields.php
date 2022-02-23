<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */
?>

<?php if ( count( $data ) > 0 ): ?>

  <table class="form-table">

    <tbody>

      <?php foreach( $data as $field_slug => $field ) : ?>

        <?php if( $field['type'] != 'hidden' ) : ?>

          <tr id="<?php echo $field_slug; ?>-wrap" class="form-group<?php echo ( !empty( $field['unit'] ) ? ' has-unit' : '' ) . ( !empty( $field['warning'] ) ? ' has-warning' : '' ) . ( !empty( $field['error'] ) ? ' has-error' : '' ); ?>"<?php echo $field['display'] ? '' : ' style="display:none;"'; ?>>

            <?php do_action( 'wpenon_form_field_' . $field_slug . '_wrap_before', $field ); ?>

            <?php if( $field['type'] == 'headline' ) : ?>
              <th class="form-headline" scope="row"><?php echo $field['label']; ?></th>
            <?php else : ?>
              <th scope="row">
                <?php if( !empty( $field['label'] ) && $field['type'] != 'checkbox' ) : ?>
                  <?php echo $field['label']; ?>
                  <?php if( $field['required'] ) : ?>
                    <span class="required">*</span>
                  <?php endif; ?>
                <?php endif; ?>
              </th>
            <?php endif; ?>

            <td>

              <?php do_action( 'wpenon_form_field_' . $field_slug . '_before', $field ); ?>

              <?php switch( $field['type'] ) :
                case 'headline':
                  break; ?>
                <?php case 'select': ?>

                  <select id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>">
                    <?php foreach( $field['options'] as $value => $label ) : ?>
                      <option value="<?php echo $value; ?>"<?php echo $value == $field['value'] ? ' selected' : ''; ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                    <?php if ( ! empty( $field['disabled_options'] ) ) : ?>
                      <?php foreach( $field['disabled_options'] as $value => $label ) : ?>
                        <option value="<?php echo $value; ?>" disabled><?php echo str_replace( array( 'm2', 'm3' ), array( 'm&sup2;', 'm&sup3;' ), $label ); ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>

                <?php break; ?>
                <?php case 'multiselect': ?>

                  <select id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>[]" multiple>
                    <?php foreach( $field['options'] as $value => $label ) : ?>
                      <option value="<?php echo $value; ?>"<?php echo in_array( $value, $field['value'] ) ? ' selected' : ''; ?>><?php echo $label; ?></option>
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
                    <label>
                      <input type="radio" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" value="<?php echo $value; ?>"<?php echo $value == $field['value'] ? ' checked' : ''; ?>>
                      <?php echo $label; ?>
                    </label>
                  <?php endforeach; ?>

                <?php break; ?>
                <?php case 'multibox': ?>

                  <?php foreach( $field['options'] as $value => $label ) : ?>
                    <label>
                      <input type="checkbox" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>[]" value="<?php echo $value; ?>"<?php echo in_array( $value, $field['value'] ) ? ' checked' : ''; ?>>
                      <?php echo $label; ?>
                    </label>
                  <?php endforeach; ?>

                <?php break; ?>
                <?php case 'checkbox': ?>

                  <label>
                    <input type="checkbox" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" value="1"<?php echo $field['value'] ? ' checked' : ''; ?>>
                    <?php echo $field['label']; ?>
                  </label>

                <?php break; ?>
                <?php case 'textarea': ?>

                  <textarea id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" rows="3"<?php echo ( $field['readonly'] ? ' readonly' : '' ) . ( $field['required'] ? ' required' : '' ); ?>>
                    <?php echo $field['value']; ?>
                  </textarea>

                <?php break; ?>
                <?php case 'int': ?>

                  <input type="number" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" class="int-control" value="<?php echo $field['value']; ?>" step="1"<?php echo ( $field['min'] !== false ? ' min="' . $field['min'] . '"' : '' ) . ( $field['max'] !== false ? ' max="' . $field['max'] . '"' : '' ) . ( $field['readonly'] ? ' readonly' : '' ) . ( $field['required'] ? ' required' : '' ); ?>>

                <?php break; ?>
                <?php case 'float': ?>

                  <input type="text" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" class="float-control" value="<?php echo $field['value']; ?>"<?php echo ( $field['readonly'] ? ' readonly' : '' ) . ( $field['required'] ? ' required' : '' ); ?>>

                <?php break; ?>
                <?php case 'float_length': ?>

                  <input type="text" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" class="float-control" value="<?php echo $field['value']; ?>"<?php echo ( $field['readonly'] ? ' readonly' : '' ) . ( $field['required'] ? ' required' : '' ); ?>>

                <?php break; ?>
                <?php case 'zip': ?>

                  <input type="text" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" value="<?php echo $field['value']; ?>" pattern="[0-9]{5}"<?php echo ( $field['readonly'] ? ' readonly' : '' ) . ( $field['required'] ? ' required' : '' ); ?>>

                <?php break; ?>
                <?php case 'image': ?>

<!-- RAW Data
<?php 
global $post;
print_r( $field_slug );
print_r( $post ); 
print_r( $field ); 
print_r( get_post_meta( $post->ID, $field_slug, false ) );
print_r( get_post_meta( $post->ID ) );
?>
-->
            
                <div id="<?php echo $field_slug; ?>_image">
                  <?php if( ! empty( $field['value'] ) ): ?>
                    <img src="<?php echo $field['value']; ?>" />
                  <?php endif; ?>
                </div>

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

                  <input type="<?php echo $field['type']; ?>" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" value="<?php echo $field['value']; ?>"<?php echo ( $field['readonly'] ? ' readonly' : '' ) . ( $field['required'] ? ' required' : '' ); ?>>

                <?php break; ?>
              <?php endswitch; ?>

              <?php if( !empty( $field['unit'] ) ) : ?>
                <span id="<?php echo $field_slug; ?>-unit" class="unit"><?php echo $field['unit']; ?></span>
              <?php endif; ?>

              <?php if( !empty( $field['error'] ) ) : ?>
                <p class="description field-error"><?php echo $field['error']; ?></p>
              <?php endif; ?>

              <?php if( !empty( $field['warning'] ) ) : ?>
                <p class="description field-warning"><?php echo $field['warning']; ?></p>
              <?php endif; ?>

              <?php do_action( 'wpenon_form_field_' . $field_slug . '_after', $field ); ?>

              <?php if( !empty( $field['description'] ) ) : ?>
                <p class="description"><?php echo $field['description']; ?></p>
              <?php endif; ?>

            </td>

            <?php do_action( 'wpenon_form_field_' . $field_slug . '_wrap_after', $field ); ?>

          </tr>

        <?php else : ?>

          <input type="hidden" id="<?php echo $field_slug; ?>" name="<?php echo $field_slug; ?>" value="<?php echo $field['value']; ?>">

        <?php endif; ?>

      <?php endforeach; ?>

    </tbody>

  </table>

<?php endif; ?>
