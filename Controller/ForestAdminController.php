<?php
/**
 * Created by PhpStorm.
 * User: jean-marc
 * Date: 26/06/16
 * Time: 19:08
 */

namespace ForestAdmin\ForestBundle\Controller;

use Doctrine\ORM\Mapping\ClassMetadata;
use ForestAdmin\Liana\Analyzer\DoctrineAnalyzer;
use ForestAdmin\Liana\Model\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class ForestAdminController extends Controller
{
    protected function getCacheDir()
    {
        return $this->container->getParameter('kernel.cache_dir') . DIRECTORY_SEPARATOR . 'forestadmin' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return Collection[]
     */
    protected function getApimap()
    {
        $apimapFilename = $this->getCacheDir() . 'apimap';
        $fs = new Filesystem;

        if(!$fs->exists($apimapFilename)) {
            $fs->mkdir(dirname($apimapFilename)); // throws IOException if not possible
            $apimap = $this->getApimapFromAnalyzer();
            file_put_contents($apimapFilename, serialize($apimap));

            return $apimap;
        }

        return unserialize(file_get_contents($apimapFilename));
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
        $metaFilename = $this->getCacheDir() . 'metadata';
        $fs = new Filesystem;

        if(!$fs->exists($metaFilename)) {
            $fs->mkdir(dirname($metaFilename));
            $fs->touch($metaFilename);
            $metadata = array();
            $em = $this->getDoctrine()->getEntityManager();

            foreach($em->getMetadataFactory()->getAllMetadata() as $cm) {
                /**
                 * @var ClassMetadata $cm
                 */
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