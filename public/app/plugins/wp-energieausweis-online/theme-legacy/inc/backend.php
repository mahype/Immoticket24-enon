<?php
/**
 * This file contains important backend functionality.
 *
 * @package immoticketenergieausweis
 */

function immoticketenergieausweis_enqueue_admin_scripts() {
  $script_url = esc_url( plugins_url( '', dirname( __FILE__ ) ) );
  wp_enqueue_script( 'enon-general-script', $script_url . '/js/general.js', array( 'jquery' ), '2.1.5', true );
}
add_action( 'admin_enqueue_scripts', 'immoticketenergieausweis_enqueue_admin_scripts' );

function immoticketenergieausweis_add_options( $wpod ) {
  $wpod->add_components( array(
    'theme'                 => array(
      'screens'               => array(
        'it-theme-options'      => array(
          'title'                 => __( 'Theme-Einstellungen', 'immoticketenergieausweis' ),
          'label'                 => __( 'Theme-Einstellungen', 'immoticketenergieausweis' ),
          'capability'            => 'edit_theme_options',
          'tabs'                  => array(
            'it-theme'              => array(
              'title'                 => __( 'Allgemein', 'immoticketenergieausweis' ),
              'description'           => __( 'Hier können Sie allgemeine Einstellungen des Themes bearbeiten.', 'immoticketenergieausweis' ),
              'capability'            => 'edit_theme_options',
              'mode'                  => 'draggable',
              'sections'              => array(
                'header'                => array(
                  'title'                 => __( 'Kopfzeile', 'immoticketenergieausweis' ),
                  'fields'                => array(
                    'header_image'          => array(
                      'title'                 => __( 'Bild', 'immoticketenergieausweis' ),
                      'description'           => __( 'Legen Sie das Bild fest, das im Header angezeigt werden soll.', 'immoticketenergieausweis' ),
                      'type'                  => 'media',
                    ),
                    'header_caption'        => array(
                      'title'                 => __( 'Beschriftung', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                    'header_url'            => array(
                      'title'                 => __( 'Link-URL', 'immoticketenergieausweis' ),
                      'type'                  => 'url',
                    ),
                    'header_anchor'         => array(
                      'title'                 => __( 'Link-Beschriftung', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                    'header_new_tab'        => array(
                      'title'                 => __( 'Link-Ziel', 'immoticketenergieausweis' ),
                      'type'                  => 'checkbox',
                      'label'                 => __( 'Link in neuem Tab öffnen?', 'immoticketenergieausweis' ),
                    ),
                  ),
                ),
                'footer'                => array(
                  'title'                 => __( 'Fußbereich', 'immoticketenergieausweis' ),
                  'fields'                => array(
                    'footer-contactform'    => array(
                      'title'                 => __( 'Kontaktformular', 'immoticketenergieausweis' ),
                      'description'           => __( 'Wählen Sie das Kontaktformular aus, welches im Fußbereich angezeigt werden soll.', 'immoticketenergieausweis' ),
                      'type'                  => 'select',
                      'options'               => immoticketenergieausweis_get_contact_forms(),
                    ),
                  ),
                ),
                'special_pages'         => array(
                  'title'                 => __( 'Spezielle Seiten', 'immoticketenergieausweis' ),
                  'fields'                => array(
                    'page_for_privacy'      => array(
                      'title'                 => __( 'Seite für Datenschutzerklärung', 'immoticketenergieausweis' ),
                      'description'           => __( 'Wählen Sie die Seite aus, welche die Datenschutzerklärung enthält.', 'immoticketenergieausweis' ),
                      'type'                  => 'select',
                      'options'               => array(
                        'posts'                 => 'page',
                      ),
                    ),
                    'page_for_terms'      => array(
                      'title'                 => __( 'AGB', 'immoticketenergieausweis' ),
                      'description'           => __( 'Wählen Sie die Seite aus, welche die AGB enthält.', 'immoticketenergieausweis' ),
                      'type'                  => 'select',
                      'options'               => array(
                        'posts'                 => 'page',
                      ),
                    ),
                    'page_for_withdrawal'   => array(
	                    'title'                 => __( 'Widerrufsbelehrung', 'immoticketenergieausweis' ),
	                    'description'           => __( 'Wählen Sie die Seite aus, welche die Widerrufsbelehrung enthält.', 'immoticketenergieausweis' ),
	                    'type'                  => 'select',
	                    'options'               => array(
		                    'posts'                 => 'page',
	                    ),
                    ),
                    'page_for_successful_payment'   => array(
	                    'title'                 => __( 'Erfolgreiche Zahlung', 'immoticketenergieausweis' ),
	                    'description'           => __( 'Wählen Sie die Seite aus, die nach einer erfolgreichen Zahlung angezeigt werden soll.', 'immoticketenergieausweis' ),
	                    'type'                  => 'select',
	                    'options'               => array(
		                    'posts'                 => 'page',
	                    ),
                    ),
                    'page_for_failed_payment'   => array(
	                    'title'                 => __( 'Fehlgeschlagene Zahlung', 'immoticketenergieausweis' ),
	                    'description'           => __( 'Wählen Sie die Seite aus, die nach einer fehlgeschlagenen Zahlung angezeigt werden soll.', 'immoticketenergieausweis' ),
	                    'type'                  => 'select',
	                    'options'               => array(
		                    'posts'                 => 'page',
	                    ),
                    ),
                  ),
                ),
              ),
            ),
            /*'it-slider'             => array(
              'title'                 => __( 'Slider', 'immoticketenergieausweis' ),
              'description'           => __( 'Hier können Sie den Slider im Header der Website bearbeiten.', 'immoticketenergieausweis' ),
              'capability'            => 'edit_theme_options',
              'mode'                  => 'draggable',
              'sections'              => array(
                'contents'              => array(
                  'title'                 => __( 'Inhalt', 'immoticketenergieausweis' ),
                  'fields'                => array(
                    'data'                  => array(
                      'title'                 => __( 'Bilder und Beschriftung', 'immoticketenergieausweis' ),
                      'description'           => __( 'Legen Sie Bilder, die jeweilige Beschriftung und Links (optional) fest. Maximal sind 10 Inhalte möglich.', 'immoticketenergieausweis' ),
                      'type'                  => 'repeatable',
                      'repeatable'            => array(
                        'limit'                 => 10,
                        'fields'                => array(
                          'image'                 => array(
                            'title'                 => __( 'Bild', 'immoticketenergieausweis' ),
                            'type'                  => 'media',
                          ),
                          'caption'               => array(
                            'title'                 => __( 'Beschriftung', 'immoticketenergieausweis' ),
                            'type'                  => 'text',
                          ),
                          'url'                   => array(
                            'title'                 => __( 'Link-URL', 'immoticketenergieausweis' ),
                            'type'                  => 'url',
                          ),
                          'anchor'                => array(
                            'title'                 => __( 'Link-Beschriftung', 'immoticketenergieausweis' ),
                            'type'                  => 'text',
                          ),
                          'new_tab'               => array(
                            'title'                 => __( 'Link-Ziel', 'immoticketenergieausweis' ),
                            'type'                  => 'checkbox',
                            'label'                 => __( 'Link in neuem Tab öffnen?', 'immoticketenergieausweis' ),
                          ),
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            ),*/
            'it-iframe'             => array(
              'title'                 => __( 'IFrame', 'immoticketenergieausweis' ),
              'description'           => __( 'Hier können Sie die Einstellungen zum Energieausweis-IFrame verwalten.', 'immoticketenergieausweis' ),
              'capability'            => 'manage_options',
              'mode'                  => 'draggable',
              'sections'              => array(
                'tokens'                => array(
                  'title'                 => __( 'Zugriffsschlüssel', 'immoticketenergieausweis' ),
                  'description'           => __( 'Dies ist die Liste der Zugriffsschlüssel für das IFrame', 'immoticketenergieausweis' ),
                  'fields'                => array(
                    'tokens'                => array(
                      'title'                 => __( 'Schlüssel', 'immoticketenergieausweis' ),
                      'type'                  => 'repeatable',
                      'repeatable'            => array(
                        'fields'                => array(
                          'email'                 => array(
                            'title'                 => __( 'Email-Adresse', 'immoticketenergieausweis' ),
                            'type'                  => 'email',
                          ),
                          'token'                 => array(
                            'title'                 => __( 'Zugriffsschlüssel zur Email', 'immoticketenergieausweis' ),
                            'type'                  => 'text',
                            'default'               => md5( microtime() ),
                          ),
                          'customer_edit_url'                 => array(
	                          'title'                 => __( 'Kunden Editierseite', 'immoticketenergieausweis' ),
	                          'type'                  => 'text',
	                          'default'               => '',
                          ),
                          'payment_successful_url'                 => array(
	                          'title'                 => __( 'Zahlung Erfolgreich', 'immoticketenergieausweis' ),
	                          'type'                  => 'text',
	                          'default'               => '',
                          ),
                          'payment_failed_url'                 => array(
	                          'title'                 => __( 'Zahlung Fehlgeschlagen', 'immoticketenergieausweis' ),
	                          'type'                  => 'text',
	                          'default'               => '',
                          ),
                          'email_from_address'                 => array(
	                          'title'                 => __( 'Email Absender Adresse', 'immoticketenergieausweis' ),
	                          'type'                  => 'text',
	                          'default'               => '',
                          ),
                          'email_from_name'                 => array(
	                          'title'                 => __( 'Email Absender Name', 'immoticketenergieausweis' ),
	                          'type'                  => 'text',
	                          'default'               => '',
                          ),
                          'email_footer'                 => array(
	                          'title'                 => __( 'Email Footer', 'immoticketenergieausweis' ),
	                          'type'                  => 'textarea',
	                          'default'               => '',
                          ),
                          'sitename'                 => array(
	                          'title'                 => __( 'Seitennname', 'immoticketenergieausweis' ),
	                          'type'                  => 'text',
	                          'default'               => '',
                          ),
                          'active'                => array(
	                          'title'                 => __( 'Aktiv?', 'immoticketenergieausweis' ),
	                          'type'                  => 'select',
	                          'options'               => array(
		                          'yes'                   => __( 'Ja', 'immoticketenergieausweis' ),
		                          'no'                    => __( 'Nein', 'immoticketenergieausweis' ),
	                          ),
                          ),
                        ),
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
        'it-business-data'      => array(
          'title'                 => __( 'Firmeninformationen', 'immoticketenergieausweis' ),
          'label'                 => __( 'Firmeninformationen', 'immoticketenergieausweis' ),
          'capability'            => 'edit_theme_options',
          'tabs'                  => array(
            'it-business'           => array(
              'title'                 => __( 'Firmeninformationen', 'immoticketenergieausweis' ),
              'description'           => __( 'Hier können Sie Ihre Firmendaten bearbeiten und aktualisieren.', 'immoticketenergieausweis' ),
              'capability'            => 'edit_theme_options',
              'mode'                  => 'draggable',
              'sections'              => array(
                'general'               => array(
                  'title'                 => __( 'Allgemeine Daten', 'immoticketenergieausweis' ),
                  'fields'                => array(
                    'firmenname'            => array(
                      'title'                 => __( 'Firmenname', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                    'geschaeftsfuehrer'     => array(
                      'title'                 => __( 'Geschäftsführer', 'immoticketenergieausweis' ),
                      'type'                  => 'repeatable',
                      'repeatable'            => array(
                        'limit'                 => 5,
                        'fields'                => array(
                          'name'                  => array(
                            'title'                 => __( 'Name', 'immoticketenergieausweis' ),
                            'type'                  => 'text',
                          ),
                        ),
                      ),
                    ),
                    'logo'                  => array(
                      'title'                 => __( 'Logo', 'immoticketenergieausweis' ),
                      'type'                  => 'media',
                      'mime_types'            => 'image',
                    ),
                    'founded'               => array(
                      'title'                 => __( 'Gründungsjahr', 'immoticketenergieausweis' ),
                      'type'                  => 'number',
                      'min'                   => 1900,
                      'max'                   => absint( date( 'Y' ) ),
                      'step'                  => 1,
                    ),
                  ),
                ),
                'contact'               => array(
                  'title'                 => __( 'Kontaktdaten', 'immoticketenergieausweis' ),
                  'fields'                => array(
                    'strassenr'             => array(
                      'title'                 => __( 'Straße und Hausnummer', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                    'plz'                   => array(
                      'title'                 => __( 'Postleitzahl', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                    'ort'                   => array(
                      'title'                 => __( 'Ort', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                    'telefon'               => array(
                      'title'                 => __( 'Telefon', 'immoticketenergieausweis' ),
                      'type'                  => 'tel',
                    ),
                    'telefon-maschinell'    => array(
                      'title'                 => __( 'Telefon (maschinell)', 'immoticketenergieausweis' ),
                      'description'           => __( 'Geben Sie hier die Telefonnummer im maschinenlesbaren Format ein (inklusive aller Vorwahlen, startet in Deutschland mit +49).', 'immoticketenergieausweis' ),
                      'type'                  => 'tel',
                    ),
                    'email'                 => array(
                      'title'                 => __( 'Email-Adresse', 'immoticketenergieausweis' ),
                      'type'                  => 'email',
                      'default'               => get_bloginfo( 'admin_email' ),
                    ),
                    'website'               => array(
                      'title'                 => __( 'Website', 'immoticketenergieausweis' ),
                      'type'                  => 'url',
                      'default'               => home_url(),
                    ),
                  ),
                ),
                'opened'                => array(
                  'title'                 => __( 'Öffnungszeiten', 'immoticketenergieausweis' ),
                  'fields'                => array(
                    'oeffnungszeiten'       => array(
                      'title'                 => __( 'Haupt-Öffnungszeiten', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                      'default'               => 'Mo.-Fr. 08:00 bis 16:00 Uhr',
                    ),
                    'oeffnungszeiten-maschinell'=> array(
                      'title'                 => __( 'Haupt-Öffnungszeiten (maschinell)', 'immoticketenergieausweis' ),
                      'description'           => sprintf( __( 'Geben Sie hier Ihre Öffnungszeiten in maschinenlesbarem Format ein. Als Hilfe zur Formatierung können Sie sich %1$sdiese Seite%2$s ansehen.', 'immoticketenergieausweis' ), '<a href="http://schema.org/openingHours" target="_blank">', '</a>' ),
                      'type'                  => 'text',
                      'default'               => 'Mo-Fr 08:00-16:00',
                    ),
                  ),
                ),
                'money'                 => array(
                  'title'                 => __( 'Kontoinformationen', 'immoticketenergieausweis' ),
                  'fields'                => array(
                    'kontoinhaber'          => array(
                      'title'                 => __( 'Kontoinhaber', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                    'kontonummer'           => array(
                      'title'                 => __( 'Kontonummer', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                    'bankleitzahl'          => array(
                      'title'                 => __( 'Bankleitzahl', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                    'kreditinstitut'        => array(
                      'title'                 => __( 'Kreditinstitut', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                    'iban'                  => array(
                      'title'                 => __( 'IBAN', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                    'bic'                   => array(
                      'title'                 => __( 'BIC (SWIFT)', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                  ),
                ),
                'legal'                 => array(
                  'title'                 => __( 'Rechtliche Informationen', 'immoticketenergieausweis' ),
                  'fields'                => array(
                    'steuernr'              => array(
                      'title'                 => __( 'Steuernummer', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                    'ustidnr'               => array(
                      'title'                 => __( 'USt-Identifikationsnummer', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                    'handelsregister'       => array(
                      'title'                 => __( 'Handelsregister', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                    'berufsaufsicht'        => array(
                      'title'                 => __( 'Berufsaufsichtsbehörde', 'immoticketenergieausweis' ),
                      'type'                  => 'text',
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
  ), 'immoticketenergieausweis' );
}
add_action( 'wpod', 'immoticketenergieausweis_add_options', 10, 1 );

function immoticketenergieausweis_get_contact_forms()
{
  $posts = get_posts( array(
    'post_type'       => 'wpcf7_contact_form',
    'post_status'     => 'any',
    'posts_per_page'  => -1,
    'offset'          => 0,
    'orderby'         => 'title',
    'order'           => 'ASC',
  ) );
  $forms = array();
  foreach( $posts as $post )
  {
    $forms[ $post->ID ] = get_the_title( $post->ID );
  }
  return $forms;
}

function immoticketenergieausweis_add_post_meta_box( $post_type ) {
  if ( 'post' !== $post_type ) {
    return;
  }

  add_meta_box( 'immoticketenergieausweis', __( 'Erweitert', 'immoticketenergieausweis' ), 'immoticketenergieausweis_render_post_meta_box', null, 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'immoticketenergieausweis_add_post_meta_box', 1, 1 );

function immoticketenergieausweis_render_post_meta_box( $post ) {
  ?>
  <table class="form-table">
    <tr>
      <th scope="row">
        <label for="immoticketenergieausweis_ad_heading"><?php _e( 'Ad: Überschrift', 'immoticketenergieausweis' ); ?></label>
      </th>
      <td>
        <input type="text" id="immoticketenergieausweis_ad_heading" name="meta_input[immoticketenergieausweis_ad_heading]" class="widefat" value="<?php echo esc_attr( get_post_meta( $post->ID, 'immoticketenergieausweis_ad_heading', true ) ); ?>" />
        <p class="description"><?php _e( 'Geben Sie eine Überschrift für die Werbung ein.', 'immoticketenergieausweis' ); ?></p>
      </td>
    </tr>
    <tr>
      <th scope="row">
        <label for="immoticketenergieausweis_ad_content"><?php _e( 'Ad: Inhalt', 'immoticketenergieausweis' ); ?></label>
      </th>
      <td>
        <textarea id="immoticketenergieausweis_ad_content" name="meta_input[immoticketenergieausweis_ad_content]" class="widefat" rows="8"><?php echo esc_textarea( get_post_meta( $post->ID, 'immoticketenergieausweis_ad_content', true ) ); ?></textarea>
        <p class="description"><?php printf( __( 'Geben Sie den Werbeinhalt ein. Das kann entweder ein kurzer Text sein oder eine Video-URL (z.B. von YouTube). Sie können zudem den Shortcode %s verwenden.', 'immoticketenergieausweis' ), '<code>[button]</code>' ); ?></p>
      </td>
    </tr>
    <tr>
      <th scope="row">
        <label for="immoticketenergieausweis_post_thumbnail_source"><?php _e( 'Bildquelle', 'immoticketenergieausweis' ); ?></label>
      </th>
      <td>
        <input type="text" id="immoticketenergieausweis_post_thumbnail_source" name="meta_input[immoticketenergieausweis_post_thumbnail_source]" class="widefat" value="<?php echo esc_attr( get_post_meta( $post->ID, 'immoticketenergieausweis_post_thumbnail_source', true ) ); ?>" />
        <p class="description"><?php _e( 'Geben Sie die Quelle des Beitragsbilds an.', 'immoticketenergieausweis' ); ?></p>
      </td>
    </tr>
  </table>
  <?php
}

function immoticketenergieausweis_customize_register( $wp_customize ) {
  $wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
  $wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
}
add_action( 'customize_register', 'immoticketenergieausweis_customize_register', 11, 1 );

function immoticketenergieausweis_customize_preview_init() {
  wp_enqueue_script( 'immoticketenergieausweis-customize-preview', IMMOTICKETENERGIEAUSWEIS_THEME_URL . '/assets/customize-preview.js', array( 'customize-preview' ), IMMOTICKETENERGIEAUSWEIS_THEME_VERSION, true );
}
add_action( 'customize_preview_init', 'immoticketenergieausweis_customize_preview_init' );

function immoticketenergieausweis_before_tinymce( $settings ) {
  ob_start();
}
add_action( 'wp_tiny_mce_init', 'immoticketenergieausweis_before_tinymce', 10, 1 );

function immoticketenergieausweis_after_tinymce( $settings ) {
  $output = ob_get_clean();

  $add = '<div class="link-target"><label><span></span><input type="checkbox" id="wp-link-is-button" /> ' . __( 'Link als Button einfügen', 'immoticketenergieausweis' ) . '</label></div>';
  $search = '<div class="link-target">';

  if ( false !== strpos( $output, $search ) ) {
    $output = str_replace( $search, $add . $search, $output );
  }

  echo $output;
}
add_action( 'after_wp_tiny_mce', 'immoticketenergieausweis_after_tinymce', 10, 1 );

function immoticketenergieausweis_tinymce_scripts( $data ) {
  wp_enqueue_script( 'immoticketenergieausweis-wplink', IMMOTICKETENERGIEAUSWEIS_THEME_URL . '/assets/wplink.js', array( 'wplink' ), IMMOTICKETENERGIEAUSWEIS_THEME_VERSION );
}
add_action( 'wp_enqueue_editor', 'immoticketenergieausweis_tinymce_scripts', 10, 1 );

