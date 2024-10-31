$(document).ready(function(){
	var baseUrl = $('#base-url').val();
	var forgotPasswordForm = $('#forgot-password-form');

	forgotPasswordForm.validate({
	        onkeyup: function (element) {
	            this.element(element);
	        },
	        onfocusout: function (element) {
	            this.element(element);
	        },
	        rules: {
	            password: {
	                required: true,
                    minlength: 7
	            },
                confirm_password: {
                    equalTo: "#password",
                    required: true,
                }
	        }
	    });


	var options = {
	        beforeSubmit: function () {},
	        success: function (response) {
	            if(response == '0' || response == 0){
					swal('An error occured!', '', 'error');
	            }
	            else{
					swal({
						title: "Success!",
						text: "Password Successfully changed",
						type: "success",
						timer: 1500
					  });

					setTimeout(function () {
						window.location.href = baseUrl + '/login'
					}, 1505);
					
	            }
	        }
	    };
	forgotPasswordForm.ajaxForm(options);
});