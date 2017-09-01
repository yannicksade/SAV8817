
<footer class="footer-container typefooter-<?php echo isset($typefooter) ? $typefooter : '1'?>">
	<!-- FOOTER TOP -->
	<?php if ($footer_block11) : ?>
	<div class="footer-top">
		<div class="container">
			<div class="col-lg-12">
				<?php echo $footer_block11; ?> 
			</div>	
		</div> 
	</div>
	<?php endif; ?>
	<!-- FOOTER CENTER -->
	<div class="footer-center">
		<div class="footer-center-1 container">
			<div class="row">
				<?php if ($footer_block10) : ?>	
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 item-ft">
					<?php echo $footer_block10; ?>
				</div>
				<?php endif; ?>	
				
				
				<?php if ($footer_block2) : ?>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 footer-contact item-ft">
					<?php echo $footer_block2; ?>
				</div>
				<?php endif; ?>
				<?php if ($informations) : ?>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 item-ft">
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
				<?php if ($footer_block12) : ?>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 footer-newletter item-ft">
					<?php echo $footer_block12; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
		
	</div>
				
	<!-- FOOTER BOTTOM -->
	<div class="footer-bottom ">
		<div class="container">
			<div class="row">
				<?php $col_copyright = ($imgpayment_status) ? 'col-sm-6' : 'col-sm-12'?>
				<div class="<?php echo $col_copyright;?> copyright">
					<?php 
					$datetime = new DateTime();
					$cur_year	= $datetime->format('Y');
					echo (!isset($copyright) || !is_string($copyright) ? $powered : str_replace('{year}', $cur_year,html_entity_decode($copyright, ENT_QUOTES, 'UTF-8')));?>
				</div>

				<?php if (isset($imgpayment_status) && $imgpayment_status != 0) : ?>
				<div class="col-sm-6 paymen">
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