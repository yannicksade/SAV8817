<?php

namespace APM\CoreBundle\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function indexAction(){
/************* Bloc Fashion *************/
        $fashionBestSellers = array(
            "1"=> array( 'id'=>1, "name"=>"Imac", "model"=>"macBook-m1", "prix"=>"1000","image"=>"imac_1-74x74.jpg"),
            "2"=> array( 'id'=>2, "name"=>"Appel Cinema", "model"=>"macBook-m2", "prix"=>"1000","image"=>"macbook_5-90x90.jpg"),
            "3"=> array( 'id'=>3, "name"=>"Iphone", "model"=>"Iphone5S", "prix"=>"9600","image"=>"iphone_6-90x90.jpg"),
            "4"=> array( 'id'=>4, "name"=>"Iphone", "model"=>"Iphone4S", "prix"=>"1000","image"=>"iphone_1-74x74.jpg"),
            "5"=> array( 'id'=>5, "name"=>"appel", "model"=>"appel core-i7", "prix"=>"5000","image"=>"apple_cinema_30-90x90.jpg"),
            "6"=> array( 'id'=>6, "name"=>"Iphone", "model"=>"Iphone1S", "prix"=>"6000","image"=>"iphone_4-1000x1000.jpg"),
            "7"=> array( 'id'=>7, "name"=>"iphone", "model"=>"IphoneModel1", "prix"=>"6000","image"=>"iphone_1-74x74.jpg"),
            "8"=> array( 'id'=>8, "name"=>"iphone", "model"=>"Iphone8S", "prix"=>"100","image"=>"iphone_3-460x460.jpg"),
            "9"=> array( 'id'=>9, "name"=>"Acanon", "model"=>"canon_eos", "prix"=>"6000","image"=>"canon_eos_5d_1-90x90.jpg"),
            "10"=> array( 'id'=>10, "name"=>"canon", "model"=>"canon_eos", "prix"=>"45000","image"=>"canon_eos_5d_1-90x90.jpg"),
            "11"=> array( 'id'=>11, "name"=>"iphone", "model"=>"Iphone3S", "prix"=>"352000","image"=>"iphone_5-460x460.jpg"),
            "12"=> array( 'id'=>12, "name"=>"HTC", "model"=>"HTC_touch", "prix"=>"145000","image"=>"htc_touch_hd_1-74x74.jpg"),
            );
            /* doit etre en chiffre paire */
        $fashionNewArr = array(
            "13"=> array( 'id'=>13, "name"=>"Dextop", "model"=>"Compaq", "prix"=>"152000","image"=>"compaq_presario-460x460.jpg"),
            "14"=> array( 'id'=>14, "name"=>"canon", "model"=>"canon 4d", "prix"=>"000","image"=>"canon_logo-90x90.jpg"),
            "15"=> array( 'id'=>15, "name"=>"ipod", "model"=>"ipod_classic", "prix"=>"42000","image"=>"ipod_classic_3-460x460.jpg"),
            "16"=> array( 'id'=>16, "name"=>"ipod", "model"=>"ipod_classic", "prix"=>"56000","image"=>"ipod_classic_4-90x90.jpg"),
            "17"=> array( 'id'=>17, "name"=>"ipod", "model"=>"ipod_shuffle Cinema", "prix"=>"8000","image"=>"ipod_shuffle_1-90x90.jpg"),
            "18"=> array( 'id'=>18, "name"=>"ipod", "model"=>"ipod_touch", "prix"=>"1000","image"=>"ipod_touch_1-90x90.jpg"),
            "19"=> array( 'id'=>19, "name"=>"sony", "model"=>"sony_vaio", "prix"=>"541000","image"=>"sony_vaio_5-180x180.jpg"),
            "20"=> array( 'id'=>20, "name"=>"samsung", "model"=>"samsung_tab", "prix"=>"451000","image"=>"samsung_tab_1-100x100.jpg"),
            "21"=> array( 'id'=>21, "name"=>"nikon", "model"=>"nikon_d300", "prix"=>"51000","image"=>"nikon_d300_3-100x100.jpg"),
            "22"=> array( 'id'=>22, "name"=>"ipod", "model"=>"ipod_classic", "prix"=>"1056100","image"=>"ipod_classic_4-90x90.jpg"),
            "23"=> array( 'id'=>23, "name"=>"ipod", "model"=>"ipod_nano", "prix"=>"123600","image"=>"ipod_nano_5-90x90.jpg"),
            "24"=> array( 'id'=>24, "name"=>"ipod", "model"=>"ipod_shuffle", "prix"=>"23100","image"=>"ipod_shuffle_1-90x90.jpg"),
            );
        $fashionRating = array(
           "25"=> array( 'id'=>25, "name"=>"ipod", "model"=>"ipod_touch", "prix"=>"55300","image"=>"ipod_touch_1-90x90.jpg"),
            "26"=> array( 'id'=>26, "name"=>"macbook", "model"=>"macbookm1", "prix"=>"256000","image"=>"macbook_1-74x74.jpg"),
            "27"=> array( 'id'=>27, "name"=>"macbook", "model"=>"macBook-m2", "prix"=>"546000","image"=>"macbook_3-460x460.jpg"),
            "28"=> array( 'id'=>28, "name"=>"macbook", "model"=>"macBook-m1", "prix"=>"354000","image"=>"macbook_5-90x90.jpg"),
            "29"=> array( 'id'=>29, "name"=>"macbook", "model"=>"macBook-m1", "prix"=>"1535000","image"=>"macbook_pro_1-100x100.jpg"),
            "30"=> array( 'id'=>30, "name"=>"nikon", "model"=>"nokon_d300", "prix"=>"51000","image"=>"nikon_d300_3-100x100.jpg"),
            "31"=> array( 'id'=>31, "name"=>"palm_treo", "model"=>"palm_treo", "prix"=>"156000","image"=>"palm_treo_pro_1-74x74.jpg"),
            "32"=> array( 'id'=>32, "name"=>"samsung", "model"=>"samsung_syncmaster", "prix"=>"561000","image"=>"samsung_syncmaster_941bw-100x100.jpg"),
            "33"=> array( 'id'=>33, "name"=>"sony", "model"=>"sony_vaio", "prix"=>"451000","image"=>"sony_vaio_5-180x180.jpg"),
            "34"=> array( 'id'=>34, "name"=>"samsung", "model"=>"samsung_tab", "prix"=>"691000","image"=>"samsung_tab_1-100x100.jpg"),
            "35"=> array( 'id'=>35, "name"=>"sony", "model"=>"sony_vaio", "prix"=>"5441000","image"=>"sony_vaio_1-100x100.jpg"),
            );
/**********   Bloc electronic   ************/
        $electronicBestSellers = array(
            "13"=> array( 'id'=>13, "name"=>"Dextop", "model"=>"Compaq", "prix"=>"152000","image"=>"compaq_presario-460x460.jpg"),
            "14"=> array( 'id'=>14, "name"=>"canon", "model"=>"canon 4d", "prix"=>"000","image"=>"canon_logo-90x90.jpg"),
            "15"=> array( 'id'=>15, "name"=>"ipod", "model"=>"ipod_classic", "prix"=>"42000","image"=>"ipod_classic_3-460x460.jpg"),
            "16"=> array( 'id'=>16, "name"=>"ipod", "model"=>"ipod_classic", "prix"=>"56000","image"=>"ipod_classic_4-90x90.jpg"),
            "17"=> array( 'id'=>17, "name"=>"ipod", "model"=>"ipod_shuffle Cinema", "prix"=>"8000","image"=>"ipod_shuffle_1-90x90.jpg"),
            "18"=> array( 'id'=>18, "name"=>"ipod", "model"=>"ipod_touch", "prix"=>"1000","image"=>"ipod_touch_1-90x90.jpg"),
            "19"=> array( 'id'=>19, "name"=>"sony", "model"=>"sony_vaio", "prix"=>"541000","image"=>"sony_vaio_5-180x180.jpg"),
            "20"=> array( 'id'=>20, "name"=>"samsung", "model"=>"samsung_tab", "prix"=>"451000","image"=>"samsung_tab_1-100x100.jpg"),
            "21"=> array( 'id'=>21, "name"=>"nikon", "model"=>"nikon_d300", "prix"=>"51000","image"=>"nikon_d300_3-100x100.jpg"),
            "22"=> array( 'id'=>22, "name"=>"ipod", "model"=>"ipod_classic", "prix"=>"1056100","image"=>"ipod_classic_4-90x90.jpg"),
            "23"=> array( 'id'=>23, "name"=>"ipod", "model"=>"ipod_nano", "prix"=>"123600","image"=>"ipod_nano_5-90x90.jpg"),
            "24"=> array( 'id'=>24, "name"=>"ipod", "model"=>"ipod_shuffle", "prix"=>"23100","image"=>"ipod_shuffle_1-90x90.jpg"),
            );

