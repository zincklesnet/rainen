<div class="zf-option-item">
    <?php
    $openlist_close_submission = 0;

    if( isset($_POST["zombify_options"]) ){

        $openlist_close_submission = isset($_POST["zombify_options"]["openlist_close_submission"]) ? 1 : 0;

    } else {

        $openlist_close_submission = ( isset( $_GET['post_id'] ) && (int)$_GET['post_id'] > 0 ) ? (int)get_post_meta( (int)$_GET['post_id'], "openlist_close_submission", true ) : 0;

    }
    ?>
    <label class="zf-checkbox-inline"><input type="checkbox" name="zombify_options[openlist_close_submission]" value="1" <?php echo $openlist_close_submission ? 'checked' : ''; ?>> <?php echo esc_attr_e("Close for submissions", "zombify"); ?></label>
</div>

<div class="zf-option-item">
    <?php
    $openlist_close_voting = 0;

    if( isset($_POST["zombify_options"]) ){

        $openlist_close_voting = isset($_POST["zombify_options"]["openlist_close_voting"]) ? 1 : 0;

    } else {

        $openlist_close_voting = ( isset( $_GET['post_id'] ) && (int)$_GET['post_id'] > 0 ) ? (int)get_post_meta( (int)$_GET['post_id'], "openlist_close_voting", true ) : 0;

    }
    ?>
    <label class="zf-checkbox-inline"><input type="checkbox" name="zombify_options[openlist_close_voting]" value="1" <?php echo $openlist_close_voting ? 'checked' : ''; ?>> <?php echo esc_attr_e("Close for voting", "zombify"); ?></label>
</div>

<div class="zf-option-item">
    <?php
    $openlist_hide_comments = 0;

    if( isset($_POST["zombify_options"]) ){

        $openlist_hide_comments = isset($_POST["zombify_options"]["openlist_hide_comments"]) ? 1 : 0;

    } else {

        $openlist_hide_comments = ( isset( $_GET['post_id'] ) && (int)$_GET['post_id'] > 0 ) ? (int)get_post_meta( (int)$_GET['post_id'], "openlist_hide_comments", true ) : 0;

    }
    ?>
    <label class="zf-checkbox-inline"><input type="checkbox" name="zombify_options[openlist_hide_comments]" value="1" <?php echo $openlist_hide_comments ? 'checked' : ''; ?>> <?php echo esc_attr_e("Hide comments", "zombify"); ?></label>
</div>