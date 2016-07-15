<?php

namespace ForestAdmin\ForestBundle\Command;

use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;


class PostMapCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('forest:postmap')
            ->setDescription('Post the API map to the Forest Server')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new ConsoleLogger($output);

        try {
            $this->getSecretKey();
            $forest = $this->getContainer()->get('forestadmin.forest');

            if($forest->postApimap()) {
                $text = 'Success!';
            } else {
                $logger->error('Could not post API map for an unknown reason.');
                $text = 'Failure!';
            }
        } catch(ClientException $exc) {
            $logger->error('Client Failure: ' . $exc->getMessage());
            $text = 'Cannot contact the client.';
        } catch(RequestException $exc) {
            $logger->critical('Request Failure: ' . $exc->getMessage());
            $text = 'Malformed request.';
        } catch(InvalidArgumentException $exc) {
            $logger->debug('Client tried to contact us without secret key');
            $text = "Failure: ".$exc->getMessage()."\n"
            ."Configure your secret key with the key you received when you registered your app to Forest.";
        } catch(\Exception $exc) {
            $logger->error('Unexpected exception triggered: ' . $exc->getMessage());
            $text = get_class($exc).' Failure: '.$exc->getMessage();
        }

        $output->writeln($text);
    }

    protected function getSecretKey()
    {
        return $this->getContainer()->getParameter('forestadmin.forest.secret_key');
    }
}