<?php

namespace APM\AchatBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Specification_achatControllerTest extends WebTestCase
{

    public function testCompleteScenario()
    {
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm/achat_specification');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm/achat_specification_index/");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['specification_achat[code]'] = 'TSPA125';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("TSPA125")')->count(), 'Missing element "TSPA125"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'specification_achat[code]' => 'TSPA000'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "TSP000"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("TSPA000")')->count(), 'Missing element [value="TSPA000"]');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/achat_specification/'), 'The response is redirect not to /apm_achat_groupe_index');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/TSPA000/', $client->getResponse()->getContent());
    }
}
