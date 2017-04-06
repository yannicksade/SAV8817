<?php

namespace APM\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Piece_jointeControllerTest extends WebTestCase
{

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm/user_piece-jointe');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_user_piece-jointe");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['piece_jointe[code]'] = 'PJT125T';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("PJT125T")')->count(), 'Missing element "PJT125T"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'piece_jointe[code]' => 'PJT000T'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "PJT000T"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("PJT000T")')->count(), 'Missing element "PJT000T"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/user_piece-jointe/'), 'The response is redirect not to /apm_user_piece-jointe/');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/PJT000T/', $client->getResponse()->getContent());
    }
}
