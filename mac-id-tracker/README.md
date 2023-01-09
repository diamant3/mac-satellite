# mac-id-tracker

This is a web-based ID cards tracking system, and **maybe** they will use it in the Makati Action Center for sorting and managing IDs.

## Features

ID Cards: Search, Add, Archive, Update

Employees: Add, Remove, Update

Profile: Edit details and Add/Change profile picture

Account Role: Admin and Employee

## Run
- webpages:
  - Download and install [XAMPP](https://www.apachefriends.org/download.html)
  - Start Apache and MySQL
  - Delete all files in `C:\xampp\htdocs` and copy the `mac-id-tracker` folder then paste inside to `C:\xampp\htdocs`.
  - rename first to `mac-id-tracker` to look something like this(*localhost/mac-id-tracker*) in your browser's search bar.

- web server & database:
  - Start Apache and MySQL.
  - Open *localhost/phpmyadmin* in your browser.
  - Import all `.sql` files from `exported_db` to your phpmyadmin's import tab to import.

### Login Creds

**Administrator**

- username: *admin*
- password: *root*

**Employee**

- username: *employee*
- password: *root*

## Credit

[Dashboard](https://github.com/startbootstrap/startbootstrap-sb-admin-2)
