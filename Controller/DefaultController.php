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
        $options = array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'forest-secret-key' => $this->container->getParameter('forestadmin.secretkey'),
            ),
            'body' => $map,
        );
        $client = new Client;
        $request = new Request('POST', $this->getApimapUri(), $options);
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
