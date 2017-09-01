<?php echo $header; ?>

<div class="container contacts">
 <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="main">
   
    <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>">
    <?php echo $content_top; ?>
      <div class="info-contact row">
        <div class="col-sm-6 col-xs-12 info-store">
          <?php if ($image) { ?>
            <div id="map-canvas"><img src="<?php echo $image; ?>" alt="<?php echo $store; ?>" title="<?php echo $store; ?>" /></div>
          <?php } ?>
          <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBf-EZpcLV72omKDDxOlhG6-i8Ga8NenRo"></script>
            <?php if ($geocode) { ?>
              <script>
                function initialize() {
                  var myLatlng = new google.maps.LatLng(<?php echo $geocode;?>);
                  var mapOptions = {
                    zoom: 16,
                    zoomControl: false,
                    scaleControl: false,
                    scrollwheel: false,
                    disableDoubleClickZoom: true,
                    center: myLatlng
                  }
                  var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
                  var marker = new google.maps.Marker({
                      position: myLatlng,
                      map: map,
                      title: 'Furnicom!'
                  });
                } 
                google.maps.event.addDomListener(window, 'load', initialize);  
              </script>
              <div id="map-canvas"></div>
            <?php } ?>  
        </div>
        <div class="col-sm-6 col-xs-12 contact-form">
           <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <fieldset>
              <legend><h2><?php echo $text_contact; ?></h2></legend>
              <p>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam</p>
              <div class="form-group required">
                <div class="col-sm-12">
                  <input type="text" name="name" value="<?php echo $name; ?>" id="input-name" class="form-control" placeholder="<?php echo $entry_name; ?>*"/>
                  <?php if ($error_name) { ?>
                  <div class="text-danger"><?php echo $error_name; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group required">
                <div class="col-sm-12">
                  <input type="text" name="email" value="<?php echo $email; ?>" id="input-email" class="form-control" placeholder="<?php echo $entry_email; ?>*" />
                  <?php if ($error_email) { ?>
                  <div class="text-danger"><?php echo $error_email; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group required">
                <div class="col-sm-12">
                  <textarea name="enquiry" rows="10" id="input-enquiry" placeholder="<?php echo $entry_enquiry; ?>*" class="form-control"><?php echo $enquiry; ?></textarea>
                  <?php if ($error_enquiry) { ?>
                  <div class="text-danger"><?php echo $error_enquiry; ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php echo $captcha; ?>
            </fieldset>
            <div class="buttons">
              <div class="pull-left">
                <button class="btn btn-info" type="submit"><span><?php echo $button_submit; ?></span></button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>
