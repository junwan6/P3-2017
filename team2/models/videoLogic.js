var mysql = require('mysql');
var fs = require('fs');

// Load the database configuration
var config = JSON.parse(fs.readFileSync(__dirname + '/../config/db-config.json', 'utf8'));


// This is the main module to get the next recommended soc to view 
module.exports.getNextSOC = function(userId, successNext, errNext){
            // Get UnviewedSOCList
        var unviewedList = [];
        var ratedsocList = [];
        module.exports.getFilters(userId, 
            function(filters){
                console.log(filters);
                //console.log("salary is: ", filters.salary);
                var sal = false;
                var edu = false;
                if (filters)
                {
                    sal = filters.salary;
                    edu = filters.edu;
                }
                module.exports.getUnviewedSOCList(userId, sal, edu,
                    function (ul){
                        unviewedList = ul;

                        // Here's where we would apply filters onto the unviewed soc list
                        // We would need to query Occupation for into and cross reference
                        // Either here using javascript or via SQL in thE UnviewedSOCList query
                        // Look up inner join via javascript

                        module.exports.getRatedSOCList(userId, 
                            function (rl){
                                ratedsocList = rl;

                                // Run weighted algorithm to get the next SOC
                                console.log("Unviewedlist:" + unviewedList + " ratedsocList:" + ratedsocList);
                                try {
                                    var resultSOC = getNextSOC(unviewedList, ratedsocList);
                                    console.log("ResultSOC:" + resultSOC);
                                                            // Puts hyphen back
                                    var resultSOCwithHyphen = resultSOC.substring(0,2)+"-"+ resultSOC.substring(2,8);
                                    successNext(resultSOCwithHyphen);
                                }
                                catch (ex){
                                    console.log("No Video Left")
                                    errNext(ex); // "No Videos Left"
                                }
                            },
                            function (err){
                                console.log(err) // unsafe but need to test
                                res.writeHead(500);
                                res.end('Servor error');
                            }
                         )
                    },

                function (err){

                    console.log(err) // unsafe but need to test
                    res.writeHead(500);
                    res.end('Servor error');
                }
            )
        },

            function (err){

                console.log(err) // unsafe but need to test
                res.writeHead(500);
                res.end('Servor error');
            }
        )

}

//gets salary and education filter selection from table UserFilters
module.exports.getFilters = function(userId, successNext, errNext){
    var connection = mysql.createConnection(config);
    connection.connect();

    connection.query("SELECT salary, edu FROM UserFilters WHERE id =?;", userId, function(err, rows, fields) {
    if (err === null) {
                // Convert to array of {soc,weight} objects [{soc:123456, weight:1}, ...];
               console.log("successfully queried UserFilters");
            
                successNext(rows[0]);

                connection.end();
            }
            else {
                console.log(err)
                connection.end();
            }
    });

}

// Function returns the unviewed list of SOC numbers as an array
// We should add filter after getting this list. Doing an inner join with occupation info-filtered.
module.exports.getUnviewedSOCList = function(userId, salary, edu, successNext, errNext){
    var connection = mysql.createConnection(config);
    connection.connect();

    // Left Join and check null to get Videos-ViewHistory = Unviewed Videos
    var queryString = "SELECT v.soc FROM Videos v LEFT JOIN ViewHistory vh on v.soc=vh.soc AND vh.id=" + userId;
        // Consider adding v.personNum=vh.personNum, if we want to potentially view another interview of the same SOC

    queryString += ' INNER JOIN Occupation o ON v.soc=o.soc WHERE vh.soc IS NULL';
    if (salary || edu){
        queryString += ' AND '
        if (salary)
        {
            switch(salary){
                case 1:
                queryString += 'averageWage < 40000'
                break;
                case 2:
                queryString += 'averageWage >= 40000 AND averageWage <= 60000'
                break;
                case 3:
                queryString += 'averageWage >= 60000 AND averageWage <= 80000'
                break;
                case 4:
                queryString += 'averageWage > 100000'
                break;
            }
            if (edu){
                queryString += ' AND '
            }
        }
        if (edu)
        {
            queryString += 'educationRequired <= ' + edu;
        }
    }
    queryString += ';'
    console.log("Query for Unviewed List with Filters", queryString);

    connection.query(queryString, function(err, rows, fields) {
        if (err === null) {
            // Convert to pure array of soc numbers [123456, 234567,...]
            var unviewedList = []
            unviewedList = rows.map(function (x){return x.soc});
            successNext(unviewedList);

            connection.end();
        }
        else {
            console.log(err)
            connection.end();
        }
    });
}

