<?php

namespace ForestAdmin\ForestBundle\Controller;

use ForestAdmin\Liana\Exception\NotFoundException;
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


    /**
     * @Route("/{modelName}/{recordId}/{associationName}", requirements={"modelName" = "\w+", "recordId" = "\d+", "associationName" = "\w+"})
     *
     * @param string $modelName
     * @param int $recordId
     * @param string $associationName
     */
    public function getHasMany($modelName, $recordId, $associationName)
    {
        try {
            $collections = $this->get('forestadmin.forest')->getCollections();
            $liana = $this->get('forestadmin.liana')->setCollections($collections);
            $resource = $liana->getHasMany($modelName, $recordId, $associationName);
        } catch (NotFoundException $exc) {
            return new Response($exc->getMessage(), 404);
        }

        //return new JsonResponse($resource);

        //Trace
        return new Response($this->render('ForestBundle:Default:liana.html.twig', array(
            'resource' => $resource,
        )));
    }
}