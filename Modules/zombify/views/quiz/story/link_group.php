<div class="zf-type-wrapper"><i class="zf-icon zf-icon-link"></i></div>

<div class="zf-item-wrapper">
    <?php
    echo $this->renderField(['story', 'link','link_headline'], $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true));
    echo $this->renderField(['story', 'link','link_description'], $field_name_prefix, $name_index, $data, array("class" => "zf-wysiwyg-light"), '', array('showPlaceholder'=>false, 'showLabel'=>true));
    echo $this->renderField(['story', 'link','link_link'], $field_name_prefix, $name_index, $data, array("placeholder"=> esc_attr( __("http://", "zombify"))), '', array('showPlaceholder'=>false, 'showLabel'=>true));
    ?>
</div>