<?php

namespace TwigCli\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Compile extends Command
{
    protected function configure()
    {
        $this
            ->setName('twig')
            ->setDescription('Compile Twig Template')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Twig file to process'
            )
            ->addOption(
                'base-dir', null, InputOption::VALUE_REQUIRED, 'Base directory where templates are stored'
            )
            ->addOption(
                'write-output', false, InputOption::VALUE_NONE, 'Do we need to write output to "[file].html"?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $baseDir = realpath($input->getOption('base-dir'));

        if (is_null($baseDir)) {
            return $output->writeln('Please provide "base-dir" option.');
        }

        $loader = new \Twig_Loader_Filesystem($baseDir);
        $twig = new \Twig_Environment($loader, array(
            'cache' => false,
            'debug' => true,
            'autoescape' => false,
        ));

        $rendered = $twig->render($file, array());
        if ($input->getOption('write-output')) {
            $outputFile = dirname($baseDir . '/' . $file) . '/' . basename($file, '.twig') . '.html';
            file_put_contents($outputFile, $rendered);
        } else {
            $output->writeln($rendered);
        }

    }
}
