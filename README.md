# Sensony

Sensony is a simple tool with which you can manage sensor data. Data can be temperature, elevation, humidity or pressure for example.  
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
php -S 127.0.0.1:8000 -t web
```

## API
The REST API can be reached at this URL:
```
api/v1/data
```

The API requires a basic authentication before accepting any data. Before you start setting up the sensor to push
data to this url, you have to setup the sensor in Sensony first. Follow these simple steps:
1. Create a `SensorType` that describes the type or group of sensor best
2. Create a `Sensor` and assign a `SensorType` to it. Make sure to add an `UUID`. After clicking on "Save" a 
token will be generated, which you can lookup by clicking "Show" on the sensor overview page.
3. `UUID` and `Token` will be the login credentials for the basic authentication. Enter those credentials in the 
setup process of your sensor.

Example JSON that is accepted by the API:
```
{
	"speed" : 249.0,
	"temp" : 23.3,
	"pressure" : 1011
}
```

TODO: API without basic auth
## Using the mapping
Different sensors are sending their data in their own formats with their own labels. Mappings are used to 
guarantee that the data is being saved in the correct attribute. You can specify a sensor mapping per type.

TODO: Implementation and documentation

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
