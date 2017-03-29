<?php

namespace APM\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Individu_to_groupeControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm_user_individu-to-groupe');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_user_individu-to-groupe");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['individu_to_groupe[code]'] = 'IGP125T';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("IGP125T")')->count(), 'Missing element "IGP125T"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'individu_to_groupe[code]' => 'IGP000T'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "IGP000T"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("IGP000T")')->count(), 'Missing element "IGP000T"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm_user_individu-to-groupe/'), 'The response is redirect not to /apm_user_individu-to-groupe/');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/IGP000T/', $client->getResponse()->getContent());
    }

}
