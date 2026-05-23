<div id="zfWrap">
    <?php
    if( $data["readyimage"] ) {
        ?>
        <figure class="zfMedia zfimage">
            <img src="<?php echo $data["readyimage"]; ?>" alt="">
            <?php
            if (isset($data["image_credit"])) { ?>
                <figcaption class="zfCaption">
                    <cite class="zfCredit"><?php zf_showCredit($data["image_credit"], $data["image_credit_text"]); ?></cite>
                </figcaption>
            <?php } ?>
        </figure>
    <?php
    }
    ?>
    <?php echo $data["image_description"]; ?>

    <?php do_action( 'zombify_after_post_layout' ); ?>
</div>