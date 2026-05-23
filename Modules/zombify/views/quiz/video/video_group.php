<?php
$disable_mp4 = zf_get_option('zombify_disable_mp4_upload', 0);
?>
<div class="zf-form-group zf-form-group_media">
    <div class="zf-media-uploader" data-format="image">
        <?php
        if( !$disable_mp4 ) {
            ?>
            <div class="zf-media-type">
                <label class="zf-checkbox-format">
                    <?php
                    echo $this->renderField($this->fieldPath(['video', 'mediatype'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), 'fields/onlyradio', array("value" => "image", "format" => "image", "class" => "zombify_medatype_radio", "label" => '<span class="zf-toggle"><span class="zf-icon zf-icon-type-video"></span><span class="zf-text">' . __("Video file", "zombify") . '</span></span>', 'checked' => true), $path_prefix);
                    ?>
                </label>
                <span class="_or"><?php esc_html_e('Or', 'zombify'); ?></span>
                <label class="zf-checkbox-format">
                    <?php
                    echo $this->renderField($this->fieldPath(['video', 'mediatype'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), 'fields/onlyradio', array("value" => "embed", "format" => "embed", "class" => "zombify_medatype_radio", "label" => '<span class="zf-toggle"><span class="zf-icon zf-icon-embed"></span><span class="zf-text">' . __("Embed / URL", "zombify") . '</span></span>', 'checked' => false), $path_prefix);
                    ?>
                </label>
            </div>
        <?php
        } else {
            echo $this->renderField($this->fieldPath(['video', 'mediatype'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), 'fields/onlyradio', array("value" => "embed", "format" => "embed", "class" => "zombify_medatype_radio zf-hidden", 'style' => 'display:none', "label" => '', 'checked_important' => true), $path_prefix);
        }
            ?>
        <div class="">
            <?php
            if( !$disable_mp4 ) {
                ?>
                <div class="zombify_medatype_image">
                    <?php
                    echo $this->renderField($this->fieldPath(['video', 'videofile'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array("data-zombify-field-video" => "1"), '', array('aliased_group_path' => $aliased_group_path, 'field_name_prefix' => $field_name_prefix, 'get_url_field_path' => array('video', 'video_external')), $path_prefix);
                    ?>
                </div>
            <?php
            }
                ?>
            <div class="zombify_medatype_embed">
                <div class="zf-embed">
                    <?php
                    echo $this->renderField($this->fieldPath(['video','embed_url'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array('data-embed-url' => '1', 'data-embed-sources' => 'facebook,youtube,vimeo,dailymotion,mp4,ok'), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
                    echo $this->renderField($this->fieldPath(['video','embed_thumb'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
                    echo $this->renderField($this->fieldPath(['video','embed_type'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
                    echo $this->renderField($this->fieldPath(['video','embed_variables'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
                    ?>
                    <div class="zf-note"><?php esc_html_e("Paste a YouTube, Vimeo or Dailymotion link or embed code.", "zombify"); ?></div>
                    <div class="zf-embed-formats">
                        <i class="zf-icon zf-icon-facebook" title="Facebook"></i>
                        <i class="zf-icon zf-icon-youtube" title="YouTube"></i>
                        <i class="zf-icon zf-icon-vimeo" title="Vimeo"></i>
                        <i class="zf-icon zf-icon-dailymotion" title="Dailymotion"></i>
						<i class="zf-icon zf-icon-instagram" title="Instagram"></i>
                        <i class="zf-icon zf-icon-x" title="X"></i>
                        <i class="zf-icon zf-icon-coubcom" title="Coub"></i>
                        <i class="zf-icon zf-icon-twitch" title="Twitch"></i>
                        <i class="zf-icon zf-icon-vk" title="VK"></i>
                        <i class="zf-icon zf-icon-odnoklassniki" title="Odnoklassniki"></i>
                        <i class="zf-icon zf-icon-tiktok" title="TikTok"></i>
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
echo $this->renderField($this->fieldPath(['video','original_source'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
echo $this->renderField($this->fieldPath(['video','video_credit'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array("placeholder"=> esc_attr( __("http://example.com", "zombify"))), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
echo $this->renderField($this->fieldPath(['video','video_credit_text'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);

echo $this->renderField($this->fieldPath(['video','video_description'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array('class' => 'zf-wysiwyg-light'), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
?>