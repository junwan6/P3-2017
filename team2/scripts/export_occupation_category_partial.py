#!/usr/bin/env python

#
#  export_occupation_category_partial.py
#
# This script reads in spreadsheets to output an HTML fragment that can be
# embedded into browse.html that creates the dropdown-by-category menu for
# browsing jobs. Generally, the output of the script should be placed into the
# path views/partials/major_group.html
#
# Future work: see if we can implement the dropdown-by-category menu without
# using this script.

import openpyxl
import os.path
import sys
from collections import OrderedDict

if len(sys.argv) == 1:
    sys.exit("export_occupation_category_partial: missing input filename")
if len(sys.argv) == 2:
    sys.exit("export_occupation_category_partial: missing output filename")
if not os.path.isfile(sys.argv[1]):
    sys.exit("export_occupation_category_partial: {0} not found".format(sys.argv[1]))
if os.path.dirname(sys.argv[2]) != "" and not os.path.exists(os.path.dirname(sys.argv[2])):
    sys.exit("export_occupation_category_partial: directory {0} does not exist".format(sys.argv[2]))

class Node:
    def __init__(self, soc, name):
        self.soc = soc;
        self.name = name;
        self.children = OrderedDict();

def splitSOC(soc):
    return (soc[:2], soc[3], soc[4:6], soc[6])

def formatName(name):
    return name.replace(',', '').replace(' ', '').lower()

workbook = openpyxl.load_workbook(sys.argv[1], read_only=True)

try:
    worksheet = workbook.get_sheet_by_name("Table 1.7")
except KeyError:
    sys.exit("export_occupation_category_partial: worksheet \"Table 1.7\" not found, please check to make sure you are using the Bureau Labor of Statistics 2014 occupational data")

# Build the tree structure of occupations
rootNode = Node("00-0000", "")

# Read all rows except the header row
rowCount = 0
for row in worksheet.rows:
    rowCount += 1

    # Skip the first four rows
    if rowCount <= 4:
        continue

    # Skip the last few rows
    if row[1].value is None:
        continue

    node = Node(row[1].value.strip(), row[0].value.strip())

    # PRE-CONDITION ASSUMPTION: The occupations are listed in order of
    # ascending SOC code. This means that we can build our occupation tree
    # in a single pass of the file.
    socFields = splitSOC(node.soc)

    # Determine type of occupation
    if socFields[1] == u'0':
        # Major group
        rootNode.children[socFields[0]] = node
    elif (socFields[2] + socFields[3] == u'000') or (node.soc == u'15-1100') or (node.soc == u'51-5100'):
        # Minor group
        # Two of the occupation groups break the minor group convention by not
        # ending with 3 0s
        majorGroup = rootNode.children[socFields[0]]
        majorGroup.children[socFields[1]] = node
    elif socFields[3] == u'0':
        # Broad occupation
        majorGroup = rootNode.children[socFields[0]]
        minorGroup = majorGroup.children[socFields[1]]
        minorGroup.children[socFields[2]] = node
    else:
        # Skip occupations that have an education requirement less than a Bachelor's
        educationRequired = row[10].value.strip()
        if (educationRequired == u'No formal educational credential' or
            educationRequired == u'High school diploma or equivalent' or
            educationRequired == u'Postsecondary nondegree award' or
            educationRequired == u'Associate\'s degree'):
            continue

        # Detailed occupations
        majorGroup = rootNode.children[socFields[0]]
        minorGroup = majorGroup.children[socFields[1]]
        if socFields[2] in minorGroup.children:
            broadOccupation = minorGroup.children[socFields[2]]
        else:
            # The broad occupation isn't defined if there is only one detailed
            # occupation in the group, so rig up a dummy broad occupation
            broadOccupation = Node(socFields[0] + '-' + socFields[1] + socFields[2] + '0', node.name)
            minorGroup.children[socFields[2]] = broadOccupation
        broadOccupation.children[socFields[3]] = node

