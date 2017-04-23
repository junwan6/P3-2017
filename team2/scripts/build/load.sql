/******************************************************************************
  load.sql

Loads in batch-importable files into the database.

Future work needed here: See if we can find a way to not have to hard-code in
the name of the batch-importable files.
******************************************************************************/

LOAD DATA LOCAL INFILE 'occupation.dat' INTO TABLE Occupation
     CHARACTER SET UTF8
     FIELDS TERMINATED BY '\t'
     LINES TERMINATED BY '\n';

LOAD DATA LOCAL INFILE 'stateOccupation.dat' INTO TABLE StateOccupation
     CHARACTER SET UTF8
     FIELDS TERMINATED BY '\t'
     LINES TERMINATED BY '\n';

/*
LOAD DATA LOCAL INFILE 'regionalOccupation.dat' INTO TABLE RegionalOccupation
     CHARACTER SET UTF8
     FIELDS TERMINATED BY '\t'
     LINES TERMINATED BY '\n';
*/

LOAD DATA LOCAL INFILE 'interest.dat' INTO TABLE OccupationInterests
     CHARACTER SET UTF8
     FIELDS TERMINATED BY '\t'
     LINES TERMINATED BY '\n';

LOAD DATA LOCAL INFILE 'skills.dat' INTO TABLE Skills
     CHARACTER SET UTF8
     FIELDS TERMINATED BY '\t'
     LINES TERMINATED BY '\n';

LOAD DATA LOCAL INFILE 'videos.csv' INTO TABLE Videos
  CHARACTER SET UTF8
  FIELDS TERMINATED BY ','
  LINES TERMINATED BY '\r';
