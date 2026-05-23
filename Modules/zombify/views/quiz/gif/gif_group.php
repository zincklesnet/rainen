<div class="zf-form-group zf-form-group_media">
    <div class="zf-media-uploader" data-format="image">
        <div class="">
            <div class="zombify_medatype_image">
                <?php
	                echo $this->renderField($this->fieldPath(['gif','image_image'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array('id'=>'image_image'), '', array(), $path_prefix);
	                echo $this->renderField($this->fieldPath(['gif','original_source'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
	                echo $this->renderField($this->fieldPath(['gif','image_credit'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array("placeholder"=> esc_attr( __("http://example.com", "zombify")), "rel"=>"nofollow"), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
	                echo $this->renderField($this->fieldPath(['gif','image_credit_text'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix);
                ?>
            </div>
        </div>
    </div>
</div>
<?php echo $this->renderField($this->fieldPath(['gif','image_description'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array('class' => 'zf-wysiwyg-light'), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix); ?>