<?php

namespace APM\AnimationBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/animation');

        $this->assertContains('Hello World', $client->getResponse()->getContent());
    }
}
