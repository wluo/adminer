# Adminer - Vitess integration

This fork of Adminer supports [Vitess](http://vitess.io/). Currently it is considered as beta, because some
features are not working yet. Feel free to test and create feature requestsm ideally together with pull requests.

## Usage

1. Clone this repository. 
2. Set up a web server with the document root in the root folder of the cloned repository.
3. Get a precompiled vtctlclient binary and put it somewhere into PATH, to be accessible from anywhere.
4. To run adminer, you have to visit this url:

```
http://HOSTNAME/adminer/index.php
```

To be able to log in into Vitess, you have to pick Vitess in the System input field and then insert the server 
string in the following format:

```
vtgateHost:vtgatePort|vtctldHost:vtctldPort|cell
```

vtgateHost: IP or hostname of one of your VtGate services
vtgatePort: port of one of your VtGate services
vtctldHost: IP or hostname of your VtCtld service
vtctldPort: port of your VtCtld service
cell: Vitess cell name, which you are connecting to

You may, but don't have to input the keyspace name in the Database input field.
Username and password may stay empty, since Vitess currently has no authentication support. Therefore we recommend
to protect your Adminer setup at least with some HTTP Basic authentication on the HTTP server level.

## Known issues

On a sharded Vitess setup, it ist currently not possible to insert or update rows in tables which use Vindexes,
because the Adminer core generates incompatible queries. Therefore, manual query correction has to be made in such 
cases for the time being.

## TODO

Get rid of the vtctlclient binary, if the Vitess team creates Protobuf stubs for the VtCtl client and commands.

# Adminer - Database management in a single PHP file
# Adminer Editor - Data manipulation for end-users

[https://www.adminer.org/](https://www.adminer.org/)
Supports: MySQL, PostgreSQL, SQLite, MS SQL, Oracle, SimpleDB, Elasticsearch
Requirements: PHP 5+
Apache License 2.0 or GPL 2

adminer/index.php - Run development version of Adminer
editor/index.php - Run development version of Adminer Editor
editor/example.php - Example customization
plugins/readme.txt - Plugins for Adminer and Adminer Editor
adminer/plugin.php - Plugin demo
compile.php - Create a single file version
lang.php - Update translations
tests/selenium.html - Selenium test suite
