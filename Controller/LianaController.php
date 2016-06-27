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
     * @param $modelName
     * @param $recordId
     */
    public function getResource($modelName, $recordId)
    {
        $qs = $this->get('forestadmin.liana', array($this->getDoctrine(), $this->getApimap()));
        
        return new JsonResponse(
            array('ok' => $qs->getResource($modelName, $recordId))
        //"Found model for name '{$modelName}' : {$collection->entityClassName}")
        );

        //return new JsonResponse(array('error' => "There is no model of name '{$modelName}' or with record ID {$recordId}."));
    }
}