<?php
$disable_mp3 = zf_get_option('zombify_disable_mp3_upload', 0);
?>
<div class="zf-form-group zf-form-group_media">
    <div class="zf-media-uploader" data-format="image">
        <?php
        if( !$disable_mp3 ) {
            ?>
            <div class="zf-media-type">
                <label class="zf-checkbox-format">
                    <?php
                    echo $this->renderField($this->fieldPath(['audio', 'mediatype'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), 'fields/onlyradio', array("value" => "image", "format" => "image", "class" => "zombify_medatype_radio", "label" => '<span class="zf-toggle"><span class="zf-icon zf-icon-type-video"></span><span class="zf-text">' . __("Audio file", "zombify") . '</span></span>', 'checked' => true), $path_prefix);
                    ?>
                </label>
                <span class="_or"><?php esc_html_e('Or', 'zombify'); ?></span>
                <label class="zf-checkbox-format">
                    <?php
                    echo $this->renderField($this->fieldPath(['audio', 'mediatype'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), 'fields/onlyradio', array("value" => "embed", "format" => "embed", "class" => "zombify_medatype_radio", "label" => '<span class="zf-toggle"><span class="zf-icon zf-icon-embed"></span><span class="zf-text">' . __("Embed / URL", "zombify") . '</span></span>', 'checked' => false), $path_prefix);
                    ?>
                </label>
            </div>
        <?php
        } else {
            echo $this->renderField($this->fieldPath(['audio', 'mediatype'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), 'fields/onlyradio', array("value" => "embed", "format" => "embed", "class" => "zombify_medatype_radio zf-hidden", 'style' => 'display:none', "label" => '', 'checked_important' => true), $path_prefix);
        }
        ?>
        <div class="">
            <?php
            if( !$disable_mp3 ) {
                ?>
                <div class="zombify_medatype_image">
                    <?php
                    echo $this->renderField($this->fieldPath(['audio', 'videofile'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array("data-zombify-field-video" => "1"), '', array('aliased_group_path' => $aliased_group_path, 'field_name_prefix' => $field_name_prefix, 'get_url_field_path' => array('audio', 'video_external')), $path_prefix);
                    ?>
                </div>
                <?php
            }
            ?>
            <div class="zombify_medatype_embed zombify_embed_margin">
                <div class="zf-embed">
                    <?php
                    echo $this->renderField($this->fieldPath(['audio','embed_url'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array('data-embed-url' => '1', 'data-embed-sources' => 'soundcloud,mixcloud'), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
                    echo $this->renderField($this->fieldPath(['audio','embed_thumb'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
                    echo $this->renderField($this->fieldPath(['audio','embed_type'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
                    echo $this->renderField($this->fieldPath(['audio','embed_variables'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
                    ?>
                    <div class="zf-note"><?php esc_html_e("Paste a SoundCloud or MixCloud link or embed code.", "zombify") ?></div>
                    <div class="zf-embed-formats">
                        <i class="zf-icon zf-icon-soundcloud" title="Soundcloud"></i>
                        <i class="zf-icon zf-icon-mixcloud" title="Mixcloud"></i>
                        <i class="zf-icon zf-icon-spotify" title="Spotify"></i>
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
echo $this->renderField($this->fieldPath(['audio','original_source'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
echo $this->renderField($this->fieldPath(['audio','audio_credit'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array("placeholder"=> esc_attr( __("http://example.com", "zombify"))), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
echo $this->renderField($this->fieldPath(['audio','audio_credit_text'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);

echo $this->renderField($this->fieldPath(['audio','audio_description'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array('class' => 'zf-wysiwyg-light'), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
?>
