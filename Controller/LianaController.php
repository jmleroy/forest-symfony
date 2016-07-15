<?php

namespace ForestAdmin\ForestBundle\Controller;

use ForestAdmin\Liana\Api\ResourceFilter;
use ForestAdmin\Liana\Exception\CollectionNotFoundException;
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
     * @Route("/forest/{modelName}/{recordId}", requirements={"modelName" = "\w+", "recordId" = "\d+"})
     * @Method({"GET"})
     * @param string $modelName
     * @param int $recordId
     * @return JsonResponse|Response
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

        return $this->returnJson($resource);
    }

    /**
     * @Route("/forest/{modelName}", requirements={"modelName" = "\w+"})
     * @Method({"GET"})
     * @param string $modelName
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function listResources($modelName, Request $request)
    {
        try {
            $collections = $this->get('forestadmin.forest')->getCollections();
            $liana = $this->get('forestadmin.liana')->setCollections($collections);
            $filter = new ResourceFilter($request->query->all());
            $resources = $liana->listResources($modelName, $filter);
        } catch (CollectionNotFoundException $exc) {
            return new Response("Collection not found", 404);
        }

        return $this->returnJson($resources);
    }


    /**
     * @Route("/forest/{modelName}/{recordId}/{associationName}", requirements={"modelName" = "\w+", "recordId" = "\d+", "associationName" = "\w+"})
     * @Method({"GET"})
     * @param string $modelName
     * @param int $recordId
     * @param string $associationName
     * @return JsonResponse|Response
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

        return $this->returnJson($resource);
    }

    /**
     * @Route("/forest/{modelName}", requirements={"modelName" = "\w+"})
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
            $contentData = $this->getContentData($request);
            $resource = $liana->createResource($modelName, $contentData);

            return $this->returnJson($resource);
        } catch (\Exception $exc) {
            //if environment = dev
            return new Response($exc->getMessage(), 400);
            //else
            //return new Response('Bad request', 400);
        }
    }

    /**
     * @Route("/forest/{modelName}/{recordId}", requirements={"modelName" = "\w+", "recordId" = "\d+"})
     * @Method({"PUT"})
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
            $contentData = $this->getContentData($request);
            $resource = $liana->updateResource($modelName, $recordId, $contentData);

            return $this->returnJson($resource);
        } catch (\Exception $exc) {
            //if environment = dev
            return new Response('Bad request: ' . $exc->getMessage(), 400);
            //else
            //return new Response('Bad request', 400);
        }
    }

    protected function returnJson($resource)
    {
        $response = new JsonResponse($resource);
        $response->setEncodingOptions(
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT |
            JSON_UNESCAPED_SLASHES |
            JSON_UNESCAPED_UNICODE
        );

        return $response;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    protected function getContentData(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        if (!array_key_exists('data', $content) || !array_key_exists('attributes', $content['data'])) {
            throw new \Exception("Malformed content");
        }

        return $content;
    }
}
