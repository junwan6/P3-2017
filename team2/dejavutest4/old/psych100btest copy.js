
var results = "";

var sound = new Audio("/static/beep.mp3");
sound.preload = 'auto';
sound.load();

function playSound() {
  var click=sound.cloneNode();
  click.play();
}

var instructionsBeep = "off";

var timerswitch;
var s = 4;

var stressTimerSwitch;
var sm = 0;
var ss = -1;

var currentName;
var currentOccupation;

var P1;
var p1;
var P2;
var p2;

var descriptionOrder = [2, 1, 4, 3];


var arousalOrder = ["low", "high", "low", "high"];


var nameOrder = ["Mary", "John", "Katy", "Michael"];


var occupationOrder = ["mechanic", "student", "writer", "nanny"];


var condition = 0;



function shuffle(array) {
  var currentIndex = array.length, temporaryValue, randomIndex ;

  // While there remain elements to shuffle...
  while (0 !== currentIndex) {

    // Pick a remaining element...
    randomIndex = Math.floor(Math.random() * currentIndex);
    currentIndex -= 1;

    // And swap it with the current element.
    temporaryValue = array[currentIndex];
    array[currentIndex] = array[randomIndex];
    array[randomIndex] = temporaryValue;
  }

  return array;
}

var display = function(){

	if ($('#titlepage').is(':visible')) {   
 		$('#titlepage').hide();
 		runDescription(condition);
	};

	if ($('#lowinstruction').is(':visible')) {
		$('#lowinstruction').hide();
		shuffle(traits);
		question();
	}

	if ($('#between').is(':visible')) {
		$('#between').hide();
		condition++;
		runDescription(condition);
	}

	if ($('#thanks').is(':visible')) {
		$('#thanks').hide();
		document.getElementById('results').innerHTML = results;
		$('#results').show();

	}
}

var updateDescription = function(dnumber) {

	if (currentName == "John" || currentName == "Michael") {
		P1 = "He";
		p1 = "he";
		P2 = "His";
		p2 = "his";
	}
	else if (currentName == "Mary" || currentName == "Katy") {
		P1 = "She";
		p1 = "she";
		P2 = "Her";
		p2 = "her";
	}

	if (dnumber == 1) {
		document.getElementById('description').innerHTML = "<br><br><br>You have 45 seconds to read the following description and memorize as much of the information as you can.<br><br><br><br><br><br><p>" + currentName + " is a 25-year-old " + currentOccupation + ". " + P1 + " enjoys watching movies at the three-dollar theater. " + currentName + " regularly jogs at the park. " + P1 + " uses an aux-cable to listen to " + p2 + " own music while driving.</p>";
	}

	if (dnumber == 2) {
		document.getElementById('description').innerHTML = "<br><br><br>You have 45 seconds to read the following description and memorize as much of the information as you can.<br><br><br><br><br><br><p>" + currentName + " is a 22-year-old " + currentOccupation + ". " + P1 + " regularly drinks coffee in the mornings with breakfast. " + currentName + " frequently buys used books from " + p2 + " local bookstore. " + P1 + " is never late to any appointment.</p>";
	}

	if (dnumber == 3) {
		document.getElementById('description').innerHTML = "<br><br><br>You have 45 seconds to read the following description and memorize as much of the information as you can.<br><br><br><br><br><br><p>" + currentName + " is a 26-year-old " + currentOccupation + ". " + P1 + " enjoys going to concerts with " + p2 + " friends. " + currentName + " books flights ahead to ensure a discount on airline tickets. " + P1 + " likes watching game shows on TV.</p>";
	}

	if (dnumber == 4) {
		document.getElementById('description').innerHTML = "<br><br><br>You have 45 seconds to read the following description and memorize as much of the information as you can.<br><br><br><br><br><br><p>" + currentName + " is a 29-year-old " + currentOccupation + ". On " + p2 + " days off, " + p1 + " enjoys spending time with " + p2 + " family. " + currentName + " likes picking up new hobbies every few months. " + P1 + " attends church every other Sunday.</p>";
	}

}


var runDescription = function(condition) {

	currentName = nameOrder[condition];
	currentOccupation = occupationOrder[condition];

	updateDescription(descriptionOrder[condition]);

	$('#description').show();

	$('#timer').show();
	timerswitch = "on";
	s = 46;
	countDown();

	setTimeout(function() {
		$('#description').hide();
		$('#filler').show();
		s = 46;
	}, 45000);

	setTimeout(function() {
		$('#filler').hide();
		$('#timer').hide();
		if (arousalOrder[condition] == "low") {
			lowinstruction();
		}
		else if (arousalOrder[condition] == "high") {
			highinstruction();
		}
	}, 90000);
}

