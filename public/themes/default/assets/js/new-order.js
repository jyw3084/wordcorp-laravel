var data_arr = [];

window.localStorage.removeItem('_discount');
window.localStorage.removeItem('order_created');
window.localStorage.removeItem('delivery_emails');
window.localStorage.removeItem('ntd_quote_data');
window.localStorage.removeItem('_discount_price');
window.localStorage.removeItem('order_id');
window.localStorage.setItem("new_order", true);

var radioSelected = $('input[name="billingLocale"]:checked').val();
if (radioSelected === 'ntd') {
	window.localStorage.setItem("billing", "ntd");
}
$('#selectBillingLocale input').on('change', function () {

	var radioSelected = $('input[name="billingLocale"]:checked').val();
	if (radioSelected === 'usd') {
		$('#proceedBtn').attr('href', './usd');
		window.localStorage.setItem("billing", "usd");
	} else {
		$('#proceedBtn').attr('href', './ntd');
		window.localStorage.setItem("billing", "ntd");
	}
});

$(function () {
	var current_fs, next_fs, previous_fs; //fieldsets
	var opacity;

	$('.next').click(function () {
		current_fs = $(this).parent();
		next_fs = $(this).parent().next();

		//Add Class Active
		$('#progressbar li').eq($('fieldset').index(next_fs)).addClass('active');

		//show the next fieldset
		next_fs.show();
		//hide the current fieldset with style
		current_fs.animate(
			{ opacity: 0 },
			{
				step: function (now) {
					// for making fielset appear animation
					opacity = 1 - now;

					current_fs.css({
						display: 'none',
						position: 'relative'
					});
					next_fs.css({ opacity: opacity });
				},
				duration: 600
			}
		);

		// append();
	});

	$('.previous').click(function () {
		current_fs = $(this).parent();
		previous_fs = $(this).parent().prev();

		//Remove class active
		$('#progressbar li')
			.eq($('fieldset').index(current_fs))
			.removeClass('active');

		//show the previous fieldset
		previous_fs.show();

		//hide the current fieldset with style
		current_fs.animate(
			{ opacity: 0 },
			{
				step: function (now) {
					// for making fielset appear animation
					opacity = 1 - now;

					current_fs.css({
						display: 'none',
						position: 'relative'
					});
					previous_fs.css({ opacity: opacity });
				},
				duration: 600
			}
		);
	});

	$('.radio-group .radio').click(function () {
		$(this).parent().find('.radio').removeClass('selected');
		$(this).addClass('selected');
	});

	validator();

	$('.docs-div .remove').on('click', function () {
		var currentDiv = $(this)[ 0 ].parentElement.parentElement.parentElement.parentElement;
		$(currentDiv).remove()
	});
});

