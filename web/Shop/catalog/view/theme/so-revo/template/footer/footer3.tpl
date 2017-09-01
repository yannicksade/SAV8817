
<footer class="footer-container typefooter-<?php echo isset($typefooter) ? $typefooter : '1'?>">
	<!-- FOOTER TOP -->
	<?php if ($footertop) : ?>
	<div class="footer-top">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
				<?php echo $footertop; ?> 
				</div>
			</div>
		</div> 
	</div>
	<?php endif; ?>
	<!-- FOOTER CENTER -->
	<div class="footer-center">
		<div class="footer-center-1 container">
			<div class="row">
				<?php if ($footer_block1) : ?>	
				<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
					<?php echo $footer_block1; ?>
				</div>
				<?php endif; ?>	
				<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
					<div class="module clearfix">
						<h3 class="footertitle"><?php echo $text_account; ?></h3>
						<div  class="modcontent" >
							<ul class="menu">
								<li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
						        <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
						        <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
								<li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
						        <li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
							</ul>
						</div>
					</div>
				</div>
				<?php if ($informations) : ?>
				<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
					<div class="module clearfix">
						<h3 class="footertitle"><?php echo $text_information; ?></h3>
						<div  class="modcontent" >
							<ul class="menu">
								<?php foreach ($informations as $information) { ?>
								<li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 footer-extra">
					<div class="module clearfix">
						<h3 class="footertitle"><?php echo $text_extra; ?></h3>
						<div  class="modcontent" >
							<ul class="menu">
								<li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
								<li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
								<li><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
								<li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
								<li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
							</ul>
						</div>
					</div>
				</div>
				<?php if ($footer_block2) : ?>
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 footer-contact">
					<?php echo $footer_block2; ?>
				</div>
				<?php endif; ?>
			
			</div>
		</div>
		
		<div class="footer-center-2">
			<div class="container">
				<div class="row">
					<?php if ($footer_block4) : ?>
					<div class="footer-newsletter col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<?php echo $footer_block4; ?>	
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="footer-center-3">
			<div class="container">
				<?php if ($footer_block6) : ?>
				<div class="block-socials2">
					<?php echo $footer_block6; ?>	
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
				
	<!-- FOOTER BOTTOM -->
	<div class="footer-bottom ">
		<div class="container">
			<div class="row">
				<?php $col_copyright = ($imgpayment_status) ? 'col-sm-12' : 'col-sm-12'?>
				<div class="<?php echo $col_copyright;?> copyright">
					<?php 
					$datetime = new DateTime();
					$cur_year	= $datetime->format('Y');
					echo (!isset($copyright) || !is_string($copyright) ? $powered : str_replace('{year}', $cur_year,html_entity_decode($copyright, ENT_QUOTES, 'UTF-8')));?>
				</div>

				<?php if (isset($imgpayment_status) && $imgpayment_status != 0) : ?>
				<div class="col-sm-12 paymen">
					<?php
					if ((isset($imgpayment) && $imgpayment != '') ) { ?>
						<img src="image/<?php echo  $imgpayment ?>"  alt="imgpayment">
					<?php } ?>
				</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</footer>