<?php

/** add admin user for dev environment */
if(!empty($_ENV['VIRTUAL_HOST']) && $_ENV['VIRTUAL_HOST'] === 'enon.test') {
	add_action('init', function () {
			$user  = 'admin';
			$pass  = 'admin';
			$email = 'admin@enon.test';
			//if a username with the email ID does not exist, create a new user account
			if (!username_exists($user) && !email_exists($email)) {
				$user_id = wp_create_user($user, $pass, $email);
				$user    = new WP_User($user_id);
				//Set the new user as a Admin
				$user->set_role('administrator');
			}
		}
	);
}
function immoticketenergieausweis_is_element_empty( $element )
{
  $element = trim( $element );
  return empty( $element ) ? false : true;
}

function immoticketenergieausweis_get_option( $option, $key = null ) {
  if ( null === $key && function_exists( 'wpod_get_options' ) ) {
    return wpod_get_options( $option );
  } elseif ( function_exists( 'wpod_get_option' ) ) {
    return wpod_get_option( $option, $key );
  }

  return false;
}

function immoticketenergieausweis_remove_width_attribute( $html ) {
   $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
   return $html;
}
add_filter( 'post_thumbnail_html', 'immoticketenergieausweis_remove_width_attribute', 10 );

function immoticketenergieausweis_comment_form_top() {
  echo '<div class="form-horizontal">';
}

function immoticketenergieausweis_comment_form_bottom() {
  echo '</div>';
}

require_once dirname( __FILE__ ) . '/theme-legacy/inc/constants.php';
require_once dirname( __FILE__ ) . '/theme-legacy/inc/theme-setup.php';
require_once dirname( __FILE__ ) . '/theme-legacy/inc/backend.php';
require_once dirname( __FILE__ ) . '/theme-legacy/inc/frontend.php';
require_once dirname( __FILE__ ) . '/theme-legacy/inc/optimizepress-compat.php';

function immoticketenergieausweis_head_cleanup() {
  remove_action( 'wp_head', 'feed_links', 2 );
  remove_action( 'wp_head', 'feed_links_extra', 3 );
  remove_action( 'wp_head', 'rsd_link' );
  remove_action( 'wp_head', 'wlwmanifest_link' );
  remove_action( 'wp_head', 'wp_generator' );
  remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
  remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
  remove_action( 'wp_head', 'wp_oembed_add_host_js' );
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

  add_filter( 'style_loader_src', 'immoticketenergieausweis_strip_version_arg', 10, 2 );
  add_filter( 'script_loader_src', 'immoticketenergieausweis_strip_version_arg', 10, 2 );
}
add_action( 'wp_loaded', 'immoticketenergieausweis_head_cleanup' );

function immoticketenergieausweis_strip_version_arg( $src, $handle ) {
  return remove_query_arg( 'ver', $src );
}

add_filter( 'xmlrpc_enabled', '__return_false' );

function immoticketenergieausweis_the_title( $title, $id ) {
  if ( 'download' != get_post_type( $id ) || is_admin() ) {
    return $title;
  }

  if ( edd_is_checkout() ) {
    $type = get_post_meta( $id, 'wpenon_type', true );
    if ( is_string( $type ) && substr( $type, 0, 1 ) == 'b' ) {
      $title = sprintf( __( 'Bedarfsausweis %s', 'immoticketenergieausweis' ), $title );
    } else {
      $title = sprintf( __( 'Verbrauchsausweis %s', 'immoticketenergieausweis' ), $title );
    }
    return $title;
  }

  if ( ! function_exists( 'get_the_ID' ) || get_the_ID() != $id ) {
    return $title;
  }

  $action = isset( $_GET['action'] ) ? $_GET['action'] : 'overview';
  if ( ! in_array( $action, array( 'overview', 'edit', 'purchase' ) ) ) {
    return $title;
  }

  return sprintf( __( 'Mein Energieausweis %s', 'immoticketenergieausweis' ), '<small>(' . $title . ')</small>' );
}
add_filter( 'the_title', 'immoticketenergieausweis_the_title', 10, 2 );

function immoticketenergieausweis_email_signature( $text ) {
  return 'Mit freundlichen Grüßen<br/><br/>Ihr Team von Immoticket24.de';
}
add_filter( 'wpenon_email_signature', 'immoticketenergieausweis_email_signature' );

