<?php

namespace ForestAdmin\ForestBundle\Service;

use Doctrine\ORM\Mapping\ClassMetadata;
use ForestAdmin\Liana\Analyzer\DoctrineAnalyzer;
use ForestAdmin\Liana\Api\Map;
use ForestAdmin\Liana\Model\Collection as ForestCollection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psecio\Jwt\Header as JwtHeader;
use Psecio\Jwt\Jwt;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;

class ForestService extends CacheWarmer
{
    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var
     */
    protected $orm;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container, $orm, $cacheDir)
    {
        $this->setContainer($container);
        $this->setOrm($orm);
        $this->setCacheDir($cacheDir);
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
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * @param string $cacheDir
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Cache Warmer: every time the cache is cleared, this method is executed to warm up the application
     * @param string $cacheDir
     */
    public function warmUp($cacheDir)
    {
        $this->setCacheDir($cacheDir);

        if (!$this->areInCache('collections')) {
            $this->saveCollectionsFromAnalyzerToCache();
        }
        if (!$this->areInCache('metadata')) {
            $this->saveMetadataFromOrmToCache();
        }
        try {
            $this->postApimap();
        } catch(ClientException $exc) {
            echo "postApimap returned the following error: ".$exc->getMessage()."\n";
        }
    }

    /**
     * Used by Cache Warmer
     * @return bool
     */
    public function isOptional()
    {
        return true;
    }

    public function getAllowedUsers($data)
    {
        $renderingId = $data->renderingId;
        
        $uri = $this->getForestUri()."/forest/renderings/".$renderingId."/allowed-users";
        $options = array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'forest-secret-key' => $this->getSecretKey(),
            ),
        );
        $client = new Client();
        $response = $client->request('GET', $uri, $options);
        $response = json_decode($response->getBody());
        $allowedUsers = array();
        foreach($response->data as $res) {
            $user = $res->attributes;
            $user->id = $res->id;
            $allowedUsers[] = $user;
        }

        return $allowedUsers;
    }

    public function generateAuthToken($user)
    {
        $header = new JwtHeader($this->getAuthKey());
        $jwt = new Jwt($header);
        $jwt->custom($this->getAuthOptions());
        $jwt->issuer($this->getForestUri())
            //->audience('http://example.com')
            ->issuedAt(time())
            ->notBefore(time()+60)
            ->expireTime(time()+3600)
            //->jwtId('id123456')
            //->type('https://example.com/register')
        ;

        return $jwt->encode();
    }

    public function postApimap()
    {
        $map = $this->getApimap();
        $options = array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'forest-secret-key' => $this->getSecretKey(),
            ),
            'body' => $map,
        );
        $client = new Client();
        $response = $client->request('POST', $this->getApimapUri(), $options);

        if ($response->getStatusCode() != 204) {
            // TODO Should log something
            return false;
        }

        return true;
    }

    public function getApimap()
    {
        $map = new Map($this->getCollections(), $this->getApimapMeta());
        return $map->getApimap();
    }

    /**
     * @return ForestCollection[]
     */
    public function getCollections()
    {
        if ($this->areInCache('collections')) {
            $collections = $this->getFromCache('collections');
        } else {
            $collections = $this->saveCollectionsFromAnalyzerToCache();
        }

        return $collections;
    }

    public function getMetadata()
    {
        if ($this->areInCache('metadata')) {
            $metadata = $this->getFromCache('metadata');
        } else {
            $metadata = $this->saveMetadataFromOrmToCache();
        }

        return $metadata;
    }

    /**
     * @return ForestCollection[]
     */
    public function saveCollectionsFromAnalyzerToCache()
    {
        $collections = $this->getCollectionsFromAnalyzer();
        $this->saveInCache('collections', $collections);

        return $collections;
    }

    /**
     * @return \Doctrine\ORM\Mapping\ClassMetadata[]
     */
    public function saveMetadataFromOrmToCache()
    {
        $metadata = $this->getMetadataFromOrm();
        $this->saveInCache('metadata', $metadata);
        return $metadata;
    }

    /**
     * @return ForestCollection[]
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
        $filename = $this->getCacheFilename($what);
        if(!file_exists(dirname($filename))) {
            (new Filesystem)->mkdir(dirname($filename));
        }

        return $this->writeCacheFile($filename, serialize($data));
    }

    /**
     * @param string $what
     * @return string
     */
    protected function getCacheFilename($what)
    {
        return $this->getCacheDirectoryName() . $what;
    }

    /**
     * @param string $what
     * @return mixed
     */
    protected function getFromCache($what)
    {
        return unserialize(file_get_contents($this->getCacheFilename($what)));
    }

    protected function getCacheDirectoryName()
    {
        return $this->getCacheDir() . DIRECTORY_SEPARATOR . 'forestadmin' . DIRECTORY_SEPARATOR;
    }

    protected function getForestUri()
    {
        return $this->getContainer()->getParameter('forestadmin.forest.uri.server');
    }
    
    protected function getApimapUri()
    {
        return $this->getContainer()->getParameter('forestadmin.forest.uri.apimap');
    }

    protected function getSecretKey()
    {
        return $this->getContainer()->getParameter('forestadmin.forest.secret_key');
    }

    protected function getAuthKey()
    {
        return $this->getContainer()->getParameter('forestadmin.forest.auth_key');
    }

    protected function getAuthOptions()
    {
        return array(
            'foo' => 'bar',
        );
    }

    protected function getApimapMeta()
    {
        return array(
            'liana' => $this->getContainer()->getParameter('forestadmin.apimap.meta.liana'),
            'liana_version' => $this->getContainer()->getParameter('forestadmin.apimap.meta.liana_version'),
        );
    }
}