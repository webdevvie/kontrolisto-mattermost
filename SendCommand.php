<?php

namespace Webdevvie\KontrolistoUtils\Mattermost;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class HelpCommand
 * @package Webdevvie\KontrolistoUtils\Mattermost
 */
class SendCommand extends Command
{
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('send')
            ->setDescription('Sends std-in to mattermost');
        $this->addArgument("channel", InputArgument::OPTIONAL, 'the channel to use', 'default');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \Exception
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = file_get_contents("php://stdin");
        $this->output = $output;
        if ($text=='') {
            $output->writeln("<error>Your input text is empty</error>");
        }
        if (substr($text, 0, 1)=="{") {
            //see if it is json
            $data = json_decode($text, true);
            if (!is_array($data)) {
                $data = ['text'=>$text];
            }
            if (!isset($data['text'])||!is_string($data['text'])||$data['text']=='') {
                $output->writeln("<error>JSON INPUT: Your input text is empty</error>");
                return;
            }
        } else {
            $data = ['text'=>$text];
        }
        $channel = $input->getArgument('channel');
        if ($channel =='') {
            $channel = 'default';
        }
        $channelData = $this->getChannelData($channel);
        if (is_null($channelData)) {
            $output->writeln("<error> Invalid channel $channel</error>");
            return;
        }
        $useData = array_merge($channelData, $data);

        $outData = [
                "text"=>$data['text']
        ];
        if ($useData['picture']!='') {
            $outData['icon_url']= $useData['picture'];
        }
        if ($useData['name']!='') {
            $outData['username']= $useData['name'];
        }
        if (isset($data['channel'])&& $data['channel']!='') {
            $outData['channel']= $useData['channel'];
        }
        $body = json_encode($outData, JSON_PRETTY_PRINT);

        try {
            $client = new Client();
            $client->post($channelData['hook'], ['body'=>$body,'headers'=>['Content-Type'=>"application/json"]]);
        } catch (\Exception $exception) {
            $output->writeln("FAIL");
            if ($input->getOption("verbose")) {
                $output->writeln("<error>".$exception->getMessage()."</error>");
            }
            return;
        }
        $output->writeln("OK");
        return;
    }

    /**
     * @param string $channel
     * @return null|array
     */
    public function getChannelData($channel)
    {
        $pharFile = \Phar::running(true);


        if ($pharFile!='') {
            $file = str_replace('.phar', '.yml', $pharFile);
            $file = str_replace('phar://', '', $file);
        } else {
            $file = __DIR__.'/mattermost.yml';
        }

        if (!file_exists($file)) {
            $this->output->writeln("<error>$file does not exist</error>");
            return null;
        }
        $data = Yaml::parseFile($file);
        if (isset($data['mattermost'][$channel])) {
            return $data['mattermost'][$channel];
        }
        return null;
    }
}