$('#word-count').click(function () {
	$('#word-count').prop('disabled', true);
	data_arr = [];

	var step_2_form_data = $('.docs-div');

	$(step_2_form_data).each(function (index) {
		var code_language_from = $(step_2_form_data[ index ]).find('.selectFromLang').val();
		var code_language_to = $(step_2_form_data[ index ]).find('.selectToLang').val();
		var language_from = $(step_2_form_data[ index ]).find('.selectFromLang option:selected').html();
		var language_to = $(step_2_form_data[ index ]).find('.selectToLang option:selected').html();
		var doc = $(step_2_form_data[ index ]).find('.upload-file')[ 0 ].files;
		var language_combo = code_language_from + ' -> ' + code_language_to;

		var formData = new FormData();
		formData.append("file", doc[ 0 ]);
		//upload
		var word_count;
		var file_path;
		var file_name;
		$.ajax({
			url: "uploadfile",
			type: "POST",
			enctype: 'multipart/form-data',
			contentType: false,
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			// dataType:"json",
			data: formData,
			processData: false,
			async: false,
			success: function (data) {
				var lang = 'count_' + code_language_from;
				lang_words = data[ 0 ].word_count[ 'count_zh' ] > data[ 0 ].word_count[ 'count_en' ] ? data[ 0 ].word_count[ 'count_zh' ] : data[ 0 ].word_count[ 'count_en' ];
				words = data[ 0 ].word_count[ lang ] != 'undefined' ? data[ 0 ].word_count[ lang ] : lang_words;

				file_name = data[ 0 ].file_name;
				file_path = data[ 0 ].path;
				file_id = data[ 0 ].id;
				word_count = words;
				$('#word-count').prop('disabled', false);
			}
		});

		var language_rate;
		var translation_rate_tw;
		var translation_rate_us;
		var translator_pay_rate;
		var editing_rate_tw;
		var editing_rate_us;
		var editor_pay_rate;

		get_lang_combo = $.ajax({
			url: 'get-language-combo-by-code',
			type: "GET",
			dataType: "json",
			async: false,
			data: {
				'language_combo': language_combo
			},
			success: function (data) {
				if (window.localStorage.getItem("billing") == 'ntd') {
					language_rate = data.translation_rate_tw;
				}
				if (window.localStorage.getItem("billing") == 'usd') {
					language_rate = data.translation_rate_us;
				}
				translation_rate_tw = data.translation_rate_tw,
					translation_rate_us = data.translation_rate_us,
					translator_pay_rate = data.translator_pay_rate,
					editing_rate_tw = data.editing_rate_tw,
					editing_rate_us = data.editing_rate_us,
					editor_pay_rate = data.editor_pay_rate
			},
			error: function (err) {
				language_rate = 1;
			}
		});

		data = [ {
			'language_combo': language_combo,
			'language_from': language_from,
			'language_to': language_to,
			'doc': file_path,
			'file_name': file_name,
			'file_id': file_id,
			'word_count': word_count,
			'service_rate': language_rate,
			'translation_rate_tw': translation_rate_tw,
			'translation_rate_us': translation_rate_us,
			'translator_pay_rate': translator_pay_rate,
			'editing_rate_tw': editing_rate_tw,
			'editing_rate_us': editing_rate_us,
			'editor_pay_rate': editor_pay_rate
		} ];

		data_arr.push(data)
	});

	window.localStorage.setItem("step_2_data", JSON.stringify(data_arr));

	append();

});

function validator() {
	$('#main-div').show(function () {
		data_arr = [];

		var billingCurrency = 'ntd';
		if (window.localStorage.getItem('billing') == 'usd') {
			billingCurrency = 'usd';
		}

		$('#uploadDoc').change(function () {
			var filename = $(this).val();
			var orig_filename = filename.replace(/C:\\fakepath\\/i, '')
			var extension = filename.replace(/^.*\./, '');

			if (extension != 'docx' && extension != 'doc' && extension != 'txt' && extension != 'odt' && extension != 'rtf') {
				$('#fileUnsupportedModal').modal('show');
				$(this).val('');
			}
			else {
				var div = $('.docs-div');
				id = div.length + 1;
				$('#main-div').append(
					`
						<div class="docs-div card bg-light mb-3">
							<div class="card-body">
								<div class="row align-items-center mb-3">
									<div class="col">
										<h5>`+ $('#trans_file').attr('data-id') + `<i class="bi bi-file-text"></i></h5>
										<p>` + orig_filename + `
										</p>
									</div>
									<div class="col-md-2">
										<button type="button" class="btn btn-outline-danger remove">
											<i class="bi bi-x-circle-fill"></i>` + $('#trans_remove').attr('data-id') +
					`</button>
									</div>
								</div>
								<div id="_main_data">
									<h5>` + $('#trans_selectLang').attr('data-id') + `</h5>
									<div class="form-inline">
										<label for="selectFromLang_` + id + `" class="mr-sm-2">` + $('#trans_from').attr('data-id') + `</label>
										<select
												name="selectFromLang"
												class="form-control mr-sm-2 selectFromLang"
												id="selectFromLang_` + id + `">
												<option value="default">`+ $('#language').data('id') + `</option>
											</select>
											<label for="selectToLang_` + id + `" class="mr-sm-2">` + $('#trans_to').attr('data-id') + `</label>
											<select
												name="selectToLang"
												class="form-control mr-sm-2 selectToLang"
												id="selectToLang_` + id + `">
												<option value="default">`+ $('#language').data('id') + `</option>
											</select>
									</div>
								</div>
								<div class="mt-3 custom-file hidde_file`+ id + `" hidden>
								</div>
							</div>
						</div>
					`
				);
				$('#word-count').prop('disabled', true)

				var $this = $(this), $clone = $this.clone();
				$clone[ 0 ].id = 'file_' + id;
				$('.hidde_file' + id).append($clone);
				$this[ 0 ].value = '';

				$.ajax({
					url: 'get-languages',
					type: "GET",
					dataType: "json",
					headers: { 'billing': billingCurrency },

					success: function (data) {
						$("#selectFromLang_" + id).children().not(':first').remove();
						$(data.code).each(function (index, value) {
							$("#selectFromLang_" + id).append('<option value=' + value + '>' + data.value[ index ] + '</option>');
						});
						$("#selectFromLang_" + id).on('change', function () {
							$(this).nextAll('select').children().not(':first').remove();
							var code = $(this).val();
							var _this = $(this);
							$.ajax({
								url: 'get-languages-to/' + code,
								type: "GET",
								dataType: "json",
								headers: { 'billing': billingCurrency },

								success: function (data) {
									$(data.code).each(function (index, value) {
										_this.nextAll('select').append('<option value=' + value + '>' + data.value[ index ] + '</option>');
									});
								},
							});
						})
					},
				});

				$('.docs-div .remove').on('click', function () {
					var currentDiv = $(this)[ 0 ].parentElement.parentElement.parentElement.parentElement;
					$(currentDiv).remove()
					$('.selectFromLang').filter(function () {
						if ($(this).val() != 'default') {
							$('#word-count').removeAttr('disabled')
						}
						else {
							$('#word-count').prop('disabled', true)
						}
					});

					$('.selectToLang').filter(function () {
						if ($(this).val() != 'default') {
							$('#word-count').removeAttr('disabled')
						}
						else {
							$('#word-count').prop('disabled', true)
						}
					});
				});
			}
		});
	});

	$("#main-div").on('change', function () {
		$('#word-count').removeAttr('disabled');
		$('.selectFromLang').filter(function () {
			if ($(this).val() == 'default') {
				$('#word-count').prop('disabled', true);
			}
		});
		$('.selectToLang').filter(function () {
			if ($(this).val() == 'default') {
				$('#word-count').prop('disabled', true);
			}
		});
	});
}

