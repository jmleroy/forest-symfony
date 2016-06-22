<?php

namespace ForestAdmin\ForestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\DriverManager;

class DefaultController extends Controller
{
    public function indexAction()
    {
        //$connection = $this->getDoctrine()->getConnection();

        $params = array('url' => 'mysql://root:secret@localhost:33060/drapo');
        $connection = DriverManager::getConnection($params);
        $em = $this->getDoctrine()->getEntityManager();

//        $database_name = 'database_name';
//
//        $metadata = array();
//        foreach($em->getMetadataFactory()->getAllMetadata() as $cm) {
//            $metadata[str_replace('\\', '_', $cm->getName())] = $cm;
//        }

//        $metaFilename = '/var/www/projects/liana/tests/drapo-metadata';
//        $fs = new \Symfony\Component\Filesystem\Filesystem();
//        $fs->touch($metaFilename);
//        file_put_contents($metaFilename, serialize($metadata));


        return new Response("index forestbundle");
    }
}