function immoticketenergieausweis_empty_cart_message( $message ) {
  $message .= '<br><br>';

  $message .= __( '<p>Um Ihre Energieausweis-Bestellung abschließen zu können, müssen Sie hierfür Cookies akzeptieren, damit die eingegebenen Angaben für die Bestellung übernommen werden können.</p>

<p>Dies können Sie in Ihrem Browser wie folgt erledigen:</p>

<p><strong>Safari:</strong></p>
<p>Einstellungen => Datenschutz => "Alle Cookies blockieren" deaktivieren</p>

<p><strong>Firefox:</strong></p>
<p>Einstellungen => Datenschutz & Sicherheit => "Annehmen von Cookies und Website-Daten" aktivieren</p>

<p><strong>Chrome:</strong></p>
<p>Einstellungen => Erweitert => Datenschutz & Sicherheit => "Bei Browserzugriffen eine Do not track-Anforderung mitsenden" deaktivieren</p>', 'immoticketenergieausweis' );

  $message .= __( '<p>Um Ihr Energieausweis-Projekt erneut aufzurufen, verwenden Sie bitte den Zugriffs-Link, den Sie bei der Erstellung des Projekts per Email erhalten haben.</p>', 'immoticketenergieausweis' );
  $message .= '<p><button onclick="javascript:history.back();" class="btn btn-primary">' . __( 'Zurück zum Energieausweis', 'immoticketenergieausweis' ) . '</button></p>';

  return $message;
}
add_filter( 'edd_empty_cart_message', 'immoticketenergieausweis_empty_cart_message' );

add_filter( 'wpenon_enable_purchase_placeholders', '__return_false' );

function immoticketenergieausweis_payment_active_statuses( $statuses ) {
  return array( 'publish' );
}
add_filter( 'wpenon_payment_active_statuses', 'immoticketenergieausweis_payment_active_statuses' );

function immoticketenergieausweis_terms_agreement() {
  if ( edd_get_option( 'show_agree_to_terms', false ) ) {
    $agree_text  = edd_get_option( 'agree_text', '' );
    $agree_label = edd_get_option( 'agree_label', __( 'Ich akzeptiere die AGB von Immoticket24.', 'immoticketenergieausweis' ) );
    ?>
      <fieldset id="edd_terms_agreement">
        <div id="edd_terms" style="display:none;">
          <?php
            do_action( 'edd_before_terms' );
            echo wpautop( stripslashes( $agree_text ) );
            do_action( 'edd_after_terms' );
          ?>
        </div>
        <div id="edd_show_terms">
          <a href="#" class="edd_terms_links"><?php _e( 'AGB anzeigen', 'immoticketenergieausweis' ); ?></a>
          <a href="#" class="edd_terms_links" style="display:none;"><?php _e( 'AGB ausblenden', 'immoticketenergieausweis' ); ?></a>
        </div>
        <label for="edd_agree_to_terms"><?php echo stripslashes( $agree_label ); ?></label>
        <input name="edd_agree_to_terms" class="required" type="checkbox" id="edd_agree_to_terms" value="1"/>
      </fieldset>
    <?php
  }
}
remove_action( 'edd_purchase_form_before_submit', 'edd_terms_agreement' );
add_action( 'edd_purchase_form_before_submit', 'immoticketenergieausweis_terms_agreement' );

function immoticketenergieausweis_widerrufsformular_shortcode( $atts = array() ) {
  $content = '– An [hier ist der Name, die Anschrift und gegebenenfalls die Faxnummer und E-MailAdresse des Unternehmers durch den Unternehmer einzufügen]:

– Hiermit widerrufe(n) ich/wir (*) den von mir/uns (*) abgeschlossenen Vertrag über den Kauf der folgenden Waren (*)/ die Erbringung der folgenden Dienstleistung (*)
– Bestellt am (*)/erhalten am (*)
– Name des/der Verbraucher(s)
– Anschrift des/der Verbraucher(s)
– Unterschrift des/der Verbraucher(s) (nur bei Mitteilung auf Papier)
– Datum

(*) Unzutreffendes streichen.';

  return '<textarea class="form-control" rows="12" readonly onclick="this.select();">' . wp_strip_all_tags( $content ) . '</textarea>';
}
add_shortcode( 'widerrufsformular', 'immoticketenergieausweis_widerrufsformular_shortcode' );

function immoticketenergieausweis_lieferzeiten_notice() {
  echo '<p class="text-center">' . __( 'Sie können Ihre Bestellung sofort nach Zahlungseingang herunterladen. Dazu benachrichtigen wir Sie per Email.', 'immoticketenergieausweis' ) . '</p>';
}
add_action( 'edd_checkout_form_top', 'immoticketenergieausweis_lieferzeiten_notice', 5 );

function immoticketenergieausweis_payment_icons() {
  ?>
  <style type="text/css">
    #edd_checkout_form_wrap legend {
      margin: 0;
    }
    #edd_checkout_form_wrap select.edd-select {
      display: inline-block;
    }
    .edd-payment-icons,
    label.edd-gateway-option {
      display: none !important;
    }
    .immoticket24-payment-buttons button {
      margin-right: 10px;
    }
    .immoticket24-payment-buttons button.active {
      border-color: #5cb85c;
      border-width: 3px;
    }
    .immoticket24-payment-buttons button img {
      width: 100px;
    }
    .is-done-checkmark {
      display: inline;
      margin-left: 10px;
    }

    #it24-stripe-button img{
	    height: 50px;
	    width: 170px;
    }
  </style>
  <script type="text/javascript">
    jQuery( document ).ready( function( $ ) {
      function check_active() {
        var value = $( 'input[name="payment-mode"]:checked' ).val();
        if ( 'stripe_sepa' == value ) {
          value = 'lastschrift';
        }

        $( '.immoticket24-payment-buttons button' ).removeClass( 'active' );
        $( '#it24-' + value + '-button' ).addClass( 'active' );
      }

      check_active();

      $( 'input[name="payment-mode"]' ).on( 'change', function( e ) {
	     $(document).trigger('wpenon.update_payment_method');
         check_active();
      });

      $( document ).on( 'click', '.immoticket24-payment-buttons button', function( e ) {
        var value = $( this ).attr( 'id' ).replace( 'it24-', '' ).replace( '-button', '' );
        if ( 'lastschrift' == value ) {
          value = 'stripe_sepa';
        }

        $( 'input[name="payment-mode"]' ).prop( 'checked', false );
        $( '#edd-gateway-' + value ).prop( 'checked', true );
        $( '#edd-gateway-' + value ).trigger( 'change' );
      });

      function maybeAddDoneCheckmark() {
        var $element = $( this );

        if ( $element.val() && $element.val().length ) {
          if ( ! $element.next( '.is-done-checkmark' ).length ) {
            $element.after( '<span class="is-done-checkmark label label-success">✔</span>' );
          }
        } else {
          if ( $element.next( '.is-done-checkmark' ).length ) {
            $element.next( '.is-done-checkmark' ).remove();
          }
        }
      }

      $( '#edd_checkout_form_wrap' ).on( 'change', 'input[type="text"], select', maybeAddDoneCheckmark );
      $( '#edd_checkout_form_wrap' ).on( 'focusout', 'input[type="text"], select', maybeAddDoneCheckmark );
      $( '#edd_checkout_form_wrap' ).find( 'input[type="text"], select' ).each( maybeAddDoneCheckmark );
      $( 'body' ).on( 'edd_gateway_loaded', function() {
        $( '#edd_checkout_form_wrap' ).find( 'input[type="text"], select' ).each( maybeAddDoneCheckmark );
      });
    });
  </script>
  <p class="lead"><strong><?php _e( 'Bitte wählen Sie die Zahlungsmethode aus, mit der Sie Ihre Bestellung bezahlen möchten!', 'immoticketenergieausweis' ); ?></strong></p>
  <div class="immoticket24-payment-buttons">
    <button type="button" id="it24-paypal-button" class="btn btn-default">
      <span class="sr-only"><?php _e( 'PayPal', 'immoticketenergieausweis' ); ?></span>
      <img src="<?php echo get_template_directory_uri(); ?>/assets/img/payment/paypal.webp">
    </button>
    <button type="button" id="it24-sofortueberweisung-button" class="btn btn-default">
      <span class="sr-only"><?php _e( 'Sofortüberweisung', 'immoticketenergieausweis' ); ?></span>
      <img src="<?php echo get_template_directory_uri(); ?>/assets/img/payment/sofort.webp">
    </button>
    <button type="button" id="it24-lastschrift-button" class="btn btn-default">
      <span class="sr-only"><?php _e( 'Lastschrift', 'immoticketenergieausweis' ); ?></span>
      <img src="<?php echo get_template_directory_uri(); ?>/assets/img/payment/sepa.webp">
    </button>
    <button type="button" id="it24-deposit-button" class="btn btn-default">
      <span class="sr-only"><?php _e( 'Banküberweisung', 'immoticketenergieausweis' ); ?></span>
      <img src="<?php echo get_template_directory_uri(); ?>/assets/img/payment/ueberweisung.webp">
    </button>

	<button type="button" id="it24-stripe-button" class="btn btn-default">
		<span class="sr-only"><?php _e( 'Kreditkarte', 'immoticketenergieausweis' ); ?></span>
		<img src="<?php echo get_template_directory_uri(); ?>/assets/img/payment/stripe.webp">
	</button>

  </div>
  <?php
}
add_action( 'edd_payment_mode_after_gateways', 'immoticketenergieausweis_payment_icons' );

