<?php

namespace APM\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Groupe_relationnelControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm/user_groupe-relationnel');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_user_groupe-relationnel");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['groupe_relationnel[code]'] = 'GRL125T';
        $form['groupe_relationnel[designation]'] = 'My Friends';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("GRL125T")')->count(), 'Missing element "GRL125T"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'groupe_relationnel[code]' => 'GRL000T'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "GRL000T"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("GRL000T")')->count(), 'Missing element "GRL000T"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/user_groupe-relationnel/'), 'The response is redirect not to /apm_user_groupe-relationnel/');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/GRL000T/', $client->getResponse()->getContent());
    }


}
