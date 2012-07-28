<?php

namespace Idephix;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Idephix\CommandWrapper;

/**
 * Encapsulates the logic behind the command execution.
 * Represents the execution of a command on a single host. 
 */

class Idephix {

    private $application;
    private $target;

    public function __construct($host, $sshParams) {
        $this->application = new Application();
        $this->target = new SshClient($host, $sshParams);
    }

    public function add($arg, \Closure $code = null) {
        if ($code !== null) {
            $command = new CommandWrapper($arg);
            $command->setCode($code, $this->target);

            $reflector = new \ReflectionFunction($code);

            if (preg_match('/\s*\*\s*@[Dd]escription(.*)/', $reflector->getDocComment(), $matches)) {
                $command->setDescription(trim($matches[1], '*/ '));
            }
            foreach ($reflector->getParameters() as $parameter) {
                if ($parameter->isOptional()) {
                    $command->addArgument($parameter->getName(), InputArgument::OPTIONAL, '', $parameter->getDefaultValue());
                } else {
                    $command->addArgument($parameter->getName(), InputArgument::REQUIRED);
                }
            }
            $this->application->add($command);
        } else {

            $reflector = new \ReflectionClass($arg);
            try {
                if ($reflector->getProperty("name")->getValue($arg) !== null) {
                    $name = $reflector->getProperty("name")->getValue($arg);
                } else {
                    $name = $reflector->getName();
                }
            } catch (\ReflectionException $e) {
                $name = $reflector->getName();
            }
            $execute = $reflector->getMethod('execute');
            $command = new CommandWrapper($name);
            $command->setCode(array($arg, 'execute'), $this->target);

            if (preg_match('/\s*\*\s*@[Dd]escription(.*)/', $execute->getDocComment(), $matches)) {
                $command->setDescription(trim($matches[1], '*/ '));
            }
            foreach ($execute->getParameters() as $parameter) {
                if ($parameter->isOptional()) {
                    $command->addArgument($parameter->getName(), InputArgument::OPTIONAL, '', $parameter->getDefaultValue());
                } else {
                    $command->addArgument($parameter->getName(), InputArgument::REQUIRED);
                }
            }
            $this->application->add($command);
        }
        return $this;
    }

    public function run() {
        $this->application->run();
    }

}