var traits = ["assertive", "daring", "arrogant", "dominant", "forceful", "tender", "compassionate", "passive", "gentle", "clinging", "likeable", "reliable", "pessimistic", "inefficient", "a college graduate", "married", "a parent", "a pet owner"];
var traitCount = 0;

var question = function() {

	timerswitch = "off";
	instructionsBeep = "off";

	document.getElementById('question').innerHTML = "<br><br><br><br>Is " + currentName + " " + traits[traitCount] + "?";

	$('#question').show();

}

var lowinstruction = function() {

	document.getElementById('lowinstruction').innerHTML = "<br><br><br><br><br>Please answer the following questions about " + currentName + ". Take your time.<br><br><br>Press enter when you are ready to begin.";

	$('#lowinstruction').show();

}

var highinstruction = function() {

	document.getElementById('highinstruction').innerHTML = "<br><br><br><br><br>Answer the following questions about " + currentName + " <span id='emphasized'>as fast as you can.</span> You are being timed, so <span id='emphasized'>respond as quickly as possible.</span>";

	$('#highinstruction').show();
	instructionsBeep = "on";

	$('#timer').show();
	s = 11;

	setTimeout(function () {
		$('#highinstruction').hide();
		$('#timer').hide();
		$('#stressTimer').show();
		stressTimerSwitch = "on";
		sm = 0;
		ss = -1;
		stressTimer();
		shuffle(traits);
		question();
	}, 10000);
}




var countDown = function() {
	if (timerswitch == "off") {
		return;
	}
	if (instructionsBeep == "on") {
	playSound();
	}
	s--;
	document.getElementById('timer').innerHTML = s;
	t = setTimeout(function() {
		countDown();
	}, 1000);
}

var stressTimerFlash = function() {
	setTimeout(function() {
		$('#stressTimer').hide();
	}, 500);
}

var stressTimer = function() {
	if (stressTimerSwitch == "off") {
		return;
	}
	$('#stressTimer').show();
	stressTimerFlash();
	playSound();
	if (ss == 59)
	{
		sm++;
		ss = 0;
	}
	else
		ss++;
	if (ss < 10)
		document.getElementById('stressTimer').innerHTML = sm + ":0" + ss;
	else
		document.getElementById('stressTimer').innerHTML = sm + ":" + ss;
	t = setTimeout(function() {
		stressTimer();
	}, 1000);

}

/*var storeData = function(){
	$.ajax({
		type: 'post',
		url: "test2.php",
		data: {'description': 'a'},
		success: function(){
			alert("Testing; ajax worked");
		}
	});
	return false;
}*/



$(document).ready(function(){

	
	$('#timer, #description, #thanks, #filler, #question, #lowinstruction, #highinstruction, #between, #results').hide();

	$('body').keydown(function(event) {
		if (event.keyCode == 82) {
			if ($('#thanks').is(':visible')){
				display();
			}
		}
		if (event.keyCode == 13) {
			if (!$('#thanks').is(':visible')){
				display();
			}			
		}
		if (event.keyCode == 89) {
			if (traitCount == 17) {
				results += traits[traitCount] + ": Yes<br><br>";
				stressTimerSwitch = "off";
				$('#stressTimer').hide();
				$('#question').hide();
				traitCount = 0;
				if (condition == 3) {
					$('#thanks').show();
				}
				else {
					$('#between').show();
				}
			}
			if ($('#question').is(':visible')) {
				if (traitCount == 0)
				{
					results+= "Condition: " + (condition+1) + "<br>" + "Description: " + descriptionOrder[condition] + "<br>" + "Name: " + nameOrder[condition] + "<br>" + "Occupation: " + occupationOrder[condition] + "<br>" + "Arousal: " + arousalOrder[condition] + "<br>";
				}
				results += traits[traitCount] + ": Yes<br>";
				traitCount++;
				question();
			}
		}
		if (event.keyCode == 78) {
			if (traitCount == 17) {
				results += traits[traitCount] + ": No<br><br>";
				stressTimerSwitch = "off";
				$('#stressTimer').hide();
				$('#question').hide();
				traitCount = 0;
				if (condition == 3) {
					$('#thanks').show();
				}
				else {
					$('#between').show();
				}
			}
			if ($('#question').is(':visible')) {
				if (traitCount == 0)
				{
					results+= "Condition: " + (condition+1) + "<br>" + "Description: " + descriptionOrder[condition] + "<br>" + "Name: " + nameOrder[condition] + "<br>" + "Occupation: " + occupationOrder[condition] + "<br>" + "Arousal: " + arousalOrder[condition] + "<br>";
				}
				results += traits[traitCount] + ": No<br>";
				traitCount++;
				question();
			}
		}
	});



});




