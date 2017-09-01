<?php

namespace APM\CoreBundle\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// contenu temporaire
class CheckoutController extends Controller
{
    public function checkoutAction()
    {
        return $this->render('::base/shop/Shop/checkout.html.twig');
    }
}
