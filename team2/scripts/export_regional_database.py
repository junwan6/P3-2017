#!/usr/bin/env python

#
#  export_regional_database.py
#
# This script reads in spreadsheets to output a batch importable file for the
# RegionalOccupation relation. Note that this script is currently deprecated,
# as the RegionalOccupation relation is no longer needed.

import openpyxl
import os.path
import sys

if len(sys.argv) == 1:
    sys.exit("export_regional_database: missing input filename")
if len(sys.argv) == 2:
    sys.exit("export_regional_database: missing output filename")
if not os.path.isfile(sys.argv[1]):
    sys.exit("export_regional_database: {0} not found".format(sys.argv[1]))
if os.path.dirname(sys.argv[2]) != "" and not os.path.exists(os.path.dirname(sys.argv[2])):
    sys.exit("export_regional_database: directory {0} does not exist".format(sys.argv[2]))

def wageOutOfRange(decimal):
    return unicode(decimal) == u"#"

def formatDecimal(decimal):
    return unicode(decimal).replace(u",", u"").replace(u"$", u"").replace(u">=", u"")

workbook = openpyxl.load_workbook(sys.argv[1], read_only=True)

# Unfortunately, it seems that each dataset provided by the BLS hardcodes
# the month and year of the data into the sheet, so there is no simple way
# to generalize this sheet name.
sheetName = "All May 2015 Data"
try:
    worksheet = workbook.get_sheet_by_name(sheetName)
except KeyError:
    sys.exit("export_regional_database: worksheet \"{0}\" not found".format(sheetName))

with open(sys.argv[2], "w") as outfile:
    # Read all rows except the header row
    rowCount = 0
    for row in worksheet.rows:
        rowCount += 1

        # Skip the first row
        if rowCount <= 1:
            continue

        # Ignore row for regions that are too broad (i.e. country or state)
        if row[2].value != 4 and row[2].value != 5:
            continue
        # Ignore row for aggregations of occupations
        if row[8].value.strip() != u"detail":
            continue
        # OPEN QUESTION: For now we will skip occupations without a listed annual wage, but we might want to do something with the hourly wages
        if row[28].value == 1:
            continue

        # Annual wages specified with a # indicate that the annual wage is at least $187,200
        lowAnnualWage = u"187200" if wageOutOfRange(row[23].value) else formatDecimal(row[23].value)
        lowAnnualWageOutOfRange = u"1" if wageOutOfRange(row[23].value) else u"0"

        medianAnnualWage = u"187200" if wageOutOfRange(row[24].value) else formatDecimal(row[24].value)
        medianAnnualWageOutOfRange = u"1" if wageOutOfRange(row[24].value) else u"0"

        highAnnualWage = u"187200" if wageOutOfRange(row[25].value) else formatDecimal(row[25].value)
        highAnnualWageOutOfRange = u"1" if wageOutOfRange(row[25].value) else u"0"

        outfile.write(u"\t".join([row[6].value, row[0].value, lowAnnualWage, lowAnnualWageOutOfRange, medianAnnualWage, medianAnnualWageOutOfRange, highAnnualWage, highAnnualWageOutOfRange]).encode("UTF-8"))
        outfile.write(u"\n")
