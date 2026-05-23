<h3 class="zfTitle"><?php echo $story_data["image_title"]; ?></h3>
<?php
if (isset(zf_array_values($story_data["image_image"])[0]["attachment_id"])) {
    $zf_media_html = zombify_get_img_tag(zf_array_values($story_data["image_image"])[0]["attachment_id"], 'full'); ?>
    <figure class="zfMedia zfimage">
        <?php
        echo $zf_media_html;

        if (isset($story_data["image_image_credit"]) && $story_data["image_image_credit"] != '') { ?>
            <figcaption class="zfCaption">
                <cite class="zfCredit">
					<a href="<?php echo $story_data["image_image_credit"]; ?>"
					   target="_blank"
					   rel="nofollow noopener"><?php echo $story_data["image_image_credit_text"] ? $story_data["image_image_credit_text"] : __('Credit', 'zombify'); ?></a></cite>
            </figcaption>
        <?php } ?>
    </figure>
<?php
}
?>
<?php if ($story_data["image_caption"]) { echo $story_data["image_caption"]; } ?>