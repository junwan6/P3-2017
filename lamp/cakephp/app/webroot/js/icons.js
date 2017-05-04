$(document).ready(function(){
	$('.iconSegment[data-dialog-trigger]').mouseenter(function() {
		var dialog = $($(this).attr('data-dialog-trigger'));
		if (!dialog.is(':visible')) {   
  			dialog.show();
		};  
	})

	$('.iconSegment[data-dialog-trigger]').mouseleave(function() {
		var dialog = $($(this).attr('data-dialog-trigger'));
		if (dialog.is(':visible')) {   
  			dialog.hide();
		};  
	})


	if($('#skillsArray').length) {

		$('#skillsDialog').css({'margin-top': '-10em', 'left': '-17em', 'width': '350px'});


		skillsArray = document.getElementById('skillsArray').innerHTML;
		skillsArray = skillsArray.split(",");
		j = 0;

		var data = [];

		while (j < skillsArray.length) {
			//For some reason this checks if it's not NaN
			if (Number(skillsArray[j])) {
				//Intelligence Percentage + Title
				data.push({name: (100*Number(skillsArray[j])).toString() + '% ' + skillsArray[j+1], y: 100*Number(skillsArray[j])});
			}
			j++;
		}

		    // Build the chart
	    $('#skillsPieChart').highcharts({
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
	            data: data
	        }]
	    });
	}

});
