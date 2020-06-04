# Adagency LLC. / Test

Simple Query Builder for MySQL

## Require

* PHP 7.3+
* MySQL 5.7+

## Install

* clone git repository

* run composer (for install composer https://getcomposer.org/doc/00-intro.md) 

` > composer install `

* run once initialization the app

` > php init `

* config your local settings in _./config/main-local.php_ for DB connection, for example

```
<?php
return [
    'db' => [
       'user'   => 'root',
       'pass'   => 'secret',
       'dbname' => 'adagency_test',
    ],
];
```

* run migration for prepare test DB tables and data

` > php app migrate `

## Usage

* list of available predefined actions can be found via command

` > php app mysql `

* for run tests

` > ./test ` (for Linux/Unix)

` > test.bat ` (for Win)