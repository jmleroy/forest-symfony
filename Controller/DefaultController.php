<?php

namespace ForestAdmin\ForestBundle\Controller;

use Doctrine\Bundle\DoctrineBundle\Registry;
use ForestAdmin\Liana\Analyzer\DoctrineAnalyzer;
use ForestAdmin\Liana\Model\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\DriverManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class DefaultController is the ForestAdmin ORM Analyzing Service
 * @package ForestAdmin\ForestBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @var Collection[]
     */
    protected $apimap;

    /**
     * @var string
     */
    protected $cacheDir;

    public function __construct()
    {
        $this->cacheDir = $this->container->getParameter('kernel.cache_dir') . DIRECTORY_SEPARATOR . 'forestadmin' . DIRECTORY_SEPARATOR;
    }

    /**
     * @Route("/")
     * @param Registry $ormService
     */
    public function indexAction()//(Registry $ormService)
    {
        $apimap = $this->getApimap();

        return $this->formatResponse($apimap);
    }

    /**
     * @return \ForestAdmin\Liana\Model\Collection[]
     */
    protected function getApimap()
    {
        $apimapFilename = $this->cacheDir . 'apimap';
        $fs = new Filesystem;

        if(!$fs->exists($apimapFilename)) {
            $fs->mkdir(dirname($apimapFilename)); // throws IOException if not possible
            $apimap = $this->getApimapFromAnalyzer();
            file_put_contents($apimapFilename, $apimap);

            return $apimap;
        }

        return file_get_contents($apimapFilename);
    }

    /**
     * @return Collection[]
     */
    protected function getApimapFromAnalyzer()
    {
        //ORM Service can currently only be the Doctrine service
        //$em = $ormService->getEntityManager();
        $em = $this->getDoctrine()->getEntityManager();
        $analyzer = new DoctrineAnalyzer();
        $analyzer->setEntityManager($em);

        return $analyzer->analyze();
    }

    protected function getMetadata()
    {
        $metaFilename = $this->cacheDir . 'metadata';
        $fs = new Filesystem;

        if(!$fs->exists($metaFilename)) {
            $fs->mkdir(dirname($metaFilename));
            $fs->touch($metaFilename);
            $metadata = array();
            $em = $this->getDoctrine()->getEntityManager();

            foreach($em->getMetadataFactory()->getAllMetadata() as $cm) {
                $metadata[$cm->getName()] = $cm;
            }

            file_put_contents($metaFilename, serialize($metadata));

            return $metadata;
        }

        return unserialize(file_get_contents($metaFilename));
    }

    /**
     * TODO : really format as JsonApi
     * @param Collection[] $apimap
     * @return JsonResponse
     */
    protected function formatResponse($apimap)
    {
        return new JsonResponse($apimap);
    }
}
