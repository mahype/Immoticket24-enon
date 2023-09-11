document.addEventListener("DOMContentLoaded", function() {
    const enonQueryString = window.location.search;
    const enonUrlParams = new URLSearchParams(enonQueryString);

    const enonIframeToken = enonUrlParams.get("iframe_token");
    const enonAccessToken = enonUrlParams.get("access_token");
    const enonSlug = enonUrlParams.get("slug");

    if( enonAccessToken !== null ) {
        const enonUrl = "https://www.energieausweis-online-erstellen.de/energieausweise/" + enonSlug + "/?iframe_token=" + enonIframeToken + "&access_token=" + enonAccessToken;
        console.log(enonUrl);
        const enonIframe = document.getElementById("iframe-energieausweis-online");

        if( enonIframe !== null ) {
            enonIframe.src = enonUrl;
            return;
        }

        const iframes = document.getElementsByClassName("iframe-energieausweis-online");
        for (let i = 0; i < iframes.length; i++) {
            console.log("Iframe by class:"+ iframes[i]);
            if( i === 0 ){
                iframes[i].src = enonUrl;
            } else {
                iframes[i].style.display = "none";
            }
        }     
    }
});

window.addEventListener("message", function(event) {
    if ( event.origin !== "https://www.energieausweis-online-erstellen.de" && event.origin !== 'https://www.energieausweis-online-erstellen.de'  ) return;
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

        // Get element by class name an scroll to first element
        document.getElementsByClassName("iframe-energieausweis-online")[0].scrollIntoView();
    }
    
    if(data.redirect_url !== undefined) {
        document.location.href = data.redirect_url;
    }
}, false) ;