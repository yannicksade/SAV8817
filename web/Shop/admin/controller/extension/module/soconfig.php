<?php
/******************************************************
 * @package	SO Theme Framework for Opencart 2.3.x
 * @author	http://www.magentech.com
 * @license	GNU General Public License
 * @copyright(C) 2008-2015 Magentech.com. All rights reserved.
*******************************************************/
 

class ControllerExtensionModuleSoconfig extends Controller {

    private $error = array();
	private $demos = array();
	private $typeheader = array();
	private $typefooter = array();
	private $typelayout = array();
	 
	public function  __construct($registry) {
		parent::__construct($registry);
		
		//Dev Custom Theme
		$this->demos = array(
			array('key'=>'default', 'title'=>'Default','link'=>'#'),
			array('key'=>'layout2', 'title'=>'Home Layout2','link'=>'#'),
			array('key'=>'layout3', 'title'=>'Home Layout3','link'=>'#'),
			array('key'=>'layout4', 'title'=>'Home Layout4','link'=>'#'),
			array('key'=>'layout5', 'title'=>'Home Layout5','link'=>'#'),
			array('key'=>'layout6', 'title'=>'Home Layout6','link'=>'#'),
			array('key'=>'layout7', 'title'=>'Home Layout7','link'=>'#'),
		);
		
		$this->listColor= array(
			'red'    =>'#ea3a3c',
			'orange' =>'#ff5c00',
			'blue'   =>'#003764',
			'blue2'  =>'#1090d4',
			'cyan'   =>'#0f8db3',
			'green'  =>'#6e8f04',
		);
		
		$this->typelayouts = array(
			array(
			'key'=>'1',
			'typelayout'=>'<p>Layout 1</p> <p class="font-small">(Header - Type 1,Footer - Type 1)</p>',
			'typeheader'=> array('key'=>'1', 'title'=>'Header 1 (used in Layout 1)'),
			'typefooter'=> array('key'=>'1', 'title'=>'Footer 1 (used in Layout 1)'),
			),
			array(
			'key'=>'2',
			'typelayout'=>'<p>Layout 2</p> <p class="font-small">(Header - Type 2,Footer - Type 2)</p>',
			'typeheader'=> array('key'=>'2', 'title'=>'Header 2 (used in Layout 2)'),
			'typefooter'=> array('key'=>'2', 'title'=>'Footer 2 (used in Layout 2)'),
			),
			array(
			'key'=>'3',
			'typelayout'=>'<p>Layout 3 </p><p class="font-small">(Header - Type 3,Footer - Type 3)</p>',
			'typeheader'=> array('key'=>'3', 'title'=>'Header 3 (used in Layout 3)'),
			'typefooter'=> array('key'=>'3', 'title'=>'Footer 3 (used in Layout 3)'),
			),
			array(
			'key'=>'4',
			'typelayout'=>'<p>Layout 4</p> <p class="font-small">(Header - Type 4,Footer - Type 4)</p>',
			'typeheader'=> array('key'=>'4', 'title'=>'Header 4 (used in Layout 4)'),
			'typefooter'=> array('key'=>'4', 'title'=>'Footer 4 (used in Layout 4)'),
			),
			array(
			'key'=>'5',
			'typelayout'=>'<p>Layout 5</p> <p class="font-small">(Header - Type 5,Footer - Type 5)</p>',
			'typeheader'=> array('key'=>'5', 'title'=>'Header 5 (used in Layout 5)'),
			'typefooter'=> array('key'=>'5', 'title'=>'Footer 5 (used in Layout 5)'),
			),
			array(
			'key'=>'6',
			'typelayout'=>'<p>Layout 6</p> <p class="font-small">(Header - Type 6,Footer - Type 6)</p>',
			'typeheader'=> array('key'=>'6', 'title'=>'Header 6 (used in Layout 6)'),
			'typefooter'=> array('key'=>'6', 'title'=>'Footer 6 (used in Layout 6)'),
			),
			array(
			'key'=>'7',
			'typelayout'=>'<p>Layout 7</p> <p class="font-small">(Header - Type 7,Footer - Type 7)</p>',
			'typeheader'=> array('key'=>'7', 'title'=>'Header 7 (used in Layout 7)'),
			'typefooter'=> array('key'=>'7', 'title'=>'Footer 7 (used in Layout 7)'),
			),
		);
		//End Dev Custom Theme
	}
    public function index() {
		$store_id = isset($this->request->get['store_id']) ? (int)$this->request->get['store_id'] : 0;
		$this->load->language('extension/module/soconfig');
		$this->document->setTitle($this->language->get('heading_title_normal'));
		$this->document->addScript('view/javascript/bs-colorpicker/js/colorpicker.js');
		$this->document->addScript('view/javascript/summernote/summernote.js');
		$this->document->addScript('view/javascript/theme/jquery.cookie.js');
		$this->document->addScript('view/javascript/theme/theme.js');
		
        $this->document->addStyle('view/javascript/bs-colorpicker/css/colorpicker.css');
        $this->document->addStyle('view/javascript/summernote/summernote.css');
        $this->document->addStyle('view/stylesheet/theme.css');
        $this->document->addStyle('view/stylesheet/banner-effect.css');
		$this->load->model('soconfig/setting');
		
		/* stores adding */
        $this->load->model('setting/store');
        $this->load->model('setting/setting');
        $stores = $this->model_setting_store->getStores();
		array_unshift($stores, array(
			'store_id' => '0',
			'name'     => $this->config->get('config_name'),
		));
		$store_data = array();
		foreach ($stores as $store) {
			$store_data[] = array(
				'name'   => $store['name'],
				'store_id' =>  $store['store_id'],
				'status' => $this->model_setting_setting->getSettingValue('theme_default_status', $store['store_id']) 
			);
		}
		$data['stores'] = $stores;
        /* end stores adding */
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$this->model_soconfig_setting->editSetting($this->request->post, $store_id);
			
			
            // buttonForm apply
			if($this->request->post['buttonForm'] == 'color' ){
				$data['scsscompile'] = $this->request->post['soconfig_advanced_store'][$store_id]['scsscompile'];
				
				if($data['scsscompile'] != 1){
					$this->session->data['success'] = 'Success Compile Sass File To Css';
					$this->soconfig->scss_compass($this->request->post['soconfig_advanced_store'][$store_id]['theme_color'],$this->request->post['soconfig_advanced_store'][$store_id]['name_color'],$this->request->post['soconfig_general_store'][$store_id]['typelayout'],$this->request->post['soconfig_advanced_store'][$store_id]['compileMutiColor'],$this->listColor);
					unset($this->request->post['buttonForm']);
					$this->response->redirect($this->url->link('extension/module/soconfig', 'token=' . $this->session->data['token'], 'SSL'));
				}else{
					$this->session->data['success'] = 'Error: Compile Sass File To Css, Select Performace -> SCSS Compile = Off';
				}
				
			}else if ($this->request->post['buttonForm'] == 'apply') {
				$this->session->data['success'] = $this->language->get('text_success');
                $this->response->redirect($this->url->link('extension/module/soconfig', 'token=' . $this->session->data['token'], 'SSL'));
				
            }else if ($this->request->post['buttonForm'] == 'install-data') {
				$install_layout = isset($this->request->post['required_layout'] ) ? $this->request->post['required_layout']  : '';
				$home_layout = isset($this->request->post['home_layout']) ? (int)$this->request->post['home_layout'] : 1;
				$blog_install = isset($this->request->post['blog_install']) ? (int)$this->request->post['blog_install'] : 0;
				
				$ret = $this->install_demo_data($stores, $store_id,$install_layout,$home_layout);
				if ($ret)
				  $this->response->redirect($this->url->link('extension/module/soconfig', 'token=' . $this->session->data['token'] .'&store_id='.$store_id.'&do=install-success', 'SSL'));
				else
				  $this->response->redirect($this->url->link('extension/module/soconfig', 'token=' . $this->session->data['token'] .'&store_id='.$store_id.'&do=install-failed', 'SSL'));  
				
			} else {
                $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
            }
			
		}
		
