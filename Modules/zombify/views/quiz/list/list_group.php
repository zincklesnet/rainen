<div class="zf-inner-wrapper">
    <button class="zf-remove zombify_delete_group <?php if( $groups_count < 2) { echo 'zf-hide-delete-icon'; } ?>">
        <i class="zf-icon-delete"></i>
    </button>
    <div class="zf-sort-area">
        <button class="zf-up js-zf-up"><i class="zf-icon zf-icon-arrow_up"></i></button>
        <button class="zf-down js-zf-down"><i class="zf-icon zf-icon-arrow_down"></i></button>
    </div>
        <div class="zf-body zf-numeric">
            <span class="zf-index"><?php echo $group_num+1; ?></span>
            <?php
            echo $this->renderField($this->fieldPath(['list','title'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
            ?>
            <div class="zf-form-group zf-form-group_media">
                <div class="zf-media-uploader" data-format="embed">
                    <div class="zf-media-type">
                        <label class="zf-checkbox-format">
                            <?php
                            echo $this->renderField($this->fieldPath(['list','mediatype'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), 'fields/onlyradio', array("value" => "image", "format" => "image", "class"=>"zombify_medatype_radio", "label" => '<span class="zf-toggle"><span class="zf-icon zf-icon-image"></span><span class="zf-text">'.__("Image", "zombify").'</span></span>', 'checked' => true), $path_prefix);
                            ?>
                        </label>
                        <span class="_or"><?php esc_html_e("Or", "zombify"); ?></span>
                        <label class="zf-checkbox-format">
                            <?php
                            echo $this->renderField($this->fieldPath(['list','mediatype'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), 'fields/onlyradio', array("value" => "embed", "format" => "embed", "class"=>"zombify_medatype_radio", "label" => '<span class="zf-toggle"><span class="zf-icon zf-icon-embed"></span><span class="zf-text">'.__("Embed / URL", "zombify").'</span></span>', 'checked' => false), $path_prefix);
                            ?>
                        </label>
                    </div>
                    <div class="">
                        <div class="zombify_medatype_image">
                            <?php
                            echo $this->renderField($this->fieldPath(['list','image'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
                            ?>
                        </div>
                        <div class="zombify_medatype_embed">
                            <div class="zf-embed">
                                <?php
                                echo $this->renderField($this->fieldPath(['list','embed_url'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array('data-embed-url' => '1'), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
                                echo $this->renderField($this->fieldPath(['list','embed_thumb'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
                                echo $this->renderField($this->fieldPath(['list','embed_type'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
                                echo $this->renderField($this->fieldPath(['list','embed_variables'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
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
            </div>
            <?php
            echo $this->renderField($this->fieldPath(['list','original_source'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
            echo $this->renderField($this->fieldPath(['list','affiliate'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);

            echo $this->renderField($this->fieldPath(['list','affiliate_url'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array("placeholder"=> esc_attr( __("http://example.com", "zombify"))), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);

            echo $this->renderField($this->fieldPath(['list','image_credit'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array("placeholder"=> esc_attr( __("http://example.com", "zombify"))), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
            echo $this->renderField($this->fieldPath(['list','image_credit_text'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);

            ?>
            <?php
            echo $this->renderField($this->fieldPath(['list','description'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array('class' => 'zf-wysiwyg-light'), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
            ?>
        </div>
</div>