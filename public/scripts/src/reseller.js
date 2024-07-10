let urlSet = false;

// Check if the DOM is already loaded
document.addEventListener("DOMContentLoaded", function() {
    if( urlSet ) return;
    setUrl();
});

// Maybe the DOM is already loaded, then check if the URL is already set and set it if not
if(! urlSet ) {
    setUrl();
} 

function setUrl() {
    const enonQueryString = window.location.search;
    const enonUrlParams = new URLSearchParams(enonQueryString);

    const enonIframeToken = enonUrlParams.get("iframe_token");
    const enonAccessToken = enonUrlParams.get("access_token");
    const enonSlug = enonUrlParams.get("slug");

    if( enonAccessToken !== null ) {
        const enonUrl = "https://energieausweis.de/energieausweise/" + enonSlug + "/?iframe_token=" + enonIframeToken + "&access_token=" + enonAccessToken;
        const enonIframe = document.getElementById("iframe-energieausweis-online");

        if( enonIframe !== null ) {
            enonIframe.src = enonUrl;
            return;
        }

        const iframes = document.getElementsByClassName("iframe-energieausweis-online");
        for (let i = 0; i < iframes.length; i++) {
            if( i === 0 ){
                iframes[i].src = enonUrl;
            } else {
                iframes[i].style.display = "none";
            }
        }     
    
    }
}

window.addEventListener("message", function(event) {
    if ( event.origin !== "https://energieausweis.de" ) return;
    if ( typeof event.data  === 'object' ) return;

    const enonQueryString = window.location.search;
    const enonUrlParams = new URLSearchParams(enonQueryString);
    const enonIframeToken = enonUrlParams.get("iframe_token");
    const enonAccessToken = enonUrlParams.get("access_token");
    
    var data = JSON.parse(event.data);

    if(data.frame_height !== undefined) {
        let iframe = document.getElementById("iframe-energieausweis-online");
        let iframes = [];

        if( iframe !== null ) {
            iframes.push(iframe);
        } else {
            iframes = document.getElementsByClassName("iframe-energieausweis-online");
        }

        for(let i = 0; i < iframes.length; i++) {
            if( iframes[i].contentWindow === event.source) {
                iframes[i].style.height = data.frame_height + "px";
                iframes[i].style.overflow = "hidden";
            }
        }

        if( enonAccessToken !== null ) {
            document.getElementsByClassName("iframe-energieausweis-online")[0].scrollIntoView();
        }        
    }

    if(data.set_to_top !== undefined) {
        document.getElementsByClassName("iframe-energieausweis-online")[0].scrollIntoView();
    }
    
    if(data.redirect_url !== undefined) {
        document.location.href = data.redirect_url;
    }
}, false) ;