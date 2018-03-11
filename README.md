# Sensony

Sensony is a simple tool with which you can manage sensors. 
Sensony includes a simple REST API that receives updates from sensors and saves them to the database.

## Requirements
To be able to run this project you have to have PHP 5.5.9 installed. 
Other tools are not required. The database used here is a file-based SQLite database.

## Installation
First of all download the project from GitHub and place it into a folder of your choice. 
Locate the configuration script `configure.sh` in the root directory.
Now create the database and install all necessary dependencies by entering the following command:
```
./configure.sh --composer --upgrade
```

After this, a database file is created in `var/` directory.
The installation process creates a dummy user with username `admin@localhost` and password `admin`.

## Usage
Using the tool is straight forward. Start the server either by creating an apache2/nginx configuration or by using the PHP server.

**Warning!** The PHP server is not intended for production use.

```
php -S 127.0.0.1:80 -t web
```

## API
TODO

## Using the mapping
TODO

## Development
During development the database schema can change. 
To update it without losing all the data you can use the configuration script once more!
```
./configure.sh --update
```

`--update` updates the database, wipes caches, creates symlinks and compiles resources (css).
If you want to clean the database and load fixtures, you should use `--upgrade`. 
You can combinate these two modes with `--composer` to get the latest composer version aswell as updated dependencies.

## Built with

* [**Symfony**][1]
* [**EasyAdminBundle**][2] 
* [**Composer**][3]

Enjoy!

[1]: https://github.com/symfony/symfony
[2]: https://github.com/EasyCorp/EasyAdminBundle
[3]: https://github.com/composer/composer
