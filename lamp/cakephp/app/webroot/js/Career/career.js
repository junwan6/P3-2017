//--------------------------------------------CAREER
var skillsData = [];
var changeFocus = function(focus){
  document.getElementsByClassName("active-body")[0].className = "inactive-body";
  document.getElementById(focus + "-body").className = "active-body";
  window.history.pushState("","", focus);
  if (focus == 'salary'){
    salary_drawChart();
  }
  if (focus == 'education'){
    edu_drawChart();
  }
  if (focus == 'skills'){
    $('#mainSkillsPieChart').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Skills'
        },
        tooltip: {
            pointFormat: '<b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            name: 'Intelligences',
            colorByPoint: true,
            data: skillsData
        }]
    });
  }
};
//--------------------------------------------WORLD_OF_WORK
var createCanvas = function() {
	var canvas = document.getElementById("d");
	var ctx = canvas.getContext('2d');
	var canover=document.getElementById("cover");

	var ctxover = canvas.getContext('2d');

	// Create an image element
	var img = new Image();

	var soc = document.getElementById('interestsTable').rows[0].textContent;
	var realistic = document.getElementById('interestsTable').rows[1].textContent;
	var investigative = document.getElementById('interestsTable').rows[2].textContent;
	var artistic = document.getElementById('interestsTable').rows[3].textContent;
	var social = document.getElementById('interestsTable').rows[4].textContent;
	var enterprising = document.getElementById('interestsTable').rows[5].textContent;
	var conventional = document.getElementById('interestsTable').rows[6].textContent;

	// http://stackoverflow.com/questions/25837158/how-to-draw-a-star-by-using-canvas-html5
	function drawStar(cx,cy,spikes,outerRadius,innerRadius){
      var rot=Math.PI/2*3;
      var x=cx;
      var y=cy;
      var step=Math.PI/spikes;

      ctx.beginPath();
      ctx.moveTo(cx,cy-outerRadius)
      for(i=0;i<spikes;i++){
        x=cx+Math.cos(rot)*outerRadius;
        y=cy+Math.sin(rot)*outerRadius;
        ctx.lineTo(x,y)
        rot+=step

        x=cx+Math.cos(rot)*innerRadius;
        y=cy+Math.sin(rot)*innerRadius;
        ctx.lineTo(x,y)
        rot+=step
      }
      ctx.lineTo(cx,cy-outerRadius);
      ctx.closePath();
      ctx.lineWidth=5;
      ctx.strokeStyle='blue';
      ctx.stroke();
      ctx.fillStyle='skyblue';
      ctx.fill();
    }

    // http://stackoverflow.com/questions/11301438/return-index-of-greatest-value-in-an-array

    function indexOfMax(arr) {
	    if (arr.length === 0) {
	        return -1;
	    }

	    var max = arr[0];
	    var maxIndex = 0;

	    for (var i = 1; i < arr.length; i++) {
	        if (arr[i] > max) {
	            maxIndex = i;
	            max = arr[i];
	        }
	    }

	    return maxIndex;
	}

    // http://cwestblog.com/2012/11/12/javascript-degree-and-radian-conversion/

	// Converts from degrees to radians.
	Math.radians = function(degrees) {
	  return degrees * Math.PI / 180;
	};
	 
	// Converts from radians to degrees.
	Math.degrees = function(radians) {
	  return radians * 180 / Math.PI;
	};

	// When the image is loaded, draw it
	img.onload = function () {
	    canvas.width=img.width;
	    canvas.height=img.height;		
	    ctx.drawImage(img, 0, 0);
	    ctx.translate(250,250);		// to move origin to (0,0)
	    
	    // based on http://www.choixdecarriere.com/pdf/5873/3.pdf
	    // NOTE: the axes on there are 90 degrees off than the WoW graphic we are using
	    var interestArray = [realistic, investigative, artistic, social, enterprising, conventional]; 
	    var coordArray = [];
	    var i = 0;
	    for (i; i < interestArray.length; i++) {
	    	if (interestArray[i] > 0.5) {
	    		if (i == 0) {	// realistic
	    			var x = realistic*215*Math.cos(Math.radians(-90));
    				var y = realistic*215*Math.sin(Math.radians(90));
    				coordArray.push([x,y]);
	    		}
	    		if (i == 1) {	// investigative
	    			var x = investigative*215*Math.cos(Math.radians(-60));
    				var y = investigative*215*Math.sin(Math.radians(60));
    				coordArray.push([x,y]);
	    		}
	    		if (i == 2) {	// artistic
	    			var x = artistic*215*Math.cos(Math.radians(240));
    				var y = artistic*215*Math.sin(Math.radians(-240));
    				coordArray.push([x,y]);    		
	    		}
	    		if (i == 3) {	// social
	    			var x = social*215*Math.cos(Math.radians(180));
    				var y = social*215*Math.sin(Math.radians(-180));
    				coordArray.push([x,y]);			
	    		}
	    		if (i == 4) {	// enterprising 
	    			var x = enterprising*215*Math.cos(Math.radians(120));
    				var y = enterprising*215*Math.sin(Math.radians(-120));
    				coordArray.push([x,y]);				
	    		}
	    		if (i == 5) {	// conventional 
	    			var x = conventional*215*Math.cos(Math.radians(60));
    				var y = conventional*215*Math.sin(Math.radians(-60));
    				coordArray.push([x,y]);	
	    		}
	    	}
	    }

	    var averageX = 0;
	    var averageY = 0;
	    var j = 0;

	    for (j; j < coordArray.length; j++) {
	    	averageX += coordArray[j][0];
	    	averageY += coordArray[j][1];
	    }

	    if (coordArray.length == 0) {
	    	var specificInterest = indexOfMax(interestArray);
    	 	if (specificInterest == 0) {	// realistic
    			var x = realistic*215*Math.cos(Math.radians(-90));
				var y = realistic*215*Math.sin(Math.radians(90));
				drawStar(x, y, 5, 12, 6);
    		}
    		if (specificInterest == 1) {	// investigative
    			var x = investigative*215*Math.cos(Math.radians(-60));
    			var y = investigative*215*Math.sin(Math.radians(60));
				drawStar(x, y, 5, 12, 6);
    		}
    		if (specificInterest == 2) {	// artistic
    			var x = artistic*215*Math.cos(Math.radians(240));
    			var y = artistic*215*Math.sin(Math.radians(-240));
				drawStar(x, y, 5, 12, 6);    		
    		}
    		if (specificInterest == 3) {	// social
    			var x = social*215*Math.cos(Math.radians(180));
    			var y = social*215*Math.sin(Math.radians(-180));
				drawStar(x, y, 5, 12, 6);			
    		}
    		if (specificInterest == 4) {	// enterprising 
    			var x = enterprising*215*Math.cos(Math.radians(120));
    			var y = enterprising*215*Math.sin(Math.radians(-120));
				drawStar(x, y, 5, 12, 6);		
    		}
    		if (specificInterest == 5) {	// conventional 
    			var x = conventional*215*Math.cos(Math.radians(60));
    			var y = conventional*215*Math.sin(Math.radians(-240));
				drawStar(x, y, 5, 12, 6);
    		}
	    }
	    else {
	    	averageX = averageX / coordArray.length;
	    	averageY = averageY / coordArray.length;
	    	drawStar(averageX, averageY, 5, 12, 6);
		}

	}

	// Specify the src to load the image
	// the WoW seems to be 500px by 530px
	img.src = "../../img/wow.png";
}
//--------------------------------------------VIDEO
function addFullScreenOverlay(){
  $("#mep_0").prepend("<div id='fullscreen-overlay' style='z-index:1001'" + 
    "class='mejs-overlay mejs-layer'><div>" +
    document.getElementsByClassName("current")[0].textContent +
    "</div></div>");
}
                  
