#!/bin/bash
# kqwait detects saves on $1 and $2, 
# calls op.sh to open $1 in a terminal
while ./kqwait $1 $2; do
	echo "Save Detected" 
	open $1
done
