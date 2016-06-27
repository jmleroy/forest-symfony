<?php

namespace ForestAdmin\ForestBundle\Controller;

use ForestAdmin\Liana\Api\QueryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class LianaController extends ForestAdminController
{
    /**
     * @Route("/{modelName}/{recordId}", requirements={"modelName" = "\w+", "recordId" = "\d+"})
     * 
     * @param $modelName
     * @param $recordId
     */
    public function getResource($modelName, $recordId)
    {
        $qs = $this->get('forest.query', $this->getApimap());
        
        return new JsonResponse(
            array('ok' => $qs->getResource($modelName, $recordId))
        //"Found model for name '{$modelName}' : {$collection->entityClassName}")
        );

        return new JsonResponse(array('error' => "There is no model of name '{$modelName}' or with record ID {$recordId}."));
    }
}