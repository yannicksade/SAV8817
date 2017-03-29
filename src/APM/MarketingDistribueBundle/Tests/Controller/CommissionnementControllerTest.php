<?php

namespace APM\MarketingDistribueBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommissionnementControllerTest extends WebTestCase
{

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm_marketing_commissionnement');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_marketing_commissionnement");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['commissionnement[code]'] = 'TCMS125';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("TCMS125")')->count(), 'Missing element "TCMS125"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'commissionnement[code]' => 'TCMS000'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "TCMS000"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("TCMS000")')->count(), 'Missing element "TCMS000"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm_marketing_commissionnement/'), 'The response is redirect not to /apm_marketing_conseiller');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/TCMS000/', $client->getResponse()->getContent());
    }
}
