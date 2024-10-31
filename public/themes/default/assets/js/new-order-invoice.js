$('#selectCarrier').on('change', function () {
	var optionSelected = $(this).children('option:selected').val();
	if (optionSelected === 'mobileCarrier') {
		$('#mobileBarcodeInput').removeClass('d-none');
	} else {
		$('#mobileBarcodeInput').addClass('d-none');
	}
});

$(function () {
	var current_fs, next_fs, previous_fs; //fieldsets
	var opacity;
	var radioSelected = 'triplicate';

	$('#updateInvoice').on('click', function () {
		current_fs = $(this).parent();
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
		current_fs = $(this).parent();
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

	$('.next').click(function () {
		current_fs = $(this).parent();
		next_fs = $(this).parent().next();

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
	});

	$('.previous').click(function () {
		current_fs = $(this).parent();
		previous_fs = $(this).parent().prev();

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

	$('.submit').click(function () {
		return false;
	});
});
