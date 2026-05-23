<h3 class="zfTitle"><?php echo $story_data["embed_title"]; ?></h3>
<?php
if ($story_data["embed_url"]) {
    ?>
    <figure class="zfMedia zfembed">
        <div class="zf-embedded-url"><?php echo Zombify_BaseQuiz::renderEmbed($story_data, true); ?></div>
        <?php if (isset($story_data["embed_credit"]) && $story_data["embed_credit"] != '') { ?>
            <figcaption class="zfCaption"><cite class="zfCredit"><a href="<?php echo $story_data["embed_credit"]; ?>"
                                                                    target="_blank"
                                                                    rel="nofollow noopener"><?php echo $story_data["embed_credit_text"] ? $story_data["embed_credit_text"] : __('Credit', 'zombify'); ?></a></cite>
            </figcaption>
        <?php } ?>
    </figure>
<?php
}

if ($story_data["embed_description"]) {
    ?>
    <?php echo $story_data["embed_description"]; ?>
<?php
}
?>