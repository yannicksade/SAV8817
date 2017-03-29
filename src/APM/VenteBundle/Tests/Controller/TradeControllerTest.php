<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 02/02/2017
 * Time: 02:04
 */

namespace APM\VenteBundle\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TradeControllerTest extends WebTestCase
{

//    public function testInsererOffreDansBoutique(){
//        // Create a new client to browse the application
//        $client=static::createClient();
//        //run this client into a separate client
//        $client->insulate(true);
//        $client->request('', 'apm_vente_boutique_inserer_offre', array(
//            'id'=>'6', 'boutique_id'=>'5'
//        ));
//        $crawler=$client->followRedirect();
//        $this->assertEquals(200,$client->getResponse()->getStatusCode(),"Unexpected HTTP status code for /apm_vente_boutique/{id}/inserer-offre/{boutique_id}");
//       // $this->assertContains("td:contains('ART125T')",$client->getResponse()->getContent());
//    }
}