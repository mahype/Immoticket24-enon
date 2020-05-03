/**
 * Iban validation and formating
 * Validate a german IBAN pattern and add spaces after 4 digits
 *
 * Include by filter edd_after_cc_fields @ public/app/plugins/wp-energieausweis-online/inc/functions.php
 */
var format_iban = function(){
	var wpenon_sepa_fields = document.querySelector('#wpenon_sepa_fields');

	var className = {
		danger: 'label-danger',
		success: 'label-success'
	};

	var labelContent = {
		danger: 'x',
		success: '✔'
	};

	if(wpenon_sepa_fields) {
		var sepaInputNode = wpenon_sepa_fields.querySelector( '#sepa_account_id' );

		if ( sepaInputNode ) {
			sepaInputNode.addEventListener( 'focusout', function() {
				var successNode = sepaInputNode.parentNode.querySelector( '.is-done-checkmark.label' );
				var iban = this.value;

				var ibanRegexp = /^DE[a-zA-Z0-9]{2}\s?([0-9]{4}\s?){4}([0-9]{2})\s?/g;

				if ( ibanRegexp.exec( iban ) ) {
					this.value = iban.replace( /[^\dA-Z]/g, '' ).replace( /(.{4})/g, '$1 ' ).trim();
					successNode.classList.add(className.success);
					successNode.classList.remove(className.danger);
					successNode.innerText = labelContent.success;
				}else{
					successNode.classList.remove(className.success);
					successNode.classList.add(className.danger);
					successNode.innerText = labelContent.danger;
				}

			} );
		}
	}
}

format_iban();
