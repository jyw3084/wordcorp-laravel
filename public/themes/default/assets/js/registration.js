$(document).ready(function(){
	var baseUrl = $('#base-url').val();
	var registrationForm = $('#registration-form');
	registrationForm.validate({
	        onkeyup: function (element) {
	            this.element(element);
	        },
	        onfocusout: function (element) {
	            this.element(element);
	        },
	        rules: {
                name: {
	                required: true,
	            },
	            email: {
	                required: true,
	            },
                phone_number : {
                    required: true,
                },
	            password: {
	                required: true,
                    minlength: 7
	            },
                confirm_password: {
                    equalTo: "#password",
                    required: true,
                }
	        },
			messages: {
				name: {
					required: $('#validation_required').attr('data-id'),
				},
				email: {
					required: $('#validation_required').attr('data-id'),
					email: $('#validation_email').attr('data-id'),
				},
				password: {
					required: $('#validation_required').attr('data-id'),
					minlength: $('#validation_minLength').attr('data-id')
				},
				confirm_password: {
					required: $('#validation_required').attr('data-id'),
					equalTo: $('#validation_equalTo').attr('data-id'),
					
				}
			}
	    });

	var options = {
	        beforeSubmit: function () {},
	        success: function (response) {
                if(response == 'success'){
                    swal({
						title: $('#modal_success').attr('data-id'),
						text: $('#registration_success').attr('data-id'),
						type: "success"
					  }, function(){
                        window.location.href = "/login";
                  });
                }else{
                    swal($('#registration_failed').attr('data-id'), '', 'error');
                }
                
	        }
	    };
    registrationForm.ajaxForm(options);
});