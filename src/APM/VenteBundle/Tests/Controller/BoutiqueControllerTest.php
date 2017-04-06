<?php

namespace APM\VenteBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BoutiqueControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm/vente_boutique');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_vente_boutique");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['boutique[designation]'] = 'My shop';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view

        $this->assertGreaterThan(0, $crawler->filter('td:contains("My shop")')->count(), 'Missing element "BTQ125T"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'boutique[designation]' => 'BTQ000T'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "BTQ000T"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("BTQ000T")')->count(), 'Missing element "BTQ000T"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/vente_boutique/'), 'The response is redirect not to /apm_vente_boutique/');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/BTQ000T/', $client->getResponse()->getContent());
    }
}
