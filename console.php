<?php

require_once 'vendor/autoload.php';

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class Minimal extends CLI
{
    // register options and arguments
    protected function setup(Options $options)
    {
        $options->setHelp('Smart-Core a modular system for displaying smart widgets on a magic mirror.');
        $options->registerOption('version', 'print version', 'v');
        $options->registerCommand('modules', 'handle modules');
        $options->registerArgument('install', 'installs a module', false, 'modules');
        $options->registerArgument('list', 'list all modules', false, 'modules');
    }

    // implement your code
    protected function main(Options $options)
    {
        $args = $options->getArgs();

        if ($options->getCmd() == 'modules') {
            if ($options->getOpt('install')) {
                $this->success('Module ' . $args[0] . 'wird jetzt installiert');
            } else if ($options->getOpt('list')) {
                $this->info('This is a list of all modules');
            }
        }

        if ($options->getOpt('version')) {
            $this->info('1.0.0');
        }

        //echo $options->help();
    }
}

// execute it
$cli = new Minimal();
$cli->run();

/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////

exit();