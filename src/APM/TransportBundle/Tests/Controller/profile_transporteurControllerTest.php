<?php

namespace APM\TransportBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class profile_transporteurControllerTest extends WebTestCase
{

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm/transport_transporteur');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_transport_transporteur");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['profile_transporteur[code]'] = 'TRP125';
        $form['profile_transporteur[matricule]'] = 'MAT125';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("TRP125")')->count(), 'Missing element "TRP125"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'profile_transporteur[code]' => 'TRP000'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "TRP000"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("TRP000")')->count(), 'Missing element "TRP000"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/transport_transporteur/'), 'The response is redirect not to /apm_transport_transporteur');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/TRP000/', $client->getResponse()->getContent());
    }
}
