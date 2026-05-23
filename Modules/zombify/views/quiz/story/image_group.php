<div class="zf-type-wrapper"><i class="zf-icon zf-icon-image"></i></div>

<div class="zf-item-wrapper">
    <?php
    echo $this->renderField(['story', 'image','image_title'], $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true));

    echo $this->renderField(['story', 'image','image_image'], $field_name_prefix, $name_index, $data);
    echo $this->renderField(['story', 'image','image_original_source'], $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true));
    echo $this->renderField(['story', 'image','image_image_credit'], $field_name_prefix, $name_index, $data, array("placeholder"=> esc_attr( __("http://example.com", "zombify"))), '', array('showPlaceholder'=>false, 'showLabel'=>true));
    echo $this->renderField(['story', 'image','image_image_credit_text'], $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true));

    echo $this->renderField(['story', 'image','image_caption'], $field_name_prefix, $name_index, $data, array("class" => "zf-wysiwyg-light"), '', array('showPlaceholder'=>false, 'showLabel'=>true));
    ?>
</div>