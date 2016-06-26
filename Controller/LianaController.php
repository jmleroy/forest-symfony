<?php

namespace ForestAdmin\ForestBundle\Controller;

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
        
        return new JsonResponse(array('error' => "There is no model of name '{$modelName}' or with record ID {$recordId}."));
    }
}