// Module returns the rated list of SOC numbers as an object array [{soc:123456, weight:1}, ...];
module.exports.getRatedSOCList = function(userId, successNext, errNext){
    var connection = mysql.createConnection(config);
    connection.connect();


    // Left Join and check null to get Videos-ViewHistory = Unviewed Videos
    var queryString = "SELECT soc, rating weight from ViewHistory where id=" + userId;
        // Consider adding v.personNum=vh.personNum, if we want to potentially view another interview of the same SOC

    connection.query(queryString, function(err, rows, fields) {
        if (err === null) {
            // Convert to array of {soc,weight} objects [{soc:123456, weight:1}, ...];
            var ratingList = []
            ratingList = rows.map(function (x){return {soc:x.soc, weight:x.weight};});

            successNext(ratingList);
            connection.end();
        }
        else {
            console.log(err)
            connection.end();
        }
    });
}


/*
// Local test code
var connection = mysql.createConnection(config);
connection.connect();

var userId = 1;
// Videos - VideoHistory = unviewed videos
var queryString = "SELECT v.soc FROM Videos v LEFT JOIN ViewHistory vh on v.soc=vh.soc AND vh.id=" + userId + " WHERE vh.soc IS NULL;";
var queryRating = "SELECT soc, rating weight from SOCratings where id=" + userId; 

connection.query(queryString, function(err, rows, fields) {
    if (err === null) {
        //successNext(rows[0]);
        
        var unviewedList = []
        unviewedList = rows.map(function (x){return x.soc});

        connection.query(queryRating, function(err2, rows2, fields2) {
            var ratingList = []

            
            ratingList = rows2.map(function (x){return {soc:x.soc, weight:x.weight};});

            //console.log(ratingList)

            var finalsoc = getNextSOC(unviewedList, ratingList);

            console.log(finalsoc);

        })

        connection.end();
    } else {
        // re-run algorithm to get 5 new SOC's
        console.log(err);
        connection.end();
    }
});
*/



function getNextSOC(unviewedsocList, ratedsocList)
{
    //console.log(ratedsocList);
    var unviewedTree = buildUnviewedMapTree(unviewedsocList);
    var ratingTree = generateWeightOnlyTree(buildMapTree(ratedsocList));

    if (!mapTreeHasMinorGroup(unviewedTree))
        throw "No Videos Left"

    var unviewedTreeWithGroupWeights = transferWeights(unviewedTree, ratingTree);

    return chooseSOC(unviewedTreeWithGroupWeights)
}


function generateWeightOnlyTree(mapTree)
{
    var weightOnlyTree = {};
    for (var majorIndex in mapTree)
    {
        weightOnlyTree[majorIndex] = {};
        weightOnlyTree[majorIndex].weight = getMajorGroupWeight(mapTree[majorIndex])


        for (var minorIndex in mapTree[majorIndex])
            weightOnlyTree[majorIndex][minorIndex] = {weight:getMinorGroupWeight(mapTree[majorIndex][minorIndex])}
    }
    //console.log("WeightOnlyTree")
    //console.log(weightOnlyTree);
    return weightOnlyTree;
}

function transferWeights(unviewedTree, ratedTree)
{
    var unviewedTreeWithGroupWeights = Object.assign({}, unviewedTree);

    for (var majorIndex in unviewedTree)
    {
        if (ratedTree[majorIndex])
        {
            unviewedTreeWithGroupWeights[majorIndex]['weight'] = ratedTree[majorIndex]['weight']
            for (var minorIndex in unviewedTree[majorIndex])
            {
                if (minorIndex != 'weight')
                {
                    if (ratedTree[majorIndex][minorIndex])
                        unviewedTreeWithGroupWeights[majorIndex][minorIndex]['weight'] = ratedTree[majorIndex][minorIndex]['weight'];
                    else
                        unviewedTreeWithGroupWeights[majorIndex][minorIndex]['weight'] = 0;
                }
            }
        }
        else 
            unviewedTreeWithGroupWeights[majorIndex]['weight'] = 0;
    }

    return unviewedTreeWithGroupWeights;
}

