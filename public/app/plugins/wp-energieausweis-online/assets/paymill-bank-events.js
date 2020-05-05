/**
 * Iban validation and formating
 * Validate a german IBAN pattern and add spaces after 4 digits
 *
 * Include by filter edd_after_cc_fields @ public/app/plugins/wp-energieausweis-online/inc/functions.php
 */
var format_iban = function(){
	var wpenon_sepa_fields = document.getElementById('wpenon_sepa_fields');

	var className = {
		danger: 'label-danger',
		success: 'label-success'
	};

	var labelContent = {
		danger: 'x',
		success: '✔'
	};

	if(wpenon_sepa_fields) {
		var sepaInputNode = document.getElementById( 'sepa_account_id' );

		if ( sepaInputNode ) {
			sepaInputNode.addEventListener( 'input', function() {
				this.value = this.value.toUpperCase().replace( /[^\dA-Z]/g, '' ).replace( /(.{4})/g, '$1 ' ).trim();
			});

			sepaInputNode.addEventListener( 'focusout', function() {
				var successNode = sepaInputNode.parentNode.querySelector( '.is-done-checkmark.label' );

				if(successNode){
					successNode.classList.remove(className.success);
					successNode.classList.add(className.danger);
					successNode.innerText = labelContent.danger;
				}

				var iban = this.value;
				var ibanRegexp = /(^[A-Z]{2}\d{2})\s?((\d{4}\s?){4}\s?)(\d{2})$/g;
				var result = ibanRegexp.exec( iban );

				if ( result ) {
					successNode.classList.add(className.success);
					successNode.classList.remove(className.danger);
					successNode.innerText = labelContent.success;
				}

			} );
		}
	}
}

format_iban();
