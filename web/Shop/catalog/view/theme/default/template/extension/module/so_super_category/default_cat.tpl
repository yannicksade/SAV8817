<div class="category-wrap-cat">
    <?php
    if (!empty($category_parent) && $category_parent != null && $display_slide_category == 1) {
        ?>
        <div class="title-imageslider  sp-cat-title-parent">

            <a title="<?php echo $category_parent['name']; ?>"
               href="<?php echo $category_parent['link']; ?>"
                target="<?php echo $item_link_target?>" >
                <?php echo ($category_title_maxlength != 0 && strlen($category_parent['name']) > $category_title_maxlength) ? utf8_substr(strip_tags(html_entity_decode($category_parent['name'], ENT_QUOTES, 'UTF-8')), 0, $category_title_maxlength) . '..' : $category_parent['name']; ?>
            </a>
        </div>
   <?php } ?>

    <?php $tag = 'cat_slider_' . rand() . time();
    if (!empty($category_tree) && $category_tree != null) {
        ?>
        <div id="<?php echo $tag ?>" class="slider">
		<?php if(isset($category_tree) && count($category_tree) > 0){?>
		 <?php if (!empty($category_parent) && $display_slide_category == 1) {?>
			<?php if ($show_category_type == 0 ){$cls_type=" so_category_type_default";}else{$cls_type = "";}?>
            <div class="cat_slider_inner<?php echo $cls_type;?>">
                <?php
                foreach ($category_tree as $cat_tree) {
					if($category_parent['name'] == $cat_tree['name'])continue;
                    ?>
                    <div class="item">
                        <div class="item-inner">
                            <div class="cat_slider_image">
                                <a href="<?php echo $cat_tree['link']; ?>"
                                   title="<?php echo $cat_tree['name']; ?>" target="<?php echo $item_link_target?>" >
                                    <img src="<?php echo $cat_tree['image']?>" alt="<?php echo $cat_tree['name']?>">
                                </a>
                            </div>

                            <?php if ($display_title_sub_category && count($category_tree) > 1) { ?>
                                <div class="cat_slider_title">
                                    <a href="<?php echo $cat_tree['link']; ?>"
                                       title="<?php echo $cat_tree['name']; ?>" target="<?php echo $item_link_target?>">
                                        <?php echo $cat_tree['name_maxlength']; ?>
                                    </a>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                <?php
                } ?>

            </div>
			 <?php } ?>
		<?php }?>
        </div>
    <?php } ?>

</div>
