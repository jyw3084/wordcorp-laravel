$(document).ready(function(){
	var baseUrl = $('#base-url').val();
	lang_locale = $('#lang_locale').attr('data-id')
	var loginForm = $('#login-form');
	var changePassForm = $('#change-password-form');


	loginForm.validate({
	        onkeyup: function (element) {
	            this.element(element);
	        },
	        onfocusout: function (element) {
	            this.element(element);
	        },
	        rules: {
	            email: {
	                required: true,
	            },
	            password: {
	                required: true
	            }
	        },
			messages: {
				email: {
					required: $('#validation_required').attr('data-id'),
					email: $('#validation_email').attr('data-id'),
				},
				password: {
					required: $('#validation_required').attr('data-id'),
				}
			}
	    });


	var options = {
	        beforeSubmit: function () {},
	        success: function (response) {
	            if(response == '0' || response == 0){
					swal($('#login_error_text').attr('data-id'), '', 'error');
	            }
	            else if(response == '1' || response == 1 || response == '3' || response == 3){
					swal({
						title: $('#modal_success').attr('data-id'),
						text: $('#login_success_text').attr('data-id'),
						type: "success",
						timer: 1500
					  });

					setTimeout(function () {
						window.location.href = baseUrl + '/translator/translator-bin'
					}, 1505);
					
	            }
				else if(response == '2' || response == 2 || response == '3' || response == 3){
					swal({
						title: $('#modal_success').attr('data-id'),
						text: $('#login_success_text').attr('data-id'),
						type: "success",
						timer: 1500
					  });

					setTimeout(function () {
						window.location.href = baseUrl + '/editor/editor-bin'
					}, 1505);
	            }
	        }
	    };
	loginForm.ajaxForm(options);


	changePassForm.validate({
	        onkeyup: function (element) {
	            this.element(element);
	        },
	        onfocusout: function (element) {
	            this.element(element);
	        },
			rules: {
	            reset_email: {
	                required: true,
	            }
	        },
			messages: {
				reset_email: {
					required: $('#validation_required').attr('data-id'),
					email: $('#validation_email').attr('data-id'),
				}
			}
	    });

	var forgotPassOptions = {
	        beforeSubmit: function () {},
	        success: function (response) {
	            if(response == '0' || response == 0){
					swal($('#forgot_password_error').attr('data-id'), '', 'error');
					
	            }
	            else {
					$('#forgot-password-modal').modal('hide');
					$('#reset_email').val('');
					swal({
						title: $('#modal_success').attr('data-id'),
						text: $('#forgot_password_success').attr('data-id'),
						type: "success",
					  });

	            }
	        }
	    };
	changePassForm.ajaxForm(forgotPassOptions);




});
