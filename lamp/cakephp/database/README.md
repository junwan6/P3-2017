# Directory:
* `init_db`: Main script to populate the database with data
  * `./init_db` `[data_dir]` `[db_config]` `[preconverted_dir]`
  * All arguments optional
    * `[data_dir]`: Directory containing excel spreadsheets above
    * `[db_config]`: JSON file containing login information
    * `[preconverted_dir]`: Directory containing converted excel data
      * Skips time-consuming excel conversion scripts
  * UNTESTED: execution from outside local directory
  * UNTESTED: nonstandard arguments (globbing, variables, ~, etc)
* `export_database.py`, `export_interests_database.py`, `export_skills_database.py`, `export_state_database.py`: Scripts called by `init_db` to convert .xlsx to MySQL-importable files.
* `export_occupation_category_partial.py`, `export_regional_database.py`: Not called by `init_db`, may be called by other scripts
* `get_db_config.php`: Reads a JSON `db_config.json` file for MySQL connection information.
* `create.sql`: MySQL script to create database tables. WARNING: DROPS EXISTING TABLES
* `load.sql`: MySQL script to load files created by .py scripts into the MySQL database
* `test_db_config.json`: Connection data for test database
* `videos.csv`: Initial data for videos
* `build-zDSPr/`: Temporary build directory, containing Excel spreadsheets converted to MySQL .dat files
