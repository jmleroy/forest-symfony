<?php

namespace ForestAdmin\ForestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $collections = $this->get('forestadmin.forest')->getCollections();
        $liana = $this->get('forestadmin.liana')->setCollections($collections);
        $resource = $liana->getResource($modelName, $recordId);

        /**
         * It seems that it is not possible (with neomerx/json-api at least) to set dynamically the data type
         * TODO : examine the replacement of neomerx/json-api with another library for reasons mentioned above
         */
        $resource = json_decode($resource);
        $resource->data->type = $modelName;
        $resource->data->links->self = str_replace('/plok/', '/'.$modelName.'/', $resource->data->links->self);
        $resource = json_encode($resource);
        
        return new Response($this->render('ForestBundle:Default:liana.html.twig', array(
            'resource' => json_decode($resource),
        )));
    }
}