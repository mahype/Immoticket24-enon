document.addEventListener("DOMContentLoaded", function() {
    const enonQueryString = window.location.search;
    const enonUrlParams = new URLSearchParams(enonQueryString);

    const enonIframeToken = enonUrlParams.get("iframe_token");
    const enonAccessToken = enonUrlParams.get("access_token");
    const enonSlug = enonUrlParams.get("slug");

    if( enonAccessToken !== null ) {
        const enonUrl = "https://www.energieausweis-online-erstellen.de/energieausweise/" + enonSlug + "/?iframe_token=" + enonIframeToken + "&access_token=" + enonAccessToken;
        document.getElementById("iframe-energieausweis-online").src = enonUrl;       
    }
});

window.addEventListener("message", function(event) {
    if ( event.origin != "https://www.energieausweis-online-erstellen.de" && event.origin !== 'https://enon.test'  ) return;
    if ( typeof event.data  === 'object' ) return;
    
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
    }
    if(data.redirect_url !== undefined) {
        document.location.href = data.redirect_url;
    }
}, false) ;
