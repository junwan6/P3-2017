$(document).ready(function() {
    $('#world-of-work').click(function (event) {
        var imageOffset = $('#world-of-work').offset();
        var width = $('#world-of-work').width();
        var height = $('#world-of-work').height();
        
        // Find the click position relative to the center of the image
        // Note that we assume a Cartesian coordinate system, so a positive
        // y is in the upwards direction
        var x = event.pageX - (imageOffset.left + width / 2);
        var y = (imageOffset.top + height / 2) - event.pageY ;
        
        // Normalize the coordinates so that they are in the range [-1, 1],
        // where the extremes map to the edge of the image
        var normalX = x / (width / 2);
        var normalY = y / (height / 2);

        console.log(normalX);
        console.log(normalY);

        // Only act if the clicks are within the circle
        if (Math.sqrt(normalX * normalX + normalY * normalY) <= 1.0) {
            var query = $.param({ x : normalX,
                                  y : normalY});

            window.location.href = "/career/random?" + query;
        }
    });
});
