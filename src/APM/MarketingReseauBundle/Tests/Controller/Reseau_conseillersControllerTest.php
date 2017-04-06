<?php

namespace APM\MarketingReseauBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Reseau_conseillersControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $client->request('GET', '/apm/marketing_reseau_conseillers');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_marketing_reseau_conseillers");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['reseau_conseillers[code]'] = 'RXC125';
        $form['reseau_conseillers[designation]'] = 'MARKETEUR';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("RXC125")')->count(), 'Missing element "RXC125"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'reseau_conseillers[code]' => 'RXC000'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "RXC000"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("RXC000")')->count(), 'Missing element "RXC000"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/marketing_reseau_conseillers/'), 'The response is redirect not to /apm_marketing_reseau_conseillers');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/RXC000/', $client->getResponse()->getContent());
    }
}
