<?php
/**
 * Created by PhpStorm.
 * User: jean-marc
 * Date: 26/06/16
 * Time: 19:08
 */

namespace ForestAdmin\ForestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class ForestAdminController extends Controller
{
    /**
     * @var string
     */
    protected $cacheDir;

    public function __construct()
    {
        $this->cacheDir = $this->container->getParameter('kernel.cache_dir') . DIRECTORY_SEPARATOR . 'forestadmin' . DIRECTORY_SEPARATOR;
    }
}