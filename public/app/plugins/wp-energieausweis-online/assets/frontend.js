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
	 * Image upload
	 **/
	$('#wpenon-thumbnail-form').on('submit',(function(e) {
		e.preventDefault();
		var thumbBodyElem = document.querySelector('#wpenon-thumbnail-form');

		var formData = new FormData(this);
			formData.append('wpenon_thumbnail_upload', true);
			formData.append('energieausweis_id', _wpenon_data.energieausweis_id);

		var xhr = new XMLHttpRequest();

		xhr.open('POST', this.action, true);

		xhr.onload = function () {
			if (xhr.status === 200) {
				var response = JSON.parse(xhr.responseText);

				if(!response.error){
					var image = response.tmpImage;

					if(image) {
						var imageWrapper = document.querySelector('.thumbnail-wrapper');
						imageWrapper.innerHTML = "";

						var divImg = document.createElement('div');
						divImg.setAttribute("style", "background: url(" + image.path + ") no-repeat center center;background-size: cover;height: 250px;");

						imageWrapper.appendChild(divImg);
					}
				}

				$("body").trigger("wpenon_ajax_end");

			} else {
				var div = document.createElement('div');
				div.innerHTML = "Fehler beim Upload " + xhr.responseText;
				div.classList.add('error');
				thumbBodyElem.appendChild(div);
			}
		};

		xhr.upload.onprogress = function (e) {
			var percentUpload = Math.floor(100 * e.loaded / e.total);
			console.log({percentUpload,e});
		};

		$("body").trigger("wpenon_ajax_start");

		xhr.send(formData);
	}));

	$("#wpenon_thumbnail_file").on("change", function() {
		$("#imageUploadForm").submit();
	});

});
