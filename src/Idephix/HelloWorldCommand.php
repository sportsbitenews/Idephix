<?php


namespace Idephix;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorldCommand extends Command {

    protected function configure() {
        $this
                ->setName('example:Helloworld')
                ->setDescription('greets everyone')
                ->addArgument('who', InputArgument::OPTIONAL, 'the person to greet');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $arg = $input->getArgument("who");
        if ($arg != null)
            $output->writeln("Hello ".$arg."!");
        else
            $output->writeln("Hello World!");
    }

}