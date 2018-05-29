# Sensony

Sensony is a simple tool with which you can manage sensor data. Data can be temperature, elevation, humidity or pressure for example.  
Sensony includes a simple REST API that receives updates from sensors and saves them to the database.

## Requirements
To be able to run this project you have to have PHP 5.5.9, Apache2 and Git installed. 
Other tools are not required. The database used here is a file-based SQLite database.

If you are starting with an OS that has nothing of those tools installed, install them with the first command. Then activate the apache rewrite module.
```
sudo apt install php git php7.0-gd php7.0-intl php7.0-xsl php7.0-sqlite3 php7.0-mbstring

sudo a2enmod mod_rewrite
```

After this, follow the virtual host setup from the [Symfony Docs][4]. Make sure to use the **optimized configuration**.

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

or

```
php bin/console server:run 127.0.0.1:8000
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
  "sensordatavalues": [
    {
	  "value_type": "temperature",
	  "value": "21.90"
	}, {
	  "value_type": "humidity",
	  "value": "29.10"
	}, {
	  "value_type": "pressure",
	  "value": "1100.00"
	}
  ]
}
```

TODO: API without basic auth

## Using the mapping
Different sensors are sending their data in their own formats with their own labels. Mappings are used to 
guarantee that the data is being saved in the correct attribute. You can specify a sensor mapping per type.

The mapping offers to translate the following attributes:
        

| **Attributes** |
| -------------- |
| `adc0`         |
| `adc1`         |
| `adc2`         |
| `adc3`         |
| `adc4`         |
| `adc5`         |
| `adc6`         |
| `adc7`         |
| `sdsp1`        |
| `sdsp2`        |
| `latitude`     |
| `longitude`    |
| `elevation`    |
| `temp`         |
| `moist`        |
| `pressure`     |
| `humidity`     |
| `speed`        |
| `date`         |
| `time`         |

You can configure the mapping in the eiditing page of the sensor by adding simple key value pairs.
Simple example:

```
adc0: 'xyz'
adc1: 'xyz'
temp: 'temperature'
...
```

What this mapping does is that the incoming JSON with the key `xyz` gets mapped to the database column with the name `adc0`. 
Same procedure is being used for temperature. Also the value of `xyz` is being filled into the attribute `adc1`. In 
this way you can spread the values to different attributes. If you don't specify a mapping, the tool searches for the default 
attribute keys as stated in the table above. 

## Configuring the sensor
When configuring the API in the sensor make sure that other APIs (i.e. luftdaten.info and madavi.de) are disabled. Then enter the following data when configuring:

```
URL:      <host>/api/v1/data
Port:     <80>/<443> # depends on protocol
Username: <UUID of sensor>
Password: <Token of sensor>
``` 

## Development
During development the database schema can change. 
To update it without losing all the data you can use the configuration script once more!
```
./configure.sh --update
```

`--update` updates the database, wipes caches, creates symlinks and compiles resources (css and js).
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
[4]: https://symfony.com/doc/3.4/setup/web_server_configuration.html