function chooseSOC(mapTreeWithGroupWeights)
{
    var majorIndex;
    var majorWeight;


    if (!mapTreeHasMinorGroup(mapTreeWithGroupWeights))
        throw "No Videos Left" 

    // Choose Major Group
    var majorGroupArray = weightMaptoArray(mapTreeWithGroupWeights); // Array with just {groupNum and weight}
    var majorGroup = chooseMajorGroup(majorGroupArray);
    //console.log(majorGroup)

    while (!hasMinorGroup(mapTreeWithGroupWeights[majorGroup]))
        majorGroup = chooseMajorGroup(majorGroupArray);
    //console.log(majorGroup);

    // Choose Minor Group
    var minorGroupArray = weightMaptoArray(mapTreeWithGroupWeights[majorGroup])
    var minorGroup = chooseMinorGroup(minorGroupArray)
    //console.log(minorGroup)


    // Choose final SOC
    var soc = chooseOccupation(mapTreeWithGroupWeights[majorGroup][minorGroup])
    return soc;
}

// Checks if a majorGroup still has minor groups
function hasMinorGroup(map)
{
    for (var index in map)
    {
        if (index != 'weight')
            return true;
    }
    return false;
}

// Checks if the mapTree contains any minor groups
function mapTreeHasMinorGroup(mapTree)
{
    for (var i in mapTree)
    {
        for (var j in mapTree[i])
            if (mapTree[i][j] != 'weight')
                return true;
    }
    return false;
}


// Converts a object of objects into an array of objects. 
function weightMaptoArray(map)
{
    var groupWeightArray = [];
    for (var index in map)//
    {
        // for every minorGroup, compute their weight, and collect into a list with {groupNum, weight}
        if (index !== "weight")
        {
            groupWeightObj = {}
            groupWeightObj['groupNum'] = index;
            groupWeightObj['weight'] = map[index]['weight']
            groupWeightArray = groupWeightArray.concat(groupWeightObj)
        }
    }
    return groupWeightArray;
}

// Choose a major group based on a major group list with weights
function chooseMajorGroup(majorGroupArray) // majorGroupArray with Weight
{
    // Get the magnitude of the weights. Use this as base shift amount
    var weightedArray = generateWeightedArray(majorGroupArray, false);  
    //console.log(weightedArray);

    // randomly choose from the weighted array
    var randIndex = Math.floor(Math.random() * weightedArray.length);

    return weightedArray[randIndex];
}


// Choose a minor group based on a majorGroupMap
function chooseMinorGroup(minorGroupArray) 
{
    var weightedArray = generateWeightedArray(minorGroupArray, true);

    //console.log(weightedArray);
    // randomly choose from the weighted array
    var randIndex = Math.floor(Math.random() * weightedArray.length);

    return weightedArray[randIndex];
}


// Randomly choose an SOC code from a list of SOC codes
function chooseOccupation(minorGroupArray)
{
    var soc = {}
    var randIndex = Math.floor(Math.random() * minorGroupArray.length);
    soc = minorGroupArray[randIndex];
    return soc;
}


// Given a list of groupNum and weights, generates a weighted array that you can randomly select and have the right probability
// weightsArray: [{'num':11, 'weight':3}, ....]
// Array with groupNum and weights

// Formula 
// If weight >= 0.   3*(weight+2)^1.7 + 2*absMaxFromZero
// If weight < 0.   2*(weight) + 2*absMaxFromZero
// This gives a reasonly balanced growth and lessens the impact of negative on branches. 
function generateWeightedArray(weightsArray, isMinor)
{
    //console.log(weightsArray)
    var absMax = 0;
    for (var i in weightsArray)
    {
        if (Math.abs(weightsArray[i]['weight']) > absMax)
            absMax = Math.abs(weightsArray[i]['weight']); 
    }

    // Use shiftAmt to make weight values positive for probabiity 
    var shiftAmt = absMax*2; 
    var power = 1.7;    // multiply weight value to increase effect
    var posScale = 3;
    var posShiftAmt = 2;

    var negScale = 2;

    // remove power effect for minorGroups, which has less choices
    if (isMinor)
    {
        power = 1;
        shiftAmt += 20
        posShiftAmt = 1;
    }

    var n; 
    var curWeight
    var total = 0;
    // so I apply shift amount based on max
    // and increase the power by 1.7
    // I increase power by 1.7 for positive, and x2 for negative
    for (var i in weightsArray)
    {
        curWeight = weightsArray[i]['weight'];
        if (curWeight > -1) // >= 0
            n = posScale * (Math.pow(curWeight,power) + posShiftAmt) + shiftAmt; 
        else  // n < 0
            n = negScale * curWeight + shiftAmt; 
        
        // if < 1, make it 1. To give some chance
        if (n < 1) n = 1;
        n = Math.floor(n);

        weightsArray[i]['scaledWeight'] = n

        total += n; // Keep track of total to calc % chance
    }


    var weightedArray = [];
    var groupNum;
    var percent; 
    var percentArray = [];
    for (var i in weightsArray)
    {
        groupNum = weightsArray[i]['groupNum'];
        n = weightsArray[i]['scaledWeight'];
        percent = (n / total + "").slice(0,6); // Slice first 6 digit to display

        percentArray = percentArray.concat({'groupNum':groupNum, 'percent':percent});

        //console.log(groupNum + ": Chance: " + n + "/" + total + "="+ percent);

        // Add the scale weight number of groupNum to array
        for (var j = 0; j < n; j++)
            weightedArray = weightedArray.concat(groupNum);
    }

    return weightedArray;
}




