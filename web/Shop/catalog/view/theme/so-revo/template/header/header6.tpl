
<header id="header" class=" variant typeheader-<?php echo isset($typeheader) ? $typeheader : '1'?>">
	<!-- HEADER TOP -->
	<div class="header-top compact-hidden">
		<div class="container">
			<div class="row">
				<div class="header-top-left  col-lg-6  hidden-sm col-md-5 hidden-xs">
					<ul class="list-inlines">
						<?php if($welcome_message_status):?>
						<li class="hidden-xs" >
							<?php
								if (isset($welcome_message) && is_string($welcome_message)) {
									echo html_entity_decode($welcome_message, ENT_QUOTES, 'UTF-8');
								} else {echo 'Default welcome msg!';}
							?>
						</li>
						<?php endif; ?>
					</ul>
				</div>
				<div class="header-top-right collapsed-block col-lg-6 col-sm-12 col-md-7 col-xs-12 ">
					<h5 class="tabBlockTitle hidden-lg hidden-sm hidden-md visible-xs"><?php echo $text_more; ?><a class="expander " href="#TabBlock-1"><i class="fa fa-angle-down"></i></a></h5>
					<div  class="tabBlock" id="TabBlock-1">
						<ul class="top-link list-inline">
							<?php if ($logged) { ?>
							<li class="logout"><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
							<?php } else { ?>
							<li class="login"><a href="<?php echo $login; ?>"></i> <?php echo $text_login; ?></a></li>
							<?php } ?>
							<li class="account" id="my_account"><a href="<?php echo $account; ?>" title="<?php echo $text_account; ?>" class="btn-xs dropdown-toggle" data-toggle="dropdown"> <span><?php echo $text_account; ?></span> <span class="fa fa-angle-down"></span></a>
								<ul class="dropdown-menu ">
									<?php if ($logged) { ?>
									<li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
									<li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
									<li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
									<li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
									<li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
									<?php } else { ?>
									<li><a href="<?php echo $register; ?>"><i class="fa fa-user"></i> <?php echo $text_register; ?></a></li>
									<li><a href="<?php echo $login; ?>"><i class="fa fa-pencil-square-o"></i> <?php echo $text_login; ?></a></li>
									<?php } ?>
								</ul>
							</li>
							<?php if($checkout_status):?><li class="checkout"><a href="<?php echo $checkout; ?>" class="btn-link" title="<?php echo $text_checkout; ?>"><span ><?php echo $text_checkout; ?></span></a></li><?php endif; ?>
							<!-- LANGUAGE CURENTY -->
							<?php if($lang_status):?>
								
								<li ><?php echo $language; ?></li>
								<li class="currency" > <?php echo $currency; ?> </li>
							<?php endif; ?>
							
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- HEADER CENTER -->
	<div class="header-center compact-hidden">
		<div class="container">
			<div class="row">
				<!-- LOGO -->
				<div class="navbar-logo col-lg-3 col-md-3 col-sm-12 col-xs-12">
				   <?php  $this->soconfig->get_logo();?>
				</div>
				<div class="header-center-right col-lg-9 col-md-9 col-sm-12 col-xs-12">
					<!-- BOX CONTENT MENU -->
					<div class="header_search">
						<?php  echo $content_search; ?>
					</div>
					<div class="block_link hidden-sm hidden-xs">
						<a href="<?php echo $wishlist; ?>" id="wishlist-total" class="top-link-wishlist" title="<?php echo $text_wishlist; ?>"><i class="fa fa-heart-o"></i></a>
					</div>
					
					<div class="block-cart">
						<div class="shopping_cart">
						 	<?php echo $cart; ?>
						</div>
					</div>
					<div class="phone-header pull-right">
						<?php if($phone_status):?>
						<div class="telephone hidden-xs" >
							<?php
								if (isset($contact_number) && is_string($contact_number)) {
									echo html_entity_decode($contact_number, ENT_QUOTES, 'UTF-8');
								} else {echo 'Telephone No';} 
							?>
							
						</div>
						<?php endif; ?>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- HEADER BOTTOM -->
	<div class="header-bottom ">
		<div class="container">
			<div class="header-bottom-inner">
				<?php echo $content_menu; ?>	
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