function updateTitle(){
  document.getElementById("vidtitle").textContent = document.getElementsByClassName("current")[0].textContent;
  document.getElementById("fullscreen-overlay").textContent = document.getElementsByClassName("current")[0].textContent;
}

function showNextCareerButton(thumb) {
	if (thumb == "up") {
		$('#upthumb').addClass("upthumb-selected");
		$('#upthumb').removeClass("upthumb");
		$('#midthumb').addClass("midthumb");
		$('#midthumb').removeClass("midthumb-selected");
		$('#downthumb').addClass("downthumb");
		$('#downthumb').removeClass("downthumb-selected");
		$('#next-career-up').show();
		$('#next-career-mid').hide();
		$('#next-career-down').hide();
	} else if (thumb == "mid") {
		$('#midthumb').addClass("midthumb-selected");
		$('#midthumb').removeClass("midthumb");
		$('#upthumb').addClass("upthumb");
		$('#upthumb').removeClass("upthumb-selected");
		$('#downthumb').addClass("downthumb");
		$('#downthumb').removeClass("downthumb-selected");
		$('#next-career-mid').show();
		$('#next-career-up').hide();
		$('#next-career-down').hide();
	} else if (thumb == "down") {
		$('#downthumb').addClass("downthumb-selected");
		$('#downthumb').removeClass("downthumb");
		$('#upthumb').addClass("upthumb");
		$('#upthumb').removeClass("upthumb-selected");
		$('#midthumb').addClass("midthumb");
		$('#midthumb').removeClass("midthumb-selected");
		$('#next-career-down').show();
		$('#next-career-up').hide();
		$('#next-career-mid').hide();
	}
}

