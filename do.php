<?php

/**
 *  Controller
 */
require_once('Net/SFTP.php');
require_once('Net/SSH2.php');
require_once('Crypt/RSA.php');
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Idephix/CommandWrapper.php';
require_once __DIR__ . '/src/Idephix/Idephix.php';
require_once __DIR__ . '/src/Idephix/Deploy.php';
require_once __DIR__ . '/src/Idephix/SshClient.php';
require_once __DIR__ . '/src/Idephix/CommandTemplate.php';
require_once __DIR__ . '/src/Idephix/BasicOperations.php';

use Idephix\PhpFunctionParser;
use Idephix\Deploy;
use Idephix\SshClient;
use Idephix\CLISshProxy;
use Idephix\Idephix;

$env = array();

/*
 * parses any argument option in the form of
 * --key=value
 * and adds it to an environment
 * current only relevant option:
 * --target=nameoftarget
 * where nameoftarget is specified in idxfile.php
 */
foreach ($GLOBALS["argv"] as $arg) {
    $optname = substr($arg, 0, strpos($arg, "="));
    if ($optname != null) {
        $env[substr($optname, strpos($optname, "--") + 2, strlen($optname))] = substr($arg, strpos($arg, "=") + 1, strlen($arg));
        unset($GLOBALS["argv"][array_search($arg, $GLOBALS["argv"])]);
        unset($_SERVER["argv"][array_search($arg, $_SERVER["argv"])]);
    }
}

$configFile = getcwd() . '/idxfile.php';

if (!is_file($configFile)) {
    echo $configFile . " does not exists!\n";

    exit(1);
}

include $configFile;

/**
 * For each host in the target host group creates a process
 * and executes the chosen command. Processes creation is
 * sequential, so that only one host is processed at any time.
 */

if (array_key_exists('target', $env)) {
    if (array_key_exists($env['target'], $targets)) {
        foreach ($targets[$env['target']]['hosts'] as $host) {
            $processID = pcntl_fork();
            if ($processID) {
                $status;
                pcntl_waitpid($processID, &$status, WUNTRACED);
                if(!pcntl_wifexited($status))
                    echo "Command execution error - abort";
                    exit(1);
            } else {
                $idx = new Idephix($host, $sshParams);
                for ($i = 0; $i < count($commands); $i++) {
                    if (count($commands[$i]) > 1) {
                        $idx->add($commands[$i][0], $commands[$i][1]);
                    } else
                        $idx->add($commands[$i][0]);
                }
                $idx->run();
            }
        }
    } else {
        throw new \Exception("Invalid target.");
    }
} else if ($GLOBALS['argc'] > 1) {
    throw new \Exception("You must specify a target");
} else {
    /**
     * No arguments specified: display the list of available commands.
     */
    $idx = new Idephix("localhost", $sshParams);
    for ($i = 0; $i < count($commands); $i++) {
        if (count($commands[$i]) > 1) {
            $idx->add($commands[$i][0], $commands[$i][1]);
        } else
            $idx->add($commands[$i][0]);
    }
    $idx->run();
}
