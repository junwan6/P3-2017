#!/usr/bin/env python

#
#  export_state_database.py
#
# This script reads in spreadsheets to output a batch importable file for the
# StateOccupation relation.

import openpyxl
import os.path
import sys
import warnings

if len(sys.argv) == 1:
    sys.exit("export_state_database: missing input filename")
if len(sys.argv) == 2:
    sys.exit("export_state_database: missing output filename")
if not os.path.isfile(sys.argv[1]):
    sys.exit("export_state_database: {0} not found".format(sys.argv[1]))
if os.path.dirname(sys.argv[2]) != "" and not os.path.exists(os.path.dirname(sys.argv[2])):
    sys.exit("export_state_database: directory {0} does not exist".format(sys.argv[2]))

def wageOutOfRange(decimal):
    return unicode(decimal) == u"#"

def formatDecimal(decimal):
    return unicode(decimal).replace(u",", u"").replace(u"$", u"").replace(u">=", u"")

# Due to the names of the sheets in the spreadsheet, a warning is raised about
# a sheet name conflicting with a reserved name. Fortunately, this does not
# affect the sheet that we're interested in, so suppress this warning.
with warnings.catch_warnings():
    warnings.simplefilter('ignore')
    workbook = openpyxl.load_workbook(sys.argv[1], read_only=True)

# Unfortunately, it seems that each dataset provided by the BLS hardcodes
# the month and year of the data into the sheet, so there is no simple way
# to generalize this sheet name.
sheetName = "All May 2015 Data"
try:
    worksheet = workbook.get_sheet_by_name(sheetName)
except KeyError:
    sys.exit("export_state_database: worksheet \"{0}\" not found".format(sheetName))

stateCodeDict = { u"01" : u"AL",
                  u"02" : u"AK",
                  u"04" : u"AZ",
                  u"05" : u"AR",
                  u"06" : u"CA",
                  u"08" : u"CO",
                  u"09" : u"CT",
                  u"10" : u"DE",
                  u"11" : u"DC",
                  u"12" : u"FL",
                  u"13" : u"GA",
                  u"15" : u"HI",
                  u"16" : u"ID",
                  u"17" : u"IL",
                  u"18" : u"IN",
                  u"19" : u"IA",
                  u"20" : u"KS",
                  u"21" : u"KY",
                  u"22" : u"LA",
                  u"23" : u"ME",
                  u"24" : u"MD",
                  u"25" : u"MA",
                  u"26" : u"MI",
                  u"27" : u"MN",
                  u"28" : u"MS",
                  u"29" : u"MO",
                  u"30" : u"MT",
                  u"31" : u"NE",
                  u"32" : u"NV",
                  u"33" : u"NH",
                  u"34" : u"NJ",
                  u"35" : u"NM",
                  u"36" : u"NY",
                  u"37" : u"NC",
                  u"38" : u"ND",
                  u"39" : u"OH",
                  u"40" : u"OK",
                  u"41" : u"OR",
                  u"42" : u"PA",
                  u"44" : u"RI",
                  u"45" : u"SC",
                  u"46" : u"SD",
                  u"47" : u"TN",
                  u"48" : u"TX",
                  u"49" : u"UT",
                  u"50" : u"VT",
                  u"51" : u"VA",
                  u"53" : u"WA",
                  u"54" : u"WV",
                  u"55" : u"WI",
                  u"56" : u"WY",
                  u"66" : u"GU",
                  u"72" : u"PR",
                  u"78" : u"VI" }

# Pre-compute the "milestone rows," which indicate when we should print out
# a progress report
milestoneRows = [worksheet.max_row * i / 10 for i in range(1, 11)]

with open(sys.argv[2], "w") as outfile:
    # Read all rows except the header row
    rowCount = 0
    for row in worksheet.rows:
        rowCount += 1

        # Print out a status message every 10%
        if rowCount in milestoneRows:
            progressPercent = (milestoneRows.index(rowCount) + 1) * 10
            print 'Export progress: {0}%'.format(progressPercent)

        # Skip the first row
        if rowCount <= 1:
            continue

        # Ignore row for non-state regions
        if row[2].value != u"2":
            continue
        # Ignore row for aggregations of occupations
        if row[8].value.strip() != u"detail":
            continue

        # Convert the region code into the 2-letter state code
        stateCode = stateCodeDict[row[0].value]

        # Wages specified with a # indicate that the annual wage is at least $187,200, or that the hourly wage is at least $90
        if not (row[28].value is None) and row[28].value == 1:
            # Hourly wage
            wageType = 'hourly'

            averageWage = u"90" if wageOutOfRange(row[14].value) else formatDecimal(row[14].value)
            averageWageOutOfRange = u"1" if wageOutOfRange(row[15].value) else u"0"

            lowWage = u"90" if wageOutOfRange(row[17].value) else formatDecimal(row[17].value)
            lowWageOutOfRange = u"1" if wageOutOfRange(row[17].value) else u"0"

            medianWage = u"90" if wageOutOfRange(row[19].value) else formatDecimal(row[19].value)
            medianWageOutOfRange = u"1" if wageOutOfRange(row[19].value) else u"0"

            highWage = u"90" if wageOutOfRange(row[21].value) else formatDecimal(row[21].value)
            highWageOutOfRange = u"1" if wageOutOfRange(row[21].value) else u"0"
        else:
            # Annual wage
            wageType = 'annual'

            averageWage = u"187200" if wageOutOfRange(row[15].value) else formatDecimal(row[15].value)
            averageWageOutOfRange = u"1" if wageOutOfRange(row[15].value) else u"0"

            lowWage = u"187200" if wageOutOfRange(row[22].value) else formatDecimal(row[22].value)
            lowWageOutOfRange = u"1" if wageOutOfRange(row[22].value) else u"0"

            medianWage = u"187200" if wageOutOfRange(row[24].value) else formatDecimal(row[24].value)
            medianWageOutOfRange = u"1" if wageOutOfRange(row[24].value) else u"0"

            highWage = u"187200" if wageOutOfRange(row[26].value) else formatDecimal(row[26].value)
            highWageOutOfRange = u"1" if wageOutOfRange(row[26].value) else u"0"

        outfile.write(u"\t".join([row[6].value, stateCode, averageWage, averageWageOutOfRange, lowWage, lowWageOutOfRange, medianWage, medianWageOutOfRange, highWage, highWageOutOfRange]).encode("UTF-8"))
        outfile.write(u"\n")
