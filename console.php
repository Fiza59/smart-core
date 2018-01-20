<?php

const P = "\n";
const DP = "\n\n";
define('MODULES_ROOT', __DIR__ . '/modules');

echo P . colorize("Smart cli tools. Smart is a magic mirror software for", "NOTE");
echo P . colorize("everyone. For all available functions use -listfunctions", "NOTE");

echo DP . colorize("YOUR INPUT: " . $argv[0] . " " . $argv[1] . " " . $argv[2] . " " . $argv[3], "NOTE") . DP;
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
    echo "All functions in this script: \n";

    $functions = [
        '-install <modulname> <source>',
    ];

    foreach ($functions as $function) {
        if(!in_array($function, ['colorize'])) {
            echo "\t" . $function . "\n";
        }
    }
}

function install($argv) {
    echo "Install new Module" . DP;

    if(empty($argv[2]) || empty($argv[3])) {
        echo "Please provide <modulename> and <source>!" . P;
        return;
    }

    $moduleName = $argv[2];
    $moduleSource = $argv[3];

    echo "Module name: " . $argv[2] . P;
    echo "Source url: " . $argv[3] . P;

    if(is_dir(MODULES_ROOT . '/' . $moduleName)) {
        echo colorize("DIRECTORY NOT EMPTY IT WILL BE REMOVED NOW!", "NOTE") . DP;
        rrmdir(MODULES_ROOT . '/' . $moduleName);
    }

    downloadZipFile($moduleSource, $moduleName);
    extractModule($moduleName);

    if(is_dir(MODULES_ROOT . '/' . $moduleName . '-master')) {
        rename(MODULES_ROOT . '/' . $moduleName . '-master', MODULES_ROOT . '/' .  $moduleName);
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
            $out = "[32m"; //Green background
            break;
        case "FAILURE":
            $out = "[41m"; //Red background
            break;
        case "WARNING":
            $out = "[43m"; //Yellow background
            break;
        case "NOTE":
            $out = "[44m"; //Blue background
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