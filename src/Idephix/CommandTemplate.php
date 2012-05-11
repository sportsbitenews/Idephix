<?php

/**
 * Command class template.
 * Define a new class extending Command
 * using the following template. 
 */

namespace Idephix;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CommandTemplate extends Command {

    /**
     * Configure the command:
     * set the command's name. Syntax "command context:command name";
     * set the command's description;
     * add any number of arguments, InputArgument::REQUIRED
     * if the argument is required or InputArgument::OPTIONAL
     * otherwise;
     * add any number of options, the option's name is the string
     * following the "-" on the console
     */
    protected function configure() {
        $this
                ->setName('example:name of the command')
                ->setDescription('command description')
                ->addArgument('name of argument, required argument', InputArgument::REQUIRED, 'description')
                ->addArgument('name of argument, optional argument', InputArgument::OPTIONAL, 'description')
                ->addOption('name of the option', null, InputOption::VALUE_NONE, 'option description');
    }

    /**
     * The code to be executed.
     * Access arguments and options through $input
     * eg. $input->getArgument('argument name');
     *     $input->getOption('option name');
     * Write to the console through $output
     * eg  $output->writeln("text");
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $arg = $input->getArgument("name of argument, required argument");
        $output->writeln("Command executed with argument ".$arg);
    }

}