<?php

namespace APM\MarketingReseauBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/marketing_reseau');

        $this->assertContains('Hello World', $client->getResponse()->getContent());
    }
}
