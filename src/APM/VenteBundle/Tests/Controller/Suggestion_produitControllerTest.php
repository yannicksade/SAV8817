<?php

namespace APM\VenteBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Suggestion_produitControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm_vente_suggestion_offre');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_vente_suggestion_offre");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        //$form['suggestion_produit[code]'] = 'SGG125T';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        //$this->assertGreaterThan(0, $crawler->filter('td:contains("SGG125T")')->count(), 'Missing element "SGG125T"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(//'suggestion_produit[code]' => 'SGG000T'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "SGG000T"
        //$this->assertGreaterThan(0, $crawler->filter('td:contains("SGG000T")')->count(), 'Missing element "SGG000T"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm_vente_suggestion_offre/'), 'The response is redirect not to /apm_vente_suggestion_offre/');
        $client->followRedirect();

        // Check the entity has been delete on the list
        //$this->assertNotRegExp('/SGG000T/', $client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_vente_suggestion_offre");
    }
}
