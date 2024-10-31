$('.radio-group .radio').on('click', function () {
	$(this).parent().find('.radio').removeClass('selected');
	$(this).addClass('selected');

	var selected = $('.radio-group .radio.selected').prop('id');
	if (selected === 'payLater') {
		window.localStorage.setItem("_discount", false);
		$('#payNowBtn').prop('hidden', true);
		$('#payLaterBtn').removeAttr('hidden');
		$('#payBtnHelp').hide();
		_total = Math.round(total);
		_discount = 0;

		_tax_total = _total * 1.05;

		$('#total').text(_total + ' ' + currency);
		$('.total_with_discount').text(Math.round(_tax_total) + ' ' + currency);
		_tax_total = (_total - _discount) * 1.05;
		$('._total_with_discount').text(Math.round(_tax_total) + ' ' + currency);
		$('#discount').text(0 + ' ' + currency);
	} else {
		window.localStorage.setItem("_discount", true);
		$('#payLaterBtn').prop('hidden', true);
		$('#payNowBtn').removeAttr('hidden');
		$('#payBtnHelp').show();

		_total = Math.round(total);
		_discount = Math.round(discount);

		_tax_total = (_total - _discount) * 1.05;

		$('#amount').val(Math.round(_tax_total));
		$('#total').text(Math.round(_total) + ' ' + currency);
		$('.total_with_discount').text(Math.round(_tax_total) + ' ' + currency);
		$('._total_with_discount').text(Math.round(_tax_total) + ' ' + currency);
		$('#discount').text(_discount + ' ' + currency);
	}
});

$('#delivery-email-cc').text(window.localStorage.getItem('delivery_emails'));

$('#changEmail').on('click', function () {
	var emails = $('[name="changEmail"]').val();
	window.localStorage.setItem("delivery_emails", emails);
	$('#changeEmailModal').modal('toggle');
	$('#delivery-email-cc').text(emails);
});

$('#quote_ntd').show(function () {
	if (window.localStorage.getItem("new_order") == 'true') {
		window.localStorage.setItem("_discount", true);
	}
	var order_id = window.localStorage.getItem('order_id');
	if (order_id) {
		$.ajax({
			url: "/api/order_status/" + order_id,
			type: "POST",
			enctype: 'multipart/form-data',
			contentType: false,
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			dataType: "json",
			async: false,
			success: function (data) {
				if (data.status == 1) {
					$('#order_title').hide();
					$('#payNowBtn').parents('.border-bottom').hide();
					$('#paypal_client_id').parents('.border-bottom').hide();
					$('#pay_success').show();
				}

			}
		});
	}

	var quote_data = JSON.parse(window.localStorage.getItem("ntd_quote_data"));

	if (window.localStorage.getItem("billing") == 'ntd') {
		currency = 'NTD';
	}
	else if (window.localStorage.getItem("billing") == 'usd') {
		currency = 'USD';
	}
	else {
		currency = $('#local').val() == '1' ? 'NTD' : 'USD';
	}
	var _order_id = $('#order_id').val();
	if (_order_id == 'null') {
		var order_date_details = JSON.parse(window.localStorage.getItem("order_time"));
		delivery_date = new Date(order_date_details[ 0 ].delivery_date.slice(1, -1));
		delivery_date = delivery_date.getFullYear() + '-' + delivery_date.getMonth() + '-' + delivery_date.getDate() + ' ' + delivery_date.getHours() + ':' + delivery_date.getMinutes() + ':' + delivery_date.getSeconds();
	}

	total = 0;
	discount = 0.05;
	discount = total * discount;
	var order_id = window.localStorage.getItem('order_id');

	$('#changEmail').on('click', function () {
		var delivery_emails = $('[name="changEmail"]').val();
		$.ajax({
			url: "/api/update-order",
			type: "POST",
			enctype: 'multipart/form-data',
			contentType: false,
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			dataType: "json",
			data: {
				id: order_id,
				delivery_emails: delivery_emails
			},
			async: false,
		});

	});
	if (currency == 'NTD') {
		_total = Math.round(total);
		var _discount = window.localStorage.getItem("_discount");
		_discount = _discount == 'true' ? Math.round(discount) : 0;

		$('#total').text(_total + ' ' + currency);
		$('.total_with_discount').text(Math.round((_total - _discount) * 1.05) + ' ' + currency);

		$('._total_with_discount').text(Math.round((_total - _discount) * 1.05) + ' ' + currency);
		$('#discount').text(_discount + ' ' + currency);
	}
	else {
		_total = Math.round(total);
		_discount = discount;
		$('#total').text(Math.round(total) + ' ' + currency);
		$('.total_with_discount').text(Math.round(total - discount) + ' ' + currency);
		$('._total_with_discount').text(Math.round(total - discount) + ' ' + currency);
		$('#discount').text(Math.round(discount) + ' ' + currency);

		//create order

		if (!order_id) {
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
					service_rate = (parseFloat(raw[ 0 ].translation_rate_tw) + parseFloat(raw[ 0 ].editing_rate_tw)).toFixed(2);
					_service_type = 1;
				}
				if (raw[ 1 ].service == "translation-only") {
					service_rate = raw[ 0 ].translation_rate_tw;
					_service_type = 0;
				}

				doc_price = Math.round((service_rate * raw[ 0 ].word_count)).toFixed(2);

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
					'translator_pay': Math.round(raw[ 0 ].translator_pay_rate * raw[ 0 ].word_count),
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

				order_price = Math.round(parseFloat(order_price) + service_rate * raw[ 0 ].word_count).toFixed(2);
				associated_doc.push(data);

			});
			var isDiscount = false;
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
			total_price = Math.round(order_price);
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
			title = 'new order';
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
				},
			});
		}
	}

	window.localStorage.setItem("_discount_price", _discount);

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
	$('#number').val(order_number);
	_tax_total = (_total - _discount) * 1.05;
	$('#amount').val(Math.round(_tax_total));
	$('#description').val('新購買');
	$('#email').val(email);
});