function immoticketenergieausweis_edd_add_default_gateway_option_none( $gateway_settings ) {
  if ( ! empty( $gateway_settings['main']['default_gateway']['options'] ) ) {
    $gateway_settings['main']['default_gateway']['options'] = array_merge( array(
      '' => array(
        'admin_label'    => __( 'None', 'easy-digital-downloads' ),
        'checkout_label' => __( 'None', 'easy-digital-downloads' ),
      ),
    ), $gateway_settings['main']['default_gateway']['options'] );
  }

  return $gateway_settings;
}
add_filter( 'edd_settings_gateways', 'immoticketenergieausweis_edd_add_default_gateway_option_none' );

function immoticketenergieausweis_edd_allow_no_default_gateway( $gateway ) {
  $original_option = edd_get_option( 'default_gateway', '' );
  if ( empty( $original_option ) ) {
    return $original_option;
  }

  return $gateway;
}
add_filter( 'edd_default_gateway', 'immoticketenergieausweis_edd_allow_no_default_gateway' );

function immoticketenergieausweis_edd_maybe_prevent_default_gateway_load( $script_vars ) {
  if ( edd_is_checkout() ) {
    $chosen_gateway = edd_get_chosen_gateway();

    // This variable needs to be set to 0 so that no default gateway loads in JS.
    if ( empty( $chosen_gateway ) ) {
      $script_vars['is_checkout'] = '0';
    }
  }

  return $script_vars;
}
add_filter( 'edd_ajax_script_vars', 'immoticketenergieausweis_edd_maybe_prevent_default_gateway_load' );

function immoticketenergieausweis_edd_maybe_print_default_gateway_free_purchase_fix() {
  if ( ! edd_is_checkout() ) {
    return;
  }

  $chosen_gateway = edd_get_chosen_gateway();
  if ( ! empty( $chosen_gateway ) ) {
    return;
  }

  ?>
  <script type="text/javascript">
    ( function() {
      var is_free = false;

      jQuery( document.body ).on( 'edd_discount_applied', function( e, discount ) {
        if ( '0.00' == discount.total_plain ) {
          is_free = true;

          if ( window.edd_load_gateway ) {
          	window.edd_load_gateway( 'deposit' );
          }
        } else if ( is_free ) {
          is_free = false;

          jQuery( '#edd_purchase_form_wrap' ).html( '' );
        }
      });
    })();
  </script>
  <?php
}
add_action( 'wp_footer', 'immoticketenergieausweis_edd_maybe_print_default_gateway_free_purchase_fix', 9999 );

/* --------------------- GDPR ------------------- */

