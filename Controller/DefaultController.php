<?php

namespace ForestAdmin\ForestBundle\Controller;

//use Doctrine\Bundle\DoctrineBundle\Registry;
use ForestAdmin\Liana\Analyzer\DoctrineAnalyzer;
use ForestAdmin\Liana\Model\Collection;
use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\DriverManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class DefaultController is the ForestAdmin ORM Analyzing Service
 * @package ForestAdmin\ForestBundle\Controller
 */
class DefaultController extends ForestAdminController
{
    /**
     * @Route("/")
     * @ param Registry $ormService
     */
    public function indexAction()//(Registry $ormService)
    {
        $apimap = $this->getApimap();

        $jsonResponse = $this->formatResponse($apimap);
        $url = "https://forestadmin-server.herokuapp.com/forest/apimaps";
        $options = array(
            'headers' => array(
                'forest-secret-key' => '0f4d2dca79f091173c009d3d1e365f3fe5ca465e26e960de6f539590cf6c1279',
            ),
            'body' => $jsonResponse,
        );
        $client = new Client;
        $request = new \GuzzleHttp\Psr7\Request('POST', $url, $options);
        $promise = $client->send($request);/*Async($request)->then(function ($response) {
            echo 'I completed! ' . $response->getBody();
        });
        $promise->wait();*/
        return new JsonResponse($promise);


        $em = $this->getDoctrine()->getEntityManager();

        $metadata = array();
        foreach($em->getMetadataFactory()->getAllMetadata() as $cm) {
            $metadata[str_replace('\\', '_', $cm->getName())] = $cm;
//            $metadata[$cm->getName()] = $cm;
        }

        return new Response($this->render('ForestBundle:Default:index.html.twig', array(
            'em' => $em,
            'apimap' => $apimap,
            'metadata' => $metadata,
        )));

        //return $this->formatResponse($apimap);
    }
}
