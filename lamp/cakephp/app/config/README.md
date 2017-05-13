# Directory:
* `app.php`: Configuration file, contains application settings, etc. Originally one single file, added database config entry `json` for loading from external file.
* `db_config.json`: External file for database settings. Overwrites a set of default values contained in `app.php`. `_comments` entry contains disabled entries, some available fields.
* `routes.php`: Sets routing information for URL accesses. Connects a Controller to a URL or URL pattern.
* `schema/`: Unmodified from base application. MySQL scripts for creation of default CakePHP modules
* `bootstrap.php`: Unmodified from base application. Startup script, sets up environment and handles MVC
* `paths.php`: Unmodified from base application. Sets folder path variables
* `app.default.php`: Unmodified from base application. Default values for application.
* `bootstrap_cli.php`: Unmodified from base application. Aditional startup for command line server startup.