function enon_show_gdpr_field( $data ) {
  if ( ! isset( $data['gdpr_acceptance'] ) ) {
    return;
  }

  $privacy_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_privacy' );
  $privacy_url  = apply_filters( 'wpenon_create_privacy_url', get_permalink( $privacy_page ) );

  $onclick         = 'onclick="return !; window.open( this.href, \'%s\', \'width=500,height=500,top=100,left=100\' )" target="_blank"';
  $privacy_onclick = sprintf( $onclick, get_the_title( $privacy_page ) );

  ?>
  <div id="gdpr_acceptance-wrap" class="checkbox">
		<label>
		  <input type="checkbox" id="gdpr_acceptance" name="gdpr_acceptance" value="1"<?php echo $data['gdpr_acceptance'] ? ' checked' : ''; ?>>
		  <?php printf( __( 'Ich habe die <a href="%1$s" %2$s>Datenschutzerklärung</a> gelesen und akzeptiere sie.', 'wpenon' ), $privacy_url, $privacy_onclick ); ?>
		</label>
  </div>
  <?php

  add_action( 'wp_footer', 'enon_acceptance_gdpr_modal' );
}
add_action( 'immoticketenergieausweis_certificate_create_form_extra_fields', 'enon_show_gdpr_field' );

function enon_show_contact_field( $data ) {
  if ( ! isset( $data['contact_acceptance'] ) ) {
    return;
  }

  ?>
  <div id="contact_acceptance-wrap" class="checkbox">
		<label>
		  <input type="checkbox" id="contact_acceptance" name="contact_acceptance" value="1"<?php echo $data['contact_acceptance'] ? ' checked' : ''; ?>>
		  <?php printf( __( 'Hiermit bestätige ich, dass Energieausweis-online-erstellen.de mich bei Fragen zu meinen Energieausweis-Angaben kontaktieren darf.', 'wpenon' ) ); ?>
		</label>
  </div>
	<input type="hidden" id="contact_question_answered" name="contact_question_answered" value="false" />
  <?php
  add_action( 'wp_footer', 'enon_acceptance_contact_modal' );
  add_action( 'wp_footer', 'enon_show_form_modals_js', 100 );
}
add_action( 'immoticketenergieausweis_certificate_create_form_extra_fields', 'enon_show_contact_field' );