# Because we skip some types of occupations, we need to go through and prune
# the occupation tree and remove nodes that have no detailed occupations
for (majorCode, majorNode) in rootNode.children.items():
    for (minorCode, minorNode) in majorNode.children.items():
        minorNode.children = OrderedDict([(broadCode, broadNode) for (broadCode, broadNode) in minorNode.children.items() if len(broadNode.children) > 0])

    majorNode.children = OrderedDict([(minorCode, minorNode) for (minorCode, minorNode) in majorNode.children.items() if len(minorNode.children) > 0])

rootNode.children = OrderedDict([(majorCode, majorNode) for (majorCode, majorNode) in rootNode.children.items() if len(majorNode.children) > 0])

# Generate the partial
with open(sys.argv[2], "w") as outfile:
    # Header
    outfile.write('<div class="panel panel-default majorSection">\n')
    outfile.write('<div class="panel heading" role="tab">\n')
    outfile.write('<a class="collapsed accordionHeading" role="button" data-toggle="collapse" data-parent="#browseAccordion" href="#broadCategoryOptions" aria-expanded="false" aria-controls="broadCategoryOptions">By Category</a>\n')
    outfile.write('</div>\n')
    outfile.write('<div id="broadCategoryOptions" class="panel-collapse collapse" role="tab-panel">\n')
    outfile.write('<div class="panel-group" id="majorGroupAccordion" role="tablist" aria-multiselectable="true">\n')

    # Body
    for (majorCode, majorNode) in rootNode.children.items():
        majorId = formatName(majorNode.name)
        outfile.write('<div class="panel panel-default minorSection">\n')
        outfile.write('<div class="panel heading majorHeading" role="tab">\n')
        outfile.write('<a class="collapsed accordionHeading" role="button" data-toggle="collapse" data-parent="#majorGroupAccordion" href="#{0}Options" aria-expanded="false" aria-controls="{0}Options">{1}</a>\n'.format(majorId, majorNode.name))
        outfile.write('</div>\n')
        outfile.write('<div class="panel-collapse collapse" id="{0}Options" role="tablist" aria-multiselectable="true">\n'.format(majorId))
        outfile.write('<div class="panel-group" id="{0}Accordion" role="tablist" aria-multiselectable="true">\n'.format(majorId))

        for (minorCode, minorNode) in majorNode.children.items():
            minorId = formatName(minorNode.name)
            outfile.write('<div class="panel panel-default broadSection">\n')
            outfile.write('<div class="panel heading" role="tab">\n')
            outfile.write('<a class="collapsed accordionHeading" role="button" data-toggle="collapse" data-parent="#{0}Accordion" href="#{1}Options" aria-expanded="false" aria-controls="{1}Options">{2}</a>\n'.format(majorId, minorId, minorNode.name))
            outfile.write('</div>\n')
            outfile.write('<div class="panel-collapse collapse" id="{0}Options" role="tablist" aria-multiselectable="true">\n'.format(minorId))
            outfile.write('<div class="panel-group" id="{0}Accordion" role="tablist" aria-multiselectable="true">\n'.format(minorId))
            
            for (broadCode, broadNode) in minorNode.children.items():
                broadId = formatName(broadNode.name)
                outfile.write('<div class="panel panel-default detailedSection">\n')
                outfile.write('<div class="panel heading broadHeading" role="tab">\n')
                outfile.write('<a class="collapsed accordionHeading" role="button" data-toggle="collapse" data-parent="#{0}Accordion" href="#{1}Options" aria-expanded="false" aria-controls="{1}Options">{2}</a>\n'.format(minorId, broadId, broadNode.name))
                outfile.write('</div>\n')
                outfile.write('<div class="panel-collapse collapse" id="{0}Options" role="tablist" aria-multiselectable="true">\n'.format(broadId))

                for (detailedCode, detailedNode) in broadNode.children.items():
                    outfile.write('<a class="detailedOccupationLink" href="/career/{0}/video">{1}</a><br>\n'.format(detailedNode.soc, detailedNode.name))

                outfile.write('</div>\n')
                outfile.write('</div>\n')

            outfile.write('</div>\n')
            outfile.write('</div>\n')
            outfile.write('</div>\n')

        outfile.write('</div>\n')
        outfile.write('</div>\n')
        outfile.write('</div>\n')

    # Footer
    outfile.write('</div>\n')
    outfile.write('</div>\n')
    outfile.write('</div>\n')
