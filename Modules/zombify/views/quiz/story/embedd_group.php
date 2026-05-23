<div class="zf-type-wrapper"><i class="zf-icon zf-icon-embed"></i></div>

<div class="zf-item-wrapper">
    <?php
    echo $this->renderField(['story', 'embedd','embed_title'], $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true));?>
        <div class="zf-form-group zf-form-group_media">
            <div class="zf-media-uploader" data-format="embed">
                <div class="zombify_medatype_embed">
                    <div class="zf-embed">
                        <?php
                        echo $this->renderField(['story', 'embedd','embed_url'], $field_name_prefix, $name_index, $data, array('data-embed-url' => '1'), '', array('showPlaceholder'=>false, 'showLabel'=>true));
                        echo $this->renderField(['story', 'embedd','embed_thumb'], $field_name_prefix, $name_index, $data, array(), '', array());
                        echo $this->renderField(['story', 'embedd','embed_type'], $field_name_prefix, $name_index, $data, array(), '', array());
                        echo $this->renderField(['story', 'embedd','embed_variables'], $field_name_prefix, $name_index, $data, array(), '', array());
                        ?>
                        <div class="zf-note"><?php esc_html_e("Paste a YouTube, Instagram or SoundCloud link or embed code.", "zombify"); ?></div>
                        <div class="zf-embed-formats">
                            <i class="zf-icon zf-icon-facebook" title="Facebook"></i>
                            <i class="zf-icon zf-icon-youtube" title="YouTube"></i>
                            <i class="zf-icon zf-icon-vimeo" title="Vimeo"></i>
                            <i class="zf-icon zf-icon-dailymotion" title="Dailymotion"></i>
                            <i class="zf-icon zf-icon-instagram" title="Instagram"></i>
                            <i class="zf-icon zf-icon-tiktok" title="TikTok"></i>
                            <i class="zf-icon zf-icon-x" title="X"></i>
                            <i class="zf-icon zf-icon-pinterest-p" title="Pinterest"></i>
                            <i class="zf-icon zf-icon-map-marker" title="Google Maps"></i>
                            <i class="zf-icon zf-icon-type-gif" title="Gif"></i>
                            <i class="zf-icon zf-icon-image" title="Image"></i>
                            <i class="zf-icon zf-icon-soundcloud" title="Soundcloud"></i>
                            <i class="zf-icon zf-icon-spotify" title="Spotify"></i>
                            <i class="zf-icon zf-icon-mixcloud" title="Mixcloud"></i>
                            <i class="zf-icon zf-icon-reddit" title="Reddit"></i>
                            <i class="zf-icon zf-icon-coubcom" title="Coub"></i>
                            <i class="zf-icon zf-icon-imgur" title="Imgur"></i>
                            <i class="zf-icon zf-icon-twitch" title="Twitch"></i>
                            <i class="zf-icon zf-icon-vk" title="VK"></i>
                            <i class="zf-icon zf-icon-odnoklassniki" title="Odnoklassniki"></i>
                            <i class="zf-icon zf-icon-giphy" title="Giphy"></i>
                        </div>
                        <div class="zf-embed-video">
                            <?php echo $this->renderEmbed( $data, false ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    echo $this->renderField(['story', 'embedd','embed_original_source'], $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true));
    echo $this->renderField(['story', 'embedd','embed_credit'], $field_name_prefix, $name_index, $data, array("placeholder"=> esc_attr( __("http://example.com", "zombify"))), '', array('showPlaceholder'=>false, 'showLabel'=>true));
    echo $this->renderField(['story', 'embedd','embed_credit_text'], $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true));
    echo $this->renderField(['story', 'embedd','embed_description'], $field_name_prefix, $name_index, $data, array("class" => "zf-wysiwyg-light"), '', array('showPlaceholder'=>false, 'showLabel'=>true));
    ?>
</div>