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
    if (event.origin != "https://www.energieausweis-online-erstellen.de") return;
    var data = JSON.parse(event.data);
    document.getElementById("iframe-energieausweis-online").setAttribute("style","width:100%;height:" + data.frame_height + "px");
}, false) ;