# P3 (Passionate People Project)

## Getting Started

### Dependencies

To run the application, you will need either a machine running either OSX or some flavor of Linux. You will also need to install some dependencies:

* [Node.js](https://nodejs.org/en/)
* [openpyxl](https://openpyxl.readthedocs.org/en/default/index.html)
* [MySQL](http://dev.mysql.com/doc/refman/5.7/en/installing.html)

Furthermore, you will need to install some Node.js packages by running the following command in this directory:

    npm install

### Data Import

You will need to import occupation data into the MySQL database before the application will work correctly. The first step to doing so is to create a database within MySQL and a MySQL administrator accuont for the application. By default, our application is configured to use a database named `p3_test`, managed by the user `p3_admin` who has no password. **Please note that these settings are only appropriate for development. Before deploying the application for public use, please make sure to adapt the instructions below to improve the security settings.**

To create the database, run the command

    mysql

to start the interactive MySQL prompt. In here, you will want to execute the following commands

    CREATE DATABASE p3_test;
    CREATE USER 'p3_admin'@'localhost';
    GRANT ALL PRIVILEGES ON p3_test . * TO 'p3_admin'@'localhost';

After setting this up, you will need to create a directory called `data`, which can be done with the following command

    mkdir data

You will need to place several spreadsheets of data in the `data` directory. The names should match the following exactly:

* `all_data_M_2015.xlsx` - download from [here](http://www.bls.gov/oes/special.requests/oesm15all.zip). This spreadsheet contains data about salaries for jobs in each state.
* `Interests.xlsx` - download from [here](http://www.onetcenter.org/dl_files/database/db_20_3_excel/Interests.xlsx). This spreadsheet contains data about how each job relates to the RIASEC codes.
* `occupation.xlsx` - download from [here](http://www.bls.gov/emp/ind-occ-matrix/occupation.xlsx). This spreadsheet contains data about education requirements and career outlook for each job.
* `skills.xlsx` - a custom-made spreadsheet containing skills for each job and how each skill relates to the 9 intelligences.

Once all of these files have been placed in the `data` directory, run the command

    scripts/init_db

This script may take several minutes to complete. Once it is almost finished, it will prompt you to enter the password for the `p3_admin` user in the MySQL database. If you followed the instruction above exactly, you can simply hit Enter to input no password to continue. If everything has completed successfully, the database is ready to be used.

A note on updating the spreadsheets: the data import process is only known to work with spreadsheets that are up-to-date as of June 3, 2016. Attempting to import data from updated spreadsheets is not guaranteed to work, due to potential changes in the spreadsheet format. In these cases, it will be necessary to modify the application's source code to adapt to the new formats.

TODO: Please add instructions on how loading videos into the database should work.

### Running the Application

You may now run the Node.js application with the command:

    node main.js

If that doesn't work, try:

    nodejs main.js

This will deploy the server *on your machine only*. You can access the website through your browser with the URL [http://localhost:8080](http://localhost:8080). Note that a few features of the website will still not function correctly, and will need a more advanced configuration described in the [Configuration section](#configuration).

## Configuration

There are several files in the `config` folder that allow you to configure various application settings.

* `app-config.json`: This configures where the ser. Currently, it consists of 3 fields: hostname (the domain name for the website), port (the port number that the application listens for requests), and portRequired (whether the port number is required for URLs to be routed correctly, generally should be `true` when using localhost as the hostname, and `false` during production).
* 'db-config.json`: This configures the MySQL database settings. Notably, it sets the username and password for the administrator account. **This should definitely be changed to add more secure credentials before deploying for public use!**
* 'mail-config.json`: This configures the mail server that should be used when sending emails to users. Note that if you are using Gmail as the mail server, you will probably need to relax the security settings on the Gmail account with [this](https://www.google.com/settings/security/lesssecureapps) and [this](https://accounts.google.com/DisplayUnlockCaptcha). Therefore, it's recommended to use some throwaway email account for development.