		$data['clear_cache_href'] = $this->url->link('extension/module/soconfig/clearcache', 'token=' . $this->session->data['token'].'&store_id='.$store_id, 'SSL');
		$data['clear_css_href'] = $this->url->link('extension/module/soconfig/clearcss', 'token=' . $this->session->data['token'].'&store_id='.$store_id, 'SSL');
		$data['compiled_css'] = $this->url->link('extension/module/soconfig/compiled_css', 'token=' . $this->session->data['token'].'&store_id='.$store_id, 'SSL');
		
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['theme_version'] = $this->language->get('theme_version');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		
        if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_title'] = $this->language->get('entry_title');
        $data['entry_description'] = $this->language->get('entry_description');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['entry_status'] = $this->language->get('entry_status');
		$data['help_code'] = $this->language->get('help_code');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		$this->load->model('design/layout');
		$data['oc_layouts'] = $this->model_design_layout->getLayouts();
		$data['do'] = isset($this->request->get['do']) ? $this->request->get['do'] : '';
		$data['layout'] = isset($this->request->get['layout']) ? $this->request->get['layout'] : ''; 
		$data['store_id']= isset($this->request->get['store_id']) ? $this->request->get['store_id'] : '';
		$data['demos'] = $this->demos;

		$data['typelayouts'] = $this->typelayouts;
		
