
<header id="header" class=" variant typeheader-<?php echo isset($typeheader) ? $typeheader : '1'?>">
	<!-- HEADER CENTER -->
	<div class="header-center">
		<div class="container">
			<div class="row">
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 header-left">
					<div class="menu-wrapper pull-left">
						<?php echo $content_menu1; ?>
					</div>
					<div class="link-wrapper pull-left">
						<?php if($lang_status):?>
							<div class="link language" ><?php echo $language; ?></div>
							<div class="link currency" > <?php echo $currency; ?> </div>
						<?php endif; ?>
					</div>
				</div>

				<div class="col-lg-4 col-md-3 col-sm-3 col-xs-12 navbar-logo">
				   <?php  $this->soconfig->get_logo();?>
				</div>
				<div class="col-lg-4 col-md-5 col-sm-8 col-xs-8 header-right">
					<div class="header-cart">
						<div class="shopping_cart">
						 	<?php echo $cart; ?>
						</div>
					</div>
					<div class="header-link collapsed-block ">
						<h5 class="tabBlockTitle hidden-lg hidden-sm hidden-md visible-xs"><?php echo $text_more; ?><a class="expander " href="#TabBlock-1"><i class="fa fa-angle-down"></i></a></h5>
						<div  class="tabBlock" id="TabBlock-1">

								<?php if($phone_status):?>
								<div class="telephone hidden-xs" >
									<?php
										if (isset($contact_number) && is_string($contact_number)) {
											echo html_entity_decode($contact_number, ENT_QUOTES, 'UTF-8');
										} else {echo 'Telephone No';} 
									?>
									
								</div>
								<?php endif; ?>
							<ul class="top-link list-inline">
								<li class="hidden-xs"><a href="<?php echo $login; ?>"><i class="fa fa-lock"></i> <?php echo $text_login; ?></a></li>
								<li class="account" id="my_account"><a href="<?php echo $account; ?>" title="<?php echo $text_account; ?>" class="btn-xs dropdown-toggle" data-toggle="dropdown"> <span><?php echo $text_account; ?></span> <span class="fa fa-angle-down"></span></a>
									<ul class="dropdown-menu ">
										
										<?php if ($logged) { ?>
										<li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
										<li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
										<li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
										<li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
										<li class="wishlist"><a href="<?php echo $wishlist; ?>" id="wishlist-total" class="top-link-wishlist" title="<?php echo $text_wishlist; ?>"><span><?php echo $text_wishlist; ?></span></a></li>
										<li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
										
										<?php } else { ?>
										<li><a href="<?php echo $register; ?>"><i class="fa fa-user"></i> <?php echo $text_register; ?></a></li>
										<li><a href="<?php echo $login; ?>"><i class="fa fa-pencil-square-o"></i> <?php echo $text_login; ?></a></li>
										<?php } ?>
									</ul>
								</li>
							
							</ul>
						</div>
					</div>
					<div class="header-search">
						<?php  echo $content_search; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	
	
	<!-- Navbar switcher -->
	<?php if (!isset($toppanel_status) || $toppanel_status != 0) : ?>
	<?php if (!isset($toppanel_type) || $toppanel_type != 2 ) :  ?>
	<div class="navbar-switcher-container">
		<div class="navbar-switcher">
			<span class="i-inactive">
				<i class="fa fa-caret-down"></i>
			</span>
			 <span class="i-active fa fa-times"></span>
		</div>
	</div>
	<?php endif; ?>
	<?php endif; ?>
</header>