jQuery( document ).ready( function($) {

  function handlePaymillResponse( error, result ) {
    if ( error ) {
      var message = '';
      if ( typeof result.apierror !== 'undefined' ) {
        switch ( result.apierror ) {
          case 'internal_server_error':
            message = 'Communication with PSP failed';
            break;
          case 'invalid_public_key':
            message = 'Invalid Public Key';
            break;
          case 'invalid_payment_data':
            message = 'not permitted for this method of payment, credit card type, currency or country';
            break;
          case 'field_invalid_account_number':
            message = 'Missing or invalid bank account number';
            break;
          case 'field_invalid_account_holder':
            message = 'Missing or invalid bank account holder';
            break;
          case 'field_invalid_bank_code':
            message = 'Missing or invalid bank code';
            break;
          case 'field_invalid_iban':
            message = 'Missing or invalid IBAN';
            break;
          case 'field_invalid_bic':
            message = 'Missing or invalid BIC';
            break;
          case 'field_invalid_country':
            message = 'Missing or unsupported country (with IBAN)';
            break;
          case 'field_invalid_bank_data':
            message = 'Missing or invalid bank data combination';
            break;
          case 'unknown_error':
          default:
            message = 'Unknown Error';
        }
      } else {
        message = 'Unknown Error';
      }
      console.error( 'Energieausweis Paymill Error: ' + message );
    } else {
      var $form = $( '#edd_purchase_form' );
      var token = result.token;

      if ( $form.find( '#wpenon_paymill_token' ).length > 0 ) {
        $form.find( '#wpenon_paymill_token' ).val( token );
      } else {
        $form.append( '<input type="hidden" id="wpenon_paymill_token" name="paymill_token" value="' + token + '">' );
      }

      $( '#edd_purchase_form' ).submit();
    }
  }

  function isPaymillSepa() {
    var reg = new RegExp( /^\D{2}/ );
    return reg.test( $( '#sepa_account_id' ).val() );
  }

  function validatePaymillData() {
    if ( '' === $( '#sepa_account_holder' ).val() ) {
      showPaymillError( $( '#sepa_account_holder' ), 'Bitte geben Sie den Namen des Kontoinhabers ein!' );
      return false;
    }

    var iban = new Iban();

    if ( isPaymillSepa() ) {
      if ( ! iban.validate( $( '#sepa_account_id' ).val() ) ) {
        showPaymillError( $( '#sepa_account_id' ), 'Bitte geben Sie eine gültige IBAN ein!' );
        return false;
      }
      if ( $( '#sepa_bank_id' ).val().length !== 8 && $( '#sepa_bank_id' ).val().length !== 11 ) {
        showPaymillError( $( '#sepa_bank_id' ), 'Bitte geben Sie eine gültige BIC ein!' );
        return false;
      }
    } else {
      if ( ! paymill.validateAccountNumber( $( '#sepa_account_id' ).val() ) || $( '#sepa_account_id' ).val().length > 10 ) {
        showPaymillError( $( '#sepa_account_id' ), 'Bitte geben Sie eine gültige Kontonummer ein!' );
        return false;
      }
      if ( ! paymill.validateBankCode( $( '#sepa_bank_id' ).val() ) ) {
        showPaymillError( $( '#sepa_bank_id' ), 'Bitte geben Sie eine gültige Bankleitzahl ein!' );
        return false;
      }
    }

    return true;
  }

  function resetPaymillError( $field ) {
    $field.removeClass( 'error' );
    $field.addClass( 'valid' );
    if ( $( '#wpenon_error_' + $field.attr( 'id' ) ).length > 0 ) {
      $( '#wpenon_error_' + $field.attr( 'id' ) ).remove();
    }
    if ( $( '#wpenon_paymill_errors' ).find( 'edd_error' ).length < 1 ) {
      $( '#wpenon_paymill_errors' ).remove();
    }
  }

  function showPaymillError( $field, message ) {
    $field.removeClass( 'valid' );
    $field.addClass( 'error' );
    if ( $( '#wpenon_paymill_errors' ).length < 1 ) {
      $( '#edd_final_total_wrap' ).before( '<div id="wpenon_paymill_errors" class="edd_errors"></div>' );
    }
    $( '#wpenon_paymill_errors' ).append( '<p class="edd_error" id="wpenon_error_' + $field.attr( 'id' ) + '"><strong>Fehler:</strong> ' + message + '</p>' );
  }

  var paymillSubmit = false;

  var checkPaymillValidity = function() {
    resetPaymillError( $( '#sepa_account_holder' ) );
    resetPaymillError( $( '#sepa_account_id' ) );
    resetPaymillError( $( '#sepa_bank_id' ) );

    paymillSubmit = false;

    console.log( 'checking payment data' );

    var status = false;
    if ( $( '#sepa_account_holder' ).length > 0 && $( '#sepa_account_id' ).length > 0 && $( '#sepa_bank_id' ).length > 0 ) {
      status = validatePaymillData();
    } else {
      showPaymillError( $( '#sepa_bank_id' ), 'Bitte geben Sie Ihre Kontodaten ein!' );
    }

    if ( status ) {
      $( '#edd_purchase_form' ).one( 'submit', function( e ) {
        if ( ! paymillSubmit ) {
          paymillSubmit = true;
          var params = null;

          e.preventDefault();

          if ( isPaymillSepa() ) {
            var iban = $( '#sepa_account_id' ).val().toUpperCase().replace( /\s+/g, '' );
            var bic = $( '#sepa_bank_id' ).val().toUpperCase();
            if ( 8 === bic.length ) {
              bic += 'XXX';
            }
            params = {
              iban: iban,
              bic: bic,
              accountholder: $( '#sepa_account_holder' ).val()
            };
          } else {
            params = {
              number: $( '#sepa_account_id' ).val(),
              bank: $( '#sepa_bank_id' ).val(),
              accountholder: $( '#sepa_account_holder' ).val()
            };
          }

          paymill.createToken( params, handlePaymillResponse );
        }
      });
    } else {
      $( '#edd_purchase_form' ).one( 'submit', function( e ) {
        e.preventDefault();
      });
    }

    return status;
  };

  if ( $( 'input[name="edd-gateway"]' ).val() == 'paymill' ) {
    var purchase_form = document.getElementById( 'edd_purchase_form' );
    purchase_form.checkValidity = checkPaymillValidity;
  }

  $( 'select#edd-gateway, input.edd-gateway' ).change( function (e) {

    var payment_mode = $( '#edd-gateway option:selected, input.edd-gateway:checked' ).val();

    var purchase_form = document.getElementById( 'edd_purchase_form' );
    if ( payment_mode == 'paymill' ) {
      purchase_form.checkValidity = checkPaymillValidity;
    } else {
      purchase_form.checkValidity = undefined;
    }
  });

  $( document ).on( 'paste cut keydown', '#sepa_bank_id', function() {
    setTimeout( function() {
      paymill.getBankName( $( '#sepa_bank_id' ).val(), function( error, result ) {
        if ( error || ! result ) {
          $( '#wpenon-sepa-bank-name-wrap' ).hide();
        } else {
          $( '#sepa_bank_name' ).val( result );
          $( '#wpenon-sepa-bank-name-wrap' ).show();
        }
      });
    }, 200 );
  });

});
