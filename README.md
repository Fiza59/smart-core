# Smart Core
"Smart Core" is the basic software for running a magic mirror. Modules can extend the system. Just feel free to create a module or support the project.
## Release Notes
### Release 1.0.0
- Basic application
- simple add modules for more features on magic mirror display
### Release 1.0.1
- changes for console.php
- directory structure
- documentation for anything

## Installation on Raspberry PI
### 1. Clone the repository
Go to your webserver folder e.g. "/var/www/html"
``` 
git clone https://github.com/smartwebtools/smart-core.git
```
### 1. Download the zip
You also can download the zip by this link:
https://github.com/smartwebtools/smart-core/archive/master.zip

You have to extract it into your webserver folders.
### 2. Run the System
Make sure your apache or nginx server is running and the folder permissions are right, see here:
...
### 3. Edit the config files
The first config file you should edit is the "configs/dashboard.json". There you find some specific settings for adjusting your mirror by define for example a offset of your display.

The next file you should be interested to is the "configs/system.json". There you can set the "mode" to "DEVELOPMENT" or "PRODUCTIVE" and you can edit the system sleep mode.
### 4. Install your first module
To see something in your mirror you have to install a module. For example the "smart-clock". This is a step by step guide to do this.
1. Open your Terminal and go to your webserver directory where the smart core files are located and enter this command 
```php console.php -install smart-clock https://github.com/smartwebtools/smart-clock/archive/master.zip```
This Command will download and install the module for you.
2. Add the module to the grid of your mirror by edit this fie "configs/grid.json"
    ``` json
    {
      "rows": [
        [{
          "size": 1,
          "module": "smart-clock"
        },
        .....
        ]
      ]
    }
    ```
3. Now just reload your website and the module e.g. the "smart-clock" should be visible.
## Available modules
- Smart Weather https://github.com/smartwebtools/smart-weather
- Smart Clock https://github.com/smartwebtools/smart-clock

## Create a module
Yes you can create a module for your and other smart mirrors! All you have to do is just follow a few rules and publish your module so everyone can install and use it.

## CLI Commands
In order to install for example a new module you have to use the console script file.
To do this simply open your server console and insert "php console.php -yourCommand". 
- To see a list of available commands just insert nothing or the command ``` -listfunctions ```

To install a new module use this:
```
php console.php -install <modulename> <source>
#smart-weather example
php console.php -install smart-weather https://github.com/smartwebtools/smart-weather/archive/master.zip
```