function append() {
	var step_2_data = JSON.parse(window.localStorage.getItem("step_2_data"));
	if (window.localStorage.getItem("billing") == 'ntd') {
		currency = 'NTD';
		translation_rate = step_2_data[ 'translation_rate_tw' ];
		editing_rate = step_2_data[ 'editing_rate_tw' ];
	}
	if (window.localStorage.getItem("billing") == 'usd') {
		currency = 'USD';
		translation_rate = step_2_data[ 'translation_rate_us' ];
		editing_rate = step_2_data[ 'editing_rate_us' ];
	}

	$(step_2_data).each(function (index) {
		var data = step_2_data[ index ];
		if (window.localStorage.getItem("billing") == 'ntd') {
			translation_rate = data[ 0 ].translation_rate_tw;
			editing_rate = data[ 0 ].editing_rate_tw;
		}
		if (window.localStorage.getItem("billing") == 'usd') {
			translation_rate = data[ 0 ].translation_rate_us;
			editing_rate = data[ 0 ].editing_rate_us;
		}
		$('.step3').append(
			`
			<div class="step2-div card bg-light mb-3">
								<div class="card-body">
									<div class="row align-items-center mb-3">
										<div class="col">
											<h5>` + $('#trans_file').attr('data-id') + ` <i class="bi bi-file-text"></i></h5>
											<p>` + data[ 0 ].file_name + `</p>
										</div>
										<div class="col-md-2">
											<h5>` + $('#trans_wordCount').attr('data-id') + `</h5>
											<p>` + data[ 0 ].word_count + `</p>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<h5>` + $('#trans_selectedLang').attr('data-id') + `</h5>
											<p>
												<span>` + data[ 0 ].language_from + `</span>
												<i class="bi bi-arrow-right"></i>
												<span>` + data[ 0 ].language_to + `</span>
											</p>
										</div>
										<div class="col-md-4">
											<h5>` + $('#trans_expertise').attr('data-id') + `</h5>
											<div class="form-group">
												<select id="expertise" name="expertise" class="form-control">
													<option value="no_need">` + $('#no_need').attr('data-id') + `</option>
													<option value="art">` + $('#art').attr('data-id') + `</option>
													<option value="bussiness">` + $('#bussiness').attr('data-id') + `</option>
													<option value="ad">` + $('#ad').attr('data-id') + `</option>
													<option value="car">` + $('#car').attr('data-id') + `</option>
													<option value="cv">` + $('#cv').attr('data-id') + `</option>
													<option value="certificates">` + $('#certificates').attr('data-id') + `</option>
													<option value="finance">` + $('#finance').attr('data-id') + `</option>
													<option value="game">` + $('#game').attr('data-id') + `</option>
													<option value="legal">` + $('#legal').attr('data-id') + `</option>
													<option value="marketing">` + $('#marketing').attr('data-id') + `</option>
													<option value="medical">` + $('#medical').attr('data-id') + `</option>
													<option value="mobile">` + $('#mobile').attr('data-id') + `</option>
													<option value="patents">` + $('#patents').attr('data-id') + `</option>
													<option value="scientific">` + $('#scientific').attr('data-id') + `</option>
													<option value="it">` + $('#it').attr('data-id') + `</option>
													<option value="technical">` + $('#technical').attr('data-id') + `</option>
													<option value="tourism">` + $('#tourism').attr('data-id') + `</option>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<h5>` + $('#trans_style').attr('data-id') + `</h5>
											<div class="form-group">
												<select id="style" name="style" class="form-control">
													<option value="1">
													` + $('#style_1').attr('data-id') + `
													</option>
													<option value="2">
													` + $('#style_2').attr('data-id') + `
													</option>
													<option value="3">
													` + $('#style_3').attr('data-id') + `
													</option>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<h5>` + $('#trans_notes').attr('data-id') + `</h5>
											<textarea
												class="form-control"
												id="notes"
												name="notes"
												rows="3"
											></textarea>
										</div>
										<div class="col">
											<h5>` + $('#trans_service').attr('data-id') + `</h5>
											<select id="service" name="service" class="form-control">
												<!-- Show rates for language combo in select options -->
												<option value="translation-editing"
													>`+ $('#option_1').attr('data-id') + `
													(`+ (parseFloat(translation_rate) + parseFloat(editing_rate)).toFixed(2) + ` ` + currency + `/` + $('#word').attr('data-id') + `)</option
												>
												<option value="translation-only"
													>`+ $('#option_0').attr('data-id') + ` (` + translation_rate + ` ` + currency + `/` + $('#word').attr('data-id') + `)</option
												>
											</select>
											<button
												type="button"
												data-toggle="modal"
												data-target="#serviceTypesModal"
												class="btn btn-link"
											>
												<i class="bi bi-question-circle-fill"></i> `+ $('#trans_type_of_service').attr('data-id') + `
											</button>
										</div>
									</div>
								</div>
							</div>
			`
		);
	});
};

