jQuery( document ).ready( function ( $ ) {
	$( 'body' ).prepend( '<div id="wpenon-preloader-overlay" class="wpenon-preloader-overlay"><div class="wpenon-preloader"></div></div>' );

	$( 'body' ).on( 'wpenon_ajax_start', function () {
		$( '#wpenon-preloader-overlay' ).show();
	});

	$( 'body' ).on('wpenon_ajax_end', function () {
		$('#wpenon-preloader-overlay').hide();
	});

	$('.field-info .label').tooltip();

	if ($('#wpenon-edit-submit').length > 0) {
		$('#wpenon-edit-submit').on('click', function () {
			var id = $('.wpenon-tab-pane.active').attr('id');
			$('#wpenon_active_tab').val(id);
		});
	}

	function wpenon_update_active_tab(mode) {
		var $active_tab = $('.wpenon-nav-tabs > .active');

		if ($active_tab.length > 0) {
			var $active_tab_content = $('.wpenon-tab-pane.active');

			var scroll_to_top = false;

			var new_tab = mode;
			if (mode == 'next') {
				if ($active_tab.next().length > 0) {
					new_tab = $active_tab.next().find('a').attr('href');
					scroll_to_top = true;
				} else {
					new_tab = '';
				}
			} else if (mode == 'previous') {
				if ($active_tab.prev().length > 0) {
					new_tab = $active_tab.prev().find('a').attr('href');
					scroll_to_top = true;
				} else {
					new_tab = '';
				}
			}

			if (new_tab !== '' && $(new_tab).length > 0) {
				$active_tab.removeClass('active');
				$active_tab_content.removeClass('active');

				$active_tab = $('a[href="' + new_tab + '"]').parent('li');
				$active_tab_content = $(new_tab);

				$active_tab.addClass('active');
				$active_tab_content.addClass('active');

				if (scroll_to_top) {
					$('html, body').animate({
						scrollTop: $('#wpenon-generate-form').offset().top - _wpenon_data.dynamic_functions.get_scroll_offset() - 40
					}, 'slow');
				}
			}

			$(document).trigger('wpenon.update_active_tab', [$active_tab_content.index()]);

			if ($active_tab_content.prev().length > 0) {
				$('#wpenon-previous-button').show();
			} else {
				$('#wpenon-previous-button').hide();
			}
			if ($active_tab_content.next().length > 0) {
				$('#wpenon-next-button').show();
			} else {
				$('#wpenon-next-button').hide();
			}
		}
	}

	wpenon_update_active_tab('');

	$('.wpenon-form-link').on('click', function (e) {
		var target = $(this).attr('href');

		if ($(target).length > 0) {
			var $tab = $(target).parents('.wpenon-tab-pane');
			wpenon_update_active_tab('#' + $tab.attr('id'));

			$('html, body').animate({
				scrollTop: $(target).offset().top - _wpenon_data.dynamic_functions.get_scroll_offset() - 40
			}, 'slow');
		}

		e.preventDefault();
	});

	$('#wpenon-previous-button').on('click', function (e) {
		wpenon_update_active_tab('previous');

		e.preventDefault();
	});

	$('#wpenon-next-button').on('click', function (e) {
		wpenon_update_active_tab('next');

		e.preventDefault();
	});

	$('.wpenon-nav-tabs a').on('click', function (e) {
		var target = $(this).attr('href');
		var $active_tab_content = $(target);

		$(document).trigger('wpenon.update_active_tab', [$active_tab_content.index()]);

		if ($active_tab_content.prev().length > 0) {
			$('#wpenon-previous-button').show();
		} else {
			$('#wpenon-previous-button').hide();
		}
		if ($active_tab_content.next().length > 0) {
			$('#wpenon-next-button').show();
		} else {
			$('#wpenon-next-button').hide();
		}
	});

	if ($('#wpenon-double-checked-entries').length) {
		$('.edd-add-to-cart').addClass('disabled');
		$('.edd_go_to_checkout').addClass('disabled');

		$('#wpenon-double-checked-entries').on('click', function (e) {
			if ($(this).prop('checked')) {
				$('.edd-add-to-cart').removeClass('disabled');
				$('.edd_go_to_checkout').removeClass('disabled');
			} else {
				$('.edd-add-to-cart').addClass('disabled');
				$('.edd_go_to_checkout').addClass('disabled');
			}
		});

		$('.edd-add-to-cart').on('click', function (e) {
			if ($(this).hasClass('disabled')) {
				e.preventDefault();
			}
		});
		$('.edd_go_to_checkout').on('click', function (e) {
			if ($(this).hasClass('disabled')) {
				e.preventDefault();
			}
		});
	}

	/**
	 * handle enon thumbnail upload
	 *
	 * @author web dev media UG  <info@web-dev-media.de>
	 * @version 1.2.0
	 *
	 * @namespace wtf_thumb
	 */
	var wtf_thumb = {};

	/**
	 * Progress functions to handle thumb form actions
	 *
	 * @namespace wtf_thumb.functions
	 * @property {function} addEvents
	 * @property {function} upload
	 * @property {function} remove
	 * @property {function} updatePreview
	 * @property {function} getFormData
	 * @property {function} appendMsg
	 * @property {function} xhr
	 */
	wtf_thumb.functions = function(){};

	/**
	 * add click event to form btn
	 *
	 * @namespace wtf_thumb.functions.addEvents
	 */
	wtf_thumb.functions.prototype.addEvents = function(){
		for (let [trigger, node] of Object.entries(wtf_thumb.triggerNodes.action)) {
			if(node) {
				node.addEventListener( 'click', function( e ) {
					e.preventDefault();

					switch ( trigger ) {
						case "upload":
							wtf_thumb.functions.prototype.upload();
							break;
						case "del":
							wtf_thumb.functions.prototype.remove();
							break;
					}
				} );
			}
		}
	};

	/**
	 * Handel the thumb upload
	 * @namespace wtf_thumb.functions.uplad
	 * @param {bool} isXhrResponse
	 */
	wtf_thumb.functions.prototype.upload = function(isXhrResponse) {
		var isXhrCallback = isXhrResponse ? isXhrResponse : false;

		if(!isXhrCallback){
			wtf_thumb.triggerNodes.action.del  = wtf_thumb.form.querySelector('[name="wpenon_thumbnail_delete"]');

			if(wtf_thumb.triggerNodes.action.del ) {
				wtf_thumb.triggerNodes.parenntNode.removeChild( wtf_thumb.triggerNodes.action.del  );
			}

			wtf_thumb.functions.prototype.xhr('upload');
			return;
		}


	};

	/**
	 * Handel the thumb delete
	 * @namespace wtf_thumb.functions.remove
	 * @param {bool} isXhrResponse
	 * @param {Object} xhrResponse
	 */
	wtf_thumb.functions.prototype.remove = function(isXhrResponse, xhrResponse ) {
		var isXhrCallback = isXhrResponse ? isXhrResponse : false;
		var response = xhrResponse ? xhrResponse : null;

		if(!isXhrCallback && !confirm('Soll das Bild wirklich gelöscht werden?')){
			return;
		}

		if(!isXhrCallback){
			wtf_thumb.functions.prototype.xhr('remove');
			return;
		}

		//toto check if this needed
		wtf_thumb.triggerNodes.action.del .value = '';

		var span = document.createElement('span');
			span.classList.add('glyphicon');
			span.classList.add('glyphicon-picture');

		wtf_thumb.preview.appendChild(span);
		wtf_thumb.triggerNodes.parenntNode.removeChild(wtf_thumb.triggerNodes.action.del );

		wtf_thumb.functions.prototype.updatePreview(response, 'restorePrev');
	};

	/**
	 * Handle thumbnail preview state
	 *
	 * @namespace wtf_thumb.functions.updatePreview
	 * @param {xhr} response
	 * @param {string} action - addTumbnail || restorePrev
	 */
	wtf_thumb.functions.prototype.updatePreview = function( response, action ) {
		var thumbnailParent = document.querySelector( '.thumbnail-wrapper' );
		thumbnailParent.innerHTML = "";

		var percentSpan = wtf_thumb.triggerNodes.parenntNode.querySelector( 'span' );

		if ( action === 'addTumbnail' ) {
			var image = response.tmpImage;

			if ( image ) {
				var img = document.createElement( 'img' );
				img.setAttribute( "src", image.path );

				thumbnailParent.appendChild( img );

				var button = document.createElement( 'button' );
				button.setAttribute( 'type', 'submit' );
				button.setAttribute( 'name', 'wpenon_thumbnail_delete' );
				button.setAttribute( 'class', 'btn btn-danger btn-xs' );
				button.innerHTML = 'Bild löschen';


				wtf_thumb.triggerNodes.parenntNode.removeChild( percentSpan );
				wtf_thumb.triggerNodes.parenntNode.appendChild( button );

				wtf_thumb.triggerNodes.action.del  = wtf_thumb.form.querySelector('[name="wpenon_thumbnail_delete"]');
				wtf_thumb.functions.prototype.addEvents();
			}

		} else if ( action === 'restorePrev' ) {
			var span = document.createElement( 'span' );
			span.classList.add( 'glyphicon' );
			span.classList.add( 'glyphicon-picture' );

			thumbnailParent.appendChild( span );

			wtf_thumb.triggerNodes.parenntNode.removeChild( percentSpan );
		}
	};

	/**
	 * Preparation: create Form object and append data
	 * @namespace wtf_thumb.functions.getFormData
	 * @param {string} action
	 */
	wtf_thumb.functions.prototype.getFormData = function(action) {
		var submitter = {};
		submitter.upload = action === 'upload';
		submitter.remove = action === 'remove';

		form_data = new FormData(wtf_thumb.form);
		form_data.append('wpenon_thumbnail_upload', submitter.upload);
		form_data.append('wpenon_thumbnail_delete', submitter.remove);
		form_data.append('energieausweis_id', wtf_thumb.energieausweis_id);

		return form_data;
	};

	/**
	 * Create a msg node and append this to the form
	 * @namespace wtf_thumb.functions.appendMsg
	 * @param {string} msg
	 */
	wtf_thumb.functions.prototype.appendMsg = function(msg) {
		var div = document.createElement('div');
			div.innerHTML = msg;
			div.classList.add('error');

		wtf_thumb.triggerNodes.parenntNode.appendChild(div);
	};

	/**
	 * Send xhr
	 * @namespace wtf_thumb.functions.xhr
	 * @param {string} action
	 */
	wtf_thumb.functions.prototype.xhr = function(action) {
		var self = this;
			self.action = action;

		var infoNode = document.createElement('span');
		infoNode.setAttribute('style', 'float:right');

		wtf_thumb.triggerNodes.parenntNode.appendChild( infoNode );
		self.percentSpan = wtf_thumb.triggerNodes.parenntNode.querySelector('span');

		var xhr = new XMLHttpRequest();
			xhr.open('POST', wtf_thumb.form.action, true);

			xhr.onload = function () {
				if (xhr.status === 200) {
					var response = JSON.parse(xhr.responseText);

					if(!response.error){
						switch (self.action) {
							case "upload":
								wtf_thumb.functions.prototype.upload(true, response);
								wtf_thumb.functions.prototype.updatePreview(response, 'addTumbnail');
								$("body").trigger("wpenon_ajax_end");
								break;
							case "remove":
								wtf_thumb.functions.prototype.remove(true, response);
								$("body").trigger("wpenon_ajax_end");
								break;
						}
					}


				} else {
					wtf_thumb.functions.appendMsg(xhr.responseText);
				}
			};

			xhr.upload.onprogress = function( /* event */ ) {
				if(self.percentSpan && self.action === "upload"){
					//var percentUpload = Math.floor( 100 * event.loaded / event.total );
					self.percentSpan.innerHTML = '<br />Verarbeite Bilddaten ...';
				}
			};

			$("body").trigger("wpenon_ajax_start");

			xhr.send(wtf_thumb.functions.prototype.getFormData(action));
	};

	/**
	 * @property {Int} energieausweis_id
	 * @property {Node} form
	 * @property {Object} triggerNodes collection of nodes
	 * @property {Node} preview
	 * @property {Function} functions collection of progress functions
	 */
	wtf_thumb.energieausweis_id = _wpenon_data.energieausweis_id;

	wtf_thumb.form = document.querySelector('#wpenon-thumbnail-form');
	wtf_thumb.form.file = wtf_thumb.form.querySelector('[name="wpenon_thumbnail_file"]');
	wtf_thumb.preview = document.querySelector('.thumbnail-wrapper');

	wtf_thumb.triggerNodes = {
		action: {
			upload: wtf_thumb.form.querySelector('[name="wpenon_thumbnail_upload"]'),
			del: wtf_thumb.form.querySelector('[name="wpenon_thumbnail_delete"]')
		},
		parenntNode: wtf_thumb.form.querySelector('.image-buttons')
	};


	wtf_thumb.functions.prototype.addEvents();
});
