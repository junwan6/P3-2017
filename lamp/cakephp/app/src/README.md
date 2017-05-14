# Directory:
* `Controller`: Business logic of application. Called on URL requests, chooses which Templates to display, after executing code. Contains MySQL database access code due to issues with Model classes
* `Template`: Contains pseudo-html files, with substituted values populated by Controllers. Capable of PHP, mostly for control structures (if, for, foreach)
## Unmodified:
* `View`: Contains PHP classes for Views, unsure of role compared to Template
* `Model`: Meant to hold classes holding information of representing Tables in a database, holding information. Not used as each class represents one Table, not feasible with existing database/Model design
* `Console`: Contains filesystem and installation functions
* `Shell`: Implementation of PsyShell, a PHP REPL/debugger/console
* `Application.php`: Controls logic and middleware
