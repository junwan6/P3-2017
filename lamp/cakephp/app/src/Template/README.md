# Directory:
* `Algorithm\`, `Career\`, `Error\`, `Pages\`, `User\`: Folders containing pseudo-html files corresponding their respective Controllers.
* `Layout\`: Higher-level template for other pages to be rendered inside. Currently used for 'p3.ctp', which contains global header and navbar.
  * Needs cleanup, currently nests `<body>` tags from layout, elements, and each page
* `Element\`: Repeated elements or elements independent enough to be in own file. Navbar included, due to possible decoupling from global layout.

# Unmodified:
* `Email\`: Unknown, may be used for password-reset email in future.
