<?php

namespace APM\CoreBundle\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
class RegionController extends Controller
{
    public function indexAction()
    {
        $les_reg =  array();
        $les_reg[] = array(
            "id"=>1,
            "continent"=>"Afrique",
            "pays"=>"Cameroun",
            "ville"=>"Yaounde"
        );
        $les_reg[] = array(
            "id"=>2,
            "continent"=>"Afrique",
            "pays"=>"Cameroun",
            "ville"=>"Douala"
        );
        $les_reg[] = array(
            "id"=>3,
            "continent"=>"Afrique",
            "pays"=>"Cameroun",
            "ville"=>"Maroua"
        );
        return new Response(json_encode($les_reg),200);
    }
    
    public function emptyAction()
    {
        return $this->render('EvmBundle::i.html.twig');
    }
}
