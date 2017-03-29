<?php
/**
 * Created by PhpStorm.
 * User: Yannick
 * Date: 28/02/2017
 * Time: 01:17
 */

namespace APM\CoreBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ShopController extends Controller
{
//traite ici la disposition des marchandises du client dans sa boutique.
    public function showAction($id)
    {
        $userData = array();
        $systemData = array();

        return $this->render(':client/shop:shop.html.twig', array(
                'data' => $userData,
                'system' => $systemData,
            )
        );
    }
}