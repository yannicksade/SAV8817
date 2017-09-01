<?php 
if (!isset($countdown_status) || ($countdown_status)) : 
    if ($product['special']) : 
		
		$special_end_date = $product['special_end_date'];
		
        if ($special_end_date) :
			
            $full_date = explode("-", $special_end_date);
            $year_end = $full_date[0];
			
            if($full_date[1] < 10) {
                $month_end = (int)$full_date[1];
            } else {
                $month_end = $full_date[1];
            }
            $day_end = $full_date[2];
            if ($day_end !== 0 && $year_end !==0 && $month_end !== 0) :

			
                if (isset($countdown_title_day) && $countdown_title_day != ''){
                    $label_day = $countdown_title_day;
                } else {
                    $label_day = 'Day';
                }

                if (isset($countdown_title_hour) && $countdown_title_hour != ''){
                    $label_hour = $countdown_title_hour;
                } else {
                    $label_hour = 'Hour';
                }
                if (isset($countdown_title_minute) && $countdown_title_minute != ''){
                    $label_minute = $countdown_title_minute;
                } else {
                    $label_minute = 'Min';
                }
                if (isset($countdown_title_second) && $countdown_title_second != ''){
                    $label_second = $countdown_title_second;
                } else {
                    $label_second = 'Sec';
                }
                ?>
				
                <div class="countdown_box">
                    <div class="countdown_inner">
                        <div class="title "><?php echo (isset($countdown_title) && $countdown_title != '' ? $countdown_title : 'This limited  offer ends in:'); ?></div>
						 <script type="text/javascript">
						 $(function () {
							var austDay = new Date(<?php echo $year_end; ?>, <?php echo $month_end; ?>- 1 , <?php echo $day_end; ?>);
							$('.defaultCountdown-<?php echo $product['product_id']; ?>').countdown(austDay, function(event) {
								var $this = $(this).html(event.strftime(''
								   + '<div class="time-item time-day"><div class="num-time">%D</div><div class="name-time"><?php echo $label_day; ?> </div></div>'
								   + '<div class="time-item time-hour"><div class="num-time">%H</div><div class="name-time"><?php echo $label_hour; ?> </div></div>'
								   + '<div class="time-item time-min"><div class="num-time">%M</div><div class="name-time"><?php echo $label_minute; ?> </div></div>'
								   + '<div class="time-item time-sec"><div class="num-time">%S</div><div class="name-time"><?php echo $label_second; ?> </div></div>'));
							});
							
							
						});
						</script>
                        <div class="defaultCountdown-<?php echo $product['product_id']; ?>"></div>
                    </div>
					
                </div>
				
            <?php
            endif;
        endif;
    endif;
endif;
?>