$('#btn_back2').click(function () {
	$('.step3').empty();
	window.localStorage.removeItem("step_2_data");
	data_arr = [];
});

$('#btn_next2').click(function () {
	var last_step = JSON.parse(window.localStorage.getItem("step_2_data"));

	last_data_arr = [];

	$('.last_step').children().remove();
	var last_form_data = $('.step2-div');
	$(last_step, last_form_data).each(function (index) {
		var data = last_step[ index ];

		//get form data

		// $(last_form_data).each(function( index ) {
		var expertise = $(last_form_data[ index ]).find('#expertise').val();
		var style = $(last_form_data[ index ]).find('#style').val();
		var service = $(last_form_data[ index ]).find('#service').val();
		var notes = $(last_form_data[ index ]).find('#notes').val();
		form_data = {
			'expertise': expertise,
			'style': style,
			'service': service,
			'notes': notes
		};

		data.push(form_data)
		last_data_arr.push(data)

	});
	window.localStorage.setItem("ntd_quote_data", JSON.stringify(last_data_arr));
	var total = 0;
	var all_wordCount = 0;
	var has_editing = false;
	$(last_data_arr).each(function (index, value) {
		//_data = last_data_arr[index];
		if (window.localStorage.getItem("billing") == 'ntd') {
			var service_rate = value[ 0 ].translation_rate_tw;

			if (value[ 1 ].service == "translation-editing") {
				service = $('#option_1').attr('data-id');
				service_rate = (parseFloat(value[ 0 ].translation_rate_tw) + parseFloat(value[ 0 ].editing_rate_tw)).toFixed(2);
				has_editing = true;
			}
			else {
				service = $('#option_0').attr('data-id');
				service_rate = (parseFloat(value[ 0 ].translation_rate_tw)).toFixed(2);
			}
		}
		if (window.localStorage.getItem("billing") == 'usd') {
			var service_rate = value[ 0 ].translation_rate_us;

			if (value[ 1 ].service == "translation-editing") {
				service = $('#option_1').attr('data-id');
				service_rate = (parseFloat(value[ 0 ].translation_rate_us) + parseFloat(value[ 0 ].editing_rate_us)).toFixed(2);
				has_editing = true;
			}
			else {
				service = $('#option_0').attr('data-id');
				service_rate = (parseFloat(value[ 0 ].translation_rate_us)).toFixed(2);
			}
		}

		sub_total = value[ 0 ].word_count * service_rate;
		total = total + sub_total;
		all_wordCount = all_wordCount + value[ 0 ].word_count;

		var currency = 'NTD';
		if (window.localStorage.getItem("billing") == 'ntd') {
			currency = 'NTD';
			total = Math.round(total);

			$('.last_step').append(
				`<div class="card bg-light mb-3">
						<div class="card-body">
							<div class="row align-items-center mb-3">
								<div class="col">
									<h5>` + $('#trans_file').attr('data-id') + ` <i class="bi bi-file-text"></i></h5>
									<p>` + value[ 0 ].file_name + `</p>
								</div>
								<div class="col-md-2">
									<h5>` + $('#trans_wordCount').attr('data-id') + `</h5>
									<p>` + value[ 0 ].word_count + `</p>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<h5>` + $('#trans_translation').attr('data-id') + `</h5>
									<p>
										<span>` + value[ 0 ].language_from + `</span>
										<i class="bi bi-arrow-right"></i>
										<span>` + value[ 0 ].language_to + `</span>
									</p>
								</div>
								<div class="col-md-4">
									<h5>` + $('#trans_service').attr('data-id') + `</h5>
									<p>` + service + `</p>
								</div>
								<div class="col-md-2">
									<h5>` + $('#trans_service_rate').attr('data-id') + `</h5>
									<p>`+ service_rate + ` ` + currency + `/` + $('#word').attr('data-id') + `</p>
								</div>
								<div class="col-md-2">
									<h5>` + $('#trans_price').attr('data-id') + `</h5>
									<p>` + Math.round(sub_total) + ` ` + currency + `</p>
								</div>
							</div>
						</div>
					</div>`
			);
		}
		if (window.localStorage.getItem("billing") == 'usd') {
			currency = 'USD';
			total = Math.round(total * 100) / 100;

			$('.last_step').append(
				`<div class="card bg-light mb-3">
						<div class="card-body">
							<div class="row align-items-center mb-3">
								<div class="col">
									<h5>` + $('#trans_file').attr('data-id') + ` <i class="bi bi-file-text"></i></h5>
									<p>` + value[ 0 ].file_name + `</p>
								</div>
								<div class="col-md-2">
									<h5>` + $('#trans_wordCount').attr('data-id') + `</h5>
									<p>` + value[ 0 ].word_count + `</p>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<h5>` + $('#trans_translation').attr('data-id') + `</h5>
									<p>
										<span>` + value[ 0 ].language_from + `</span>
										<i class="bi bi-arrow-right"></i>
										<span>` + value[ 0 ].language_to + `</span>
									</p>
								</div>
								<div class="col-md-4">
									<h5>` + $('#trans_service').attr('data-id') + `</h5>
									<p>` + service + `</p>
								</div>
								<div class="col-md-2">
									<h5>` + $('#trans_service_rate').attr('data-id') + `</h5>
									<p>`+ service_rate + ` ` + currency + `/` + $('#word').attr('data-id') + `</p>
								</div>
								<div class="col-md-2">
									<h5>` + $('#trans_price').attr('data-id') + `</h5>
									<p>` + Math.round(sub_total * 100) / 100 + ` ` + currency + `</p>
								</div>
							</div>
						</div>
					</div>`
			);
		}
	});

	if (window.localStorage.getItem("billing") == 'ntd' && total < 300) {
		total = 300;
	}
	if (window.localStorage.getItem("billing") == 'usd' && total < 10) {
		total = 10;
	}

	_order_date = new Date();
	_delivery_date = new Date();
	var _translation_hours = 0;
	var _translation_deadline = new Date();
	var _editing_hours = 0;
	const monthNames = [ "January", "February", "March", "April", "May", "June",
		"July", "August", "September", "October", "November", "December"
	];
	_order_date_display = monthNames[ _order_date.getMonth() ] + ' ' + _order_date.getDate() + ', ' + _order_date.getFullYear();

	function formatAMPM(date) {
		let hours = _delivery_date.getHours();
		let minutes = _delivery_date.getMinutes();
		const ampm = hours >= 12 ? 'PM' : 'AM';
		hours = hours % 12;
		hours = hours ? hours : 12; // the hour '0' should be '12'
		minutes = minutes < 10 ? '0' + minutes : minutes;
		const strTime = hours + ':' + minutes + ' ' + ampm;
		return strTime;
	}

	_translation_hours = Math.round(all_wordCount / 2000 * 24) + 36;
	if (has_editing) _editing_hours = Math.round(_translation_hours / 4);

	_delivery_time = _translation_hours + _editing_hours;
	_translation_deadline.setHours(_translation_deadline.getHours() + _translation_hours);
	_delivery_date.setHours(_delivery_date.getHours() + _delivery_time);
	_estimated_delivery_date = monthNames[ _delivery_date.getMonth() ] + ' ' + _delivery_date.getDate() + ', ' + _delivery_date.getFullYear() + ' ' + formatAMPM(_delivery_date);

	order_date_data = [ {
		'order_date': _order_date.toString(),
		'order_date_display': _order_date_display,
		'translation_hours': _translation_hours,
		'translation_deadline': _translation_deadline.toString(),
		'editing_hours': _editing_hours,
		'delivery_time': _delivery_time,
		'delivery_date': _delivery_date.toString(),
		'estimated_delivery_date': _estimated_delivery_date
	} ];
	window.localStorage.setItem("order_time", JSON.stringify(order_date_data));

	$('#span-total').text(total + ' ' + currency);
	$('#delivery_hours').text(_delivery_time);
	$('#delivery_date').text(_estimated_delivery_date);
});

