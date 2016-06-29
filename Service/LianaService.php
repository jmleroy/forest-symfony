<?php

namespace ForestAdmin\ForestBundle\Service;

use ForestAdmin\Liana\Adapter\DoctrineAdapter;
use ForestAdmin\Liana\Model\Collection;
use ForestAdmin\Liana\Model\Resource as ForestResource;
use ForestAdmin\Liana\Schema\ResourceSchema as ForestResourceSchema;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Encoder\EncoderOptions;

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
     * @var array
     */
    protected $encoderConfig = array(
        ForestResource::class => ForestResourceSchema::class,
    );

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
        $queryAdapter = $this->getQueryAdapter($modelName);
        if($queryAdapter) {
            $resource = $queryAdapter->getResource($recordId);

            return $this->formatJsonApi($resource);
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
     * @return null|Collection
     */
    protected function findCollection($entityName)
    {
        foreach($this->getCollections() as $collection) {
            if($collection->name == $entityName) {
                return $collection;
            }
        }

        return null;
    }

    /**
     * @param ForestResource $forestResource
     * @return string
     */
    protected function formatJsonApi($forestResource)
    {
        $encoder = Encoder::instance(
            $this->encoderConfig,
            new EncoderOptions(JSON_PRETTY_PRINT, '/forest')
        );
        
        $ret = $encoder->encodeData($forestResource);
        // TODO add relationships
        foreach($forestResource->getIncluded() as $includedResource) {

        }
        return $ret . PHP_EOL;
        
        return $forestResource;
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
     * @return DoctrineAdapter|null
     */
    protected function getQueryAdapter($modelName)
    {
        $collection = $this->findCollection($modelName);
        $entityName = $collection->entityClassName;
        $adapter = null;

        if ($this->isOrmDoctrine()) {
            $adapter = new DoctrineAdapter(
                $this->getCollections(),
                $collection,
                $this->getOrm()->getManager(),
                $this->getOrm()->getRepository($entityName)
            );
        }

        return $adapter;
    }
}