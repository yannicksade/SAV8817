<?php

namespace APM\CoreBundle\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

// contenu temporaire
class ProductsController extends Controller
{
    public function ProductDetailAction($id_prod){
        $produits = array(
            "1"=> array( 'id'=>1, "name"=>"Imac", "model"=>"macBook-m1", "priceUnit"=>"1000","path"=>"imac_1-74x74.jpg"),
            "2"=> array( 'id'=>2, "name"=>"Appel Cinema", "model"=>"macBook-m2", "priceUnit"=>"1000","path"=>"macbook_5-90x90.jpg"),
            "3"=> array( 'id'=>3, "name"=>"Iphone", "model"=>"Iphone5S", "priceUnit"=>"9600","path"=>"iphone_6-90x90.jpg"),
            "4"=> array( 'id'=>4, "name"=>"Iphone", "model"=>"Iphone4S", "priceUnit"=>"1000","path"=>"iphone_1-74x74.jpg"),
            "5"=> array( 'id'=>5, "name"=>"appel", "model"=>"appel core-i7", "priceUnit"=>"5000","path"=>"apple_cinema_30-90x90.jpg"),
            "6"=> array( 'id'=>6, "name"=>"Iphone", "model"=>"Iphone1S", "priceUnit"=>"6000","path"=>"iphone_4-1000x1000.jpg"),
            "7"=> array( 'id'=>7, "name"=>"iphone", "model"=>"IphoneModel1", "priceUnit"=>"6000","path"=>"iphone_1-74x74.jpg"),
            "8"=> array( 'id'=>8, "name"=>"iphone", "model"=>"Iphone8S", "priceUnit"=>"100","path"=>"iphone_3-460x460.jpg"),
            "9"=> array( 'id'=>9, "name"=>"Acanon", "model"=>"canon_eos", "priceUnit"=>"6000","path"=>"canon_eos_5d_1-90x90.jpg"),
            "10"=> array( 'id'=>10, "name"=>"canon", "model"=>"canon_eos", "priceUnit"=>"45000","path"=>"canon_eos_5d_1-90x90.jpg"),
            "11"=> array( 'id'=>11, "name"=>"iphone", "model"=>"Iphone3S", "priceUnit"=>"352000","path"=>"iphone_5-460x460.jpg"),
            "12"=> array( 'id'=>12, "name"=>"HTC", "model"=>"HTC_touch", "priceUnit"=>"145000","path"=>"htc_touch_hd_1-74x74.jpg"),
            "13"=> array( 'id'=>13, "name"=>"Dextop", "model"=>"Compaq", "priceUnit"=>"152000","path"=>"compaq_presario-460x460.jpg"),
            "14"=> array( 'id'=>14, "name"=>"canon", "model"=>"canon 4d", "priceUnit"=>"000","path"=>"canon_logo-90x90.jpg"),
            "15"=> array( 'id'=>15, "name"=>"ipod", "model"=>"ipod_classic", "priceUnit"=>"42000","path"=>"ipod_classic_3-460x460.jpg"),
            "16"=> array( 'id'=>16, "name"=>"ipod", "model"=>"ipod_classic", "priceUnit"=>"56000","path"=>"ipod_classic_4-90x90.jpg"),
            "17"=> array( 'id'=>17, "name"=>"ipod", "model"=>"ipod_shuffle Cinema", "priceUnit"=>"8000","path"=>"ipod_shuffle_1-90x90.jpg"),
            "18"=> array( 'id'=>18, "name"=>"ipod", "model"=>"ipod_touch", "priceUnit"=>"1000","path"=>"ipod_touch_1-90x90.jpg"),
            "19"=> array( 'id'=>19, "name"=>"sony", "model"=>"sony_vaio", "priceUnit"=>"541000","path"=>"sony_vaio_5-180x180.jpg"),
            "20"=> array( 'id'=>20, "name"=>"samsung", "model"=>"samsung_tab", "priceUnit"=>"451000","path"=>"samsung_tab_1-100x100.jpg"),
            "21"=> array( 'id'=>21, "name"=>"nikon", "model"=>"nikon_d300", "priceUnit"=>"51000","path"=>"nikon_d300_3-100x100.jpg"),
            "22"=> array( 'id'=>22, "name"=>"ipod", "model"=>"ipod_classic", "priceUnit"=>"1056100","path"=>"ipod_classic_4-90x90.jpg"),
            "23"=> array( 'id'=>23, "name"=>"ipod", "model"=>"ipod_nano", "priceUnit"=>"123600","path"=>"ipod_nano_5-90x90.jpg"),
            "24"=> array( 'id'=>24, "name"=>"ipod", "model"=>"ipod_shuffle", "priceUnit"=>"23100","path"=>"ipod_shuffle_1-90x90.jpg"),
            "25"=> array( 'id'=>25, "name"=>"ipod", "model"=>"ipod_touch", "priceUnit"=>"55300","path"=>"ipod_touch_1-90x90.jpg"),
            "26"=> array( 'id'=>26, "name"=>"macbook", "model"=>"macbookm1", "priceUnit"=>"256000","path"=>"macbook_1-74x74.jpg"),
            "27"=> array( 'id'=>27, "name"=>"macbook", "model"=>"macBook-m2", "priceUnit"=>"546000","path"=>"macbook_3-460x460.jpg"),
            "28"=> array( 'id'=>28, "name"=>"macbook", "model"=>"macBook-m1", "priceUnit"=>"354000","path"=>"macbook_5-90x90.jpg"),
            "29"=> array( 'id'=>29, "name"=>"macbook", "model"=>"macBook-m1", "priceUnit"=>"1535000","path"=>"macbook_pro_1-100x100.jpg"),
            "30"=> array( 'id'=>30, "name"=>"nikon", "model"=>"nokon_d300", "priceUnit"=>"51000","path"=>"nikon_d300_3-100x100.jpg"),
            "31"=> array( 'id'=>31, "name"=>"palm_treo", "model"=>"palm_treo", "priceUnit"=>"156000","path"=>"palm_treo_pro_1-74x74.jpg"),
            "32"=> array( 'id'=>32, "name"=>"samsung", "model"=>"samsung_syncmaster", "priceUnit"=>"561000","path"=>"samsung_syncmaster_941bw-100x100.jpg"),
            "33"=> array( 'id'=>33, "name"=>"sony", "model"=>"sony_vaio", "priceUnit"=>"451000","path"=>"sony_vaio_5-180x180.jpg"),
            "34"=> array( 'id'=>34, "name"=>"samsung", "model"=>"samsung_tab", "priceUnit"=>"691000","path"=>"samsung_tab_1-100x100.jpg"),
            "35"=> array( 'id'=>35, "name"=>"sony", "model"=>"sony_vaio", "priceUnit"=>"5441000","path"=>"sony_vaio_1-100x100.jpg"),
            );
        $produit = array();
        $produit = $produits[$id_prod];
        return $this->render('::base/shop/Shop/viewProductId.html.twig',
            array('produit'=>$produit));
    }

