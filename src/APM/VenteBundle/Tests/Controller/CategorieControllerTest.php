<?php

namespace APM\VenteBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategorieControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm/vente_categorie');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_vente_categorie");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['categorie[designation]'] = 'PRODUCTS';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("PRODUCTS")')->count(), 'Missing element "PRODUCTS"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'categorie[designation]' => 'CAT000T'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "CAT000T"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("CAT000T")')->count(), 'Missing element "CAT000T"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/vente_categorie/'), 'The response is redirect not to /apm_vente_categorie/');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/CAT000T/', $client->getResponse()->getContent());
    }
}
