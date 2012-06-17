<?php

namespace Idephix;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class CommandWrapper extends Command {

    public function __construct($name, $hosts = null, $dry_run = null) {
        parent::__construct($name);
    }

    public function setCode($code) {

        parent::setCode(function (ArgvInput $input, ConsoleOutput $output) use ($code) {
                    $args = $input->getArguments();
                    array_shift($args);
                    foreach (Idephix::$sshclients as $client) {
                        Idephix::$currentclient = $client;
                        call_user_func_array($code, $args);
                    }
                });
        return $this;
    }

}
