<?php
if (!empty($tabs)) {
    ?>
    <div class="spcat-tabs-wrap">
        <span class='spcat-tab-selected'></span>
        <span class='spcat-tab-arrow'>&#9660;</span>
        <ul class="spcat-tabs cf">
            <?php
            foreach ($tabs as $tab) {
                ?>
                <li class="spcat-tab <?php echo isset($tab['sel']) ? '  tab-sel tab-loaded' : ''; ?>"
                    data-active-content=".items-category-<?php echo $tab['category_id']; ?>"
                    data-field_order="<?php echo $tab['category_id']; ?>"
                    >
					<span class="spcat-tab-label">
						<?php echo $tab['title']; ?>
					</span>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>