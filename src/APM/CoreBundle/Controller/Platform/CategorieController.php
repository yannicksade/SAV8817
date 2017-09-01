<?php

namespace APM\CoreBundle\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
class CategorieController extends Controller
{
    public function indexAction()
    {
        $les_cat =  array();
        $les_cat[] = array(
            "id"=>1,
            "name"=>"Machines",
            "logo"=>"machine"
        );
        $les_cat[] = array(
            "id"=>2,
            "name"=>"Electronique",
            "logo"=>"phone"
        );
        $les_cat[] = array(
            "id"=>3,
            "name"=>"Vetement",
            "logo"=>"vetement"
        );
        $les_cat[] = array(
            "id"=>4,
            "name"=>"Parking",
            "logo"=>"accessoire"
        );
        $les_cat[] = array(
            "id"=>5,
            "name"=>"Sport",
            "logo"=>"sport"
        );
        $les_cat[] = array(
            "id"=>5,
            "name"=>"Accesoires",
            "logo"=>"diamant"
        );
        $les_cat[] = array(
            "id"=>5,
            "name"=>"Accesoires",
            "logo"=>"diamant"
        );
        $les_cat[] = array(
            "id"=>5,
            "name"=>"Immobilier",
            "logo"=>"bed"
        );
        return new Response(json_encode($les_cat),200);
    }
    
    public function emptyAction()
    {
        return $this->render('::base/platform/i.html.twig');
    }
}
