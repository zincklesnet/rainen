<div class="zf-inner-wrapper">
    <button class="zf-remove zombify_delete_group <?php if( $groups_count < 2) { echo 'zf-hide-delete-icon'; } ?>">
        <i class="zf-icon-delete"></i>
    </button>
    <div class="zf-sort-area">
        <button class="zf-up js-zf-up"><i class="zf-icon zf-icon-arrow_up"></i></button>
        <button class="zf-down js-zf-down"><i class="zf-icon zf-icon-arrow_down"></i></button>
    </div>
    <div class="zf-head">
        <h4><?php esc_html_e("Result", "zombify") ?>: <span class="zf-index"><?php echo $group_num + 1; ?></span></h4>
    </div>
    <div class="zf-body">
        <div class="zf-row">
            <div class="zf-col-lg-1">
                <?php echo $this->renderField($this->fieldPath(['results','image'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array(), $path_prefix); ?>
            </div>
            <div class="zf-col-lg-3">
                <?php if( isset($data['description']) && $data['description'] !== '' ) {
                    $zf_result_wysiwyg_light = 'zf-result-wysiwyg-light active';
                } else {
                    $zf_result_wysiwyg_light = 'zf-result-wysiwyg-light';
                } ?>
                <?php echo $this->renderField($this->fieldPath(['results','result'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix); ?>
                <?php echo $this->renderField($this->fieldPath(['results','description'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array('class' => $zf_result_wysiwyg_light), '', array('showPlaceholder'=>false, 'showLabel'=>true), $path_prefix); ?>
                <div class="zf-form-group">
                    <label><?php esc_html_e("Result Range", "zombify") ?></label>
                    <div class="zf-result-range">
                        <?php echo $this->renderField($this->fieldPath(['results','range_from'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>false, 'hideContainer' => true), $path_prefix); ?>
                        <span class="zf_to">-</span>
                        <?php echo $this->renderField($this->fieldPath(['results','range_to'], $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array(), '', array('showPlaceholder'=>false, 'showLabel'=>false, 'hideContainer' => true), $path_prefix); ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>