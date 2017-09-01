<?php

namespace APM\CoreBundle\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class BoutiqueController extends Controller
{
    public function getAllAction(){
        
        $noms =  array("Screen Lock","Cuuter","Cuisino","Cuisino","Screen Lock","Screen Lock","Pan Couture","Pan Couture","Pan Couture","Pan Couture","Pan Couture","Pan Couture","Pan Couture","Pan Couture","Pan Couture","Pan Couture","Pan Couture","Pan Couture","Pan Couture","Pan Couture");
        $photo =  array("plateform/demo/banners/home5/banner-53.jpg","plateform/demo/banners/home4/h4-img-7.jpg","plateform/demo/banners/home3/h3-img-5.jpg","plateform/demo/banners/home2/h2-img-2.jpg","plateform/demo/banners/home2/h2-img-2.jpg","plateform/demo/blog/11.jpg","plateform/demo/menu/feature/home-1.jpg","plateform/demo/menu/feature/home-2.jpg","plateform/demo/menu/feature/home-3.jpg","plateform/demo/menu/feature/home-4.jpg","plateform/demo/menu/feature/home-5.jpg","plateform/demo/menu/feature/home-6.jpg","plateform/demo/menu/feature/home-7.jpg","plateform/demo/banners/home7/1.png","plateform/demo/menu/feature/home-3.jpg","plateform/demo/menu/feature/home-4.jpg","plateform/demo/menu/feature/home-5.jpg","plateform/demo/menu/feature/home-6.jpg","plateform/demo/menu/feature/home-7.jpg","plateform/demo/banners/home7/1.png");
        $photo2  =  array("plateform/demo/product/iphone.jpg","plateform/demo/product/macbook.jpg","plateform/demo/product/samsungtab.jpg","plateform/demo/product/samsungtab.jpg","plateform/demo/sony_vaio_4.jpg","plateform/demo/ipod_shuffle_1.jpg","plateform/demo/samsung_tab_5.jpg","plateform/demo/ipod_touch_1.jpg","plateform/demo/ipod_classic_1.jpg","plateform/demo/macbook_air_1.jpg","plateform/demo/compaq_presario.jpg","plateform/demo/nikon_d300_4.jpg","plateform/demo/nikon_d300_3.jpg");
        $categorie = array("vetement","voiture","electromenager","phone","computer","ordinateur","sport","beaute");
        $boutiques = array();
        $MAX = 1000;
        for($i=0;$i<count($noms);$i++){
            $index = $i%count($noms);
            $nom = $noms[$index];
            $face = $photo[ $index ];
            $face2 = $photo[ $index ];
            $cat = array(array("nom"=>$categorie[rand()%count($categorie)]),array("nom"=>$categorie[rand()%count($categorie)]));
            $zone = array(
                array("nom"=>"yaounde","continent"=>"afrique","pays"=>"cameroun"),
                array("nom"=>"maroua","continent"=>"afrique","pays"=>"cameroun"),
                array("nom"=>"garoua","continent"=>"afrique","pays"=>"cameroun"),
                array("nom"=>"ngaoundere","continent"=>"afrique","pays"=>"cameroun")
            );
            $boutiques[] = array(
                "categorie"=>$cat,
                "nom"=> $nom,
                "m"=>rand()%12,
                "y"=>rand()%15,
                "s"=>rand()%5,
                "logo"=>$face,
                "produit"=>$face2,
                "zone"=>array($zone[rand()%count($zone)]),
                "boutique"=> 1000+$index,
                "code"=>"".(1000+$i),
                "like" => rand()%$MAX,
                "sell" => rand()%$MAX,
                "share" => rand()%$MAX,
                "rate" => rand()%$MAX,
                "advisor" => rand()%$MAX,
                "visits" => rand()%$MAX,
                "new" => rand()%2,
                "solde" => rand()%2,
                "type" => 1+rand()%3,
                "price" => 1000+(rand()%$MAX)*rand()%$MAX
            );
        }
//        die();
        return new Response(json_encode($boutiques));
    }
    public function indexAction()
    {
        return $this->render('::base/platform/page/boutique.html.twig');
    }
    public function findAction(Request $request)
    {
        $data = $request->query;
        $categorie=$data->get("search_categories");
        $region=$data->get("search_region");
        $recherche=$data->get("search_query");
        if($categorie==0)
            $categorie="Toutes les categories";
        if($region==0)
            $region="Toutes les regions";
        $filter = array(
            "categorie"=>$categorie,
            "region"=>$region,
            "recherche"=>$recherche,
            "limit"=>8
        );
        return $this->render('::base/platform/page/result.html.twig',array("filter"=>$filter));
    }
}
