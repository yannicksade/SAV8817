<?php 
$this->oc_config 	= $registry->get('config');
$config_logo 		= $this->oc_config->get('config_logo');
$this->themes 		= $this->oc_config->get('theme_default_directory');
$colorScheme_cache   = (isset($_GET['scheme']))? $_GET['scheme'] :  $themecolor; 
$server 			= $this->oc_config->get('config_url');

	$output = '';
	$output .= '<div class="so-pre-loader">';
		if ($preloader_animation == 'double-loop') {
			// 1.Double Bounce loader
			$output .= '<div class="so-loader-center">';
				$output .= '<div class="spinner-bounce">';
				$output .= '<div class="double-bounce1"></div>'; 
				$output .= '<div class="double-bounce2"></div>'; 
				$output .= '</div>';
			$output .= '</div>';
		} elseif ($preloader_animation == 'audio-wave') {
			// 2.Audio Wave loader
			$output .= '<div class="so-loader-center">';
			$output .= '<div class="spinner-wave">';
				$output .= '<div class="rect1"></div>';
				$output .= '<div class="rect2"></div>';
				$output .= '<div class="rect3"></div>';
				$output .= '<div class="rect4"></div>';
				$output .= '<div class="rect5"></div>';
			$output .= '</div>';
			$output .= '</div>'; 
		} elseif ($preloader_animation == 'cube-move') {
			// 3.Cube Move Loader
			$output .= '<div class="so-loader-center">';
				$output .= '<div class="spinner-cube">';
				$output .= '<div class="cube1"></div>'; 
				$output .= '<div class="cube2"></div>'; 
				$output .= '</div>';
			$output .= '</div>';
		} elseif ($preloader_animation == 'circle-bounce') {
			// 4.Circle Bounce Loader
			$output .= '<div class="so-loader-center">';
			$output .= '<div class="spinner-bounce2">';
				$output .= '<div class="bounce1"></div>';
				$output .= '<div class="bounce2"></div>'; 
				$output .= '<div class="bounce3"></div>'; 
			$output .= '</div>'; 
			$output .= '</div>'; 
		} elseif ($preloader_animation == 'circle-bounce2') {
			// 5.Circle loader
			$output .= '<div class="so-loader-center">';
			$output .= '<div class="spinner-circle">';
				$output .= '<div class="sk-circle1 sk-child"></div>';
				$output .= '<div class="sk-circle2 sk-child"></div>'; 
				$output .= '<div class="sk-circle3 sk-child"></div>'; 
				$output .= '<div class="sk-circle4 sk-child"></div>'; 
				$output .= '<div class="sk-circle5 sk-child"></div>'; 
				$output .= '<div class="sk-circle6 sk-child"></div>'; 
				$output .= '<div class="sk-circle7 sk-child"></div>'; 
				$output .= '<div class="sk-circle8 sk-child"></div>'; 
				$output .= '<div class="sk-circle9 sk-child"></div>'; 
				$output .= '<div class="sk-circle10 sk-child"></div>'; 
				$output .= '<div class="sk-circle11 sk-child"></div>'; 
				$output .= '<div class="sk-circle12 sk-child"></div>'; 
			$output .= '</div>'; 
			$output .= '</div>'; 
		} elseif ($preloader_animation == 'cube-grid') {
			// 6.Cube Grid loader
			$output .= '<div class="so-loader-center">';
			$output .= '<div class="spinner-cube-grid">';
				$output .= '<div class="sk-cube sk-cube1"></div>';
				$output .= '<div class="sk-cube sk-cube2"></div>'; 
				$output .= '<div class="sk-cube sk-cube3"></div>'; 
				$output .= '<div class="sk-cube sk-cube4"></div>'; 
				$output .= '<div class="sk-cube sk-cube5"></div>'; 
				$output .= '<div class="sk-cube sk-cube6"></div>'; 
				$output .= '<div class="sk-cube sk-cube7"></div>'; 
				$output .= '<div class="sk-cube sk-cube8"></div>'; 
				$output .= '<div class="sk-cube sk-cube9"></div>'; 
			$output .= '</div>'; 
			$output .= '</div>'; 
		}elseif ($preloader_animation == 'folding-cube') {
			// 7.Folding Cube loader
			$output .= '<div class="so-loader-center">';
			$output .= '<div class="spinner-folding-cube">';
				$output .= '<div class="sk-cube1 sk-cube"></div>';
				$output .= '<div class="sk-cube2 sk-cube"></div>'; 
				$output .= '<div class="sk-cube4 sk-cube"></div>'; 
				$output .= '<div class="sk-cube3 sk-cube"></div>'; 
			$output .= '</div>'; 
			$output .= '</div>'; 
		} elseif ($preloader_animation == 'logo') {
			if(!empty($config_logo)){
				$logo = $server.'image/'.$this->oc_config->get('config_logo');
			}else{
				if (is_file(DIR_TEMPLATE.$this->themes.'/images/styling/'.$colorScheme_cache.'/logo.png')) {
					$logo = $server . 'catalog/view/theme/'.$this->themes.'/images/styling/'.$colorScheme_cache.'/logo.png';
				} else {
					$logo = '';
				}
			}
			// 8.Line loader with logo
			$output .= '<div class="so-loader-background">';
			$output .= '<div class="so-loader-with-logo">';
				$output .= '<div class="logo">';
				$output .= '<img src="' . $logo . '" alt="">';
				$output .= '</div>';
				$output .= '<span class="run-number"></span>';
				$output .= '<div class="line" id="line-load"></div>';
			$output .= '</div>';
			$output .= '</div>';
			

		} else {
			// 9.Circle loader 
			$output .= '<div class="so-loader-center">';
			$output .= '<div class="spinner"></div>'; 
			$output .= '</div>';
		}
		
	$output .= '</div>'; 
	
	echo $output;
	
 // if enable preloader
?>