<?php

namespace APM\AchatBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Service_apres_venteControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm/achat_service_apres_vente');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm/achat_groupe_index/");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['service_apres_vente[codeSav]'] = 'SAV125';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("SAV125")')->count(), 'Missing element "SAV125"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'service_apres_vente[codeSav]' => 'SAV000'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "TG000"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("SAV000")')->count(), 'Missing element [value="TG000"]');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/achat_service_apres_vente/'), 'The response is redirect not to /apm/achat_groupe_index');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/SAV000/', $client->getResponse()->getContent());
    }

}
