<?php

namespace ForestAdmin\ForestBundle\Controller;

use ForestAdmin\Liana\Api\Map;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class ForestController
 * @package ForestAdmin\ForestBundle\Controller
 */
class ForestController extends Controller
{
    /**
     * This route is called by ForestAdmin to test if ForestBundle is properly installed
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
        $forest = $this->get('forestadmin.forest');
        $forest->postApimap();
        
        return new Response('posted');
    }

    /**
     * TODO to delete or use in dev only
     * @Route("/trace")
     * @return JsonResponse
     */
    public function traceAction()
    {
        $forest = $this->get('forestadmin.forest');
        $apimap = $forest->getApimap();

        return new Response($apimap);
    }

    /**
     * TODO to delete or use in dev only
     * @Route("/dump")
     * @return Response
     */
    public function dumpAction()
    {
        $forest = $this->get('forestadmin.forest');
        $apimap = $forest->getApimap();
        $metadata = $forest->getMetadata();
        foreach($metadata as $old_key => $cm) {
            $new_key = str_replace('\\', '_', $cm->getName());
            $metadata[$new_key] = $cm;
            unset($metadata[$old_key]);
        }

        return new Response($this->render('ForestBundle:Default:index.html.twig', array(
            'em' => $em,
            'apimap' => $apimap,
            'metadata' => $metadata,
        )));
    }
}
