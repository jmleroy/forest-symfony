<?php

namespace ForestAdmin\ForestBundle\Service;

use Doctrine\ORM\Mapping\ClassMetadata;
use ForestAdmin\Liana\Analyzer\DoctrineAnalyzer;
use ForestAdmin\Liana\Api\Map;
use ForestAdmin\Liana\Model\Collection;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;

class ForestService 
{
    /**
     * @var
     */
    protected $container;

    /**
     * @var
     */
    protected $orm;
    
    public function __construct($container, $orm)
    {
        $this->setContainer($container);
        $this->setOrm($orm);
    }

    public function postApimap()
    {
        $map = $this->getApimap();
        $options = array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'forest-secret-key' => $this->getContainer()->getParameter('forestadmin.secretkey'),
            ),
            'body' => $map,
        );
        $client = new Client;
        $request = new Request('POST', $this->getApimapUri(), $options);
        $promise = $client->send($request);
//        Async($request)->then(function ($response) {
//            echo 'I completed! ' . $response->getBody();
//        });
//        $promise->wait();
//        return new JsonResponse($promise);
    }

    public function getApimap()
    {
        $collections = $this->getCollections();
        $map = new Map($collections);
        return new JsonResponse($map->getApimap());
    }

    /**
     * @return Collection[]
     */
    public function getCollections()
    {
        if($this->areInCache('collections')) {
            $collections = $this->getFromCache('collections');
        } else {
            $collections = $this->getCollectionsFromAnalyzer();
            $this->saveInCache('collections', $collections);
        }

        return $collections;
    }

    public function getMetadata()
    {
        if ($this->areInCache('metadata')) {
            $metadata = $this->getFromCache('metadata');
        } else {
            $metadata = $this->getMetadataFromOrm();
            $this->saveInCache('metadata', $metadata);
        }

        return $metadata;
    }

    /**
     * @return Collection[]
     */
    protected function getCollectionsFromAnalyzer()
    {
        //$this->orm can currently only be the Doctrine service
        $em = $this->getOrm()->getEntityManager();
        $analyzer = new DoctrineAnalyzer;
        $analyzer->setEntityManager($em);

        return $analyzer->analyze();
    }

    /**
     * @return ClassMetadata[]
     */
    protected function getMetadataFromOrm()
    {
        //$this->orm can currently only be the Doctrine service
        $em = $this->getOrm()->getEntityManager();
        $metadata = array();

        foreach ($em->getMetadataFactory()->getAllMetadata() as $cm) {
            /**
             * @var ClassMetadata $cm
             */
            $metadata[$cm->getName()] = $cm;
        }

        return $metadata;
    }

    /**
     * @param string $what
     * @return bool
     */
    protected function areInCache($what)
    {
        return file_exists($this->getCacheFilename($what));
    }

    /**
     * @param string $what
     * @param mixed $data
     * @return int
     */
    protected function saveInCache($what, $data)
    {
        $fs = new Filesystem;

        $filename = $this->getCacheFilename($what);
        $fs->mkdir(dirname($filename));
        $fs->touch($filename);

        return file_put_contents($filename, serialize($data));
    }

    /**
     * @param string $what
     * @return string
     */
    protected function getCacheFilename($what)
    {
        return $this->getCacheDir() . $what;
    }

    /**
     * @param string $what
     * @return mixed
     */
    protected function getFromCache($what)
    {
        return unserialize(file_get_contents($this->getCacheFilename($what)));
    }

    protected function getCacheDir()
    {
        return $this->getContainer()->getParameter('kernel.cache_dir') . DIRECTORY_SEPARATOR . 'forestadmin' . DIRECTORY_SEPARATOR;
    }

    protected function getApimapUri()
    {
        return $this->getContainer()->getParameter('forestadmin.apimap_server_uri');
    }

    /**
     * @return mixed
     */
    public function getOrm()
    {
        return $this->orm;
    }

    /**
     * @param mixed $orm
     */
    public function setOrm($orm)
    {
        $this->orm = $orm;
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param mixed $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
}