function buildUnviewedMapTree(socList)
{
    // making minor group
    var mapTree = {}
    var majorGroupMap = {}
    socList = socList.map(socSanitize)
    for (var majorIndex = 11; majorIndex < 56; majorIndex+=2)
    { // 11 to 55. 
        majorGroupMap[majorIndex] = socList.filter(filterMajor(majorIndex));
    }

    for (var majorIndex in majorGroupMap){
        mapTree[majorIndex] = makeMinorGroupMap(majorGroupMap[majorIndex]);
    }

    return mapTree;
}

// Cleans up SOC codes from 12-3456 to 123456
function socSanitize(x){ 
    if (typeof x != 'string')
        x += "";
    return x.replace('-','');
}


// maps soc into major groups
function buildMapTree(socWeightedList)
{
    soclist = socWeightedList;

    // making minor group
    var mapTree = {}
    var majorGroupMap = {}
    soclist = soclist.map(socToString);
    for (var majorIndex = 11; majorIndex < 56; majorIndex+=2)
    { // 11 to 55. 
        majorGroupMap[majorIndex] = soclist.filter(filterMajor(majorIndex));
    }

    for (var majorIndex in majorGroupMap){
        mapTree[majorIndex] = makeMinorGroupMap(majorGroupMap[majorIndex]);
    }

    return mapTree;
}


// Filtering functions
// filterMajor: Returns true if soc code in specified majorGroup 
function filterMajor(majorGroup)
{
    majorGroup += "";
    return function (soc){
        if (typeof soc == 'object')
            socMajor = soc['soc'].substring(0,2); // take first 2 digits
        else
            socMajor = soc.substring(0,2);
        //if (socMajor === majorGroup)
        //  console.log(socMajor + " " + soc);
        return (socMajor === majorGroup)
    }
}

// filterMinor: Returns true if soc code is in specified minorGroup
function filterMinor(minorGroup)
{
    minorGroup += "";
    return function (soc){
        if (typeof soc == 'object')
            socMinor = soc['soc'].substring(2,3); // take 3rd digit
        else 
            socMinor = soc.substring(2,3); // take 3rd digit

        // if (socMinor === minorGroup)
        //  console.log(socMinor + " " + soc);
        return (socMinor === minorGroup)
    }
}

// takes a array in major groups and converts it into an map, grouped by minor groups
function makeMinorGroupMap(majorArray)
{
    var minorGroupMap = {};
    var tempMinorArray;
    for (var i = 1; i < 10; i++)
    {
        tempMinorArray = majorArray.filter(filterMinor(i));
        if (tempMinorArray.length > 0)
            minorGroupMap[i] = tempMinorArray;
    }
    return minorGroupMap;
}

// Gets weight of the minorGroup
function getMinorGroupWeight(minorGroupMap)
{
    var weight = 0;
    for (var i in minorGroupMap)
    {
        weight += minorGroupMap[i].weight;
    }
    return weight;
}

// Gets weight of the major group, calling getMinorGroupWeight
function getMajorGroupWeight(majorGroupMap)
{
    var weight = 0;
    for (var i in majorGroupMap)
        weight += getMinorGroupWeight(majorGroupMap[i]);
    
    return weight;
}

// Make sure soc is string, prep for filter functions
function socToString(x){ 
    x['soc'] += '';
    x['soc'] = x['soc'].replace('-','');
    return x;
}

// To generate weight. I saved the weight as JSON
// Generates random weights to the SOC
function generateRandomWeights(socQueryList)
{
    // socQueryList is a list of SOC's, ideally we are given socList with weights of 1, 0 or -1
    /*if (!socQueryList)
        throw "No Input" */

    socQueryList = socQueryList.map(socSanitize);

    var socWeigthedList = [];
    var temp = {}

    for (var i in socQueryList)
    {
        temp = {}
        temp['soc'] = socQueryList[i];
        temp['weight'] = (Math.floor(Math.random()* 3) -1); // assigns -1,0, or 1
        socWeigthedList[i] = temp;

    }
    return socWeigthedList;
}
