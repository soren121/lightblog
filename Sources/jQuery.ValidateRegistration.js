$(document).ready(function() {
	$('#register').validate({
		onkeyup: false,
		rules: {
			username: {
				required: true,
				minlength: 5,
				maxlength: 20,
				validChars: true,
				usernameCheck: true
			},
			email: {
				required: true,
				email: true,
				maxlength: 255
			},
			password: {
				required: true,
				minlength: 6,
				maxlength: 20
			},
			cpassword: {
				required: true,
				equalTo: "#password"
			},		
		},
		messages: {
			username: {
				required: "username is required.",
				minlength: jQuery.format("username must be at least {0} characters in length."),
				maxlength: jQuery.format("username can not exceed {0} characters in length."),
				validChars: "please supply valid characters only.",
				usernameCheck:"this username is already in use."
			},
			email: {
				required: "email address is required.",
				email: "email address must be valid.",
				maxlength: jQuery.format("email address can not exceed {0} characters in length.")
			},
			password_first: {
				required: "password is required.",
				minlength: jQuery.format("password must be at least {0} characters in length."),
				maxlength: jQuery.format("password can not exceed {0} characters in length.")
			},
			password_verify: {
				required: "confirmed password is required.",
				equalTo: "confirmed password does not match."
			}
		}
	}); 
});

// Extend the validation plugin to allow for a unique username check
jQuery.validator.addMethod('usernameCheck', function(username) {
	var postURL = "";
	$.ajax({
		cache:false,
		async:false,
		type: "POST",
		data: "username=" + username,
		url: postURL,
		success: function(msg) {
			result = (msg=='TRUE') ? true : false;
		}
	});
	
	return result;
}, '');

// Extend the plugin again to check for valid characters
$.validator.addMethod('validChars', function (value) {
	var result = true;
	// unwanted characters
	var iChars = "!@#$%^&*()+=-[]\\\';,./{}|\":<>?";
	for (var i = 0; i < value.length; i++) {
		if (iChars.indexOf(value.charAt(i)) != -1) {
			return false;
		}
	}
	return result;
}, '');