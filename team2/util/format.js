module.exports.formatWithThousandSeparators = function (num) {
    var text = num.toFixed();
    var formattedText = new String();

    // Scan backwards from the end to add commas
    var i;
    for (i = text.length - 3; i >= 0; i -= 3) {
        formattedText = text.substring(i, i+3) + formattedText;
        if (i != 0) {
            formattedText = ',' + formattedText;
        }
    }
    // If i < 0, then some leading digits were skipped, so add them in
    if (i < 0) {
        formattedText = text.substring(0, i+3) + formattedText;
    }

    return formattedText;
}

module.exports.formatPercentage = function (num) {
    var text = num.toFixed() + "%";
    if (num > 0) {
        text = "+" + text;
    }
    if (text == "-0%" || text == "+0%") {
        text = "0%";
    }
    return text;
}
