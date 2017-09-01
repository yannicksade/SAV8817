<div class="responsive <?php echo $ustawienia['suffix_class'];?>">
	<?php  if($ustawienia['orientation'] == 1){
		echo '<div class="col-sm-3">';
	}
	?>
	<?php if($ustawienia['disp_title_module'] && isset($head_name) && $head_name) { ?>
		<h3><?php echo $head_name; ?></h3>
	<?php } ?>
	<nav class="navbar-default">
	<div class=" container-megamenu <?php if($ustawienia['full_width'] != '1') { echo 'container'; } ?> <?php if($ustawienia['orientation'] == 1) { echo 'vertical '; } else { echo 'horizontal'; } ?>">
		<?php if($ustawienia['orientation'] == 1) { ?>
		<div id="menuHeading">
			<div class="megamenuToogle-wrapper">
				<div class="megamenuToogle-pattern">
					<div class="container">
							<div><span></span><span></span><span></span></div>
							<?php echo $navigation_text; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="navbar-header">
			<button type="button" id="show-verticalmenu" data-toggle="collapse"  class="navbar-toggle">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<?php } else { ?>
		<div class="navbar-header">
			<button type="button" id="show-megamenu" data-toggle="collapse"  class="navbar-toggle">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<?php } ?>

		<?php if($ustawienia['orientation'] == 1) { ?>
			<div class="vertical-wrapper">
		<?php } else { ?>
			<div class="megamenu-wrapper">
		<?php } ?>

			<?php if($ustawienia['orientation'] == 1) { ?>
				<span id="remove-verticalmenu" class="fa fa-times"></span>
			<?php } else { ?>
				<span id="remove-megamenu" class="fa fa-times"></span>
			 <?php } ?>

			<div class="megamenu-pattern">
				<div class="container">
					<ul class="megamenu"
					data-transition="<?php if($ustawienia['animation'] != '') { echo $ustawienia['animation']; } else { echo 'none'; } ?>" data-animationtime="<?php if($ustawienia['animation_time'] > 0 && $ustawienia['animation_time'] < 5000) { echo $ustawienia['animation_time']; } else { echo 500; } ?>">

						<?php if($ustawienia['home_item'] == 'icon' || $ustawienia['home_item'] == 'text') { ?>
							<li class="home">
								<a href="<?php echo $home; ?>">
								<?php if($ustawienia['home_item'] == 'icon') { ?>
									<i class="fa fa-home"></i><?php
								}
								else {
									echo '<span><strong>'.$home_text.'</strong></span>';
								} ?>
								</a>
							</li>
						<?php } ?>

						<?php
						foreach($menu as $key=> $row) {
						$class = '';
						$icon_font = '';
						$class_link = 'clearfix';
						$label_item = '';
						$title = false;
						$target = false;
						if(strpos($row['icon'], 'no_image') !== false)
							$row['icon'] = '';
						if($row['description'] != '')
							$class_link .= ' description';
						if(isset($_GET['route']) && $_GET['route'] == $row['route'] && isset($_GET['path']) && $_GET['path'] = $row['path'])
							$class .= ' active_menu';
						if($row['class_menu'])
							$class .= $row['class_menu'];
						if(!empty($row['icon_font']))
							$icon_font .= "<i class='".$row['icon_font']."'></i>";
						if(!empty($row['icon_font']))
							$label_item .= "<span class='label'> ".$row['label_item']."</span>";
						if(is_array($row['submenu']) && !empty($row['submenu'])) {
							$class .= ' with-sub-menu';
							if($row['submenu_type'] == 1)
								$class .= ' click';
							else
								$class .= ' hover';
						}
						if($row['position'] == 1)
							$class .= ' pull-right';
						if($row['submenu_type'] == 2)
							$title = 'title="hover-intent"';
						if($row['new_window'] == 1)
							$target = 'target="_blank"';
						if(!isset($row['name'][$lang_id]))
							$row['name'][$lang_id] = 'Set name';
						if($ustawienia['orientation']=='1'){
						//Verticalmenu
							echo "<li class='item-vertical".$class."' ".$title.">";

							echo "<p class='close-menu'></p>";

							if(is_array($row['submenu']) && !empty($row['submenu'])){
								echo "<a href='".$row['link']."' class='".$class_link."' ".$target.">
										<span><strong>
											".$icon_font.$row['icon'].$row['name'][$lang_id].$row['description']."
										</strong></span>
										 ".$label_item."
										<b class='fa fa-angle-right' ></b>
										</a>";
							}else{
								echo 	"<a href='".$row['link']."' class='".$class_link."' ".$target.">
										<span><strong>
											".$icon_font.$row['icon'].$row['name'][$lang_id].$row['description']."
										</strong></span>
										 ".$label_item."
										</a>";
							}
							if(is_array($row['submenu']) && !empty($row['submenu'])) {
								if( preg_match("#%#", $row['submenu_width']) ){
									$row['submenu_width'] = str_replace( "%", "", $row['submenu_width']);
									echo '<div class="sub-menu" data-subwidth="'.$row['submenu_width'].'" >';
								}
								else
									echo '<div class="sub-menu" style="width:'.$row['submenu_width'].'">';

								echo '<div class="content">';
									echo '<div class="row">';
										$row_fluid = 0;
										foreach($row['submenu'] as $submenu) {
										if(($row_fluid+$submenu['content_width']) > 12) {
										$row_fluid = $submenu['content_width'];
										 echo '</div><div class="border"></div><div class="row">';
										} else {
										$row_fluid = $row_fluid+$submenu['content_width'];
										}
										echo '<div class="col-sm-'.$submenu['content_width'].'">';

											if($submenu['content_type'] == '0') {
												echo '<div class="html '.$submenu['class_menu'].'">';
													echo $submenu['html'];
												echo '</div>';
											} elseif($submenu['content_type'] == '1') {

												if(is_array($submenu['product'])) {
												echo '<div class="product '.$submenu['class_menu'].'">';
													echo '<div class="image"><a href="'.$submenu['product']['link'].'" onclick="window.location = \''.$submenu['product']['link'].'\';"><img src="'.$submenu['product']['image'].'" alt=""></a></div>';
													echo '<div class="name"><a href="'.$submenu['product']['link'].'" onclick="window.location = \''.$submenu['product']['link'].'\';">'.$submenu['product']['name'].'</a></div>';
													echo '<div class="price">';
													if (!$submenu['product']['special'])
														echo $submenu['product']['price'];
													else
														echo $submenu['product']['special'];
													echo '</div>';
												echo '</div>';
												}

											} elseif($submenu['content_type'] == '2') {
												echo '<div class="categories '.$submenu['class_menu'].'">';
													echo $submenu['categories'];
												echo '</div>';
											}
											elseif($submenu['content_type'] == '3') {
												if(is_array($submenu['manufactures'])) {
													echo '<ul class="manufacturer '.$submenu['class_menu'].'">';
														foreach($submenu['manufactures'] as $manufacturer)
															echo '<li><a href="'.$manufacturer['link'].'">'.$manufacturer['name'].'</a></li>';
													echo '</ul>';
												}
											}elseif($submenu['content_type'] == '4') {
												if($submenu['images']['show_title'] == 1)
													echo '<span class="title-submenu">'.$submenu['name'][$lang_id].'</span>';
													echo '<div class="link '.$submenu['class_menu'].'">';
														echo $submenu['images']['link'];
													echo '</div>';
											}
											elseif($submenu['content_type'] == '5') { 
												if($submenu['subcategory']['categories']){
													echo '<ul class="subcategory '.$submenu['class_menu'].'">';
														foreach($submenu['subcategory']['categories'] as $categories){
															echo'<li>';
																if($submenu['subcategory']['show_title'] == 1)
																	echo'<a href="'.$categories['href'].'" class="title-submenu '.$submenu['class_menu'].'">'.$categories['name'].'</a>';
																	if($categories['categories'])
																		echo $categories['categories'];
																if($submenu['subcategory']['show_image'] == 1)
																	echo '<img src="'.$categories['thumb'].'" alt="" >';
															echo '</li>';		
														}
													echo '</ul>';
												}
											}
											elseif($submenu['content_type'] == '6') {
													if($submenu['productlist']['show_title'] == 1)
														echo '<span class="title-submenu">'.$submenu['name'][$lang_id].'</span>';
														if(isset($submenu['productlist']['products']) && $submenu['productlist']['products']){
															foreach ($submenu['productlist']['products'] as $product) {
															$itempage = (isset($submenu['productlist']['col']) && $submenu['productlist']['col']) ? 12/$submenu['productlist']['col'] : 4;
															echo '<div class="col-xs-'.$itempage.' '.$submenu['class_menu'].'">';
																echo '<div class="product-thumb">';
																	echo '<div class="image">
																		<a href="'.$product['href'].'">
																			<img src="'. $product['thumb'].'" alt="'. $product['name'].'" title="'. $product['name'].'" class="img-responsive" />
																		</a></div>';
																	echo '<div>';
																		echo '<div class="caption">';
																			echo '<h4><a href="'. $product['href'].'">'. $product['name'].'</a></h4>';
																			echo '<p>'. $product['description'].'</p>';
																			if ($product['rating']) {
																				echo '<div class="rating">';
																				for ($i = 1; $i <= 5; $i++) {
																					if ($product['rating'] < $i) {
																						echo '<span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>';
																					} else {
																						echo '<span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>';
																					}
																				}
																		echo '</div>';
																	}
																	if ($product['price']) {
																		echo '<p class="price">';
																		if (!$product['special']) {
																			echo $product['price'];
																	   } else {
																			echo '<span class="price-new">'. $product['special'].'</span> <span class="price-old">'. $product['price'].'</span>';
																	   }
																	   if ($product['tax']) {
																			echo '<span class="price-tax">'. $product['tax'].'</span>';
																		}
																		echo '</p>';
																	}
																  echo '</div>';
																echo '</div>';
															  echo '</div>';
															echo '</div>';
														 }
													}
												}

											echo '</div>';
											}
										echo '</div>';
									echo '</div>';
								echo '</div>';
							}
							echo "</li>";
							echo "\n";
						//End Vertical
						} else {
						//Megamenu
						echo "<li class='".$class."' ".$title."><p class='close-menu'></p>";
							if(is_array($row['submenu']) && !empty($row['submenu']))
								echo 	"<a href='".$row['link']."' class='".$class_link."' ".$target.">
											<strong>
												".$icon_font.$row['icon'].$row['name'][$lang_id].$row['description']."
											</strong>
											 ".$label_item."
											<b class='caret' ></b>
										</a>";
							else
								echo 	"<a href='".$row['link']."' class='".$class_link."' ".$target.">
											<strong>
												".$icon_font.$row['icon'].$row['name'][$lang_id].$row['description']."
											</strong>
											 ".$label_item."
										</a>";

							if(is_array($row['submenu']) && !empty($row['submenu'])) {
							echo '<div class="sub-menu" style="width:'.$row['submenu_width'].'">';
								echo '<div class="content">';
									echo '<div class="row">';

										$row_fluid = 0;
										foreach($row['submenu'] as $submenu) {

										if(($row_fluid+$submenu['content_width']) > 12) {
											$row_fluid = $submenu['content_width'];
											echo '</div><div class="border"></div><div class="row">';
										} else {
											$row_fluid = $row_fluid+$submenu['content_width'];
										}

										echo '<div class="col-sm-'.$submenu['content_width'].'">';
												if($submenu['content_type'] == '0') {
													echo '<div class="html '.$submenu['class_menu'].'">';
														echo $submenu['html'];
													echo '</div>';
												} elseif($submenu['content_type'] == '1') {

													if(is_array($submenu['product'])) {
													echo '<div class="product '.$submenu['class_menu'].'">';
														echo '<div class="image"><a href="'.$submenu['product']['link'].'" onclick="window.location = \''.$submenu['product']['link'].'\';"><img src="'.$submenu['product']['image'].'" alt=""></a></div>';
														echo '<div class="name"><a href="'.$submenu['product']['link'].'" onclick="window.location = \''.$submenu['product']['link'].'\';">'.$submenu['product']['name'].'</a></div>';
														echo '<div class="price">';
															if (!$submenu['product']['special'])
																echo $submenu['product']['price'];
															else
																echo $submenu['product']['special'];
														echo '</div>';
													echo '</div>';
													}

												} elseif($submenu['content_type'] == '2') {
														echo '<div class="categories '.$submenu['class_menu'].'">';
															echo $submenu['categories'];
														echo '</div>';
												}
												elseif($submenu['content_type'] == '3') {
													if(is_array($submenu['manufactures'])) {
														echo '<ul class="manufacturer '.$submenu['class_menu'].'">';
														foreach($submenu['manufactures'] as $manufacturer)
															echo '<li><a href="'.$manufacturer['link'].'">'.$manufacturer['name'].'</a></li>';
														echo '</ul>';
													}
												}elseif($submenu['content_type'] == '4') {
													if($submenu['images']['show_title'] == 1)
														echo '<span class="title-submenu">'.$submenu['name'][$lang_id].'</span>';
														echo '<div class="link '.$submenu['class_menu'].'">';
															echo $submenu['images']['link'];
														echo '</div>';
												}
												elseif($submenu['content_type'] == '5') {
													if(isset($submenu['subcategory']['categories'])){
														echo '<ul class="subcategory '.$submenu['class_menu'].'">';
															foreach($submenu['subcategory']['categories'] as $categories){
																echo'<li>';
																	if($submenu['subcategory']['show_title'] == 1)
																		echo'<a href="'.$categories['href'].'" class="title-submenu '.$submenu['class_menu'].'">'.$categories['name'].'</a>';
																		if($categories['categories'])
																			echo $categories['categories'];
																	if($submenu['subcategory']['show_image'] == 1)
																		echo '<img src="'.$categories['thumb'].'" alt="" >';
																echo '</li>';		
															}
														echo '</ul>';
													}	
												}
												elseif($submenu['content_type'] == '6') {
													if($submenu['productlist']['show_title'] == 1)
														echo '<span class="title-submenu">'.$submenu['name'][$lang_id].'</span>';
														if(isset($submenu['productlist']['products']) && $submenu['productlist']['products']){
															foreach ($submenu['productlist']['products'] as $product) {
															$itempage = (isset($submenu['productlist']['col']) && $submenu['productlist']['col']) ? 12/$submenu['productlist']['col'] : 4;
															echo '<div class="col-sm-'.$itempage.' '.$submenu['class_menu'].'">';
																echo '<div class="product-thumb">';
																	echo '<div class="image">
																		<a href="'.$product['href'].'">
																			<img src="'. $product['thumb'].'" alt="'. $product['name'].'" title="'. $product['name'].'" class="img-responsive" />
																		</a></div>';
																	echo '<div>';
																		echo '<div class="caption">';
																			echo '<h4><a href="'. $product['href'].'">'. $product['name'].'</a></h4>';
																			echo '<p>'. $product['description'].'</p>';
																			if ($product['rating']) {
																				echo '<div class="rating">';
																				for ($i = 1; $i <= 5; $i++) {
																					if ($product['rating'] < $i) {
																						echo '<span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>';
																					} else {
																						echo '<span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>';
																					}
																				}
																		echo '</div>';
																	}
																	if ($product['price']) {
																		echo '<p class="price">';
																		if (!$product['special']) {
																			echo $product['price'];
																	   } else {
																			echo '<span class="price-new">'. $product['special'].'</span> <span class="price-old">'. $product['price'].'</span>';
																	   }
																	   if ($product['tax']) {
																			echo '<span class="price-tax">'. $product['tax'].'</span>';
																		}
																		echo '</p>';
																	}
																  echo '</div>';
																echo '</div>';
															  echo '</div>';
															echo '</div>';
														 }
													}
												}
											echo '</div>';
										}
										echo '</div>';
									echo '</div>';
								echo '</div>';
							}
						echo "</li>";
						echo "\n";
						//End Megamenu
						}}

						?>
					</ul>
					</div>
				</div>
			</div>
		</div>
	</nav>
	<?php  if($ustawienia['orientation'] == 1){
		echo '</div>';
	}
	?>
