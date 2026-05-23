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
            echo $this->renderField($this->fieldPath(['questions','question'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
            ?>
            <div class="zf-form-group zf-form-group_media">
                <div class="zf-media-uploader" data-format="embed">
                    <div class="zf-media-type">
                        <label class="zf-checkbox-format">
                            <?php
                            echo $this->renderField($this->fieldPath(['questions','mediatype'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), 'fields/onlyradio', array("value" => "image", "format" => "image", "class"=>"zombify_medatype_radio", "label" => '<span class="zf-toggle"><span class="zf-icon zf-icon-image"></span><span class="zf-text">'.__('Question Image', 'zombify').'</span></span>', 'checked' => true), $path_prefix);
                            ?>
                        </label>
                        <span class="_or"><?php esc_html_e( 'Or', 'zombify' ); ?></span>
                        <label class="zf-checkbox-format">
                            <?php
                            echo $this->renderField($this->fieldPath(['questions','mediatype'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), 'fields/onlyradio', array("value" => "embed", "format" => "embed", "class"=>"zombify_medatype_radio", "label" => '<span class="zf-toggle"><span class="zf-icon zf-icon-embed"></span><span class="zf-text">'.__('Embed / URL', 'zombify').'</span></span>', 'checked' => false), $path_prefix);
                            ?>
                        </label>
                    </div>
                    <div class="">
                        <div class="zombify_medatype_image">
                            <?php
                            echo $this->renderField($this->fieldPath(['questions','image'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
                            ?>
                        </div>
                        <div class="zombify_medatype_embed">
                            <div class="zf-embed">
                                <?php
                                echo $this->renderField($this->fieldPath(['questions','embed_url'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array('data-embed-url' => '1'), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
                                echo $this->renderField($this->fieldPath(['questions','embed_thumb'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
                                echo $this->renderField($this->fieldPath(['questions','embed_type'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
                                echo $this->renderField($this->fieldPath(['questions','embed_variables'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
                                ?>
                                <div class="zf-note"><?php esc_html_e("Paste a YouTube, Instagram or SoundCloud link or embed code.", "zombify") ?></div>
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
            echo $this->renderField($this->fieldPath(['questions','original_source'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>true, 'showLabel'=>false), $path_prefix);
            echo $this->renderField($this->fieldPath(['questions','image_credit'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array("placeholder"=> esc_attr( __("http://example.com", "zombify"))), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
            echo $this->renderField($this->fieldPath(['questions','image_credit_text'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);

            echo $this->renderField($this->fieldPath(['questions','description'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array('class' => 'zf-wysiwyg-light'), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
            ?>
        </div>

    <div class="zf-head">
        <h4><?php esc_html_e("Answers", "zombify"); ?></h4>
    </div>
    <div class="zf-body">
        <div class="zf-form-group zf-ui-checkbox js-zf-answer-format">
            <label><?php esc_html_e("Answer Format", "zombify"); ?></label>
            <label class="zf-checkbox-format">
                <?php
                echo $this->renderField($this->fieldPath(['questions','answers_format'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), 'fields/onlyradio', array("value" => "3up", "format" => "3up", "label" => '<span class="zf-toggle"><span class="zf-icon zf-icon-up3"></span>'.__('3 up', 'zombify').'</span>', 'checked' => true), $path_prefix);
                ?>
            </label>
            <label class="zf-checkbox-format">
                <?php
                echo $this->renderField($this->fieldPath(['questions','answers_format'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), 'fields/onlyradio', array("value" => "2up", "format" => "2up", "label" => '<span class="zf-toggle"><span class="zf-icon zf-icon-up2"></span>'.__('2 up', 'zombify').'</span>', 'checked' => false), $path_prefix);
                ?>
            </label>
            <label class="zf-checkbox-format">
                <?php
                echo $this->renderField($this->fieldPath(['questions','answers_format'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), 'fields/onlyradio', array("value" => "text", "format" => "text", "label" => '<span class="zf-toggle"><span class="zf-icon zf-icon-text"></span>'.__('Text', 'zombify').'</span>', 'checked' => false), $path_prefix);
                ?>
            </label>
            <label class="zf-checkbox-format">
                <?php
                echo $this->renderField($this->fieldPath(['questions','answers_format'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), 'fields/onlyradio', array("value" => "input", "format" => "input", "label" => '<span class="zf-toggle"><span class="zf-icon zf-icon-input"></span>'.__('Input', 'zombify').'</span>', 'checked' => false), $path_prefix);
                ?>
            </label>
        </div>
        <?php
        /* We need to store flag for trivia quiz correct answer to change `previous selected correct` accordingly in case it was deleted on edit */
        $questions_correct_options = array( 'data-previous-correct-pointer' => 'true' );
        echo $this->renderField($this->fieldPath(['questions','correct'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, $questions_correct_options, 'fields/hidden', array(), $path_prefix);
        ?>
        <div class="zf-answers-box" data-format="3up">
            <div class="zf-answers_container">
                <?php echo $this->renderGroups($this->fieldPath(['questions', 'answers'], $aliased_group_path, $path_prefix), $group_name_prefix, $data, 'create', $path_prefix, $aliased_group_path); ?>
            </div>
            <?php echo $this->renderAddGroupButton($this->fieldPath(['questions', 'answers'], $aliased_group_path, $path_prefix), esc_html__('Add answer', 'zombify'), '', array('beforeText' => '<i class="zf-icon zf-icon-add"></i>', 'class' => 'zf-add-answer-button')) ?>
        </div>
    </div>
    <div class="zf-head">
        <h4><?php esc_html_e("Reveal When Answered", "zombify"); ?>: </h4>
    </div>
    <div class="zf-body">
        <div class="zf-row">
            <div class="zf-col-lg-1">
                <?php
                echo $this->renderField($this->fieldPath(['questions', 'after_answer_image'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
                ?>
            </div>
            <div class="zf-col-lg-3">
                <?php if( isset($data['after_answer_description']) && $data['after_answer_description'] !== '' ) {
                    $zf_result_wysiwyg_light = 'zf-result-wysiwyg-light active';
                } else {
                    $zf_result_wysiwyg_light = 'zf-result-wysiwyg-light';
                } ?>
                <?php
                echo $this->renderField($this->fieldPath(['questions','after_answer_title'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);

                echo $this->renderField($this->fieldPath(['questions','after_answer_description'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array('class' => $zf_result_wysiwyg_light), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
                echo $this->renderField($this->fieldPath(['questions','after_answer_original_source'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>true, 'showLabel'=>false), $path_prefix);
                echo $this->renderField($this->fieldPath(['questions','after_answer_image_credit'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array("placeholder"=> esc_attr( __("http://example.com", "zombify"))), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
                echo $this->renderField($this->fieldPath(['questions','after_answer_image_credit_text'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
                ?>
            </div>
        </div>
    </div>
</div>