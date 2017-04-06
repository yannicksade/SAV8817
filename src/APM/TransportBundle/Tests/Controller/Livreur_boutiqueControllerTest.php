<?php

namespace APM\TransportBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Livreur_boutiqueControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm/transport_livreur_boutique');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_transport_livreur_boutique");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['livreur_boutique[reference]'] = 'LVR125T';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("LVR125T")')->count(), 'Missing element "LVR125T"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'livreur_boutique[reference]' => 'LVR000T'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "LVR000T"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("LVR000T")')->count(), 'Missing element "LVR000T"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/transport_livreur_boutique/'), 'The response is redirect not to /apm_transport_livreur_boutique/');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/LVR000T/', $client->getResponse()->getContent());
    }

}
