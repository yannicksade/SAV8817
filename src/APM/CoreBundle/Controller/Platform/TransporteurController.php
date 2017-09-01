<?php

namespace APM\CoreBundle\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TransporteurController extends Controller
{
    public function getAllAction(){
        $noms = array("Rapide Livro","Comodo Trixx","Comodo Trixx","Livreur de Vin","Burgerking","Burgerking","Rapide Livro","Comodo Trixx","Comodo Trixx","Livreur de Vin","Burgerking","Burgerking","Rapide Livro","Comodo Trixx","Comodo Trixx","Burgerking","Burgerking","Rapide Livro","Comodo Trixx","Comodo Trixx");
        $photo =  array("plateform/img/livraison/3.png","plateform/img/livraison/2.jpg","plateform/img/livraison/6.jpg","plateform/demo/manufacturer/shell.png","plateform/img/livraison/2.jpg","plateform/img/livraison/6.jpg","plateform/img/livraison/3.png","plateform/img/livraison/2.jpg","plateform/img/livraison/6.jpg","plateform/demo/manufacturer/shell.png","plateform/img/livraison/2.jpg","plateform/img/livraison/6.jpg","plateform/img/livraison/3.png","plateform/img/livraison/2.jpg","plateform/img/livraison/6.jpg","plateform/img/livraison/2.jpg","plateform/img/livraison/6.jpg","plateform/img/livraison/3.png","plateform/img/livraison/2.jpg","plateform/img/livraison/6.jpg");
        $photo2  =  array("plateform/img/livraison/3.png","plateform/img/livraison/4.png","plateform/img/livraison/5.png","plateform/img/livraison/7.png","plateform/demo/manufacturer/burgerking.png","plateform/demo/manufacturer/redbull.png","plateform/img/livraison/3.png","plateform/img/livraison/4.png","plateform/img/livraison/5.png","plateform/img/livraison/7.png","plateform/demo/manufacturer/burgerking.png","plateform/demo/manufacturer/redbull.png","plateform/img/livraison/3.png","plateform/img/livraison/4.png","plateform/img/livraison/5.png","plateform/demo/manufacturer/burgerking.png","plateform/demo/manufacturer/redbull.png","plateform/img/livraison/3.png","plateform/img/livraison/4.png","plateform/img/livraison/5.png");
        $categorie = array("vetement","voiture","electromenager","phone","computer","ordinateur","sport","beaute");
        $boutiques = array();
        $MAX = 1000;
        for($i=0;$i<count($noms);$i++){
            $index = $i%count($noms);
            $nom = $noms[$index];
            $face = $photo[ $index % count($photo) ];
            $face2 = $photo2[ $index % count($photo2) ];
            $cat = array(array("nom"=>$categorie[rand()%count($categorie)]),array("nom"=>$categorie[rand()%count($categorie)]));
            $zone = array(
                array("nom"=>"yaounde","continent"=>"afrique","pays"=>"cameroun","prix"=>5000),
                array("nom"=>"maroua","continent"=>"afrique","pays"=>"cameroun","prix"=>400),
                array("nom"=>"garoua","continent"=>"afrique","pays"=>"cameroun","prix"=>9000),
                array("nom"=>"ngaoundere","continent"=>"afrique","pays"=>"cameroun","prix"=>2000)
            );
            $transp = array("avion","camion","voiture","moto");
            $boutiques[] = array(
                "categorie"=>$cat,
                "transport"=>array($transp[rand()%count($transp)]),
                "nom"=> $nom,
                "m"=>rand()%12,
                "y"=>rand()%15,
                "s"=>rand()%5,
                "avatar"=>$face,
                "logo"=>$face2,
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
        return $this->render('::base/platform/page/transporteurs.html.twig');
    }
        public function detailAction()
    {
        return $this->render('::base/platform/page/transporteur_detail.html.twig');
    }
}
