<div class="zf-answer-item">
    <button class="zf-remove zombify_delete_group <?php if( $groups_count < 2) { echo 'zf-hide-delete-icon'; } ?>">
        <i class="zf-icon-delete"></i>
    </button>
    <?php
    echo $this->renderField($this->fieldPath(['questions', 'answers','image'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix);
    echo $this->renderField($this->fieldPath(['questions', 'answers','answer_text'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>true, 'showLabel'=>false), $path_prefix);
    echo $this->renderField($this->fieldPath(['questions', 'answers','image_credit'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>true, 'showLabel'=>false), $path_prefix);
    ?>
    <label class="zf-checkbox-currect">
        <?php

        echo $this->renderField($this->fieldPath(['questions','correct'], $aliased_group_path, $path_prefix), $name_prefix, $name_index, $groupData, array('data-zombify-erase-on-clone' => '1'), 'fields/onlyradio', array("value" => "1", "label" => '<span class="zf-toggle"><span class="zf-icon"></span>'.__('Correct', 'zombify').'</span>', 'checked' => false, "use_index_as_value" => true, "showerrors" => true, "showContainer"=>true), $path_prefix);
        ?>
    </label>
</div>