<?php

/**
 *  Controller
 */
require_once('Net/SSH2.php');
require_once('Crypt/RSA.php');
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/src/Idephix/CommandWrapper.php';
require_once __DIR__.'/src/Idephix/Idephix.php';
require_once __DIR__.'/src/Idephix/Deploy.php';
require_once __DIR__.'/src/Idephix/SshClient.php';
require_once __DIR__.'/src/Idephix/CLISshProxy.php';
require_once __DIR__.'/src/Idephix/PeclSsh2Proxy.php';
require_once __DIR__.'/src/Idephix/PhpFunctionParser.php';
require_once __DIR__.'/src/Idephix/CommandTemplate.php';


use Idephix\PhpFunctionParser;
use Idephix\Deploy;
use Idephix\SshClient;
use Idephix\CLISshProxy;
use Idephix\Idephix;

$idx = new Idephix();
	
$configFile = getcwd().'/idxfile.php';

if (!is_file($configFile)) {
    echo $configFile." does not exists!\n";

    exit(1);
}

include $configFile;

Idephix::addSshClients($targets, $sshParams);

//$idx->addLibrary(new Deploy($targets, $sshParams));
$idx->run();