$('.y_notes').on('click', function () {
	var note = $(this).attr('data-note');
	var fileid = $(this).data('fileid');
	var order_number = $(this).data('order_number');
	$('#adjustNotesModal').modal('show');
	$('#note_data').val(note);
	$('#adjustNotesModal').attr('data-fileid', fileid);
	$('#adjustNotesModal').attr('data-order_number', order_number);
});

$('#quote-submit-notes').on('click', function () {
	note = $('#note_data').val();
	var thisFileid = $(this).parent().closest('#adjustNotesModal').attr('data-fileid');
	var thisOrderNumber = $(this).parent().closest('#adjustNotesModal').attr('data-order_number');
	console.log(thisFileid);
	console.log(thisOrderNumber);
	$.ajax({
		url: "/api/change-note",
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		dataType: "json",
		data: {
			order_number: thisOrderNumber,
			note: note,
			fileid: thisFileid
		},
		success: function (data) {
			$('button[data-fileid="' + thisFileid + '"]').attr('data-note', data.note);
			$('#adjustNotesModal').modal('hide');
		},
	});
});

$('#show-qoute').show(function () {
	$('header').hide();
	var quote_data = JSON.parse(window.localStorage.getItem("ntd_quote_data"));
	var order_date_details = JSON.parse(window.localStorage.getItem("order_time"));
	var _discount = window.localStorage.getItem("_discount");
	var _discount_price = JSON.parse(window.localStorage.getItem("_discount_price"));
	order_date = new Date(order_date_details[ 0 ].order_date);
	order_date_month = order_date.getMonth() + 1;
	order_date_date = order_date.getDate();
	order_date = order_date.getFullYear() + '-' + (order_date_month < 10 ? '0' + order_date_month : order_date_month) + '-' + (order_date_date < 10 ? '0' + order_date_date : order_date_date);

	$('#order_date').text(order_date)
	total = 0;
	discount_price = _discount == 'true' ? _discount_price : 0;
	$(quote_data).each(function (index) {
		data = quote_data[ index ]
		language_combo = data[ 0 ][ 'language_from' ] + ' => ' + data[ 0 ][ 'language_to' ];
		word_count = data[ 0 ][ 'word_count' ];
		filename = data[ 0 ].file_name

		if (data[ 1 ].service == "translation-editing") {
			service_rate = (parseFloat(data[ 0 ].translation_rate_tw) + parseFloat(data[ 0 ].editing_rate_tw)).toFixed(2);
		}
		if (data[ 1 ].service == "translation-only") {
			service_rate = data[ 0 ].translation_rate_tw;
		}
		subtotal = Math.round(word_count * service_rate);
		total = total + subtotal;
		total_with_discount = Math.round((total - discount_price) * 1.05);
		total_with_discount = total_with_discount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		$('#total_amount').text(total_with_discount)
		$('#qoute_data').append(
			`
			<tr>
				<td>` + filename + `
					
					
				</td>
				<td>` + language_combo + `</td>
				<td>` + word_count.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + `</td>
				<td>`+ service_rate + `</td>
				<td>` + subtotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + `</td>
			</tr>
			`
		);
	});

	$('#qoute_data').append(
		`
		<tr class="summary">
			<td><a href='/new-order/quote-ntd'>報價連結</a></td>
			<td colspan=2>新臺幣` + total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' 元' + `</td>
			<td colspan=2>小計</td>
		</tr>
		<tr class="summary">
			<td></td>
			<td colspan=2>新臺幣 `+ discount_price + ` 元</td>
			<td colspan=2>優惠</td>
		</tr>
		<tr class='summary'>
			<td></td>
			<td colspan=2>新臺幣`+ total_with_discount + ` 元</td>
			<td colspan=2>總額（含 5%營業稅）</td>
		</tr>
		`
	);
});

