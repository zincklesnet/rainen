<?php
$zombify_active_formats = zombify()->get_active_formats();
$zombify_active_formats = array_filter($zombify_active_formats);
?>
<div id="zombify-main-section-front" class="zombify-main-section-front zombify-screen">
    <div class="zf-create-page">
        <h1 class="zf-title"><?php esc_html_e('Choose A Format', 'zombify'); ?></h1>
        <div class="zf-create-box" data-count="<?php echo count($zombify_active_formats); ?>">
            <?php

            $post_types = zombify()->get_active_post_types();

            foreach( $post_types as $post_type_data ){

                $post_type_slug = $post_type_data['post_type_slug'];

                if( $post_type_data['post_type_level'] == 1 ){

                    ?>
                    <div class="zf-item">
                        <div class="zf-wrapper">
                            <a class="zf-link <?php if( !is_user_logged_in() ) echo 'js-authentication'; ?>"
                               href="<?php echo is_user_logged_in() ? add_query_arg('type', $post_type_slug, get_permalink(zf_get_option("zombify_post_create_page"))) : '#sign-in'; ?>"></a>
                            <i class="zf-icon zf-icon-type-<?php echo $post_type_slug; ?>"></i>

                            <div class="zf-item_title"><?php echo $post_type_data['name']; ?></div>
                            <div class="zf-item_description">
                                <?php echo $post_type_data['description']; ?>
                            </div>
                        </div>
                    </div>
                <?php

                } else {

                    ?>
                    <div class="zf-item">
                        <div class="zf-wrapper">
                            <a class="zf-link <?php if( !is_user_logged_in() ) echo 'js-authentication'; ?>"
                               href="<?php echo is_user_logged_in() ? add_query_arg('subtype', $post_type_slug, get_permalink(zf_get_option("zombify_post_create_page"))) : '#sign-in'; ?>"></a>
                            <i class="zf-icon zf-icon-type-<?php echo $post_type_data["icon"]; ?>"></i>

                            <div class="zf-item_title"><?php echo $post_type_data['name']; ?></div>
                            <div class="zf-item_description">
                                <?php echo $post_type_data['description']; ?>
                            </div>
                        </div>
                    </div>
                <?php

                }

            }


            ?>
        </div>

<!--        <a class="zf-create-popup" href="#">Create Popup</a>-->



    </div>
</div>


