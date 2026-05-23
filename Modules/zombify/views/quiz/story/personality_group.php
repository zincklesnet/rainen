<div class="zf-type-wrapper"><i class="zf-icon zf-icon-type-personality"></i></div>

<div class="zf-results-container zf-item-wrapper">
        <h3><?php esc_html_e("Add Results", "zombify"); ?></h3>

        <div class="zf-results_container">
                <?php echo $this->renderGroups(['story', 'personality', 'results'], $group_name_prefix, $data, $action, ['story', 'personality'], 'results', '', ''); ?>
        </div>
        <?php echo $this->renderAddGroupButton(['story', 'personality', 'results'], esc_html__('Add Result', 'zombify')) ?>
</div>

<div class="zf-questions-container zf-item-wrapper">
        <h3><?php esc_html_e("Add Questions", "zombify"); ?></h3>

        <div class="zf-questions_container">
                <?php echo $this->renderGroups(['story', 'personality', 'questions'], $group_name_prefix, $data, $action, ['story', 'personality'], 'questions', '', ''); ?>
        </div>
        <?php echo $this->renderAddGroupButton(['story', 'personality', 'questions'], esc_html__('Add Question', 'zombify')) ?>
</div>