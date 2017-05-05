// need initiallyHidden?

var formatMoney = function(number) {
	return ((number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
}

var salaryState = 0;

// I believe I need a calculateData() function
// dynamically push in value for salary (every new one though, you must pop out the old value)
var calculateData = function() {
	var averageSalary = document.getElementById('careerTable').rows[salaryState].cells[0].innerHTML;
	var lowSalary = document.getElementById('careerTable').rows[salaryState].cells[1].innerHTML;
	var medianSalary = document.getElementById('careerTable').rows[salaryState].cells[2].innerHTML;
	var highSalary = document.getElementById('careerTable').rows[salaryState].cells[3].innerHTML;

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

var drawChart = function() {
	$('#container').highcharts({
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

$(document).ready(function(){
	$(function () {
		calculateData();
		drawChart();

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
	});
});




