<?php

namespace APM\TransportBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Zone_interventionControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm_transport_zone_intervention');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_transport_zone_intervention");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['zone_intervention[code]'] = 'ZOI125';
        $form['zone_intervention[designation]'] = 'MA ZONE';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("ZOI125")')->count(), 'Missing element "ZOI125"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'zone_intervention[code]' => 'ZOI000'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "ZOI000"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("ZOI000")')->count(), 'Missing element "ZOI000"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm_transport_zone_intervention/'), 'The response is redirect not to/apm_transport_zone_intervention/');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/ZOI000/', $client->getResponse()->getContent());
    }
}