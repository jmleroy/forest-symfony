<?php

namespace ForestAdmin\ForestBundle\Controller;

use ForestAdmin\Liana\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class LianaController extends Controller
{
    /**
     * @Route("/{modelName}/{recordId}", requirements={"modelName" = "\w+", "recordId" = "\d+"})
     * @Method({"GET"})
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
     * @Method({"GET"})
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

    /**
     * @Route("/{modelName}", requirements={"modelName" = "\w+"})
     * @Method({"POST"})
     * @param string $modelName
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function createResource($modelName, Request $request)
    {
        try {
            $collections = $this->get('forestadmin.forest')->getCollections();
            $liana = $this->get('forestadmin.liana')->setCollections($collections);
            $postData = $request->request->all();
            $resource = $liana->createResource($modelName, $postData);

            return new JsonResponse($resource);
        } catch(\Exception $exc) {
            //if environment = dev
            return new Response($exc->getMessage(), 400);
            //else
            //return new Response('Bad request', 400);
        }
    }

    /**
     * @Route("/{modelName}/{recordId}", requirements={"modelName" = "\w+", "recordId" = "\d+"})
     * @Method({"POST"})
     * @param string $modelName
     * @param int $recordId
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function updateResource($modelName, $recordId, Request $request)
    {
        try {
            $collections = $this->get('forestadmin.forest')->getCollections();
            $liana = $this->get('forestadmin.liana')->setCollections($collections);
            $postData = $request->request->all();
            $resource = $liana->updateResource($modelName, $recordId, $postData);

            return new JsonResponse($resource);
        } catch(\Exception $exc) {
            //if environment = dev
            return new Response($exc->getMessage());
            //else
            //return new Response('Bad request', 400);
        }
    }
}
