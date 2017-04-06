<?php

namespace APM\AnimationBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Base_documentaireControllerTest extends WebTestCase
{

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $client->request('GET', '/apm/animation_base_documentaire');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_animation_base_documentaire/");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['base_documentaire[code]'] = 'TNWL125';
        $form['base_documentaire[objet]'] = 'Newsletter solde';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("TNWL125")')->count(), 'Missing element "TNWL125"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'base_documentaire[code]' => 'TNWL000'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "TSP000"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("TNWL000")')->count(), 'Missing element "TNWL000"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/animation_base_documentaire/'), 'The response is redirect not to /apm_animation_base_documentaire_index');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/TNWL000/', $client->getResponse()->getContent());
    }
}
