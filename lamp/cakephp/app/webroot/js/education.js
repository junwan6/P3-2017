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

var salaryState = 0;
var salaryPosition = 0;

var data;

var calculateData = function() {
	var salary = document.getElementById('careerTable').rows[salaryState].cells[salaryPosition].innerHTML;
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


	data = [];

	var currentDebt = 0;
	var currentYears = 0;

	data.push({y: currentDebt, x: currentYears, marker:{enabled: false}});

	//Cost of getting the degree
	while (currentYears < yearsInUndergrad) {
		currentDebt -= undergradCostPerYear;
		currentYears += 1;
		if (currentYears == yearsInSchool) {
			data.push({y: currentDebt, x: currentYears, marker:{enabled: true}});
		} else {
			data.push({y: currentDebt, x: currentYears, marker:{enabled: false}});
		}
	}

	if ($('#graduateInputs').length) {
		while (currentYears < yearsInSchool) {
			currentDebt -= gradCostPerYear;
			currentYears += 1;
			if (currentYears == yearsInSchool) {
				data.push({y: currentDebt, x: currentYears, marker:{enabled: true}});
			} else {
				data.push({y: currentDebt, x: currentYears, marker:{enabled: false}});
			}
		}
	}

	while (currentDebt < (-salary)) {
		currentDebt += salary;
		currentYears += 1;
		data.push({y: currentDebt, x: currentYears, marker:{enabled: false}});
	}

	var untilZero = -currentDebt;
	var restOfSalary = salary - untilZero;

	var timeUntilZero = (untilZero/salary);
	var nextYear = 1-timeUntilZero;

	currentDebt += untilZero;
	currentYears += timeUntilZero;
	data.push({y: currentDebt, x: currentYears, marker:{enabled: true}});

	currentDebt += restOfSalary;
	currentYears += nextYear;
	data.push({y: currentDebt, x: currentYears, marker:{enabled: false}});

	while (currentDebt < (3*salary)) {
		currentDebt += salary;
		currentYears += 1;
		data.push({y: currentDebt, x: currentYears, marker:{enabled: false}});
	}

}

var drawChart = function() {
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
            data: data
        }]
    });
}

$(document).ready(function(){


	var i;
	for (i = 0; i < initiallyHidden.length; i++) {
		$(initiallyHidden[i]).hide();
	}	


	calculateData();
	drawChart();


	$('#salaryPositionInput').change(function() {
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

		calculateData();
		drawChart();

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

		calculateData();
		drawChart();
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

		calculateData();
		drawChart();
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

		calculateData();
		drawChart();
	});

	$('#salaryStateInput').change(function() {
		switch($('#salaryStateInput').val()) {
			case "US":
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

		calculateData();
		drawChart();
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

	$('#signUpCloseButton').click(function() {

		$('#fader').fadeOut();
		$('#signUpBox').fadeOut();
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




