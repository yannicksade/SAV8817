<?php

namespace APM\AchatBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Groupe_offreControllerTest extends WebTestCase
{

    public function testCompleteScenario()
    {

        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm_achat_groupe');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_achat_groupe_index/");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['groupe_offre[designation]'] = 'TG125';
        $form['groupe_offre[code]'] = 'G125';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("TG125")')->count(), 'Missing element td:contains("TG125")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'groupe_offre[designation]' => 'TG000'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "TG000"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("TG000")')->count(), 'Missing element [value="TG000"]');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm_achat_groupe/'), 'The response is redirect not to /apm_achat_groupe_index');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/TG000/', $client->getResponse()->getContent());
    }

}
