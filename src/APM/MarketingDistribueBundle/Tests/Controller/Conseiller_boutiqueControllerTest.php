<?php

namespace APM\MarketingDistribueBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Conseiller_boutiqueControllerTest extends WebTestCase
{

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm/marketing_conseiller_boutique');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_marketing_conseiller_boutique");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        //$form['conseiller_boutique[code]'] = 'CONBTQ125T';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("CONBTQ125T")')->count(), 'Missing element "CONBTQ125T"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(// 'conseiller_boutique[code]' => 'CONBTQ000T'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "CONBTQ000T"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("CONBTQ000T")')->count(), 'Missing element "CONBTQ000T"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/marketing_conseiller_boutique/'), 'The response is redirect not to/apm_marketing_conseiller_boutique');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/CONBTQ000T/', $client->getResponse()->getContent());
    }
}