$('#payLater').click(function () {
	order_number = $(this).data('number');

	$.ajax({
		url: "/api/change-payment",
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		dataType: "json",
		data: {
			order_number: order_number,
			type: 0
		},
		success: function (data) {
			$('#_discount').text(data.discount);
		},
	});
});

$('#payLaterBtn').click(function () {
	order_number = window.localStorage.getItem('order_number');
	$.ajax({
		url: "/api/do-notice",
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		dataType: "json",
		data: {
			order_number: order_number,
		},
		success: function (data) {
			window.location = '/new-order/order/' + order_number;
		},
	});
});

$('#payNow').click(function () {
	order_number = $(this).data('number');

	$.ajax({
		url: "/api/change-payment",
		type: "POST",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		dataType: "json",
		data: {
			order_number: order_number,
			type: 1
		},
		success: function (data) {
			$('#_discount').text(data.discount);
		},
	});
});

$('#payNowBtn').click(function () {
	//order_id = window.localStorage.getItem('order_id');
	order_id = $(this).data('id');
	window.location = '/api/pay/newebpay/' + order_id;

});

function makeid(length) {
	var result = '';
	var characters = '0123456789';
	var charactersLength = characters.length;
	for (var i = 0; i < length; i++) {
		result += characters.charAt(Math.floor(Math.random() * charactersLength));
	}
	return result.toString();
}

