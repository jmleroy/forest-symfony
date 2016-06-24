<?php

namespace ForestAdmin\LianaBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/forest');

        $this->assertContains('Test Doctrine Metadata', $client->getResponse()->getContent());
    }
}
