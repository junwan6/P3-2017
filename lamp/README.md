# Configuring CakePHP 3.x:
* Install via Composer or zip file
* Install PHP intl and mbstring modules
* Enable mod\_rewrite in Apache server
* Set config/bootstrap.php database login information
  
# Deploying website:
* Create empty database (Arbitrary name)
* Set database (MySQL) information in `app/config/db_config.json`
* Place excel spreadsheets in configurable data folder (default: data/)
  * `all_data_M_2015.xlsx`  `Interests.xlsx`  `occupation.xlsx`  `skills.xlsx`
* Populate database by executing `database/init_db`
  *  `./init_db [data_dir] [db_config] [preconverted_dir]`
    * All arguments optional
    * `[data_dir]`: Directory containing excel spreadsheets above
    * `[db_config]`: JSON file containing login information
    * `[preconverted_dir]`: Directory containing converted excel data
      * Skips time-consuming excel conversion scripts
    * UNTESTED: execution from outside local directory
    * UNTESTED: nonstandard arguments (globbing, variables, ~, etc)
* Set Apache documentroot or alias to app/webroot directory