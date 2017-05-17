var initiallyHidden = [
	'#fader',
	'#signUpBox',
	'#loginBox'
];

$(document).ready(function(){


	var i;
	for (i = 0; i < initiallyHidden.length; i++) {
		$(initiallyHidden[i]).hide();
	}

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


});