</div>

<?php if($ustawienia['orientation'] == 1) { ?>
<script type="text/javascript">
	$(document).ready(function() {
		var itemver =  <?php echo $ustawienia['show_itemver'];?> ;
		if(itemver <= $( ".vertical ul.megamenu >li" ).length)
			$('.vertical ul.megamenu').append('<li class="loadmore"><i class="fa fa-plus-square-o"></i><span class="more-view"> More Categories</span></li>');
		$('.horizontal ul.megamenu li.loadmore').remove();

		var show_itemver = itemver-1 ;
		$('ul.megamenu > li.item-vertical').each(function(i){
			if(i>show_itemver){
					$(this).css('display', 'none');
			}
		});
		$(".megamenu .loadmore").click(function(){
			if($(this).hasClass('open')){
				$('ul.megamenu li.item-vertical').each(function(i){
						if(i>show_itemver){
								$(this).slideUp(200);
								$(this).css('display', 'none');
						}
				});
				$(this).removeClass('open');
				$('.loadmore').html('<i class="fa fa-plus-square-o"></i><span class="more-view">More Categories</span>');
			}else{
				$('ul.megamenu li.item-vertical').each(function(i){
						if(i>show_itemver){
								$(this).slideDown(200);
						}
				});
				$(this).addClass('open');
				$('.loadmore').html('<i class="fa fa-minus-square-o"></i><span class="more-view">Close Categories</span>');
			}
		});
	});
</script>
<?php } ?>
