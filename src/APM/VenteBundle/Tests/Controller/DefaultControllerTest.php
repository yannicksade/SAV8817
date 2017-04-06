<?php

namespace APM\VenteBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/apm/vente');

        $this->assertContains('Hello World', $client->getResponse()->getContent());
    }
}
