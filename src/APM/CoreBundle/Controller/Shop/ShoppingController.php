<?php

namespace APM\CoreBundle\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ShoppingController extends Controller
{
    public function shoppingAction()
    {
        return $this->render('::base/shop/Shop/shopping.html.twig');
    }

    public function getCartAction()
    {
    	$produits = "ceci est un tableau contenant les produits du panier";
    	return new Response($produits,200);
    }
}
