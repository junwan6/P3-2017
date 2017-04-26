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
	img.src = "/images/wow.png";
}

$(document).ready(function(){

	$(function () {

		createCanvas();

		// tooltips
		$(document).ready(function(){
	    	$('[data-toggle="tooltip"]').tooltip();
		});

	});

});




