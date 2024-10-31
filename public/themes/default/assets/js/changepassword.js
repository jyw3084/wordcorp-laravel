$(document).ready(function(){
	var baseUrl = $('#base-url').val();
	var changePassForm = $('#change-password-form');
	changePassForm.validate({
	        onkeyup: function (element) {
	            this.element(element);
	        },
	        onfocusout: function (element) {
	            this.element(element);
	        },
	        rules: {
                oldPassword: {
	                required: true,
                    minlength: 7
	            },
	            newPassword: {
	                required: true,
                    minlength: 7
	            },
                confirmPassword: {
                    equalTo: "#newPassword",
                    required: true
                }
	        }, messages: {
	        	confirmPassword: {
	        		equalTo: "Password doesn't match"
	        	}
	        }
	    });

	var options = {
	        beforeSubmit: function () {},
	        success: function (response) {
                if(response == 'success'){
                    $('#change-success-modal').modal('show');
                }else{
                    swal('wrong password!', '', 'error');
                }
                
	        }
	    };
    changePassForm.ajaxForm(options);
});