        $electronicNewArr = array(
            "1"=> array( 'id'=>1, "name"=>"Imac", "model"=>"macBook-m1", "prix"=>"1000","image"=>"imac_1-74x74.jpg"),
            "2"=> array( 'id'=>2, "name"=>"Appel Cinema", "model"=>"macBook-m2", "prix"=>"1000","image"=>"macbook_5-90x90.jpg"),
            "3"=> array( 'id'=>3, "name"=>"Iphone", "model"=>"Iphone5S", "prix"=>"9600","image"=>"iphone_6-90x90.jpg"),
            "4"=> array( 'id'=>4, "name"=>"Iphone", "model"=>"Iphone4S", "prix"=>"1000","image"=>"iphone_1-74x74.jpg"),
            "5"=> array( 'id'=>5, "name"=>"appel", "model"=>"appel core-i7", "prix"=>"5000","image"=>"apple_cinema_30-90x90.jpg"),
            "6"=> array( 'id'=>6, "name"=>"Iphone", "model"=>"Iphone1S", "prix"=>"6000","image"=>"iphone_4-1000x1000.jpg"),
            "7"=> array( 'id'=>7, "name"=>"iphone", "model"=>"IphoneModel1", "prix"=>"6000","image"=>"iphone_1-74x74.jpg"),
            "8"=> array( 'id'=>8, "name"=>"iphone", "model"=>"Iphone8S", "prix"=>"100","image"=>"iphone_3-460x460.jpg"),
            "9"=> array( 'id'=>9, "name"=>"Acanon", "model"=>"canon_eos", "prix"=>"6000","image"=>"canon_eos_5d_1-90x90.jpg"),
            "10"=> array( 'id'=>10, "name"=>"canon", "model"=>"canon_eos", "prix"=>"45000","image"=>"canon_eos_5d_1-90x90.jpg"),
            "11"=> array( 'id'=>11, "name"=>"iphone", "model"=>"Iphone3S", "prix"=>"352000","image"=>"iphone_5-460x460.jpg"),
            "12"=> array( 'id'=>12, "name"=>"HTC", "model"=>"HTC_touch", "prix"=>"145000","image"=>"htc_touch_hd_1-74x74.jpg"),
            );
            /* doit etre en chiffre paire */
        $electronicRating = array(
           "25"=> array( 'id'=>25, "name"=>"ipod", "model"=>"ipod_touch", "prix"=>"55300","image"=>"ipod_touch_1-90x90.jpg"),
            "26"=> array( 'id'=>26, "name"=>"macbook", "model"=>"macbookm1", "prix"=>"256000","image"=>"macbook_1-74x74.jpg"),
            "27"=> array( 'id'=>27, "name"=>"macbook", "model"=>"macBook-m2", "prix"=>"546000","image"=>"macbook_3-460x460.jpg"),
            "28"=> array( 'id'=>28, "name"=>"macbook", "model"=>"macBook-m1", "prix"=>"354000","image"=>"macbook_5-90x90.jpg"),
            "29"=> array( 'id'=>29, "name"=>"macbook", "model"=>"macBook-m1", "prix"=>"1535000","image"=>"macbook_pro_1-100x100.jpg"),
            "30"=> array( 'id'=>30, "name"=>"nikon", "model"=>"nokon_d300", "prix"=>"51000","image"=>"nikon_d300_3-100x100.jpg"),
            "31"=> array( 'id'=>31, "name"=>"palm_treo", "model"=>"palm_treo", "prix"=>"156000","image"=>"palm_treo_pro_1-74x74.jpg"),
            "32"=> array( 'id'=>32, "name"=>"samsung", "model"=>"samsung_syncmaster", "prix"=>"561000","image"=>"samsung_syncmaster_941bw-100x100.jpg"),
            "33"=> array( 'id'=>33, "name"=>"sony", "model"=>"sony_vaio", "prix"=>"451000","image"=>"sony_vaio_5-180x180.jpg"),
            "34"=> array( 'id'=>34, "name"=>"samsung", "model"=>"samsung_tab", "prix"=>"691000","image"=>"samsung_tab_1-100x100.jpg"),
            "35"=> array( 'id'=>35, "name"=>"sony", "model"=>"sony_vaio", "prix"=>"5441000","image"=>"sony_vaio_1-100x100.jpg"),
            );

/************  Bloc Sport **************/

