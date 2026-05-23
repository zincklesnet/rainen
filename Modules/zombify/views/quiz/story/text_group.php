<div class="zf-type-wrapper"><i class="zf-icon zf-icon-text_story"></i></div>

<div class="zf-item-wrapper">
    <?php
    echo $this->renderField(['story', 'text','text_title'], $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true));
    echo $this->renderField(['story', 'text','text_description'], $field_name_prefix, $name_index, $data, array('class' => 'zf-wysiwyg-advanced'), '', array('showPlaceholder'=>false, 'showLabel'=>true));
    ?>
</div>