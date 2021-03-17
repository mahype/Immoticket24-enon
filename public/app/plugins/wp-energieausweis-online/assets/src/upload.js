const axios = require('axios');

let fileInputs = document.querySelectorAll('.file-control');

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
       setPercentage( field, 0 );
       document.getElementById( field + "_field" ).value = response.data.url;
       document.getElementById( field + "_image" ).innerHTML = `<img src="${response.data.url}" />`;
    });
}
