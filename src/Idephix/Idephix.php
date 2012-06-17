<?php

namespace Idephix;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Idephix\CommandWrapper;

class Idephix {

    private $application;
    private $library = array();
    public static $sshclients = array();
    public static $currentclient;
    public static $env = array();

    public function __construct() {
        $this->application = new Application();
        $this->parseParams();
    }

    private function parseParams() {
        foreach ($GLOBALS["argv"] as $arg) {
            $optname = substr($arg, 0, strpos($arg, "="));
            if ($optname != null) {
                Idephix::$env[substr($optname, strpos($optname, "--") + 2, strlen($optname))] = substr($arg, strpos($arg, "=") + 1, strlen($arg));
                unset($GLOBALS["argv"][array_search($arg, $GLOBALS["argv"])]); //= ltrim(substr($arg, strpos($arg, "=") + 1, strlen($arg)),"-");
                unset($_SERVER["argv"][array_search($arg, $_SERVER["argv"])]); // = ltrim(substr($arg, strpos($arg, "=") + 1, strlen($arg)),"-");
            }
        }
    }

    /**
     * @todo come facciamo i parametri tipo "--go"? Con convention? Tipo se il nome è flag_* allora...
     * @param $name
     * @param Closure $code
     */
    public function add($arg, \Closure $code = null) {
        if ($code !== null) {
//            $basic = new BasicCommand($arg, $code);
//            foreach ($this->env as $var) {
//                $basic->addProperty(array_search($var, $this->env), $var);
//            }
//            $reflector = new \ReflectionClass($basic);
//            try {
//                $name = $reflector->getProperty("name")->getValue($basic);
//            } catch (\ReflectionException $e) {
//                $name = $reflector->getName();
//            }
//            
//            $command = new CommandWrapper($name);
//            $command->setCode(array($basic,'execute'));
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

            $reflector = new \ReflectionClass($arg);
            try {
                $name = $reflector->getProperty("name")->getValue($arg);
            } catch (\ReflectionException $e) {
                $name = $reflector->getName();
            }
            $execute = $reflector->getMethod('execute');
            $command = new CommandWrapper($name);
            $command->setCode(array($arg, 'execute'));

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

    public static function addSshClients($targets, $params) {
        if (array_key_exists('target', Idephix::$env)) {
            if (array_key_exists(Idephix::$env['target'], $targets)) {
                foreach ($targets[Idephix::$env['target']]['hosts'] as $host) {
                    $sshclient = new SshClient();
                    $sshclient->setHost($host);
                    $sshclient->setParams($params);
                    $sshclient->connect();
                    Idephix::$sshclients[] = $sshclient;
                }
            } else {
                throw new \Exception("Invalid target.");
            }
        } else if ($GLOBALS['argc'] > 1) {
            throw new \Exception("You must specify a target");
        }
    }

}

function run($cmd, $dryRun = false) {
    if (!$dryRun) {
        return Idephix::$currentclient->exec($cmd);
    } else {
        echo("Dry run: " . $cmd);
    }
}

function local($cmd) {
    exec($cmd . ' 2>&1', $output, $return);
    if ($return != 0) {
        throw new \Exception("local: returned non-0 value: " . $return . "\n" . $output[0]);
    } else {
        return $output;
    }
}