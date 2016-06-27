<?php

namespace ForestAdmin\ForestBundle\Controller;

use ForestAdmin\Liana\Api\Map;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class DefaultController is the ForestAdmin ORM Analyzing Service
 * @package ForestAdmin\ForestBundle\Controller
 */
class DefaultController extends ForestAdminController
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $response = new Response;
        $response->setStatusCode(204);

        return $response;
    }

    /**
     * TODO to delete or use in dev only
     * @Route("/post")
     */
    public function postAction()
    {
        $apimap = $this->getApimap();
        $map = new Map($apimap);
        $url = "https://forestadmin-server.herokuapp.com/forest/apimaps";
        $options = array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'forest-secret-key' => '0f4d2dca79f091173c009d3d1e365f3fe5ca465e26e960de6f539590cf6c1279',
            ),
            'body' => $map,
        );
        $client = new Client;
        $request = new Request('POST', $url, $options);
        $promise = $client->send($request);
//        Async($request)->then(function ($response) {
//            echo 'I completed! ' . $response->getBody();
//        });
//        $promise->wait();
//        return new JsonResponse($promise);
    }

    /**
     * TODO to delete or use in dev only
     * @Route("/trace")
     * @return JsonResponse
     */
    public function traceAction()
    {
        $apimap = $this->getApimap();

        $map = new Map($apimap);

        return new Response($map->getApimap());
    }

    /**
     * TODO to delete or use in dev only
     * @Route("/dump")
     * @return Response
     */
    public function dumpAction()
    {
        $apimap = $this->getApimap();
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
    }
}
