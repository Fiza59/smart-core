<?php

const P = "\n";
const DP = "\n\n";
define('MODULES_ROOT', __DIR__ . '/modules');
define('CONFIGS_ROOT', __DIR__ . '/configs');

echo P . colorize("Smart cli tools. Smart is a magic mirror software for", "NOTE");
echo P . colorize("everyone. For all available functions use -listfunctions", "NOTE") . DP;

if(empty($argv[1])) {
    echo "\n\nNo Command given please look in the list below:\n\n";
    listFunctions();
}else {
    $callFunction = str_replace('-' , '', $argv[1]);
    $callFunction = ucwords($callFunction);
    call_user_func($callFunction, $argv);
    echo DP;
}

function listFunctions() {
    echo "All functions in this script: " . P;

    $functions = [
        '-install <modulname> <version>',
        '-listmodules'
    ];

    foreach ($functions as $function) {
        if(!in_array($function, ['colorize'])) {
            echo "\t" . $function . P;
        }
    }
}

function listModules()
{
    $modules = file_get_contents(CONFIGS_ROOT . '/modules.json');

    foreach (json_decode($modules, true) as $module) {
        echo colorize($module['module'], "NOTE") . P;
        foreach ($module['releases'] as $release) {
            echo 'Release: ' . $release['release'] . DP;
        }
    }
}

function install($argv) {
    echo "Install new Module" . DP;

    if(empty($argv[2]) || empty($argv[3])) {
        echo "Please provide <modulename> and <version>!" . P;
        return;
    }

    $moduleName = $argv[2];
    $moduleSource = 'https://github.com/smartwebtools/' . $moduleName . '/archive/' . $argv[3] . '.zip';

    echo "Module: " . $argv[2] . P;
    echo "Version: " . $argv[3] . P;

    //TODO Test url before removing anything

    if(is_dir(MODULES_ROOT . '/' . $moduleName)) {
        echo colorize("DIRECTORY NOT EMPTY IT WILL BE REMOVED NOW!", "NOTE") . DP;
        rrmdir(MODULES_ROOT . '/' . $moduleName);
    }

    downloadZipFile($moduleSource, $moduleName);
    extractModule($moduleName);

    if(is_dir(MODULES_ROOT . '/' . $moduleName . '-' . $argv[3])) {
        rename(MODULES_ROOT . '/' . $moduleName . '-' . $argv[3], MODULES_ROOT . '/' .  $moduleName);
    }

    echo colorize($moduleName . " was installed successfully!", "SUCCESS") . P;

    unlink(MODULES_ROOT . '/' .  $moduleName . '.zip');
}
function downloadZipFile($url, $module)
{
    $fh = fopen(MODULES_ROOT . '/' . $module . '.zip', 'w');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FILE, $fh);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // this will follow redirects
    curl_exec($ch);
    curl_close($ch);
    fclose($fh);
    echo colorize("Download of module: " . $module . " successful!", "SUCCESS") . P;
}
function extractModule($module)
{
    $zip = new ZipArchive();
    $file = $zip->open(MODULES_ROOT . '/' . $module . '.zip');
    if ($file === TRUE) {
        $zip->extractTo(MODULES_ROOT . '/');
        $zip->close();
        echo colorize("Extract of module: " . $module . " successful!", "SUCCESS") . P;
    }else {
        echo colorize("Extract of module: " . $module . " failed!", "FAILURE") . P;
    }
}

//HELPER FUNCTIONS
function colorize($text, $status) {
    $out = "";
    switch($status) {
        case "SUCCESS":
            $out .= "[32m"; //Green background
            break;
        case "FAILURE":
            $out .= "[41m"; //Red background
            break;
        case "WARNING":
            $out .= "[43m"; //Yellow background
            break;
        case "NOTE":
            $out .= "[94m"; //Blue background
            break;
        default:
            new \Exception("Invalid status: " . $status);
    }
    return chr(27) . "$out" . "$text" . chr(27) . "[0m";
}
function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir."/".$object))
                    rrmdir($dir."/".$object);
                else
                    unlink($dir."/".$object);
            }
        }
        rmdir($dir);
    }
}

exit();