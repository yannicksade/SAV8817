<?php

namespace APM\AchatBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/achat');

        $this->assertContains('100', $client->getResponse()->getContent());
    }
}
