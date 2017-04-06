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
        $client->request('GET', '/apm/user_individu-to-groupe');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_user_individu-to-groupe");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['individu_to_groupe[propriete]'] = 0;

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("0")')->count(), 'Missing element "IGP125T"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array('individu_to_groupe[propriete]' => 1
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "IGP000T"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("1")')->count(), 'Missing element "IGP000T"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/user_individu-to-groupe/'), 'The response is redirect not to /apm_user_individu-to-groupe/');
        $client->followRedirect();

        // Check the entity has been delete on the list
        //$this->assertNotRegExp('/1/', $client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_user_individu-to-groupe");

    }

}
