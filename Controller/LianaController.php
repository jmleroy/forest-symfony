<?php

namespace ForestAdmin\ForestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        return new JsonResponse(
            array('ok' => $liana->getResource($modelName, $recordId))
        );
    }
}