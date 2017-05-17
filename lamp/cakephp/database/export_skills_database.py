#!/usr/bin/env python

#
#  export_skills_database.py
#
# This script reads in spreadsheets to output a batch importable file for the
# Skills relation.

import itertools
import json
import openpyxl
import os.path
import re
import sys

if len(sys.argv) != 3:
    sys.exit("export_skills_database: missing filenames; first filename is the input file for national data, second filename is the output file")
if not os.path.isfile(sys.argv[1]):
    sys.exit("export_skills_database: {0} not found".format(sys.argv[1]))
if os.path.dirname(sys.argv[2]) != "" and not os.path.exists(os.path.dirname(sys.argv[2])):
    sys.exit("export_skills_database: directory {0} does not exist".format(sys.argv[2]))

# Derived from the Python itertools recipes
# Collect data into fixed-length chunks or blocks
def grouper(iterable, n, fillvalue=None):
    # grouper('ABCDEFG', 3, 'x') --> ABC DEF Gxx
    args = [iter(iterable)] * n
    return itertools.izip_longest(fillvalue=fillvalue, *args)

def isPercent(text):
    match = re.match('^[0-9]+%$', text)
    return not (match is None)

def insertSkill(skillsText, intelligenceType, text):
    typeToKey = { u'naturalist' : 'naturalistSkills',
                  u'musical' : 'musicalSkills',
                  u'logical-mathematical' : 'logicalSkills',
                  u'existential' : 'existentialSkills',
                  u'interpersonal' : 'interpersonalSkills',
                  u'bodily-kinesthetic' : 'bodySkills',
                  u'linguistic' : 'linguisticSkills',
                  u'intra-personal' : 'intrapersonalSkills',
                  u'spatial' : 'spatialSkills' }

    key = typeToKey[intelligenceType]

    if key not in skillsText:
        skillsText[key] = []

    skillsText[key].append(text)

workbook = openpyxl.load_workbook(sys.argv[1], read_only=True)

try:
    worksheet = workbook.get_sheet_by_name("Skills")
except KeyError:
    sys.exit("export_skills_database: worksheet \"Skills\" not found")

# Read all rows in pairs of two
with open(sys.argv[2], "w") as outfile:
    for (topRow, bottomRow) in grouper(worksheet.rows, 2):
        # First cell of first row contains SOC code
        soc = topRow[0].value

        # Create a dictionary that represents a JSON object with the textual skills data
        skillsText = {}
        # Set default values for the percentages of each, since they are assumed to be 0
        # if the spreadsheet does not specify
        naturalistPercent = 0
        musicalPercent = 0
        logicalPercent = 0
        existentialPercent = 0
        interpersonalPercent = 0
        bodyPercent = 0
        linguisticPercent = 0
        intrapersonalPercent = 0
        spatialPercent = 0

        # Scan through the remaining cells in the two rows to fill out the skills information
        rowLen = min(len(topRow), len(bottomRow))
        for i in range(1, rowLen):
            # Skip cells if either the top or bottom row is empty; this will occur because
            # not all rows will have the same length, and openpyxl will pad the row with
            # empty cells.
            if ((topRow[i].value is None) or (bottomRow[i].value is None)):
                continue
            
            # Parse the cell containing the intelligence datatype
            # intelligenceType will be a string containing the first word of
            # the intelligence type in lowercase (e.g. 'naturalist')
            intelligenceType = topRow[i].value
            intelligenceType = intelligenceType.strip().split(' ')[0].lower()

            # Note: openpyxl automatically converts cells formatted as a percentage into
            # its numeric representation in decimal (i.e. 90% -> 0.9). Relying on this
            # behavior feels shaky, but openpyxl does not provide any way for us to unformat
            # it.
            if bottomRow[i].data_type == openpyxl.cell.Cell.TYPE_NUMERIC:
                percent = bottomRow[i].value
                if intelligenceType == u'naturalist':
                    naturalistPercent = percent
                elif intelligenceType == u'musical':
                    musicalPercent = percent
                elif intelligenceType == u'logical-mathematical':
                    logicalPercent = percent
                elif intelligenceType == u'existential':
                    existentialPercent = percent
                elif intelligenceType == u'interpersonal':
                    interpersonalPercent = percent
                elif intelligenceType == u'bodily-kinesthetic':
                    bodyPercent = percent
                elif intelligenceType == u'linguistic':
                    linguisticPercent = percent
                elif intelligenceType == u'intra-personal':
                    intrapersonalPercent = percent
                elif intelligenceType == u'spatial':
                    spatialPercent = percent
            else:
                skill = bottomRow[i].value.strip()
                insertSkill(skillsText, intelligenceType, skill)

        # Write out a row to the database
        outfile.write(u'\t'.join([soc,
                                  unicode(naturalistPercent),
                                  unicode(musicalPercent),
                                  unicode(logicalPercent),
                                  unicode(existentialPercent),
                                  unicode(interpersonalPercent),
                                  unicode(bodyPercent),
                                  unicode(linguisticPercent),
                                  unicode(intrapersonalPercent),
                                  unicode(spatialPercent),
                                  json.dumps(skillsText)]).encode("UTF-8"))
        outfile.write(u'\n'.encode("UTF-8"))
