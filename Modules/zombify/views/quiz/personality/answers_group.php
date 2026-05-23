<div class="zf-answer-item">
    <button class="zf-remove zombify_delete_group <?php if( $groups_count < 2) { echo 'zf-hide-delete-icon'; } ?>">
    	<i class="zf-icon-delete"></i>
    </button>
    <?php
    echo $this->renderField($this->fieldPath(['questions', 'answers','image'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
    echo $this->renderField($this->fieldPath(['questions', 'answers','answer_text'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>true, 'showLabel'=>false), $path_prefix);
    echo $this->renderField($this->fieldPath(['questions', 'answers','image_credit'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>true, 'showLabel'=>false), $path_prefix);
    echo $this->renderField($this->fieldPath(['questions', 'answers','answer_result'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>true, 'showLabel'=>false), $path_prefix);
    ?>

</div>