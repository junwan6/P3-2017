# Installing dependencies:
* Python (Database loading) `apt-get install python python-pip`
  * OpenPyXl `pip install openpyxl`
* PHP Modules `intl` and `mb_string` for CakePHP
  * `sudo apt-get install php-intl php-mbstring`
  
# Deploying website:
* Create empty database (Arbitrary name)
  * `mysql --user=<user> --password=<pass>`
  * `CREATE DATABASE <database>;`
* Set database (MySQL) information in `app/config/db_config.json`
* Place excel spreadsheets in configurable data folder (default: data/)
  * `all_data_M_2015.xlsx`  `Interests.xlsx`  `occupation.xlsx`  `skills.xlsx`
* Set owner of CakePHP files to webserver user
  * `sudo chown -R www-data:www-data app/`
* Populate database by executing `database/init_db`
  *  `./init_db [data_dir] [db_config] [preconverted_dir]`
    * All arguments optional
    * `[data_dir]`: Directory containing excel spreadsheets above
    * `[db_config]`: JSON file containing login information
    * `[preconverted_dir]`: Directory containing converted excel data
      * Skips time-consuming excel conversion scripts
    * UNTESTED: execution from outside local directory
    * UNTESTED: nonstandard arguments (globbing, variables, ~, etc)
  * SETTING UP ADMIN USER: The first account to be created will be the admin
    * The admin may access the admin portal and add/remove admin status
* Set Apache documentroot or alias to app/webroot directory
  * Enable modules `ssl` and `rewrite`
    * `sudo a2enmod ssl rewrite`
  * If setting up from scratch, (Fresh instal):
    * Enable ssl site `sudo a2ensite default-ssl.conf`
    * If no legit SSL cert: `sudo make-ssl-cert generate-default-snakeoil --force-overwrite`
    * Set `DocumentRoot <path>` or `Alias "<alias>" "<path>"` 
    * Set up `<Directory "<path>">` tag
    * `Options Indexes FollowSymLinks` `AllowOverride All` `Require all granted` 
  * Restart apache server for changes to take effect
* Set PHP options (`/etc/php/7.0/apache2/php.ini` on Ubuntu Server 16.04):
  * `upload_max_filesize`, `post_max_size`, `file_uploads`, `max_file_uploads`
  * Ensure `www-data` or webserver user has access to `upload_tmp_dir`
  * Restart apache server for changes to take effect
* To set up tests, see README.md of lamp/cakephp/tests
  * Kept separate from main installation due to additional dependencies

# Directory:
* `cakephp`: Current working directory, contains the implementation of the website in CakePHP 3.x
* `www`: Contains 'view' files converted to PHP from NodeJS template format.
  * SEMI-DEPRECATED: Any versions in cakephp are more up-to-date, but not all have been added.
  * Set up for basic viewing through Apache alias at [address]/p3/
  * Contains placeholder values for all database-populated variables
