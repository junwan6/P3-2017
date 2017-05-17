var initiallyHidden = [
	'#fader',
	'#loginBox',
	'#salaryDialog',
	'#educationDialog',
	'#skillsDialog',
	'#careerOutlookDialog',
	'#worldOfWorkDialog'
];

$(document).ready(function(){


	var i;
	for (i = 0; i < initiallyHidden.length; i++) {
		$(initiallyHidden[i]).hide();
	}



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

	$('.icon').mouseenter(function() {


		var whichDialog = "#" + this.id + "Dialog";
		if (!$(whichDialog).is(':visible')) {   
  			$(whichDialog).show();
		};  

	})

	$('.icon').mouseleave(function() {

		var whichDialog = "#" + this.id + "Dialog";
		if ($(whichDialog).is(':visible')) {   
  			$(whichDialog).hide();
		};  

	})


});




