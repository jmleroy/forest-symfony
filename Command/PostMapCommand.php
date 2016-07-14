<?php

namespace ForestAdmin\ForestBundle\Command;

use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
//            ->addArgument(
//                'env',
//                InputArgument::OPTIONAL,
//                'From which environment does come the map that you want to post?'
//            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            //$this->getSecretKey();
            $forest = $this->getContainer()->get('forestadmin.forest');
            //$name = $input->getArgument('name');

                if($forest->postApimap()) {
                    $text = 'Success!';
                } else {
                    $text = 'Failure!';
                }
        } catch(ClientException $exc) {
            $text = 'Client Failure: ' . $exc->getMessage();
        } catch(RequestException $exc) {
            $text = 'Request Failure: ' . $exc->getMessage();
        } catch(InvalidArgumentException $exc) {
            $text = "Failure: ".$exc->getMessage()."\n"
            ."Configure your secret key with the key you received when you registered your app to Forest.";
        } catch(\Exception $exc) {
            $text = get_class($exc).' Failure: '.$exc->getMessage();
        }

        $output->writeln($text);
    }

    protected function getSecretKey()
    {
        return $this->getContainer()->getParameter('forestadmin.forest.secret_key');
    }
}