$('#order_summary_back').click(function () {
	$('.last_step').empty();
});

function IsEmail(email) {
	var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (!regex.test(email)) {
		return false;
	} else {
		return true;
	}
}

function makeid(length) {
	var result = '';
	var characters = '0123456789';
	var charactersLength = characters.length;
	for (var i = 0; i < length; i++) {
		result += characters.charAt(Math.floor(Math.random() * charactersLength));
	}
	return result.toString();
}

$('#agree_submit').click(function () {
	if ($('#clientEmail').val() == '') {
		$('#emailError1').removeAttr('hidden');
		$('#emailError2').prop('hidden', true);
	}
	else if (IsEmail($('#clientEmail').val()) == false) {
		$('#emailError1').prop('hidden', true);
		$('#emailError2').removeAttr('hidden');
	}
	else {
		window.localStorage.setItem("order_email", $('#clientEmail').val());
		if (window.localStorage.getItem("billing") == 'ntd') {
			window.location = '/new-order/invoice/' + $('#clientEmail').val();
		}
		if (window.localStorage.getItem("billing") == 'usd') {
			window.localStorage.setItem("new_order", false);
			storage_data = JSON.parse(window.localStorage.getItem("ntd_quote_data"));
			var order_date_details = JSON.parse(window.localStorage.getItem("order_time"));

			// work hours
			var hours = order_date_details[ 0 ].delivery_time;

			// delivery dates
			delivery_date = new Date(order_date_details[ 0 ].delivery_date.slice(1, -1));
			translation_deadline = new Date(order_date_details[ 0 ].translation_deadline.slice(1, -1));

			// delivery dates in format
			delivery_date = delivery_date.getFullYear() + '-' + (delivery_date.getMonth() + 1) + '-' + delivery_date.getDate() + ' ' + delivery_date.getHours() + ':' + delivery_date.getMinutes() + ':' + delivery_date.getSeconds();
			translation_deadline = translation_deadline.getFullYear() + '-' + (translation_deadline.getMonth() + 1) + '-' + translation_deadline.getDate() + ' ' + translation_deadline.getHours() + ':' + translation_deadline.getMinutes() + ':' + translation_deadline.getSeconds();

			associated_doc = [];
			var order_price = 0;
			$(storage_data).each(function (i) {
				raw = storage_data[ i ];
				if (raw[ 1 ].service == "translation-editing") {
					service_rate = (parseFloat(raw[ 0 ].translation_rate_us) + parseFloat(raw[ 0 ].editing_rate_us)).toFixed(2);
					_service_type = 1;
				}
				if (raw[ 1 ].service == "translation-only") {
					service_rate = raw[ 0 ].translation_rate_us;
					_service_type = 0;
				}

				doc_price = Math.round((service_rate * raw[ 0 ].word_count) * 100) / 100;
				var translator_pay = Math.round(raw[ 0 ].translator_pay_rate * raw[ 0 ].word_count);
				if (translator_pay <= 150) {
					translator_pay = 150;
				}

				data = {
					'id': '',
					'language_combination': raw[ 0 ].language_combo,
					'translator_deliver_date': '',
					'editor_deliver_date': '',
					'filename': raw[ 0 ].file_name,
					'file': raw[ 0 ].doc,
					'file_id': raw[ 0 ].file_id,
					'word_count': raw[ 0 ].word_count,
					'currency': 'USD',
					'service_rate': service_rate,
					'doc_price': doc_price,
					'translator_pay': translator_pay,
					'editor_pay': _service_type == 1 ? Math.round(raw[ 0 ].editor_pay_rate * raw[ 0 ].word_count) : 0,
					'translation_rate': raw[ 0 ].translation_rate_us,
					'translator_pay_rate': raw[ 0 ].translator_pay_rate,
					'editing_rate': _service_type == 1 ? raw[ 0 ].editing_rate_us : 0,
					'editor_pay_rate': _service_type == 1 ? raw[ 0 ].editor_pay_rate : 0,
					'expertise': raw[ 1 ].expertise,
					'style': raw[ 1 ].style,
					'notes': raw[ 1 ].notes,
					'service_type': _service_type,
					'translation_deadline': translation_deadline,
					'doc_deadline': delivery_date,
				}

				order_price = Math.round((order_price + service_rate * raw[ 0 ].word_count) * 100) / 100;
				associated_doc.push(data);
			});

			if (order_price < 10) {
				order_price = 10;
			}

			var now = new Date();

			month = now.getMonth() + 1;
			if (month < 10) {
				month = 0 + month.toString();
			}
			else {
				month = month.toString();
			}
			now = now.getFullYear().toString() + month + now.getDate().toString();
			order_number = now + makeid(4);
			email = window.localStorage.getItem("order_email");
			associated_doc = JSON.stringify(associated_doc);
			discount = 0;
			total_price = Math.round((order_price - discount) * 1.05 * 100) / 100;
			hours = hours;
			deadline = delivery_date;
			payment_status = 0;
			order_status = 0;
			urgent = 0;
			note = 'note';
			delivery_date = delivery_date;
			depreciation = 0;
			overseas = 1;
			tax = 0;
			invoice_type = 0;
			title = null;
			carrier = null;
			barcode = null;
			serial_no = null;
			service_type = 0;
			local = 0;
			tax_amount = 0;
			invoice_status = 0;
			delivery_emails = window.localStorage.getItem('delivery_emails');

			window.localStorage.setItem("order_created", true);
			$.ajax({
				url: "/api/create-order",
				type: "POST",
				enctype: 'multipart/form-data',
				contentType: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				dataType: "json",
				data: {
					order_number: order_number,
					email: email,
					associated_doc: associated_doc,
					order_price: order_price,
					discount: discount,
					total_price: total_price,
					hours: hours,
					deadline: deadline,
					payment_status: payment_status,
					order_status: order_status,
					urgent: urgent,
					note: note,
					delivery_date: delivery_date,
					depreciation: depreciation,
					overseas: overseas,
					invoice_type: invoice_type,
					title: title,
					carrier: carrier,
					barcode: barcode,
					serial_no: serial_no,
					service_type: service_type,
					tax: tax,
					tax_amount: tax_amount,
					invoice_status: invoice_status,
					local: local,
					delivery_emails: delivery_emails,
					locale: $('#lang_locale').attr('data-id')
				},
				async: false,
				success: function (data) {
					//window.localStorage.clear();
					window.localStorage.setItem('order_id', data.id);
					window.localStorage.setItem('order_number', data.number);
					window.location = '/new-order/order/' + data.number;
				},
			});
		}
	}
});
