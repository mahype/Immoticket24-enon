jQuery(document).ready(function($) {
	if (typeof _wpenon_data === "object") {
		$(_wpenon_data.select2_selector).select2({
			containerCss: {
				width: "100%"
			},
			closeOnSelect: false,
			escapeMarkup: function(m) {
				return m;
			}
		});

		_wpenon_data.active_requests = 0;

		_wpenon_data.values = {};

		_wpenon_data.field = function(field) {
			if (field instanceof jQuery) {
				return field;
			}
			return $("#" + field);
		};

		_wpenon_data.get_selector = function(field) {
			var $field = _wpenon_data.field(field);
			if (field instanceof jQuery) {
				field = $field.attr("id");
			}
			if ($field.is('input[type="radio"]')) {
				return "#" + field + '-wrap input[type="radio"]';
			}
			return "#" + field;
		};

		_wpenon_data.get_trigger = function(field) {
			var $field = _wpenon_data.field(field);
			if ($field.is('input[type="checkbox"]') || $field.is('input[type="radio"]')) {
				return "click";
			}
			return "blur";
		};

		_wpenon_data.get_value = function(field, force_get) {
			var $field = _wpenon_data.field(field);
			if (typeof force_get === "undefined") {
				force_get = false;
			}
			if (typeof _wpenon_data.values[$field.attr("id")] === "undefined" || force_get) {
				var value = "";

				if ($field.is('input[type="checkbox"]')) {
					value = $field.prop("checked");
				} else if ($field.is('input[type="radio"]')) {
					var $elements = $("#" + $field.attr("id") + '-wrap input[type="radio"]');
					value = $elements.filter(":checked").val();
				} else if ($field.is("select[multiple]")) {
					if ($field.val() === null) {
						value = [];
					} else {
						value = $field.val();
					}
				} else {
					value = $field.val();
					if ($field.hasClass("float-control")) {
						value = _wpenon_data.parser.parseFloat(value);
					} else if ($field.hasClass("int-control")) {
						value = _wpenon_data.parser.parseInt(value);
					}
				}

				_wpenon_data.values[$field.attr("id")] = value;
			}
			return _wpenon_data.values[$field.attr("id")];
		};

		_wpenon_data.set_value = function(field, value, force) {
			var $field = _wpenon_data.field(field);
			var old_value = _wpenon_data.get_value($field);
			if (typeof force === "undefined") {
				force = false;
			}

			if (value === null || value === "null") {
				if (force) {
					$field.prop("readonly", false);
				}
			} else {
				if (force) {
					$field.prop("readonly", true);
				}
				if ($field.is('input[type="checkbox"]')) {
					value = _wpenon_data.parser.parseBoolean(value);
					$field.prop("checked", value);
				} else if ($field.is('input[type="radio"]')) {
					var $elements = $("#" + $field.attr("id") + '-wrap input[type="radio"]');
					$elements.prop("checked", false);
					$elements.filter('[value="' + value + '"]').prop("checked", true);
				} else if ($field.hasClass("float-control")) {
					$field.val(_wpenon_data.formatter.formatFloat(value));
				} else if ($field.hasClass("int-control")) {
					$field.val(_wpenon_data.formatter.formatInt(value));
				} else {
					$field.val(value);
				}

				_wpenon_data.values[$field.attr("id")] = value;

				if (value != old_value) {
					$field.trigger(_wpenon_data.get_trigger($field));
				}
			}
		};

		_wpenon_data.format_unit = function(unit) {
			if (unit.indexOf("2") > -1) {
				if (unit.indexOf("&sup2;") < 0) {
					unit = unit.replace("2", "&sup2;");
				}
			} else if (unit.indexOf("3") > -1) {
				if (unit.indexOf("&sup3;") < 0) {
					unit = unit.replace("3", "&sup3;");
				}
			}
			return unit;
		};

		if ($("#_wpenon_progress").length) {
			$(".form-group input, .form-group select").each(function() {
				var $field = $(this);
				//var trigger = $field.is( 'select' ) ? 'select2-open' : _wpenon_data.get_trigger( $field );
				var trigger = _wpenon_data.get_trigger($field);
				$field.on(trigger, function() {
					var field_slug = $(this).attr("id");
					if (!$("#_wpenon_progress").val()) {
						$("#_wpenon_progress").val(field_slug);
						$(this)
							.parents(".form-group")
							.addClass("is-done");
					} else if (
						0 >
						$("#_wpenon_progress")
							.val()
							.search(field_slug)
					) {
						$("#_wpenon_progress").val($("#_wpenon_progress").val() + "," + field_slug);
						$(this)
							.parents(".form-group")
							.addClass("is-done");
					}
				});
			});
		} else if ($("#group-basisdaten").length) {
			$("#group-basisdaten .form-group input").on("change", function() {
				var value = $(this).val();

				if (value && value.length) {
					$(this)
						.parents(".form-group")
						.addClass("is-done");
				} else {
					$(this)
						.parents(".form-group")
						.removeClass("is-done");
				}
			});
		}

		$.each(_wpenon_data.dynamic_fields, function(origin_field, target_fields) {
			var event_selector = _wpenon_data.get_selector(origin_field);
			var event_trigger = _wpenon_data.get_trigger(origin_field);

			$(event_selector).on(event_trigger, target_fields, function(event) {
				_wpenon_data.get_value($(this), true);
				$.each(event.data, function(key, field) {
					var callback_args = [];
					for (var i in field.callback_args) {
						var arg = field.callback_args[i];
						var j;
						if (typeof field.callback_args[i] === "object") {
							if (typeof field.callback_args[i].length !== "undefined") {
								arg = [];
								for (j in field.callback_args[i]) {
									if (typeof field.callback_args[i][j] === "string" && field.callback_args[i][j].indexOf("field::") === 0) {
										arg[j] = _wpenon_data.get_value(field.callback_args[i][j].replace("field::", ""));
									} else {
										arg[j] = field.callback_args[i][j];
									}
								}
							} else {
								var properties = Object.keys(field.callback_args[i]);
								arg = {};
								for (j in properties) {
									if (typeof field.callback_args[i][properties[j]] === "string" && field.callback_args[i][properties[j]].indexOf("field::") === 0) {
										arg[properties[j]] = _wpenon_data.get_value(field.callback_args[i][properties[j]].replace("field::", ""));
									} else {
										arg[properties[j]] = field.callback_args[i][properties[j]];
									}
								}
							}
						} else if (typeof field.callback_args[i] === "string" && field.callback_args[i].indexOf("field::") === 0) {
							arg = _wpenon_data.get_value(field.callback_args[i].replace("field::", ""));
						} else {
							arg = field.callback_args[i];
						}
						callback_args.push(arg);
					}

					var wpenon_process_response = function(response) {
						if (typeof response === "string" && response.indexOf("error::") === 0 && _wpenon_data.debug) {
							console.warn("WPENON AJAX callback error: " + response.replace("error::", ""));
						} else {
							if (field.mode === "value") {
								_wpenon_data.set_value(field.target_slug, response, field.callback_hard);
							} else if (field.mode == "options") {
								if (typeof response !== "object") {
									response = $.parseJSON(response);
								}
								response = _wpenon_data.parser.parseObject(response);
								var $target = $("#" + field.target_slug);
								if ($target.is("select")) {
									var oldval = $target.val();
									var newoption = "";
									var change = false;
									$target.empty();
									if ($target.prop("multiple")) {
										if (oldval !== null) {
											change = true;
										}
										oldval = [];
									} else {
										if (oldval !== null && oldval !== "" && !response.hasOwnProperty(oldval)) {
											change = true;
											//oldval = Object.keys( response )[0];
											oldval = "";
										}
										oldval = [oldval];
									}
									$target.append('<option value=""' + (oldval.indexOf("") > -1 ? " selected" : "") + ">" + _wpenon_data.i18n.please_select + "</option>");
									for (var option in response) {
										newoption = '<option value="' + option + '"' + (oldval.indexOf(option) > -1 ? " selected" : "") + ">" + response[option] + "</option>";
										$target.append(newoption);
									}
									if (change) {
										_wpenon_data.set_value(field.target_slug, oldval);
									}
								}
							} else if (field.mode === "display") {
								response = _wpenon_data.parser.parseBoolean(response);
								if (response) {
									$("#" + field.target_slug + "-wrap").fadeIn();
									if (field.target_required) {
										$("#" + field.target_slug).prop("required", true);
									}
								} else {
									$("#" + field.target_slug + "-wrap").fadeOut();
									if (field.target_required) {
										$("#" + field.target_slug).prop("required", false);
									}
								}
							} else {
								response = _wpenon_data.parser.parseString(response);
								var selector = "";
								if (field.mode === "label") {
									if (field.target_type === "headline") {
										selector = "#" + field.target_slug + "-wrap .form-headline";
									} else {
										selector = 'label[for="' + field.target_slug + '"]';
									}
								} else if (field.mode === "description") {
									selector = "#" + field.target_slug + '-wrap [data-toggle="tooltip"]';
								} else {
									selector = "#" + field.target_slug + "-unit";
									response = _wpenon_data.format_unit(response);
								}
								if (field.mode === "description") {
									$(selector).attr("data-original-title", response);
								} else if (field.mode === "unit") {
									if ($(selector).length) {
										$(selector).html(response);
									} else {
										$("#" + field.target_slug + "-wrap").addClass("has-unit");
										$("#" + field.target_slug).after('<span id="' + field.target_slug + '-unit" class="unit">' + response + "</span>");
									}
								} else {
									$(selector).html(response);
								}
							}
						}
					};

					if (typeof _wpenon_data.dynamic_functions[field.callback] !== "undefined") {
						wpenon_process_response(_wpenon_data.dynamic_functions[field.callback].apply(null, callback_args));
					} else {
						if (_wpenon_data.active_requests === 0) {
							$("body").trigger("wpenon_ajax_start");
						}
						_wpenon_data.active_requests += 1;
						$.post(
							_wpenon_data.ajax_url,
							{
								action: "wpenon_dynamic_callback",
								security_nonce: _wpenon_data.security_nonce,
								callback: field.callback,
								callback_args: callback_args
							},
							function(response) {
								wpenon_process_response(response);
								_wpenon_data.active_requests -= 1;
								if (_wpenon_data.active_requests === 0) {
									$("body").trigger("wpenon_ajax_end");
								}
							}
						);
					}
				});
			});
		});
	}
});
