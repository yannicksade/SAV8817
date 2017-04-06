<?php

namespace APM\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommunicationControllerTest extends WebTestCase
{

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm/user_communication');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_user_communication");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['communication[reference]'] = 'COM125T';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("COM125T")')->count(), 'Missing element "COM125T"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'communication[reference]' => 'COM000T'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "COM000T"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("COM000T")')->count(), 'Missing element "COM000T"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/user_communication/'), 'The response is redirect not to /apm_user_communication/');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/COM000T/', $client->getResponse()->getContent());
    }

}
