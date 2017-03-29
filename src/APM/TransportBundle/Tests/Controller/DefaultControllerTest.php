<?php

namespace APM\TransportBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/transport');

        $this->assertContains('Hello World', $client->getResponse()->getContent());
    }
}
