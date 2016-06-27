<?php

namespace ForestAdmin\LianaBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LianaControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/forest/modelName/1');

        $this->assertContains("There is no model of name 'modelName' or with record ID 1.", $client->getResponse()->getContent());
    }
}