		$data['base_href'] = $this->url->link('extension/module/soconfig', 'token=' . $this->session->data['token'].'&store_id='.$store_id, 'SSL');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token']. '&type=module', 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/soconfig', 'token=' . $this->session->data['token'] . '&store_id='.$store_id, true)
		);

		$data['action'] = $this->url->link('extension/module/soconfig', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

        /*Variables for theme */
        $data['mproduct'] = $this->url->link('catalog/mproduct', 'token=' . $this->session->data['token'], 'SSL');
        $data['mcategory'] = $this->url->link('catalog/mcategory', 'token=' . $this->session->data['token'], 'SSL');
		
        /* Edit so config images */
        $this->load->model('tool/image');
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		$text_strings = array(
			'maintabs_general',
			'maintabs_layout',
			'maintabs_products',
			'maintabs_fonts',
			'maintabs_custom',
			'maintabs_social',
			'maintabs_sampledata',
			
			'general_tab_general',
			'general_tab_header',
			'general_tab_footer',
			'general_tab_mainmenu',
			'general_tab_language',
			'general_res_layout',
			'general_cpanel',
			'general_back_top',
			'general_copyright',
			
			'fonts_body',
			'fonts_menu',
			'fonts_heading',
			'fonts_custome',
			
			'pages_pro_listings',
			'pages_pro_label',
			'pages_pro_detail',
			'product_catalog_title',
			'product_catalog_image',
			'product_list_compare',
			'product_catalog_mode',
			'product_catalog_column',
			'product_catalog_refine',
			'product_catalog_refine_col',
			'show_product_item_desc',
			
			'custom_tab_css_input',
			'custom_tab_css_file',
			'custom_tab_js_input',
			'custom_tab_js_file',
			
			'slide_tab_facebook',
			'slide_tab_twitter',
			'slide_tab_video',
			'slide_tab_custom',
			
			'entry_position',
			'entry_standard',
			'entry_google_font',
			'entry_google_url',
			'entry_google_url_help',
			'entry_google_family',
			'entry_google_family_help',
			'entry_custom_block',
			'entry_title_label',
			'entry_custom_column',
			'entry_my_account',
			'entry_contact_us',
			'entry_catalog_column_help',
			'mobile_menu_title',
			'entry_sort_order',
                
        );
        foreach ($text_strings as $text) {
            $data[$text] = $this->language->get($text);
        }
		
		//Get Store config data
		$checktable = $this->db->query("SHOW TABLES LIKE '".DB_PREFIX."soconfig'");
		$table_exists = $checktable->num_rows;
		if($table_exists){
			foreach($stores as $storeid => $store){
				$load_config = $this->model_soconfig_setting->getSetting($stores);
				if(!empty($load_config) ){
					foreach ($load_config as $conf_name => $conf_value){
						if(isset($conf_value[$store['store_id']])){
							$data[$conf_name][$store['store_id']] = $conf_value[$store['store_id']];
						}else{
							$data[$conf_name][$store['store_id']] = '';
						}
					}
				}else{
					echo 'Error Remove module Theme Control Panel. Pleases install module Theme Control Panel';exit;
				}
			}
		}else{
			echo 'Pleases install module Theme Control Panel';exit;
		}
		// End store config data
		
		
		//Get Variables Theme Config
		if ($this->config->get('theme_default_directory')) $data['theme'] = $this->config->get('theme_default_directory');
		else $data['theme'] = 'default';
		$data['allThemeColor'] = array();
		foreach($store_data as  $store){
			if($store['status']){
				$contentbg 		= isset($data['soconfig_general_store'][$store['store_id']]['contentbg']) ? $data['soconfig_general_store'][$store['store_id']]['contentbg'] : '';
				$imgpayment 	= isset($data['soconfig_general_store'][$store['store_id']]['imgpayment']) ? $data['soconfig_general_store'][$store['store_id']]['imgpayment']: '';
				$imgpreloader 	= isset($data['soconfig_general_store'][$store['store_id']]['imgpreloader']) ? $data['soconfig_general_store'][$store['store_id']]['imgpreloader'] : '';
				$typelayout 	= isset($data['soconfig_general_store'][$store['store_id']]['typelayout']) ? $data['soconfig_general_store'][$store['store_id']]['typelayout']: '';
				$data['cssfile'][]	= isset($data['soconfig_custom_store'][$store['store_id']]['cssfile']) ? $data['soconfig_custom_store'][$store['store_id']]['cssfile']: '';
				$data['jsfile'][]	= isset($data['soconfig_custom_store'][$store['store_id']]['jsfile']) ? $data['soconfig_custom_store'][$store['store_id']]['jsfile']: '';
				if($this->soconfig !=null){
						$data['allThemeColor'][$store['store_id']] =  $this->soconfig->getColorScheme($typelayout);
					}else{
						echo 'Changes: Go to Extensions > Modifications and click on the Blue Refresh button to apply changes.';exit;
				}
				
				if (is_file(DIR_IMAGE . $contentbg)) {
					$data['contentbg'][$store['store_id']] = $this->model_tool_image->resize($contentbg, 100, 100);
				} else {
					$data['contentbg'][$store['store_id']] = $this->model_tool_image->resize('no_image.png', 100, 100);
				}
				if (is_file(DIR_IMAGE.$imgpayment)) {
					$data['imgpayment'][$store['store_id']] = $this->model_tool_image->resize($imgpayment, 100, 100);
				} else {
					$data['imgpayment'][$store['store_id']] = $this->model_tool_image->resize('no_image.png', 100, 100);
				}
				if (is_file(DIR_IMAGE.$imgpreloader)) {
					$data['imgpreloader'][$store['store_id']] = $this->model_tool_image->resize($imgpreloader, 100, 100);
				} else {
					$data['imgpreloader'][$store['store_id']] = $this->model_tool_image->resize('no_image.png', 100, 100);
				}
			}
		}
	
		
        /* information pages */
        $this->load->model('catalog/information');

        foreach ($this->model_catalog_information->getInformations() as $result) {
            $data['information_pages'][] = array(
                'title' => $result['title'],
                'information_id' => $result['information_id'],
                'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
            );
        }
        /* end information pages */
        $data['token'] = $this->session->data['token'];
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        /*end variables for theme */

        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/soconfig/soconfig.tpl', $data));
	}
	
	public function uninstall() {
        $this->load->model('soconfig/setting');
        $this->model_soconfig_setting->deleteSetting();
    }
	
    public function install(){
        $this->load->model('soconfig/mproduct');
		$this->load->model('setting/setting');
		$this->load->model('soconfig/setting');
		
		/* stores adding */
        $this->load->model('setting/store');
        $stores = $this->model_setting_store->getStores();
		array_unshift($stores, array(
			'store_id' => '0',
			'name'     => $this->config->get('config_name'),
		));
		$data['stores'] = $stores;
        /* end stores adding */
		
        $this->model_soconfig_mproduct->createColumnsInProducts();
		$this->model_soconfig_setting->createTableSoconfig();
		
		//Import sample data current theme
		$install_layout='default'; $store_id = 0;$home_layout =1; 
		$main_sql = DIR_SYSTEM.'soconfig/demo/'.$install_layout.'/install.php';
		if (!file_exists($main_sql)) return false;   
		include($main_sql);
		
		$this->session->data['success'] = $this->language->get('text_success');
    }
	
	
	public function clearcache(){
      $this->soconfig->cache->clear();
      $this->session->data['success'] = 'Cache cleared';
      $this->response->redirect($this->url->link('extension/module/soconfig', 'token=' . $this->session->data['token'], 'SSL'));
    }
	
	public function clearcss(){
      $this->soconfig->cache->clear_css();
      $this->session->data['success'] = 'Cache cleared';
      $this->response->redirect($this->url->link('extension/module/soconfig', 'token=' . $this->session->data['token'], 'SSL'));
	 
    }
	
	public function install_demo_data($stores, $store_id,$install_layout,$home_layout){
		if ($home_layout == 0) return false;
		
		$install_layout_exists = false;
		foreach($this->demos as $demo){
		if ($demo['key'] == $install_layout)
		  $install_layout_exists = true;
		}

		if (!$install_layout_exists) return false;
		$main_sql = DIR_SYSTEM.'soconfig/demo/'.$install_layout.'/install.php';
		if (!file_exists($main_sql)) return false;   
		
		include($main_sql);
		return true;  
    }
	
	public function getColorScheme() {
		$json = array();
		if (isset($this->request->get['filter_name'])) {
			$filter_data = $this->request->get['filter_name'];
			$results = $this->soconfig->getColorScheme($filter_data);
			
			if(!empty($results)){
				foreach ($results as $result) {
					$json[] = array(
						'name'        => html_entity_decode($result, ENT_QUOTES, 'UTF-8')
					);
				}
			}else{
				$json[] = array(
					'name'        => 'No Value'
				);
			}
			
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
    protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/soconfig')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
}
