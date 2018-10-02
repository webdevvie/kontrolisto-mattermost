<?php

namespace Webdevvie\KontrolistoUtils\Mattermost;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

/**
 * Class AddChannelCommand
 * @package Webdevvie\KontrolistoUtils\Mattermost
 */
class AddChannelCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('add')
            ->setDescription('creates/updates a new channel');
        $this->addArgument("channel", InputArgument::OPTIONAL, 'the channel to make/update');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \Exception
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $channel = $input->getArgument('channel');
        if ($channel == '') {
            $channel = 'default';
        }
        $helper = $this->getHelper('question');
        $hookUrl = "https://";
        $question = new Question('Please enter the hook url: ', $hookUrl);
        $question->setMaxAttempts(null);
        $hookUrl = $helper->ask($input, $output, $question);
        $name = "";
        $question = new Question('The name to use for messages: ', $name);
        $question->setMaxAttempts(null);
        $name = $helper->ask($input, $output, $question);
        $picture = "";
        $question = new Question('The picture url to use for messages: ', $picture);
        $question->setMaxAttempts(null);
        $picture = $helper->ask($input, $output, $question);
        $this->setChannelData($channel, ['name' => $name, 'hook' => $hookUrl, 'picture' => $picture]);
    }

    /**
     * @param string $channel
     * @param array  $cdata
     * @return void
     */
    public function setChannelData($channel, array $cdata)
    {
        $pharFile = \Phar::running(true);
        if ($pharFile != '') {
            $file = str_replace('.phar', '.yml', $pharFile);
            $file = str_replace('phar://', '', $file);
        } else {
            $file = __DIR__ . '/mattermost.yml';
        }
        if (file_exists($file)) {
            $data = Yaml::parseFile($file);
            if (is_null($data)) {
                $data = ["mattermost" => []];
            }
        } else {
            $data = ["mattermost" => []];
        }
        $data['mattermost'][$channel] = $cdata;
        file_put_contents($file, Yaml::dump($data, 10, 2));
    }
}
