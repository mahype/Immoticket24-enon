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
	 * @property {Int} energieausweis_id
	 * @property {Node} form
	 * @property {Object} trigger collection of nodes
	 * @property {Node} preview
	 * @property {Function} functions collection of progress functions
	 */
	var wtf_thumb = {};
	wtf_thumb.energieausweis_id = _wpenon_data.energieausweis_id;

	wtf_thumb.form = document.querySelector('#wpenon-thumbnail-form');
	wtf_thumb.form.file = wtf_thumb.form.querySelector('[name="wpenon_thumbnail_file"]');

	wtf_thumb.trigger = {};
	wtf_thumb.trigger.upload = wtf_thumb.form.querySelector('[name="wpenon_thumbnail_upload"]');
	wtf_thumb.trigger.del = wtf_thumb.form.querySelector('[name="wpenon_thumbnail_delete"]');
	wtf_thumb.trigger.parenntNode = wtf_thumb.form.querySelector('.image-buttons');

	wtf_thumb.form.percentSpan = wtf_thumb.trigger.parenntNode.querySelector('small');

	wtf_thumb.preview = document.querySelector('.thumbnail-wrapper');

	/**
	 * Progress functions to handle thumb form actions
	 *
	 * @namespace wtf_thumb.functions
	 * @property {function} addEvents
	 * @property {function} upload
	 * @property {function} remove
	 * @property {function} updatePreview
	 * @property {function} xhr
	 */
	wtf_thumb.functions = function(){};

	/**
	 * add click event to form btn
	 *
	 * @namespace wtf_thumb.functions.addEvents
	 */
	wtf_thumb.functions.prototype.addEvents = function(){
		wtf_thumb.functions.prototype.appendFormData();

		for (let [trigger, node] of Object.entries(wtf_thumb.trigger)) {
			node.addEventListener('click', function(e) {
				e.preventDefault();

				switch (trigger) {
					case "upload":
						wtf_thumb.functions.prototype.upload();
						break;
					case "del":
						wtf_thumb.functions.prototype.remove();
						break;
				}

				console.log(node,trigger);
			});
		}
	};

	/**
	 * Handel the thumb upload
	 * @namespace wtf_thumb.functions.uplad
	 */
	wtf_thumb.functions.prototype.upload = function() {
		if(wtf_thumb.trigger.del) {
			wtf_thumb.trigger.parenntNode.removeChild( wtf_thumb.trigger.del );
		}

		var xhrResponse = wtf_thumb.functions.prototype.xhr();
		wtf_thumb.functions.prototype.updatePreview(xhrResponse, 'restorePrev');
	};

	/**
	 * Handel the thumb delete
	 * @namespace wtf_thumb.functions.remove
	 */
	wtf_thumb.functions.prototype.remove = function() {
		if(!confirm('Soll das Bild wirklich gelöscht werden?')){
			return;
		}

		//toto check if this needed
		wtf_thumb.trigger.del.value = '';

		var span = document.createElement('span');
		span.classList.add('glyphicon');
		span.classList.add('glyphicon-picture');

		self.imageWrapper.appendChild(span);
		wtf_thumb.trigger.parenntNode.removeChild(wtf_thumb.trigger.del);

		var xhrResponse = wtf_thumb.functions.prototype.xhr();
		wtf_thumb.functions.prototype.updatePreview(xhrResponse, 'restorePrev');
	};

	/**
	 * Handle thumbnail preview state
	 *
	 * @namespace wtf_thumb.functions.updatePreview
	 * @param {xhr} response
	 * @param {string} action - addTumbnail || restorePrev
	 */
	wtf_thumb.functions.prototype.updatePreview = function(response, action) {
		var image = response.tmpImage;

		if(image) {
			var thumbnailParent = document.querySelector('thumbnail-wrapper');
			thumbnailParent.innerHTML = "";

			if(action === 'addTumbnail'){
				var img = document.createElement('img');
				img.setAttribute("src", image.path);

				thumbnailParent.appendChild(img);

				var button = document.createElement('button');
				button.setAttribute('type', 'submit');
				button.setAttribute('name', 'wpenon_thumbnail_delete');
				button.setAttribute('class', 'btn btn-danger btn-xs');
				button.innerHTML = 'Bild löschen';

				var percentSpan = wtf_thumb.trigger.parenntNode.querySelector('small');

				thumbnailParent.removeChild(percentSpan);
				wtf_thumb.trigger.parenntNode.appendChild(button);

				wtf_thumb.functions.prototype.addEvents();

			}else if(action === 'restorePrev'){
				var span = document.createElement('span');
					span.classList.add('glyphicon');
					span.classList.add('glyphicon-picture');

				thumbnailParent.appendChild(span);

				var delBtn = self.querySelector('.btn-danger');
				self.imageButtons.removeChild(delBtn);
			}
		}
	};

	/**
	 * Preparation: create Form object and append data
	 * @namespace wtf_thumb.functions.appendFormData
	 */
	wtf_thumb.functions.prototype.appendFormData = function() {
		wtf_thumb.form.data = new FormData(wtf_thumb.form);
		wtf_thumb.form.data.append('wpenon_thumbnail_upload', wtf_thumb.trigger.upload);
		wtf_thumb.form.data.append('wpenon_thumbnail_delete', wtf_thumb.trigger.del);
		wtf_thumb.form.data.append('energieausweis_id', wtf_thumb.energieausweis_id);
	};

	/**
	 * Send xhr
	 * @namespace wtf_thumb.functions.xhr
	 */
	wtf_thumb.functions.prototype.xhr = function() {
		console.log('xhr');
		return {};
	};

	wtf_thumb.functions.prototype.addEvents();

	console.log(wtf_thumb);
/*
		self.errorMsg = function(responseText) {
			var div = document.createElement('div');
			div.innerHTML = "Fehler beim Upload " + responseText;
			div.classList.add('error');
			self.parentElem.appendChild(div);
		};

		var xhr = new XMLHttpRequest();

		xhr.open('POST', this.action, true);

		xhr.onload = function () {
			if (xhr.status === 200) {
				var response = JSON.parse(xhr.responseText);

				if(!response.error){
					self.updatePreview(response, self.upload);
					self.deleteAttachment(response, self.removeUpload);
				}

				$("body").trigger("wpenon_ajax_end");
			} else {
				self.errorMsg(xhr.responseText)
			}
		};

		if(self.upload) {
			self.imageButtons.appendChild( document.createElement('small') );
			self.percentSpan = self.imageButtons.querySelector('small');

			xhr.upload.onprogress = function( e ) {
				var percentUpload = Math.floor( 100 * e.loaded / e.total );
				self.percentSpan.innerHTML = '<br />Verarbeite Bilddaten ...';
			};
		}

		$("body").trigger("wpenon_ajax_start");

		xhr.send(self.formData);

	$("#wpenon_thumbnail_file").on("change", function() {
		$("#imageUploadForm").submit();
	});
*/

});