        $sportBestSellers = array(
           "25"=> array( 'id'=>25, "name"=>"ipod", "model"=>"ipod_touch", "prix"=>"55300","image"=>"ipod_touch_1-90x90.jpg"),
            "26"=> array( 'id'=>26, "name"=>"macbook", "model"=>"macbookm1", "prix"=>"256000","image"=>"macbook_1-74x74.jpg"),
            "27"=> array( 'id'=>27, "name"=>"macbook", "model"=>"macBook-m2", "prix"=>"546000","image"=>"macbook_3-460x460.jpg"),
            "28"=> array( 'id'=>28, "name"=>"macbook", "model"=>"macBook-m1", "prix"=>"354000","image"=>"macbook_5-90x90.jpg"),
            "29"=> array( 'id'=>29, "name"=>"macbook", "model"=>"macBook-m1", "prix"=>"1535000","image"=>"macbook_pro_1-100x100.jpg"),
            "30"=> array( 'id'=>30, "name"=>"nikon", "model"=>"nokon_d300", "prix"=>"51000","image"=>"nikon_d300_3-100x100.jpg"),
            "31"=> array( 'id'=>31, "name"=>"palm_treo", "model"=>"palm_treo", "prix"=>"156000","image"=>"palm_treo_pro_1-74x74.jpg"),
            "32"=> array( 'id'=>32, "name"=>"samsung", "model"=>"samsung_syncmaster", "prix"=>"561000","image"=>"samsung_syncmaster_941bw-100x100.jpg"),
            "33"=> array( 'id'=>33, "name"=>"sony", "model"=>"sony_vaio", "prix"=>"451000","image"=>"sony_vaio_5-180x180.jpg"),
            "34"=> array( 'id'=>34, "name"=>"samsung", "model"=>"samsung_tab", "prix"=>"691000","image"=>"samsung_tab_1-100x100.jpg"),
            "35"=> array( 'id'=>35, "name"=>"sony", "model"=>"sony_vaio", "prix"=>"5441000","image"=>"sony_vaio_1-100x100.jpg"),
            );
        $sportNewArr = array(
            "13"=> array( 'id'=>13, "name"=>"Dextop", "model"=>"Compaq", "prix"=>"152000","image"=>"compaq_presario-460x460.jpg"),
            "14"=> array( 'id'=>14, "name"=>"canon", "model"=>"canon 4d", "prix"=>"000","image"=>"canon_logo-90x90.jpg"),
            "15"=> array( 'id'=>15, "name"=>"ipod", "model"=>"ipod_classic", "prix"=>"42000","image"=>"ipod_classic_3-460x460.jpg"),
            "16"=> array( 'id'=>16, "name"=>"ipod", "model"=>"ipod_classic", "prix"=>"56000","image"=>"ipod_classic_4-90x90.jpg"),
            "17"=> array( 'id'=>17, "name"=>"ipod", "model"=>"ipod_shuffle Cinema", "prix"=>"8000","image"=>"ipod_shuffle_1-90x90.jpg"),
            "18"=> array( 'id'=>18, "name"=>"ipod", "model"=>"ipod_touch", "prix"=>"1000","image"=>"ipod_touch_1-90x90.jpg"),
            "19"=> array( 'id'=>19, "name"=>"sony", "model"=>"sony_vaio", "prix"=>"541000","image"=>"sony_vaio_5-180x180.jpg"),
            "20"=> array( 'id'=>20, "name"=>"samsung", "model"=>"samsung_tab", "prix"=>"451000","image"=>"samsung_tab_1-100x100.jpg"),
            "21"=> array( 'id'=>21, "name"=>"nikon", "model"=>"nikon_d300", "prix"=>"51000","image"=>"nikon_d300_3-100x100.jpg"),
            "22"=> array( 'id'=>22, "name"=>"ipod", "model"=>"ipod_classic", "prix"=>"1056100","image"=>"ipod_classic_4-90x90.jpg"),
            "23"=> array( 'id'=>23, "name"=>"ipod", "model"=>"ipod_nano", "prix"=>"123600","image"=>"ipod_nano_5-90x90.jpg"),
            "24"=> array( 'id'=>24, "name"=>"ipod", "model"=>"ipod_shuffle", "prix"=>"23100","image"=>"ipod_shuffle_1-90x90.jpg"),
            );
        $sportRating = array(
            "1"=> array( 'id'=>1, "name"=>"Imac", "model"=>"macBook-m1", "prix"=>"1000","image"=>"imac_1-74x74.jpg"),
            "2"=> array( 'id'=>2, "name"=>"Appel Cinema", "model"=>"macBook-m2", "prix"=>"1000","image"=>"macbook_5-90x90.jpg"),
            "3"=> array( 'id'=>3, "name"=>"Iphone", "model"=>"Iphone5S", "prix"=>"9600","image"=>"iphone_6-90x90.jpg"),
            "4"=> array( 'id'=>4, "name"=>"Iphone", "model"=>"Iphone4S", "prix"=>"1000","image"=>"iphone_1-74x74.jpg"),
            "5"=> array( 'id'=>5, "name"=>"appel", "model"=>"appel core-i7", "prix"=>"5000","image"=>"apple_cinema_30-90x90.jpg"),
            "6"=> array( 'id'=>6, "name"=>"Iphone", "model"=>"Iphone1S", "prix"=>"6000","image"=>"iphone_4-1000x1000.jpg"),
            "7"=> array( 'id'=>7, "name"=>"iphone", "model"=>"IphoneModel1", "prix"=>"6000","image"=>"iphone_1-74x74.jpg"),
            "8"=> array( 'id'=>8, "name"=>"iphone", "model"=>"Iphone8S", "prix"=>"100","image"=>"iphone_3-460x460.jpg"),
            "9"=> array( 'id'=>9, "name"=>"Acanon", "model"=>"canon_eos", "prix"=>"6000","image"=>"canon_eos_5d_1-90x90.jpg"),
            "10"=> array( 'id'=>10, "name"=>"canon", "model"=>"canon_eos", "prix"=>"45000","image"=>"canon_eos_5d_1-90x90.jpg"),
            "11"=> array( 'id'=>11, "name"=>"iphone", "model"=>"Iphone3S", "prix"=>"352000","image"=>"iphone_5-460x460.jpg"),
            "12"=> array( 'id'=>12, "name"=>"HTC", "model"=>"HTC_touch", "prix"=>"145000","image"=>"htc_touch_hd_1-74x74.jpg"),
            );
            /* doit etre en chiffre paire */

        return $this->render('::base/shop/Shop/index.html.twig',
            array('fashionBestSellers'=>$fashionBestSellers, 'fashionNewArr'=>$fashionNewArr, 'fashionRating'=>$fashionRating,
                    'electronicBestSellers'=>$electronicBestSellers, 'electronicNewArr'=>$electronicNewArr, 'electronicRating'=>$electronicRating,
                        'sportBestSellers'=>$sportBestSellers, 'sportNewArr'=>$sportNewArr, 'sportRating'=>$sportRating));
    }

    public function getNomBoutiqueAction()
    {
        return new Response("Boutique Evm Hamadou sarl",200);
    }

    public function getCodeBoutiqueAction()
    {
        return new Response("002654",200);
    }
}
