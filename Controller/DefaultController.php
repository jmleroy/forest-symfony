<?php

namespace ForestAdmin\ForestBundle\Controller;

//use Doctrine\Bundle\DoctrineBundle\Registry;
use ForestAdmin\Liana\Analyzer\DoctrineAnalyzer;
use ForestAdmin\Liana\Model\Collection;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\DriverManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class DefaultController is the ForestAdmin ORM Analyzing Service
 * @package ForestAdmin\ForestBundle\Controller
 */
class DefaultController extends ForestAdminController
{
    /**
     * @Route("/")
     * @ param Registry $ormService
     */
    public function indexAction()//(Registry $ormService)
    {
        $apimap = $this->getApimap();

        $em = $this->getDoctrine()->getEntityManager();

        $metadata = array();
        foreach($em->getMetadataFactory()->getAllMetadata() as $cm) {
            $metadata[str_replace('\\', '_', $cm->getName())] = $cm;
//            $metadata[$cm->getName()] = $cm;
        }

        return new Response($this->render('ForestBundle:Default:index.html.twig', array(
            'em' => $em,
            'apimap' => $apimap,
            'metadata' => $metadata,
        )));

        //return $this->formatResponse($apimap);
    }
}