    public function getProductSearchAction(){
        $categorie = 0;
        $search ="";
        if(isset($_POST['category_id']) && isset($_POST['category_id']) !=null ){
            $categorie =  $_POST['category_id'];
        }
        if(isset($_POST['search']) && isset($_POST['search']) !=null ){
            $search =  $_POST['search'];
        }
        return $this->render('::base/shop/Shop/result_produits.html.twig',
            array('categorie'=> $categorie, 'search'=>$search));
    }

    public function getProductIdAction(Request $request=null){
        $produits = array(
            "1"=> array( 'id'=>1, "name"=>"Imac", "model"=>"macBook-m1", "priceUnit"=>"1000","path"=>"imac_1-74x74.jpg"),
            "2"=> array( 'id'=>2, "name"=>"Appel Cinema", "model"=>"macBook-m2", "priceUnit"=>"1000","path"=>"macbook_5-90x90.jpg"),
            "3"=> array( 'id'=>3, "name"=>"Iphone", "model"=>"Iphone5S", "priceUnit"=>"9600","path"=>"iphone_6-90x90.jpg"),
            "4"=> array( 'id'=>4, "name"=>"Iphone", "model"=>"Iphone4S", "priceUnit"=>"1000","path"=>"iphone_1-74x74.jpg"),
            "5"=> array( 'id'=>5, "name"=>"appel", "model"=>"appel core-i7", "priceUnit"=>"5000","path"=>"apple_cinema_30-90x90.jpg"),
            "6"=> array( 'id'=>6, "name"=>"Iphone", "model"=>"Iphone1S", "priceUnit"=>"6000","path"=>"iphone_4-1000x1000.jpg"),
            "7"=> array( 'id'=>7, "name"=>"iphone", "model"=>"IphoneModel1", "priceUnit"=>"6000","path"=>"iphone_1-74x74.jpg"),
            "8"=> array( 'id'=>8, "name"=>"iphone", "model"=>"Iphone8S", "priceUnit"=>"100","path"=>"iphone_3-460x460.jpg"),
            "9"=> array( 'id'=>9, "name"=>"Acanon", "model"=>"canon_eos", "priceUnit"=>"6000","path"=>"canon_eos_5d_1-90x90.jpg"),
            "10"=> array( 'id'=>10, "name"=>"canon", "model"=>"canon_eos", "priceUnit"=>"45000","path"=>"canon_eos_5d_1-90x90.jpg"),
            "11"=> array( 'id'=>11, "name"=>"iphone", "model"=>"Iphone3S", "priceUnit"=>"352000","path"=>"iphone_5-460x460.jpg"),
            "12"=> array( 'id'=>12, "name"=>"HTC", "model"=>"HTC_touch", "priceUnit"=>"145000","path"=>"htc_touch_hd_1-74x74.jpg"),
            "13"=> array( 'id'=>13, "name"=>"Dextop", "model"=>"Compaq", "priceUnit"=>"152000","path"=>"compaq_presario-460x460.jpg"),
            "14"=> array( 'id'=>14, "name"=>"canon", "model"=>"canon 4d", "priceUnit"=>"000","path"=>"canon_logo-90x90.jpg"),
            "15"=> array( 'id'=>15, "name"=>"ipod", "model"=>"ipod_classic", "priceUnit"=>"42000","path"=>"ipod_classic_3-460x460.jpg"),
            "16"=> array( 'id'=>16, "name"=>"ipod", "model"=>"ipod_classic", "priceUnit"=>"56000","path"=>"ipod_classic_4-90x90.jpg"),
            "17"=> array( 'id'=>17, "name"=>"ipod", "model"=>"ipod_shuffle Cinema", "priceUnit"=>"8000","path"=>"ipod_shuffle_1-90x90.jpg"),
            "18"=> array( 'id'=>18, "name"=>"ipod", "model"=>"ipod_touch", "priceUnit"=>"1000","path"=>"ipod_touch_1-90x90.jpg"),
            "19"=> array( 'id'=>19, "name"=>"sony", "model"=>"sony_vaio", "priceUnit"=>"541000","path"=>"sony_vaio_5-180x180.jpg"),
            "20"=> array( 'id'=>20, "name"=>"samsung", "model"=>"samsung_tab", "priceUnit"=>"451000","path"=>"samsung_tab_1-100x100.jpg"),
            "21"=> array( 'id'=>21, "name"=>"nikon", "model"=>"nikon_d300", "priceUnit"=>"51000","path"=>"nikon_d300_3-100x100.jpg"),
            "22"=> array( 'id'=>22, "name"=>"ipod", "model"=>"ipod_classic", "priceUnit"=>"1056100","path"=>"ipod_classic_4-90x90.jpg"),
            "23"=> array( 'id'=>23, "name"=>"ipod", "model"=>"ipod_nano", "priceUnit"=>"123600","path"=>"ipod_nano_5-90x90.jpg"),
            "24"=> array( 'id'=>24, "name"=>"ipod", "model"=>"ipod_shuffle", "priceUnit"=>"23100","path"=>"ipod_shuffle_1-90x90.jpg"),
            "25"=> array( 'id'=>25, "name"=>"ipod", "model"=>"ipod_touch", "priceUnit"=>"55300","path"=>"ipod_touch_1-90x90.jpg"),
            "26"=> array( 'id'=>26, "name"=>"macbook", "model"=>"macbookm1", "priceUnit"=>"256000","path"=>"macbook_1-74x74.jpg"),
            "27"=> array( 'id'=>27, "name"=>"macbook", "model"=>"macBook-m2", "priceUnit"=>"546000","path"=>"macbook_3-460x460.jpg"),
            "28"=> array( 'id'=>28, "name"=>"macbook", "model"=>"macBook-m1", "priceUnit"=>"354000","path"=>"macbook_5-90x90.jpg"),
            "29"=> array( 'id'=>29, "name"=>"macbook", "model"=>"macBook-m1", "priceUnit"=>"1535000","path"=>"macbook_pro_1-100x100.jpg"),
            "30"=> array( 'id'=>30, "name"=>"nikon", "model"=>"nokon_d300", "priceUnit"=>"51000","path"=>"nikon_d300_3-100x100.jpg"),
            "31"=> array( 'id'=>31, "name"=>"palm_treo", "model"=>"palm_treo", "priceUnit"=>"156000","path"=>"palm_treo_pro_1-74x74.jpg"),
            "32"=> array( 'id'=>32, "name"=>"samsung", "model"=>"samsung_syncmaster", "priceUnit"=>"561000","path"=>"samsung_syncmaster_941bw-100x100.jpg"),
            "33"=> array( 'id'=>33, "name"=>"sony", "model"=>"sony_vaio", "priceUnit"=>"451000","path"=>"sony_vaio_5-180x180.jpg"),
            "34"=> array( 'id'=>34, "name"=>"samsung", "model"=>"samsung_tab", "priceUnit"=>"691000","path"=>"samsung_tab_1-100x100.jpg"),
            "35"=> array( 'id'=>35, "name"=>"sony", "model"=>"sony_vaio", "priceUnit"=>"5441000","path"=>"sony_vaio_1-100x100.jpg"),
            );
        $produit = [];
        
        if($request->isXMLHttpRequest()){
            $id = $request->get("id");
            $produit = $produits[$id];
            return new Response(json_encode($produit));
        }else
            return new response("n est pas une requete de type XMLHttpRequest");
       
    }
    
    public function getProductCompareAction()
    {
        return $this->render('::base/shop/Shop/compareProduct.html.twig');
    }
}
