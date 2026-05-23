<div id="zf-fixed-bottom-pane" class="zf-fixed-bottom-pane zf-text-right">
    <button class="zf-button zf-outline zf-icon zf-float-left zombify_save">
        <i class="zf-icon zf-icon-save"></i>
        <span class="zf-text"><?php echo $action == 'create' ? __('Save Draft', 'zombify') : __('Save', 'zombify'); ?></span>

        <span class="zf-spinner-pulse"></span>
    </button>
    <?php
    if( $this->virtual ) {
        ?>
        <div class="zf-button-cont zf-draft zf-float-left ">
            <?php esc_html_e("Last session loaded", 'zombify'); ?>

            <button class="zf-button zf-outline zf-icon zf_discard_virtual">
                <i class="zf-icon zf-icon-trash"></i>
                <span class="zf-text"><?php echo __('Discard', 'zombify'); ?></span>

                <span class="zf-spinner-pulse"></span>
            </button>
        </div>
    <?php
    }
    ?>

    <div class="zf-errors-btn" style="display: none;">
        <i class="zf-icon zf-icon-notice"></i>
        <span class="zf-errors-count">5</span>
        <span class="zf-single-error-case" style="display: none;"><?php esc_html_e("error has been found", 'zombify'); ?></span>
        <span class="zf-many-errors-case" style="display: none;"><?php esc_html_e("errors has been found", 'zombify'); ?></span>
    </div>
    <a href="" class="zf-button zf-outline zf-disabled zf-icon zombify_preview" target="_blank" rel="noopener">
        <i class="zf-icon zf-icon-preview"></i>
        <?php esc_html_e("Preview", 'zombify'); ?>
    </a>
    <button class="zf-button zombify_publish">
        <span class="zf-text"><?php echo zf_user_can_publish() ? __('Publish', 'zombify') : __('Submit', 'zombify') ?></span>
        <span class="zf-spinner-pulse"></span>
    </button>
</div>