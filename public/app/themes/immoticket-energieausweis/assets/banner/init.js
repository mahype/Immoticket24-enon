(function( $ ) {

	var canvas, stage, exportRoot;

	function init() {
		if ( ! $( '#energieausweis-canvas' ).length ) {
			return;
		}
		
		createjs.MotionGuidePlugin.install();

		canvas = document.getElementById("energieausweis-canvas");
		images = images||{};

		var loader = new createjs.LoadQueue(false);
		loader.addEventListener("fileload", handleFileLoad);
		loader.addEventListener("complete", handleComplete);
		loader.loadManifest(lib.properties.manifest);
	}

	function handleFileLoad(evt) {
		if (evt.item.type == "image") { images[evt.item.id] = evt.result; }
	}

	function handleComplete(evt) {
		exportRoot = new lib._250_350();

		stage = new createjs.Stage(canvas);
		stage.addChild(exportRoot);
		stage.update();
		stage.enableMouseOver();

		createjs.Ticker.setFPS(lib.properties.fps);
		createjs.Ticker.addEventListener("tick", stage);
	}

	$( document ).ready( init );

})( jQuery );