<?php

namespace Webdevvie\KontrolistoUtils\Mattermost;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReadmeCommand
 * @package Webdevvie\KontrolistoUtils\Mattermost
 */
class ReadmeCommand extends Command
{
    /**
     * @return  void
     */
    protected function configure()
    {
        $this
            ->setName('readme')
            ->setDescription('displays the README.MD file');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $content = file_get_contents(__DIR__ . '/README.md');
        $output->writeln("<info>" . $content . "</info>");
    }
}
