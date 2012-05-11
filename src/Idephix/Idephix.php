<?php

namespace Idephix;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Idephix\CommandWrapper;

class Idephix {

    private $application;
    private $library = array();

    public function __construct() {
        $this->application = new Application();
    }

    /**
     * @todo come facciamo i parametri tipo "--go"? Con convention? Tipo se il nome è flag_* allora...
     * @param $name
     * @param Closure $code
     */
    public function add($arg, \Closure $code = null) {
        if ($code != null) {
            $command = new CommandWrapper($arg);
            $command->setCode($code);

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
            $this->application->add($arg);
        }
        return $this;
    }

    public function run() {
        $this->application->run();
    }

    public function addLibrary($library) {
        $this->library[] = $library;
    }

    public function __call($name, $arguments) {
        foreach ($this->library as $library) {
            if (is_callable(array($library, $name))) {
                call_user_func_array(array($library, $name), $arguments);
                break;
            }
        }
    }

}
