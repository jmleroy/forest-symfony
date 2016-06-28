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
        
        return new Response($this->render('ForestBundle:Default:liana.html.twig', array(
            'resource' => $resource,
        )));
    }
}