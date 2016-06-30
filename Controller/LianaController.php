<?php

namespace ForestAdmin\ForestBundle\Controller;

use ForestAdmin\Liana\Exception\CollectionNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LianaController extends Controller
{
    /**
     * @Route("/{modelName}/{recordId}", requirements={"modelName" = "\w+", "recordId" = "\d+"})
     *
     * @param string $modelName
     * @param int $recordId
     */
    public function getResource($modelName, $recordId)
    {
        try {
            $collections = $this->get('forestadmin.forest')->getCollections();
            $liana = $this->get('forestadmin.liana')->setCollections($collections);
            $resource = $liana->getResource($modelName, $recordId);
        } catch (CollectionNotFoundException $exc) {
            return new Response("Collection not found", 404);
        }
        
        return new JsonResponse($resource);
        
        //Trace
        return new Response($this->render('ForestBundle:Default:liana.html.twig', array(
            'resource' => $resource,
        )));
    }
}