# Smart Core
"Smart Core" is the basic software for running a magic mirror. Modules can extend the system. Just feel free to create a module or support the project.

## Available modules
- Smart Weather https://github.com/frederikdengler/smart-weather
- Smart Clock https://github.com/frederikdengler/smart-clock

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
php console.php -install smart-weather https://github.com/frederikdengler/smart-weather/archive/master.zip
```