$(function () {
	var current_fs, next_fs, previous_fs; //fieldsets
	var opacity;
	var radioSelected;
	var invoice;
	radioSelected = 'triplicate';
	invoice = window.localStorage.getItem('invoice');
	invoice = JSON.parse(invoice);

	if (!invoice) {
		current_fs = $('#updateInvoiceField');
		next_fs = $('#newClient');
		// show fieldset
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
	}
	else {
		$('#newClient').hide();
		if (!invoice.phone_no) {
			$('#updateInvoiceField [name="submit"]').prop('disabled', true);
		}
		switch (invoice.invoice_type) {
			case 0:
				radioSelected = 'duplicate';

				$('#invoice_duplicate').show();
				$('#invoice_triplicate').hide();
				$('.buyer').text(invoice.title);
				$('#buyerName').val(invoice.title);
				$('#selectCarrier option[value=' + invoice.carrier + ']').prop('selected', true);
				$('#mobileBarcode').val(invoice.barcode);
				$('.phone_no').text(invoice.phone_no);
				$('#phoneNumber').val(invoice.phone_no);

				if (invoice.barcode)
					invoice.carrier = 2;

				if ((invoice.carrier == 1 && invoice.title != '') || (invoice.carrier == 2 && invoice.barcode != ''))
					$('#duplicateInvoice [name="next"]').prop('disabled', false);

				switch (invoice.carrier) {
					case 1:
						$('.carrier').text($('#carrier_member').attr('data-id'));
						$('.barcode').prev().hide();
						$('.barcode').hide();
						break;
					case 2:
						$('#mobileBarcodeInput').removeClass('d-none').val(invoice.barcode);
						$('.carrier').text($('#carrier_mobile').attr('data-id'));
						$('.barcode').prev().show();
						$('.barcode').show();
						$('.barcode').text(invoice.barcode);
						break;
				}
				break;
			case 1:
				radioSelected = 'triplicate';

				$('#invoice_duplicate').hide();
				$('#invoice_triplicate').show();
				$('.company').text(invoice.title);
				$('#companyName').val(invoice.title);
				$('.serial_no').text(invoice.serial_no);
				$('#bizNumber').val(invoice.serial_no);
				$('.phone_no').text(invoice.phone_no);
				$('#phoneNumber').val(invoice.phone_no);

				if (invoice.serial_no != '' && invoice.title != '')
					$('#triplicateInvoice [name="next"]').prop('disabled', false);
				break;
		}

		$('[name="invoiceType"]').filter('[value=' + radioSelected + ']').prop('checked', true);
	}

	$('.updateInvoice').on('click', function () {
		$('#buyerName').val('');
		$('#mobileBarcode').val('');
		$('#companyName').val('');
		$('#bizNumber').val('');
		$('#phoneNumber').val('');
		$('#phoneNumber').prop('disabled', false);
		$('#code').val('');
		$('#recode').val('');
		current_fs = $(this).parents('fieldset');
		next_fs = $('#newClient');
		// show fieldset
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
	});

	$('input[name="invoiceType"]').on('change', function () {
		radioSelected = $('input[name="invoiceType"]:checked').val();
	});

	$('#firstNext').on('click', function () {
		current_fs = $(this).parents('fieldset');
		if (radioSelected === 'triplicate') {
			next_fs = $('#triplicateInvoice');
		} else {
			next_fs = $('#duplicateInvoice');
		}
		// show fieldset
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
	});

	$('.previous').click(function () {
		current_fs = $(this).parents('fieldset');
		// previous_fs = $(this).parents('fieldset').prev();

		var fld = $(this).parents('fieldset').attr('id');
		switch (fld) {
			case 'duplicateInvoice':
				previous_fs = $('#newClient');
				break;

			case 'triplicateInvoice':
				previous_fs = $('#newClient');
				break;

			case 'triplicateInvoiceConfirm':
				previous_fs = $('#triplicateInvoice');
				break;

			case 'duplicateInvoiceConfirm':
				previous_fs = $('#duplicateInvoice');
				break;

			default:
				previous_fs = $('#newClient');
				break;
		}
		// if (fld == 'duplicateInvoice')
		// 	previous_fs = $('#newClient');

		//show the previous fieldset
		previous_fs.show();
		window.scrollTo(0, 0);
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

	$('#buyerName').on('keyup touchend', function () {
		var title = $(this).val();

		carrier = $('#selectCarrier').val();
		barcode = $('#mobileBarcode').val();
		if ((carrier == 1 && title != '') || (carrier == 2 && title != '' && barcode != ''))
			$('#duplicateInvoice [name="next"]').prop('disabled', false);
		else
			$('#duplicateInvoice [name="next"]').prop('disabled', true);

	});

	$('#selectCarrier').on('change', function () {
		var carrier = $(this).val();

		if (carrier == 2) {
			$('#mobileBarcodeInput').removeClass('d-none');
		} else {
			$('#mobileBarcodeInput').addClass('d-none');
		}

		barcode = $('#mobileBarcode').val();
		buyerName = $('#buyerName').val();
		if ((carrier == 1 && buyerName != '') || (carrier == 2 && buyerName != '' && barcode != ''))
			$('#duplicateInvoice [name="next"]').prop('disabled', false);
		else
			$('#duplicateInvoice [name="next"]').prop('disabled', true);

	});

	$('#mobileBarcode').on('keyup touchend', function () {
		var barcode = $(this).val();

		carrier = $('#selectCarrier').val();
		buyerName = $('#buyerName').val();
		if ((carrier == 1 && buyerName != '') || (carrier == 2 && buyerName != '' && barcode != ''))
			$('#duplicateInvoice [name="next"]').prop('disabled', false);
		else
			$('#duplicateInvoice [name="next"]').prop('disabled', true);

	});

	$('#duplicateInvoice [name="next"]').on('click', function () {
		carrier = $('#selectCarrier :selected').val();
		carrier = parseInt(carrier);
		barcode = $('#mobileBarcode').val();
		title = $('#buyerName').val();
		$('.buyer').text(title);
		var data = {};
		switch (carrier) {
			case 1:
				$('.carrier').text($('#carrier_member').attr('data-id'));
				$('.barcode').prev().hide();
				$('.barcode').text('');
				data = {
					'invoice_type': 0,
					'title': title,
					'carrier': 1,
				};
				invoice = Object.assign({}, invoice, data);
				delete invoice.barcode;
				break;
			case 2:
				$('#mobileBarcodeInput').removeClass('d-none').val(barcode);
				$('.barcode').text(barcode);
				$('.carrier').text($('#carrier_mobile').attr('data-id'));
				$('.barcode').prev().show();
				data = {
					'invoice_type': 0,
					'title': title,
					'carrier': 2,
					'barcode': barcode,
				};
				invoice = Object.assign({}, invoice, data);
				break;
		}
		delete invoice.serial_no;

		invoice = Object.assign({}, invoice, data);
		delete invoice.serial_no;

		window.localStorage.setItem('invoice', JSON.stringify(invoice));

		current_fs = $(this).parents('fieldset');
		next_fs = $('#updatePhoneField');

		//show the next fieldset
		next_fs.show();
		window.scrollTo(0, 0);
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
	})

	$('#companyName').on('keyup touchend', function () {
		var title = $(this).val();
		var data = {
			'invoice_type': 1,
			'title': title,
		};
		invoice = Object.assign({}, invoice, data);
		delete invoice.carrier;
		delete invoice.barcode;

		window.localStorage.setItem('invoice', JSON.stringify(invoice));

		serial_no = $('#bizNumber').val();
		if (serial_no != '' && title != '')
			$('#triplicateInvoice [name="next"]').prop('disabled', false);
		else
			$('#triplicateInvoice [name="next"]').prop('disabled', true);


	});

	$('#bizNumber').on('keyup touchend', function () {
		var serial_no = $(this).val();

		title = $('#companyName').val();
		if (serial_no.length == 8 && title != '')
			$('#triplicateInvoice [name="next"]').prop('disabled', false);
		else
			$('#triplicateInvoice [name="next"]').prop('disabled', true);

	});

	$('#triplicateInvoice [name="next"]').on('click', function () {
		title = $('#companyName').val();
		serial_no = $('#bizNumber').val();

		$('.company').text(title);
		$('.serial_no').text(serial_no);

		var data = {
			'invoice_type': 1,
			'title': title,
			'serial_no': serial_no,
		};
		invoice = Object.assign({}, invoice, data);
		delete invoice.carrier;
		delete invoice.barcode;

		window.localStorage.setItem('invoice', JSON.stringify(invoice));

		current_fs = $(this).parents('fieldset');
		next_fs = $('#updatePhoneField');

		//show the next fieldset
		next_fs.show();
		window.scrollTo(0, 0);
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
	})

	$('#phoneNumber').on('keyup touchend', function () {
		// $('#sendCode').prop('disabled', true);
		// $('#phoneNumber').siblings('.error').addClass('d-none');

		var phoneNumber = $(this).val();

		if (phoneNumber.length == 10) {
			$('#sendCode').prop('disabled', false);
			$('#phoneNumber').siblings('.error').addClass('d-none');
		} else {
			$('#sendCode').prop('disabled', true);
			$('#phoneNumber').siblings('.error').removeClass('d-none');
		}
	});

	$('#sendCode').on('click', function () {
		$('#phoneNumber').prop('disabled', true);
	})

	$('#code').on('change', function () {
		var code = $(this).val();
		var phoneNumber = $('#phoneNumber').val();
		$('#code').siblings('.error').addClass('d-none');
		if (phoneNumber != '' && code != '')
			$('#updatePhoneField [name="next"]').prop('disabled', false);
		else
			$('#updatePhoneField [name="next"]').prop('disabled', true);
	});

	$('#updatePhoneField [name="previous"]').on('click', function () {
		$('#phoneNumber').val('');
		$('#phoneNumber').prop('disabled', false);
		$('#code').val('');
		$('#recode').val('');
		current_fs = $(this).parents('fieldset');

		if (invoice.invoice_type == 0) {
			previous_fs = $('#duplicateInvoice');
		} else {
			previous_fs = $('#triplicateInvoice');
		}

		//show the previous fieldset
		previous_fs.show();
		window.scrollTo(0, 0);
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

	$('#updatePhoneField [name="next"]').on('click', function () {
		phoneNumber = $('#phoneNumber').val();
		code = $('#code').val();
		var recode = $('#recode').val();
		if (code != recode) {
			$('#code').siblings('.error').removeClass('d-none');
			return
		}
		$('.phone_no').text(phoneNumber);

		var data = {
			'phone_no': phoneNumber,
		};
		invoice = Object.assign({}, invoice, data);

		window.localStorage.setItem('invoice', JSON.stringify(invoice));

		if (invoice.invoice_type == 0) {
			current_fs = $(this).parents('fieldset');
			next_fs = $('#duplicateInvoiceConfirm');

			//show the next fieldset
			next_fs.show();
			window.scrollTo(0, 0);
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
		} else {
			current_fs = $(this).parents('fieldset');
			next_fs = $('#triplicateInvoiceConfirm');

			//show the next fieldset
			next_fs.show();
			window.scrollTo(0, 0);
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
		}
	});

	$("#msform").validate({
		submitHandler: function (form) {
			window.localStorage.setItem("_discount", true);
			window.localStorage.setItem("new_order", false);
			invoice = window.localStorage.getItem('invoice');
			invoice = JSON.parse(invoice);
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
					service_rate = (parseFloat(raw[ 0 ].translation_rate_tw) + parseFloat(raw[ 0 ].editing_rate_tw)).toFixed(2);
					_service_type = 1;
				}
				if (raw[ 1 ].service == "translation-only") {
					service_rate = raw[ 0 ].translation_rate_tw;
					_service_type = 0;
				}

				doc_price = Math.round((service_rate * raw[ 0 ].word_count));

				data = {
					'id': '',
					'language_combination': raw[ 0 ].language_combo,
					'translator_deliver_date': '',
					'editor_deliver_date': '',
					'filename': raw[ 0 ].file_name,
					'file': raw[ 0 ].doc,
					'file_id': raw[ 0 ].file_id,
					'word_count': raw[ 0 ].word_count,
					'currency': 'NTD',
					'service_rate': service_rate,
					'doc_price': doc_price,
					'translator_pay': Math.round(raw[ 0 ].translator_pay_rate * raw[ 0 ].word_count),
					'editor_pay': _service_type == 1 ? Math.round(raw[ 0 ].editor_pay_rate * raw[ 0 ].word_count) : 0,
					'translation_rate': raw[ 0 ].translation_rate_tw,
					'translator_pay_rate': raw[ 0 ].translator_pay_rate,
					'editing_rate': _service_type == 1 ? raw[ 0 ].editing_rate_tw : 0,
					'editor_pay_rate': _service_type == 1 ? raw[ 0 ].editor_pay_rate : 0,
					'expertise': raw[ 1 ].expertise,
					'style': raw[ 1 ].style,
					'notes': raw[ 1 ].notes,
					'service_type': _service_type,
					'translation_deadline': translation_deadline,
					'doc_deadline': delivery_date,
				}

				order_price = Math.round(parseFloat(order_price) + service_rate * raw[ 0 ].word_count).toFixed(2);
				associated_doc.push(data);

			});

			if (order_price < 300) {
				order_price = 300;
			}

			var isDiscount = true;
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
			discount = isDiscount ? Math.round(order_price * 0.05) : 0;
			total_price = Math.round((order_price - discount) * 1.05);
			hours = hours;
			deadline = delivery_date;
			payment_status = 0;
			order_status = 0;
			urgent = 0;
			note = 'note';
			delivery_date = delivery_date;
			depreciation = isDiscount ? 1 : 0;
			overseas = 0;
			tax = 0;
			invoice_type = invoice.invoice_type ? invoice.invoice_type : 0;
			title = invoice.title ? invoice.title : 'new order';
			carrier = invoice.carrier ? invoice.carrier : null;
			barcode = invoice.barcode ? invoice.barcode : null;
			serial_no = invoice.serial_no ? invoice.serial_no : null;
			phone_no = invoice.phone_no ? invoice.phone_no : null;
			service_type = 0;
			local = 1;
			if (tax == 0) {
				tax_amount = Math.round(order_price * 0.05);
			}
			if (tax == 1) {
				tax_amount = Math.round(order_price / 1.05);
			}
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
					phone_no: phone_no,
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
	});
});
