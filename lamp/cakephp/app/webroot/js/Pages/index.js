var initiallyHidden = [];

var clearError = function() {
	$('#errorBox').fadeOut();
}

$(document).ready(function(){


	if($('#loginAttempt').length) {
		initiallyHidden.push('#signUpBox');
	} else if ($('#signUpAttempt').length) {
		initiallyHidden.push('#loginBox');
	} else {
		initiallyHidden.push('#loginBox');
		initiallyHidden.push('#signUpBox');
		initiallyHidden.push('#fader');
	}

	var i;
	for (i = 0; i < initiallyHidden.length; i++) {
		$(initiallyHidden[i]).hide();

	}

	
	t = setTimeout(function(){clearError();}, 3000);


	$('#freeAccountContent').click(function(){

		$('#fader').fadeIn();
		$('#signUpBox').fadeIn();
		$('body').addClass('stop-scrolling')

	});


	$('#signUpCloseButton').click(function() {

		$('#fader').fadeOut();
		$('#signUpBox').fadeOut();
		$('body').removeClass('stop-scrolling')

	});



	$('#signUpLogin').click(function(){

		$('#fader').fadeIn();
		$('#loginBox').fadeIn();
		$('body').addClass('stop-scrolling')



	});

	$('#loginCloseButton').click(function() {

		$('#fader').fadeOut();
		$('#loginBox').fadeOut();
		$('body').removeClass('stop-scrolling')

	});

	$('#switchToSignUp').click(function() {
		$('#loginBox').fadeOut();
		$('#signUpBox').fadeIn();
	});

	$('#switchToLogin').click(function() {
		$('#signUpBox').fadeOut();
		$('#loginBox').fadeIn();
	});




});




