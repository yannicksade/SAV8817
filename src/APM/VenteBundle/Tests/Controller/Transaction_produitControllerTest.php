<?php

namespace APM\VenteBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Transaction_produitControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm/vente_transaction_produit');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_vente_transaction_produit");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['transaction_produit[reference]'] = "TXP555T";

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("TXP555T")')->count(), 'Missing element "TXP125T"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array('transaction_produit[reference]' => "TXP000T"
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "TXP000T"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("TXP000T")')->count(), 'Missing element "100"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/vente_transaction_produit/'), 'The response is redirect not to /apm/vente_transaction_produit/');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/TXP000T/', $client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET");
    }
}
