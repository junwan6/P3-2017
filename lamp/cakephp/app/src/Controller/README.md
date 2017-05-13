# Directory:
* `AppController.php`: Base class of Controllers, moved display() function from PagesController to allow all subclasses to render .ctp files in corresponding Template folder.
* `PagesController.php`: Base class of Controllers for P3 project, sets default layout to 'p3', for global header, navbar, and Passion definition. Handles rendering of static pages (index, donors, browse)
* `CareerController.php`: Handles career pages (video, salary, education, outlook, skills, WoW)
* `UserController.php`: Handles signup, login, password reset, profile pages.
* `AlgorithmController.php`: Handles video rating and recommendation pages/buttons.
* `AdminController.php`: TODO: Admiin portal controller, will handle activity summaries, career information addition/modification/removal.
## Unmodified:
* `Component/`: Empty, unknown purpose.
* `ErrorController.php`: Handles rendering of error pages by errorcode, ex. error400.ctp