function updateRank(choice) {
	var pname = window.location.href;
	var socPos = pname.search(/[0-9][0-9]-[0-9][0-9][0-9][0-9]/);
  //var soc = pname.substring(socPos, socPos+2).concat(pname.substring(socPos+2, socPos+7));
  var soc = pname.substring(socPos, socPos+7);
	
	if (choice == "like"){
		$.get('../' + soc + '/vidup');
    } else if (choice == "neutral") {
		$.get('../' + soc + '/vidmid');
	} else if (choice == "dislike") {
		$.get('../' + soc + '/viddown');
	}
}
//--------------------------------------------SALARY
var formatMoney = function(number) {
	return ((number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
}

var salary_salaryState = 0;

// I believe I need a calculateData() function
// dynamically push in value for salary (every new one though, you must pop out the old value)
var salary_calculateData = function() {
	var averageSalary = document.getElementById('careerTable').rows[salary_salaryState].cells[0].innerHTML;
	var lowSalary = document.getElementById('careerTable').rows[salary_salaryState].cells[1].innerHTML;
	var medianSalary = document.getElementById('careerTable').rows[salary_salaryState].cells[2].innerHTML;
	var highSalary = document.getElementById('careerTable').rows[salary_salaryState].cells[3].innerHTML;

	averageSalary = parseInt(averageSalary);
	lowSalary = parseInt(lowSalary);
	medianSalary = parseInt(medianSalary);
	highSalary = parseInt(highSalary);

	averageSalaryData = [];
	lowSalaryData = [];
	medianSalaryData = [];
	highSalaryData = [];

	averageSalaryData.push(averageSalary);
	lowSalaryData.push(lowSalary);
	medianSalaryData.push(medianSalary);
	highSalaryData.push(highSalary);
}

var salary_drawChart = function() {
	$('#salary-container').highcharts({
		chart: {
			type: 'column'
		},
		title: {
			text: 'Occupational Salary Averages'
		},
		yAxis: {
			allowDecimals: false,
			title: {
				text: 'Yearly salary'
			}
		},
		xAxis: {
			visible: false
		},
		tooltip: {
			// TODO: It'd be nice if the tooltip was centered
			formatter: function () {
				return '<b>' + this.series.name + '</b>: <br/>$' + formatMoney(this.point.y);
			}
		},
		series: [{
			name: 'Average Salary',
			data: averageSalaryData
		},
		{
			name: 'Low Salary',
			data: lowSalaryData
		},
		{
			name: 'Median Salary',
			data: medianSalaryData
		},
		{
			name: 'High Salary',
			data: highSalaryData
		}]
	});
}
//--------------------------------------------EDUCATION
var initiallyHidden = [
	'#fader',
	'#signUpBox',
	'#loginBox',
	'#salaryDialog',
	'#educationDialog',
	'#skillsDialog',
	'#careerOutlookDialog',
	'#worldOfWorkDialog'
];

var formatMoney = function(number) {
	return ((number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
}

var yearsInSchool;

var undergradPublicPrivate = 0;
var undergradTuitionRoomBoard = 0;

var gradPublicPrivate = 0;

var edu_salaryState = 0;
var salaryPosition = 0;

var edu_data;

var edu_calculateData = function() {
	var salary = document.getElementById('careerTable').rows[edu_salaryState].cells[salaryPosition].innerHTML;
	salary = parseInt(salary);
	document.getElementById('careerSalaryDisplay').innerHTML = "Annual Salary: $" + formatMoney(salary);

	var yearsInUndergrad = document.getElementById('yearsInUndergrad').innerHTML;
	yearsInUndergrad = parseInt(yearsInUndergrad);

	var yearsInGrad = document.getElementById('yearsInGrad').innerHTML;
	yearsInGrad = parseInt(yearsInGrad);

	var undergradCostPerYear = document.getElementById('undergradTable').rows[undergradPublicPrivate].cells[undergradTuitionRoomBoard].innerHTML;
	undergradCostPerYear = parseInt(undergradCostPerYear);
	document.getElementById('undergraduateCostDisplay').innerHTML = "Annual Cost: $" + formatMoney(undergradCostPerYear);

	if ($('#graduateInputs').length) {
		var gradCostPerYear = document.getElementById('gradTable').rows[gradPublicPrivate].cells[0].innerHTML;
		gradCostPerYear = parseInt(gradCostPerYear);
		document.getElementById('graduateCostDisplay').innerHTML = "Annual Cost: $" + formatMoney(gradCostPerYear);
	}

	yearsInSchool = yearsInUndergrad + yearsInGrad;


	edu_data = [];

	var currentDebt = 0;
	var currentYears = 0;

	edu_data.push({y: currentDebt, x: currentYears, marker:{enabled: false}});

	//Cost of getting the degree
	while (currentYears < yearsInUndergrad) {
		currentDebt -= undergradCostPerYear;
		currentYears += 1;
		if (currentYears == yearsInSchool) {
			edu_data.push({y: currentDebt, x: currentYears, marker:{enabled: true}});
		} else {
			edu_data.push({y: currentDebt, x: currentYears, marker:{enabled: false}});
		}
	}

	if ($('#graduateInputs').length) {
		while (currentYears < yearsInSchool) {
			currentDebt -= gradCostPerYear;
			currentYears += 1;
			if (currentYears == yearsInSchool) {
				edu_data.push({y: currentDebt, x: currentYears, marker:{enabled: true}});
			} else {
				edu_data.push({y: currentDebt, x: currentYears, marker:{enabled: false}});
			}
		}
	}

	while (currentDebt < (-salary)) {
		currentDebt += salary;
		currentYears += 1;
		edu_data.push({y: currentDebt, x: currentYears, marker:{enabled: false}});
	}

	var untilZero = -currentDebt;
	var restOfSalary = salary - untilZero;

	var timeUntilZero = (untilZero/salary);
	var nextYear = 1-timeUntilZero;

	currentDebt += untilZero;
	currentYears += timeUntilZero;
	edu_data.push({y: currentDebt, x: currentYears, marker:{enabled: true}});

	currentDebt += restOfSalary;
	currentYears += nextYear;
	edu_data.push({y: currentDebt, x: currentYears, marker:{enabled: false}});

	while (currentDebt < (3*salary)) {
		currentDebt += salary;
		currentYears += 1;
		edu_data.push({y: currentDebt, x: currentYears, marker:{enabled: false}});
	}

};

var edu_drawChart = function() {
	$('#chartContainer').highcharts({
    	title: {
            text: 'Investment -> Profit',
            x: -20 //center
        },
        xAxis: {
        	title: {
        		text: 'Years'
        	}
        },
        yAxis: {
            title: {
                text: 'Money ($)'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valuePrefix: '$',
            formatter: function() {
            	if (this.x < yearsInSchool) {
            		return "Year " + this.x + "<br>Investment: $" + formatMoney(-this.y);
            	} else if (this.x == yearsInSchool) {
            		return "Degree Earned!<br>Year " + this.x + "<br>Investment: $" + formatMoney(-this.y);
            	} else if (this.y < 0) {
            		return "Year " + this.x + "<br>Debt: $" + formatMoney(-this.y);
            	} else if (this.y == 0 && this.x != 0) {
            		return "Breakeven Point!<br>Debt: $0";
            	} else {
            		return "Year " + this.x + "<br>Profit: $" + formatMoney(this.y);
            	}
		    }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            showInLegend: false,
            data: edu_data
        }]
    });
};

var salaryStateToCode = function(stateCode){
	var salaryState = 0;
	switch(stateCode) {
		case "NAT":
			salaryState = 0;
			break;
		case "AL":
			salaryState = 1;
			break;
		case "AK":
			salaryState = 2;
			break;
		case "AZ":
			salaryState = 3;
			break;
		case "AR":
			salaryState = 4;
			break;
		case "CA":
			salaryState = 5;
			break;
		case "CO":
			salaryState = 6;
			break;
		case "CT":
			salaryState = 7;
			break;
		case "DE":
			salaryState = 8;
			break;
		case "DC":
			salaryState = 9;
			break;
		case "FL":
			salaryState = 10;
			break;
		case "GA":
			salaryState = 11;
			break;
		case "HI":
			salaryState = 12;
			break;
		case "ID":
			salaryState = 13;
			break;
		case "IL":
			salaryState = 14;
			break;
		case "IN":
			salaryState = 15;
			break;
		case "IA":
			salaryState = 16;
			break;
		case "KS":
			salaryState = 17;
			break;
		case "KY":
			salaryState = 18;
			break;
		case "LA":
			salaryState = 19;
			break;
		case "ME":
			salaryState = 20;
			break;
		case "MD":
			salaryState = 21;
			break;
		case "MA":
			salaryState = 22;
			break;
		case "MI":
			salaryState = 23;
			break;
		case "MN":
			salaryState = 24;
			break;
		case "MS":
			salaryState = 25;
			break;
		case "MO":
			salaryState = 26;
			break;
		case "MT":
			salaryState = 27;
			break;
		case "NE":
			salaryState = 28;
			break;
		case "NV":
			salaryState = 29;
			break;
		case "NH":
			salaryState = 30;
			break;
		case "NJ":
			salaryState = 31;
			break;
		case "NM":
			salaryState = 32;
			break;
		case "NY":
			salaryState = 33;
			break;
		case "NC":
			salaryState = 34;
			break;
		case "ND":
			salaryState = 35;
			break;
		case "OH":
			salaryState = 36;
			break;
		case "OK":
			salaryState = 37;
			break;
		case "OR":
			salaryState = 38;
			break;
		case "PA":
			salaryState = 39;
			break;
		case "RI":
			salaryState = 40;
			break;
		case "SC":
			salaryState = 41;
			break;
		case "SD":
			salaryState = 42;
			break;
		case "TN":
			salaryState = 43;
			break;
		case "TX":
			salaryState = 44;
			break;
		case "UT":
			salaryState = 45;
			break;
		case "VT":
			salaryState = 46;
			break;
		case "VA":
			salaryState = 47;
			break;
		case "WA":
			salaryState = 48;
			break;
		case "WV":
			salaryState = 49;
			break;
		case "WI":
			salaryState = 50;
			break;
		case "WY":
			salaryState = 51;
			break;
		default:
	}
	return salaryState;
}

$(document).ready(function(){
	try{
		//--------------------------------------------WORLD_OF_WORK
		createCanvas();
		// tooltips 
		// Disabled, does not appear to serve any purpose
		// $('[data-toggle="tooltip"]').tooltip();
	} catch(err){
		console.log('World of Work failed to render');
		console.log(err);
	}
	try{
		//--------------------------------------------SKILLS
		if($('#skillsArray').length) {
			skillsArray = document.getElementById('skillsArray').innerHTML;
			skillsArray = skillsArray.split(",");
			j = 0;
			var skills_data = [];
			while (j < skillsArray.length) {
				//For some reason this checks if it's not NaN
				if (Number(skillsArray[j])) {

					//Intelligence Percentage + Title
					//document.getElementById('contentContainer').innerHTML += "<div class='intelligenceTitle'>" + skillsArray[j+1] + " " + 100*Number(skillsArray[j]) + "%</div>";
					document.getElementById('skills-contentContainer').innerHTML += "<div class='intelligenceTitle'>" + 100*Number(skillsArray[j]) + "% " + skillsArray[j+1] + "</div>";
					
					skills_data.push({name: (100*Number(skillsArray[j])).toString() + '% ' + skillsArray[j+1], y: 100*Number(skillsArray[j])});

					//Definitions + Tasks
					var taskString = "";
					taskString += "<div class='intelligenceDefinition'><ul><li>";

					switch(skillsArray[j+1]) {

					case "Naturalistic Intelligence":
						taskString += "Designates the human ability to discriminate among living things (plants, animals) as well as sensitivity to other features of the natural world (clouds, rock configurations).  This ability was clearly of value in our evolutionary past as hunters, gatherers, and farmers; it continues to be central in such roles as botanist or chef.  It is also speculated that much of our consumer society exploits the naturalist intelligences, which can be mobilized in the discrimination among cars, sneakers, kinds of makeup, and the like.";
						break;
					case "Musical Intelligence":
						taskString += "Musical intelligence is the capacity to discern pitch, rhythm, timbre, and tone.  This intelligence enables us to recognize, create, reproduce, and reflect on music, as demonstrated by composers, conductors, musicians, vocalist, and sensitive listeners.  Interestingly, there is often an affective connection between music and the emotions; and mathematical and musical intelligences may share common thinking processes.  Young adults with this kind of intelligence are usually singing or drumming to themselves.  They are usually quite aware of sounds others may miss.";
						break;
					case "Logical-Mathematical Intelligence":
						taskString += "Logical-mathematical intelligence is the ability to calculate, quantify, consider propositions and hypotheses, and carry out complete mathematical operations.  It enables us to perceive relationships and connections and to use abstract, symbolic thought; sequential reasoning skills; and inductive and deductive thinking patterns.  Logical intelligence is usually well developed in mathematicians, scientists, and detectives.  Young adults with lots of logical intelligence are interested in patterns, categories, and relationships.  They are drawn to arithmetic problems, strategy games and experiments.";
						break;
					case "Existential Intelligence":
						taskString += "Sensitivity and capacity to tackle deep questions about human existence, such as the meaning of life, why do we die, and how did we get here.";
						break;
					case "Interpersonal Intelligence":
						taskString += "Interpersonal intelligence is the ability to understand and interact effectively with others.  It involves effective verbal and nonverbal communication, the ability to note distinctions among others, sensitivity to the moods and temperaments of others, and the ability to entertain multiple perspectives.  Teachers, social workers, actors, and politicians all exhibit interpersonal intelligence.  Young adults with this kind of intelligence are leaders among their peers, are good at communicating, and seem to understand others' feelings and motives.";
						break;
					case "Bodily-Kinesthetic Intelligence":
						taskString += "Bodily kinesthetic intelligence is the capacity to manipulate objects and use a variety of physical skills.  This intelligence also involves a sense of timing and the perfection of skills through mind–body union.  Athletes, dancers, surgeons, and craftspeople exhibit well-developed bodily kinesthetic intelligence.";
						break;
					case "Linguistic Intelligence":
						taskString += "Linguistic intelligence is the ability to think in words and to use language to express and appreciate complex meanings.  Linguistic intelligence allows us to understand the order and meaning of words and to apply meta-linguistic skills to reflect on our use of language.  Linguistic intelligence is the most widely shared human competence and is evident in poets, novelists, journalists, and effective public speakers.  Young adults with this kind of intelligence enjoy writing, reading, telling stories or doing crossword puzzles.";
						break;
					case "Intra-personal Intelligence":
						taskString += "Intra-personal intelligence is the capacity to understand oneself and one's thoughts and feelings, and to use such knowledge in planning and directioning one's life.  Intra-personal intelligence involves not only an appreciation of the self, but also of the human condition.  It is evident in psychologist, spiritual leaders, and philosophers.  These young adults may be shy.  They are very aware of their own feelings and are self-motivated.";
						break;
					case "Spatial Intelligence":
						taskString += "Spatial intelligence is the ability to think in three dimensions.  Core capacities include mental imagery, spatial reasoning, image manipulation, graphic and artistic skills, and an active imagination.  Sailors, pilots, sculptors, painters, and architects all exhibit spatial intelligence.  Young adults with this kind of intelligence may be fascinated with mazes or jigsaw puzzles, or spend free time drawing or daydreaming.";
						break;
					default:
					}

					taskString += "</li></ul></div>";

					//Intelligence Tasks
					taskString += "<div class='intelligenceTasks'><ul>";

					j += 2;
					counter = 1;
					while (j < skillsArray.length && skillsArray[j][0] == counter.toString()) {
						
						taskString += "<li>" + skillsArray[j].substring(3);
						j++;

						while (j < skillsArray.length) {
							if ((skillsArray[j][0] == (counter+1).toString() && skillsArray[j][1] == '.' && skillsArray[j][2] == ' ') || Number(skillsArray[j])) {
								break;
							}
							taskString += "," + skillsArray[j];
							j++;
						}


						taskString += "</li>";
						counter++;

					}

					taskString += "</ul></div>";
					document.getElementById('skills-contentContainer').innerHTML += taskString;

				}
			}
		// global set, for reuse for rerendering chart on focus change
		skillsData = skills_data;
		// Build the chart
		$('#mainSkillsPieChart').highcharts({
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie'
			},
			title: {
				text: 'Skills'
			},
			tooltip: {
				pointFormat: '<b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: false
					},
					showInLegend: true
				}
			},
			series: [{
				name: 'Intelligences',
				colorByPoint: true,
				data: skills_data
			}]
		});
		}
	} catch(err){
		console.log('Skills failed to render');
		console.log(err);
	}
	try{
		//--------------------------------------------SALARY
	  salary_calculateData();
	  salary_drawChart();

	  $('#salary-salaryStateInput').change(function() {
		salary_salaryState = salaryStateToCode($('#salary-salaryStateInput').val());
		salary_calculateData();
		salary_drawChart();
	  });
	} catch(err){
		console.log('Salary failed to render');
		console.log(err);
	}
	try{
		//--------------------------------------------EDUCATION
		var i;
		for (i = 0; i < initiallyHidden.length; i++) {
			$(initiallyHidden[i]).hide();
		}	
		
		edu_calculateData();
		edu_drawChart();


		$('#salaryPositionInput').change(function() {
		
		console.log($('#education-salaryStateInput').val());
			switch ($('#salaryPositionInput').val()) {
				case "average":
					salaryPosition = 0;
					break;
				case "low":
					salaryPosition = 1;
					break;
				case "median":
					salaryPosition = 2;
					break;
				case "high":
					salaryPosition = 3;
					break;
				default:
			}

			edu_calculateData();
			edu_drawChart();

		});
		
		$('#undergradPublicPrivateInput').change(function() {
			switch ($('#undergradPublicPrivateInput').val()) {
				case "public":
					undergradPublicPrivate = 0;
					break;
				case "private":
					undergradPublicPrivate = 1;
					break;
				default:
			}

			edu_calculateData();
			edu_drawChart();
		});

		$('#undergradTuitionRoomBoardInput').change(function() {
			switch ($('#undergradTuitionRoomBoardInput').val()) {
				case "tuition":
					undergradTuitionRoomBoard = 0;
					break;
				case "roomBoard":
					undergradTuitionRoomBoard = 1;
					break;
				case "tuitionRoomBoard":
					undergradTuitionRoomBoard = 2;
					break;
				default:
			}

			edu_calculateData();
			edu_drawChart();
		});

		$('#gradPublicPrivateInput').change(function() {
			switch ($('#gradPublicPrivateInput').val()) {
				case "public":
					gradPublicPrivate = 0;
					break;
				case "private":
					gradPublicPrivate = 1;
					break;
				default:
			}

			edu_calculateData();
			edu_drawChart();
		});

		$('#education-salaryStateInput').change(function() {
			edu_salaryState = salaryStateToCode($('#education-salaryStateInput').val());
			edu_calculateData();
			edu_drawChart();
		});
	} catch(err){
		console.log('Education failed to render');
		console.log(err);
	}
});




