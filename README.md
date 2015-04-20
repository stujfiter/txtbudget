# txtbudget

Summary
======
A web based checking account ledger and budgeting tool.
Developed in 2009 as a way for me to learn basic web app development.

Tech Stack
==========
Runs on an apache web server with php preprocessor installed.  
Uses mysql for datatabase.  The schema is in txtbudget.schema.

Security
========
inc/databaseConnect.inc contains the database password.  
Change it to your default database password.

Add Account
===========
The only way to add users and accounts is to do so manually through sql statements in mysql.
