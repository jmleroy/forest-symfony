<?php

namespace ForestAdmin\ForestBundle\Service;

use ForestAdmin\Liana\Adapter\DoctrineAdapter;
use ForestAdmin\Liana\Model\Collection;

class LianaService
{
    /**
     * At this moment, only Doctrine service
     * @var
     */
    protected $orm;

    /**
     * @var Collection[]
     */
    protected $collections;

    /**
     * LianaService constructor.
     * @param $orm
     */
    public function __construct($orm)
    {
        $this->setOrm($orm);
        $this->setCollections(array());
    }

    /**
     * Find a resource by its name and identifier
     *
     * @param string $modelName
     * @param mixed $recordId
     * @return array
     */
    public function getResource($modelName, $recordId)
    {
        $entityName = $this->findEntityInCollections($modelName);

        $queryAdapter = $this->getQueryAdapter($entityName);
        if($queryAdapter) {
            $resource = $queryAdapter->getResource($recordId);
            return $resource;
            //return $this->formatJsonApi($modelName, $resource);
        }
        /** TODO else throw CannotCreateRepositoryException */

        return null;
    }

    /**
     * Find all resources by its name and filter
     * @param string $modelName
     * @param ResourceFilter $filter
     * @return array
     */
    public function getResources($modelName, $filter)
    {

    }

    /**
     * @param string $modelName
     * @param mixed $recordId
     * @param string $associationName
     * @return array The hasMany resources with one relationships and a link to their many relationships
     */
    public function getResourceAndRelationships($modelName, $recordId, $associationName)
    {

    }

    /**
     * @param string $modelName
     * @param array $postData
     * @return array The created resource
     */
    public function createResource($modelName, $postData)
    {

    }

    /**
     * @param string $modelName
     * @param mixed $recordId
     * @param array $postData
     * @return array The updated resource
     */
    public function updateResource($modelName, $recordId, $postData)
    {

    }

    /**
     * @return Collection[]
     */
    public function getCollections()
    {
        return $this->collections;
    }

    /**
     * @param Collection[] $collections
     * @return $this
     */
    public function setCollections($collections)
    {
        $this->collections = $collections;

        return $this;
    }

    /**
     * @param mixed $orm
     * @return $this
     */
    public function setOrm($orm)
    {
        $this->orm = $orm;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrm()
    {
        return $this->orm;
    }

    /**
     * @param $entityName
     * @return null|string
     */
    protected function findEntityInCollections($entityName)
    {
        foreach($this->getCollections() as $collection) {
            if($collection->name == $entityName) {
                return $collection->entityClassName;
            }
        }

        return null;
    }

    protected function formatJsonApi($modelName, $resource)
    {
        return $resource;
    }

    /**
     * @return mixed
     */
    protected function isOrmDoctrine()
    {
        return get_class($this->getOrm()) == "Doctrine\\Bundle\\DoctrineBundle\\Registry";
    }

    /**
     * @param $entityName
     * @return DoctrineProxy|null
     */
    protected function getQueryAdapter($entityName)
    {
        $adapter = null;

        if ($this->isOrmDoctrine()) {
            $adapter = new DoctrineAdapter($this->getOrm()->getManager(), $this->getOrm()->getRepository($entityName));
        }

        return $adapter;
    }
}