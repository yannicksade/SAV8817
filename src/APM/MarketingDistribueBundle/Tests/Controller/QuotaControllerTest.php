<?php

namespace APM\MarketingDistribueBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QuotaControllerTest extends WebTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        //run this client to a separate client
        $client->insulate();
        // Create a new entry in the database
        $client->request('GET', '/apm/marketing_quota');
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /apm_marketing_quota");

        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form();
        $form['quota[code]'] = 'TQOT125';
        $form['quota[libelleQuota]'] = 'Mon QUOTA';

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("TQOT125")')->count(), 'Missing element "TQOT125"');

        // Edit the entity from the show view
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'quota[code]' => 'TQOT000'
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "TQOT000"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("TQOT000")')->count(), 'Missing element "TQOT000"');

        // Delete the entity and redirect to the list
        $client->submit($crawler->selectButton('Delete')->form());
        $this->assertTrue($client->getResponse()->isRedirect('/apm/marketing_quota/'), 'The response is redirect not to /apm_marketing_quota');
        $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/TQOT000/', $client->getResponse()->getContent());
    }
}
