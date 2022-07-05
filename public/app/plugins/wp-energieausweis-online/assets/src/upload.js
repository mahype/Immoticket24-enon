const axios = require('axios');

let fileInputs        = document.querySelectorAll('.file-control');
let fileDeleteButtons = document.querySelectorAll('.file-delete');

fileInputs.forEach( ( fileInput )=> {
    fileInput.addEventListener( 'change', ( event ) => {
        if ( event.target.files[0] === undefined ) {
            return;
        }

        let ecId  = _wpenon_data.energieausweis_id;
        let field = event.target.id;        
        let file  = event.target.files[0];
        let data  = new FormData();

        data.append('action', 'ec_image_upload');
        data.append('field', field  );
        data.append('ecId', ecId );        
        data.append('file', file );
        
        sendUpload( data, field );
    });
});

fileDeleteButtons.forEach( ( fileDeleteButton ) => {
    fileDeleteButton.addEventListener( 'click', ( event ) => {
        event.preventDefault();
        let ecId  = _wpenon_data.energieausweis_id;
        let field = event.target.dataset.image_name;
        let data  = new FormData();

        data.append('action', 'ec_image_delete');
        data.append('field', field  );
        data.append('ecId', ecId ); 

        sendDelete( data, field );
        event.target.classList.add('hidden');
    });
});

const setPercentage = ( field, percent ) => {
    percentage    = document.querySelector( '#' + field + '-wrap .percentage' );
    percentageBar = document.querySelector( '#' + field + '-wrap .percentage-bar' );

    if ( percent === 0 ) {
        percentage.style.display = 'none';
        percentageBar.style.width = '0%';

        return;
    }
    
    percentage.style.display = 'block';
    percentageBar.style.width = percent + '%';
}

const sendUpload = ( data, field ) => {
    return axios.post(
        _wpenon_data.rest_url + 'ec/image_upload', 
        data,
        {
            headers: {'X-WP-Nonce': _wpenon_data.upload_nonce},
            onUploadProgress: function(progressEvent) {
                var percentCompleted = Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                setPercentage( field, percentCompleted );
            }
        }
    ).then( ( response ) => {
        console.log( response );
        setPercentage( field, 0 );
        if( response.data.error !== undefined ) {
            document.getElementById( field + "_notice" ).innerHTML = response.data.error;
            document.getElementById( field + "_notice" ).style.display = 'block';
        } else {
            document.getElementById( field + "_notice" ).style.display = 'none';
            document.getElementById( field + "_field" ).value = response.data.url;
            document.getElementById( field + "_image" ).innerHTML = `<img src="${response.data.url}" />`;
            document.getElementById( "file-delete-" + field ).classList.remove('hidden');
        }
    }).catch( function ( error ) { 
        console.log( error );
    });
}

const sendDelete = ( data, field ) => {
    return axios.post(
        _wpenon_data.rest_url + 'ec/image_delete', 
        data,
        {
            headers: {'X-WP-Nonce': _wpenon_data.upload_nonce},
        }
    ).then( ( response ) => {
       document.getElementById( field + "_field" ).value = '';
       document.getElementById( field + "_image" ).innerHTML = '';
    });
}
