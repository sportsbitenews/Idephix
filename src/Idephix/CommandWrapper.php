<?php

namespace Idephix;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Idephix\BasicOperations;

/**
 * Wraps a Command object, adding the ability
 * to set the code to any Closure. Also
 * adds the $target BasicOperations argument
 * to every command.
 */

class CommandWrapper extends Command {

    public function __construct($name) {
        parent::__construct($name);
        
    }

    public function setCode($code, $tar) {
        $target = new BasicOperations($tar);
        parent::setCode(function (ArgvInput $input, ConsoleOutput $output) use ($code, $target) {
                    $args = $input->getArguments();
                    array_shift($args);
                    $args['target'] = $target;
                    call_user_func_array($code, $args);
                });
        return $this;
    }

}
