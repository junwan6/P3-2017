var likedCareers = [];
var dislikedCareers = [];
var neutralCareers = [];

var plotRadius = 10;
var hoverTitle = null;
var lastMouseX = 0;
var lastMouseY = 0;

$(window).load(function(){
    $(window).resize(render);

    $('#occupationPlotter').click(function(event) {
        var career = getCareerUnderMouse(event);

        if (career) {
            window.location.href = "/career/" + career.soc + "/video";
        }
    });
    
    $('#occupationPlotter').mousemove(function(event) {
        var career = getCareerUnderMouse(event);

        hoverTitle = (career ? career.title : null);
        lastMouseX = event.pageX;
        lastMouseY = event.pageY;

        render();
    });

    // Load in the values stored into the HTML page
    $('career').each(function(index) {
        var careerObject = new Object();
        careerObject.title = $(this).attr('title');
        careerObject.soc = $(this).attr('soc');
        careerObject.x = $(this).attr('x');
        careerObject.y = $(this).attr('y');

        var type = $(this).attr('type');
        if (type == 'like') {
            likedCareers.push(careerObject);
        }
        else if (type == 'dislike') {
            dislikedCareers.push(careerObject);
        }
        else if (type == 'neutral') {
            neutralCareers.push(careerObject);
        }
    });

    render();
});

function render() {
    var canvas = $('#occupationPlotter')[0];
    var image = $('#wowImage')[0];
    var ctx = canvas.getContext('2d');

    // Keep the canvas and image in sync with each other
    var width = image.width;
    var height = image.height;
    canvas.width = width;
    canvas.height = height;
    canvas.style.width = width;
    canvas.style.height = height;

    ctx.fillStyle = "red";
    for (var i = 0; i < dislikedCareers.length; i++) {
        var career = dislikedCareers[i];
        var x = career.x * width / 2 + width / 2;
        var y = -career.y * height / 2 + height / 2;
        drawCircle(ctx, x, y, plotRadius);
    }

    ctx.fillStyle = "yellow";
    for (var i = 0; i < neutralCareers.length; i++) {
        var career = neutralCareers[i];
        var x = career.x * width / 2 + width / 2;
        var y = -career.y * height / 2 + height / 2;
        drawCircle(ctx, x, y, plotRadius);
    }

    ctx.fillStyle = "green";
    for (var i = 0; i < likedCareers.length; i++) {
        var career = likedCareers[i];
        var x = career.x * width / 2 + width / 2;
        var y = -career.y * height / 2 + height / 2;
        drawCircle(ctx, x, y, plotRadius);
    }
    
    if (hoverTitle) {
        $('#hoverOccupationTitle').show();
        $('#hoverOccupationTitle').offset({top: lastMouseY - 10, left: lastMouseX + 20});
        $('#hoverOccupationTitle').text(hoverTitle);
    }
    else {
        $('#hoverOccupationTitle').hide();
    }
}

function drawCircle(ctx, x, y, rad) {
    ctx.beginPath();
    ctx.arc(x, y, rad, 0, Math.PI * 2)
    ctx.closePath();
    ctx.fill();
}

function getCareerUnderMouse(event) {
    var offset = $('#wowImage').offset();
    var width = $('#wowImage')[0].width;
    var height = $('#wowImage')[0].height;

    // Convert the event's pageX and Y coordinates to WoW coordinates
    var x = ((event.pageX - offset.left) - width / 2) / (width / 2);
    var y = ((offset.top - event.pageY) + height / 2) / (height / 2);
    var radius = plotRadius / (width / 2);

    // Store the mouse position
    lastMouseX = x;
    lastMouseY = y;

    // Search through the careers
    for (var i = 0; i < likedCareers.length; i++) {
        var career = likedCareers[i];
        if (dist(x, y, career.x, career.y) <= radius) {
            return career;
        }
    }

    for (var i = 0; i < neutralCareers.length; i++) {
        var career = neutralCareers[i];
        if (dist(x, y, career.x, career.y) <= radius) {
            return career;
        }
    }

    for (var i = 0; i < dislikedCareers.length; i++) {
        var career = dislikedCareers[i];
        if (dist(x, y, career.x, career.y) <= radius) {
            return career;
        }
    }

    return null;
}

function dist(x1, y1, x2, y2) {
    return Math.sqrt((x1 - x2) * (x1 - x2) + (y1 - y2) * (y1 - y2));
}
