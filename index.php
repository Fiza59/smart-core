<?php

require __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL);

define('APPLICATION_ROOT', __DIR__);
define('CONFIGS_ROOT', __DIR__ . '/configs');
define('MODULES_ROOT', __DIR__ . '/modules');
define('TEMPLATES_ROOT', __DIR__ . '/templates');
define('CACHE_ROOT', __DIR__ . '/var/cache');
define('RESOURCE_DIR', __DIR__ . '/resources');

$app = new core\Smart();
$app->run();
