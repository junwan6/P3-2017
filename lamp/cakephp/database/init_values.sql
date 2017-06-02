/******************************************************************************
  init_admin.sql

This script adds initial fields to the database.
Currently only initialization is setting of first registered user as admin

TODO: Register first user in script (password, salt, etc.)
******************************************************************************/
INSERT INTO AdminUsers VALUES (1);