function enon_show_form_modals_js() {
	?>
	<script type="text/javascript">
		( function( $ ) {
			if ( ! $ ) {
				return;
			}

			var $form                = $( '#wpenon-generate-form' );

			if ( ! $form.length ) {
				return;
			}

			var $acceptanceGdpr           = $( '#gdpr_acceptance' );
			var $acceptanceContact        = $( '#contact_acceptance' );
			var $acceptanceEingabesupport = $( '#wpenon_eingabesupport' );

			var $modalGdprAcceptance           = $( '#enon_acceptance_gdpr_modal' );
			var $modalContactAcceptance        = $( '#enon_acceptance_contact_modal' );
		    var $modalEingabesupportAcceptance = $( '#enon_acceptance_eingabesupport_modal');

			$modalContactAcceptance.modal({
				show: false,
				closeExisting: false
			});

			$modalGdprAcceptance.modal({
				show: false,
				closeExisting: false
			});

			$modalEingabesupportAcceptance.modal({
				show: false,
				closeExisting: false
			});

			$( '.modal-backdrop' ).css( 'z-index', 1000 );

			var $setContanct       = false;
			var $setEingabesupport = false;

			function onFormSubmission( e ) {
				e.preventDefault();

				if( ! $acceptanceGdpr.prop('checked') ) {
					$modalGdprAcceptance.modal('show');
					return false;
				}

				if( $acceptanceContact.prop('checked') ) {
					$setContanct = true;
				}

				if( ! $acceptanceContact.prop('checked') &&  $setContanct === false ) {
					$modalContactAcceptance.modal('show');
					return false;
				}

				if( $setContanct && $acceptanceGdpr.prop('checked') ) {
					$modalEingabesupportAcceptance.modal('show');
					return false;
				}

				return false;
			}

			$form.on( 'submit', onFormSubmission );

			/**
			 * GDPR acceptance
			 */
			$('#wpit_gdpr_ok').on('click', function()  {
				$modalGdprAcceptance.modal('hide');
			});

			/**
			* Contact acceptance
			*/
			$( '#wpit_contact_proceed_accept' ).on( 'click', function() {
				$acceptanceContact.prop( 'checked', true );
				$modalContactAcceptance.modal( 'hide' );
				$setContanct = true;

				if( ! $setEingabesupport ) {
					$modalEingabesupportAcceptance.modal('show');
				}
			});

			$( '#wpit_contact_proceed_noaccept' ).on( 'click', function() {
				$acceptanceContact.prop( 'checked', false );
				$modalContactAcceptance.modal( 'hide' );
				$setContanct = true;
				if( ! $setEingabesupport ) {
					$modalEingabesupportAcceptance.modal('show');
				}
			});

			/**
			 * Eingabesupport
			 */
			$('#wp-enon-eingabehilfe-yes').on('click', function () {
				$acceptanceEingabesupport.val('true');
				$modalEingabesupportAcceptance.modal('hide');
				$setEingabesupport = true;
				$form.off('submit', onFormSubmission );
				$form.submit();
			});

			$('#wp-enon-eingabehilfe-no').on('click', function () {
				$acceptanceEingabesupport.val('false');
				$modalEingabesupportAcceptance.modal('hide');
				$setEingabesupport = true;
				$form.off('submit', onFormSubmission );
				$form.submit();
			});

			$(document).on('show.bs.modal', '.modal', function () {
				var zIndex = 1040 + (10 * $('.modal:visible').length);
				$(this).css('z-index', zIndex);
				setTimeout(function() {
					$('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
				}, 0);
			});

		} )( window.jQuery );
	</script>
	<?php
}

function enon_acceptance_contact_modal() {
  ?>
  <div id="enon_acceptance_contact_modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="margin-top:140px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php _e( 'Achtung', 'wpenon' ); ?></h4>
			</div>
			<div class="modal-body">
				<?php _e( 'Beachten Sie, dass wir Sie ohne Einwilligung bei Fragen zu Ihren Angaben nicht kontaktieren können.', 'wpenon' ); ?>
			</div>
			<div class="modal-footer">
				<button id="wpit_contact_proceed_noaccept" type="button" class="btn btn-default"><?php _e( 'Ohne Rückmeldung weiter', 'wpenon' ); ?></button>
				<button id="wpit_contact_proceed_accept" type="button" class="btn btn-primary"><?php _e( 'Kontaktieren Sie mich bei Fragen', 'wpenon' ); ?></button>
			</div>
		</div>
		<input type="hidden" name="gpdr_accepted" value="false" />
	</div>
  </div>
  <?php
}

function enon_acceptance_gdpr_modal() {
  ?>
  <div id="enon_acceptance_gdpr_modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="margin-top:140px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php _e( 'Achtung', 'wpenon' ); ?></h4>
			</div>
			<div class="modal-body">
				<?php _e( 'Sie müssen die Datenschutzerklärung akzeptieren um fortfahren zu können.', 'wpenon' ); ?>
			</div>
			<div class="modal-footer">
				<button id="wpit_gdpr_ok" type="button" class="btn btn-default"><?php _e( 'OK', 'wpenon' ); ?></button>
			</div>
		</div>
	</div>
  </div>
  <?php
}

function immoticketenergieausweis_check_certificate_gdpr_acceptance( $energieausweis ) {
  if ( ! empty( $_POST['gdpr_acceptance' ] ) ) {
    update_post_meta( $energieausweis->ID, 'gdpr_acceptance', '1' );
  }
}
add_action( 'wpenon_energieausweis_create', 'immoticketenergieausweis_check_certificate_gdpr_acceptance', 1 );

function immoticketenergieausweis_check_certificate_contact_acceptance( $energieausweis ) {
  if ( ! empty( $_POST['contact_acceptance' ] ) ) {
    update_post_meta( $energieausweis->ID, 'contact_acceptance', '1' );
  } else {
    add_filter( 'wpenon_send_certificate_create_confirmation_email', '__return_false' );
  }
}
add_action( 'wpenon_energieausweis_create', 'immoticketenergieausweis_check_certificate_contact_acceptance', 1 );

function immoticketenergieausweis_include_certificate_gdpr_acceptance_in_data( $data, $energieausweis = null ) {
  if ( $energieausweis ) {
    $data['gdpr_acceptance'] = (bool) get_post_meta( $energieausweis->ID, 'gdpr_acceptance', '1' );
  } else {
    $data['gdpr_acceptance'] = ! empty( $_POST['gdpr_acceptance'] );
  }

  return $data;
}
add_filter( 'wpenon_overview_page_data', 'immoticketenergieausweis_include_certificate_gdpr_acceptance_in_data', 10, 2 );
add_filter( 'wpenon_edit_page_data', 'immoticketenergieausweis_include_certificate_gdpr_acceptance_in_data', 10, 2 );
add_filter( 'wpenon_editoverview_page_data', 'immoticketenergieausweis_include_certificate_gdpr_acceptance_in_data', 10, 2 );
add_filter( 'wpenon_purchase_page_data', 'immoticketenergieausweis_include_certificate_gdpr_acceptance_in_data', 10, 2 );
add_filter( 'wpenon_create_page_data', 'immoticketenergieausweis_include_certificate_gdpr_acceptance_in_data', 10, 1 );

function immoticketenergieausweis_include_certificate_contact_acceptance_in_data( $data, $energieausweis = null ) {
  if ( $energieausweis ) {
    $data['contact_acceptance'] = (bool) get_post_meta( $energieausweis->ID, 'contact_acceptance', '1' );
  } else {
    $data['contact_acceptance'] = ! empty( $_POST['contact_acceptance'] );
  }

  if ( $data['contact_acceptance'] && ! empty( $data['meta']['email'] ) ) {
    $email = $data['meta']['email'];

    add_filter( 'immoticketenergieausweis_uptain_email', function() use ( $email ) {
      return $email;
    });
  }

  return $data;
}
add_filter( 'wpenon_overview_page_data', 'immoticketenergieausweis_include_certificate_contact_acceptance_in_data', 10, 2 );
add_filter( 'wpenon_edit_page_data', 'immoticketenergieausweis_include_certificate_contact_acceptance_in_data', 10, 2 );
add_filter( 'wpenon_editoverview_page_data', 'immoticketenergieausweis_include_certificate_contact_acceptance_in_data', 10, 2 );
add_filter( 'wpenon_purchase_page_data', 'immoticketenergieausweis_include_certificate_contact_acceptance_in_data', 10, 2 );
add_filter( 'wpenon_create_page_data', 'immoticketenergieausweis_include_certificate_contact_acceptance_in_data', 10, 1 );

function immoticketenergieausweis_get_special_affiliates() {
  return array(
    'muk@dkb-grund.de'           => 'dkb',
    'm.wedel@agas-immobilien.de' => 'agas',
  );
}

function immoticketenergieausweis_is_special_affiliate() {
  $special_affiliates = immoticketenergieausweis_get_special_affiliates();

  $cookie_id = 'it24_special_affiliate';
  $cookie    = filter_input( INPUT_COOKIE, $cookie_id );
  if ( ! empty( $cookie ) && in_array( $cookie, $special_affiliates, true ) ) {
    return $cookie;
  }

  if ( ! empty( $_REQUEST['iframe'] ) && 'true' === $_REQUEST['iframe'] && ! empty( $_REQUEST['iframe_token'] ) ) {
    $email = immoticketenergieausweis_get_email_by_iframe_token( wp_unslash( $_REQUEST['iframe_token'] ) );
    $email = strtolower( $email );
    if ( ! empty( $email ) && isset( $special_affiliates[ $email ] ) ) {
      setcookie( $cookie_id, $special_affiliates[ $email ], time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
      return $special_affiliates[ $email ];
    }
  }

  return false;
}

function immoticketenergieausweis_store_special_affiliate() {
  if ( 'download' === get_post_type() ) {

    // This will store the special affiliate in a cookie, if one is detected.
    immoticketenergieausweis_is_special_affiliate();
  }
}
add_action( 'template_redirect', 'immoticketenergieausweis_store_special_affiliate' );

function immoticketenergieausweis_show_terms_text( $purchase_button ) {
  $content = '';

  $privacy_page    = immoticketenergieausweis_get_option( 'it-theme', 'page_for_privacy' );
  $terms_page      = immoticketenergieausweis_get_option( 'it-theme', 'page_for_terms' );
  $withdrawal_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_withdrawal' );

  $privacy_url    = add_query_arg( 'iframe', 'true', get_permalink( $privacy_page ) );
  $terms_url      = add_query_arg( 'iframe', 'true', get_permalink( $terms_page ) );
  $withdrawal_url = add_query_arg( 'iframe', 'true', get_permalink( $withdrawal_page ) );

  $onclick = 'onclick="return !window.open( this.href, \'%s\', \'width=500,height=500,top=100,left=100\' )" target="_blank"';

  $privacy_onclick    = sprintf( $onclick, get_the_title( $privacy_page ) );
  $terms_onclick      = sprintf( $onclick, get_the_title( $terms_page ) );
  $withdrawal_onclick = sprintf( $onclick, get_the_title( $withdrawal_page ) );

  $terms_checkboxes = array(
    'extra_terms' => array(
      'id'       => 'it-agree-to-extra-terms',
      'name'     => 'it_agree_to_extra_terms',
      'label'    => sprintf( __( 'Ich erkläre mich mit den <a href="%3$s" %4$s>AGB</a>, den <a href="%5$s" %6$s>Widerrufsbestimmungen</a> und der <a href="%1$s" %2$s>Datenschutzerklärung</a> einverstanden.', 'immoticketenergieausweis' ), esc_url( $privacy_url ), $privacy_onclick, esc_url( $terms_url ), $terms_onclick, esc_url( $withdrawal_url ), $withdrawal_onclick ),
      'required' => true,
    ),
    'newsletter_terms' => array(
      'id'       => 'it-agree-to-newsletter-terms',
      'name'     => 'it_agree_to_newsletter_terms',
      'label'    => __( 'Ich erkläre mich damit einverstanden nach Bestellabschluss weitere Informationen über aktuelle und künftige Angebote und Produkte zu erhalten, denen ich jederzeit unter info@immoticket24.de oder über den Abmeldelink in der Email widersprechen kann.', 'immoticketenergieausweis' ),
      'required' => false,
    ),
  );

  $special_affiliate = immoticketenergieausweis_is_special_affiliate();
  if ( $special_affiliate ) {
    switch ( $special_affiliate ) {
      case 'dkb':
        $terms_checkboxes[] = array(
          'id'       => 'it-agree-to-dkb-extra-terms',
          'name'     => 'it_agree_to_dkb_extra_terms',
          'label'    => __( 'Ich bin damit einverstanden, dass meine Kontaktdaten und Bestellinformationen der DKB Grund GmbH, dem Anbieter dieser Website, zur Kontaktaufnahme zur Verfügung gestellt werden.', 'immoticketenergieausweis' ),
          'required' => false,
        );

        $content .= '<input type="hidden" name="it_dkb_extra_terms" value="1">';
        $content .= '<input type="hidden" name="it_is_special_affiliate" value="dkb">';
        break;

      case 'agas':
        $terms_checkboxes[] = array(
          'id'       => 'it-agree-to-agas-extra-terms',
          'name'     => 'it_agree_to_agas_extra_terms',
          'label'    => __( 'Ich bin damit einverstanden, dass meine Kontaktdaten und Bestellinformationen der Agas Immobilien GmbH, dem Anbieter dieser Website, zur Kontaktaufnahme zur Verfügung gestellt werden.', 'immoticketenergieausweis' ),
          'required' => true,
        );

        $content .= '<input type="hidden" name="it_agas_extra_terms" value="1">';
        $content .= '<input type="hidden" name="it_is_special_affiliate" value="agas">';
        break;

      default:
        $content .= '<input type="hidden" name="it_is_special_affiliate" value="' . $special_affiliate . '">';
    }
  }
	/**
	 * Filtering terms checkboxes.
	 *
	 * @param array $terms_checkboxes Terms checkboxes.
	 *
	 * @return array Filtered terms.
	 *
	 * @since 1.0.0
	 */
  $terms_checkboxes = apply_filters( 'immoticketenergieausweis_checkout_terms_checkboxes', $terms_checkboxes );

  foreach ( $terms_checkboxes as $terms_checkbox ) {
    $content .= '<div id="' . esc_attr( $terms_checkbox['id'] . '-wrap' ) . '" class="it24-terms-acceptance">';
    $content .= '<input type="checkbox" id="' . esc_attr( $terms_checkbox['id'] ) . '" name="' . esc_attr( $terms_checkbox['name'] ) . '" value="1">';
    $content .= '<label for="' . esc_attr( $terms_checkbox['id'] ) . '" style="font-weight:normal;">' . $terms_checkbox['label'] . '</label>';
    $content .= '</div>';
  }

  return $content . $purchase_button;
}
add_filter( 'edd_checkout_button_purchase', 'immoticketenergieausweis_show_terms_text' );

function immoticketenergieausweis_check_extra_terms() {
  if ( empty( $_POST ) ) {
    return;
  }

  if ( empty( $_POST['it_agree_to_extra_terms'] ) ) {
    edd_set_error( 'it_terms_not_agreed', __( 'Sie haben den Bedingungen von Energieausweis-online-erstellen.de nicht zugestimmt.', 'immoticketenergieausweis' ) );
  }

  if ( ! empty( $_POST['it_agas_extra_terms'] ) && empty( $_POST['it_agree_to_agas_extra_terms'] ) ) {
    edd_set_error( 'it_agas_terms_not_agreed', __( 'Sie haben den Bedingungen von Agas Immobilien GmbH nicht zugestimmt.', 'immoticketenergieausweis' ) );
  }
}
add_action( 'edd_checkout_error_checks', 'immoticketenergieausweis_check_extra_terms' );

function immoticketenergieausweis_set_customer_terms_acceptance_flags( $customer_id ) {
  if ( ! empty( $_POST['it_agree_to_newsletter_terms'] ) ) {
    $customer = new EDD_Customer( $customer_id );
    if ( ! $customer->id ) {
      return;
    }

    $customer->update_meta( 'it24_agree_to_newsletter_terms', '1' );
  }
}
add_action( 'edd_post_insert_customer', 'immoticketenergieausweis_set_customer_terms_acceptance_flags', 10, 1 );

function immoticketenergieausweis_set_terms_acceptance_flags( $payment_id ) {
  if ( empty( $payment_id ) ) {
    return;
  }

  $payment = edd_get_payment( $payment_id );
  if ( ! $payment || ! $payment->ID ) {
    return;
  }

  if ( ! empty( $_POST['it_agree_to_newsletter_terms'] ) ) {
    $payment->update_meta( 'it24_agree_to_newsletter_terms', '1' );
    $payment->update_meta( 'it24_agree_to_trustedshops_terms', '1' );
    $payment->update_meta( 'it24_agree_to_ekomi_terms', '1' );
  }

  $customer_id = edd_get_payment_customer_id( $payment_id );
  if ( ! empty( $customer_id ) ) {
    $customer = new EDD_Customer( $customer_id );
    if ( ! empty( $_POST['it_agree_to_newsletter_terms'] ) ) {
      $customer->update_meta( 'it24_agree_to_newsletter_terms', '1' );
    } else {
      $customer->delete_meta( 'it24_agree_to_newsletter_terms' );
    }
  }

  $special_affiliates = immoticketenergieausweis_get_special_affiliates();
  if ( ! empty( $_POST['it_is_special_affiliate'] ) && in_array( $_POST['it_is_special_affiliate'], $special_affiliates, true ) ) {
    $payment->update_meta( 'it24_is_special_affiliate', $_POST['it_is_special_affiliate'], '1' );

    // Delete the cookie.
    $cookie_id = 'it24_special_affiliate';
    setcookie( $cookie_id, '', time() - HOUR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );

    switch ( $_POST['it_is_special_affiliate'] ) {
      case 'dkb':
        if ( empty( $_POST['it_dkb_extra_terms'] ) || ! empty( $_POST['it_agree_to_dkb_extra_terms'] ) ) {
          $payment->update_meta( 'it24_agree_to_dkb_terms', '1' );
        }
        break;
    }
  }
}
add_action( 'edd_insert_payment', 'immoticketenergieausweis_set_terms_acceptance_flags', 10, 1 );

function immoticketenergieausweis_set_klicktipp_agreement( $result, $customer ) {
  return (bool) $customer->get_meta( 'it24_agree_to_newsletter_terms', true );
}
add_filter( 'eddkti_agreed_to_subscribe', 'immoticketenergieausweis_set_klicktipp_agreement', 10, 2 );

function immoticketenergieausweis_send_order_to_ekomi( $post_id, $payment_id, $download_type = 'default', $download = null, $cart_index = 0 ) {
  if ( ! function_exists( 'EDD' ) ) {
    return;
  }

  if ( ! $payment_id ) {
    return;
  }

  // Has the user agreed to receive an email from eKomi?
  if ( ! (bool) get_post_meta( $payment_id, 'it24_agree_to_ekomi_terms', true ) ) {
    return;
  }

  $sent_to_ekomi = get_post_meta( $payment_id, 'it24_sent_to_ekomi', true );
  if ( $sent_to_ekomi ) {
    return;
  }

  $user_info = edd_get_payment_meta_user_info( $payment_id );

  $order_no = get_the_title( $payment_id );
  $order_name = $user_info['first_name'] . ' ' . $user_info['last_name'];
  $order_email = $user_info['email'];

  $emails = EDD()->emails;

  $to = '81266-energieausweis-online@connect.ekomi.de';
  $subject = 'Neue Bestellung';
  $message = "Hier finden sich die Daten zur neuen Bestellung:\n\n";
  $message .= "Vorgangskennung: " . $order_no . "\n";
  $message .= "Mailadresse des Kunden: " . $order_email . "\n";
  $message .= "Name des Kunden: " . $order_name . "\n";

  $emails->__set( 'heading', $subject );

  $result = $emails->send( $to, $subject, $message );
  if ( ! $result ) {
    edd_record_log( '', sprintf( 'Payment %d eKomi email could not be sent.', $payment_id ), 0, 'templog' );
  }

  update_post_meta( $payment_id, 'it24_sent_to_ekomi', true );
}
add_action( 'edd_complete_download_purchase', 'immoticketenergieausweis_send_order_to_ekomi', 100, 5 );

function immoticketenergieausweis_send_order_to_trustedshops( $payment_id ) {
  $payment = get_post( $payment_id );

  if ( empty( $payment ) ) {
    return '';
  }

  // Has the user agreed to receive an email from TrustedShops?
  if ( ! (bool) get_post_meta( $payment->ID, 'it24_agree_to_trustedshops_terms', true ) ) {
    return '';
  }

  $sent_to_trustedshops = get_post_meta( $payment_id, 'it24_sent_to_trustedshops', true );
  if ( $sent_to_trustedshops ) {
    return '';
  }

  $order_number = get_the_title( $payment->ID );
  $buyer_email_address = edd_get_payment_user_email( $payment->ID );
  $shopping_basket_total = edd_get_payment_amount( $payment->ID );
  $order_currency = edd_get_currency();
  $payment_method = edd_get_gateway_checkout_label( edd_get_payment_gateway( $payment->ID ) );
  $completed_date = date_i18n( 'Y-m-d', strtotime( edd_get_payment_completed_date( $payment->ID ) ) );

  $output = '
<div id="trustedShopsCheckout" style="display: none;">
  <span id="tsCheckoutOrderNr">' . $order_number . '</span>
  <span id="tsCheckoutBuyerEmail">' . $buyer_email_address . '</span>
  <span id="tsCheckoutOrderAmount">' . $shopping_basket_total . '</span>
  <span id="tsCheckoutOrderCurrency">' . $order_currency . '</span>
  <span id="tsCheckoutOrderPaymentType">' . $payment_method . '</span>
  <span id="tsCheckoutOrderEstDeliveryDate">' . $completed_date . '</span>
</div>';

  $output .= '<div id="trusted-shops-warranty"></div>';

  update_post_meta( $payment_id, 'it24_sent_to_trustedshops', true );

  return $output;
}

function immoticketenergieausweis_hack_iframe_ajax_url( $vars ) {
  if ( ! function_exists( 'edd_is_checkout' ) || ! edd_is_checkout() ) {
    return $vars;
  }

  $special_affiliates = immoticketenergieausweis_get_special_affiliates();

  if ( ! empty( $_REQUEST['iframe'] ) && 'true' === $_REQUEST['iframe'] && ! empty( $_REQUEST['iframe_token'] ) ) {
    $iframe_token = wp_unslash( $_REQUEST['iframe_token'] );
    $email        = immoticketenergieausweis_get_email_by_iframe_token( $iframe_token );
    $email        = strtolower( $email );
    if ( ! empty( $email ) && isset( $special_affiliates[ $email ] ) ) {
      $vars['ajaxurl'] = add_query_arg( array(
        'iframe'       => 'true',
        'iframe_token' => $iframe_token,
      ), $vars['ajaxurl'] );
    }
  }

  return $vars;
}
add_filter( 'edd_global_checkout_script_vars', 'immoticketenergieausweis_hack_iframe_ajax_url' );
add_filter( 'edd_ajax_script_vars', 'immoticketenergieausweis_hack_iframe_ajax_url' );

function immoticketenergieausweis_filter_admin_notice_emails_for_special_affiliate( $emails ) {
  $special_affiliates = immoticketenergieausweis_get_special_affiliates();
  $special_affiliate  = $GLOBALS['it24_send_special_affiliate'];

  $email = array_search( $special_affiliate, $special_affiliates, true );
  if ( ! empty( $email ) ) {
    $emails[] = $email;
  }

  return $emails;
}

function immoticketenergieausweis_special_affiliate_email_notice( $payment_id = 0, $payment_data = array() ) {
  $send_special_affiliate = false;

  $payment = edd_get_payment( $payment_id );
  if ( $payment && $payment->ID ) {
    $special_affiliate = $payment->get_meta( 'it24_is_special_affiliate', true );
    if ( $special_affiliate ) {
      switch ( $special_affiliate ) {
        case 'dkb':
          if ( $payment->get_meta( 'it24_agree_to_dkb_terms', true ) ) {
            $send_special_affiliate = true;
          }
          break;

        default:
          $send_special_affiliate = true;
      }
    }
  }

  if ( $send_special_affiliate ) {
    $GLOBALS['it24_send_special_affiliate'] = $special_affiliate;
    add_filter( 'edd_admin_notice_emails', 'immoticketenergieausweis_filter_admin_notice_emails_for_special_affiliate' );
    edd_admin_email_notice( $payment_id, $payment_data );
    remove_filter( 'edd_admin_notice_emails', 'immoticketenergieausweis_filter_admin_notice_emails_for_special_affiliate' );
    unset( $GLOBALS['it24_send_special_affiliate'] );
  } else {
    edd_admin_email_notice( $payment_id, $payment_data );
  }
}
remove_action( 'edd_admin_sale_notice', 'edd_admin_email_notice', 10 );
add_action( 'edd_admin_sale_notice', 'immoticketenergieausweis_special_affiliate_email_notice', 10, 2 );

function immoticketenergieausweis_enable_extended_upload ( $mime_types = array() ) {
	$mime_types['csv'] = 'text/plain';
	return $mime_types;
}

add_filter( 'upload_mimes', 'immoticketenergieausweis_enable_extended_upload' );
