<div class="zf-option-item">
    <?php
    $list_numbering_reverse = 0;

    if( isset($_POST["zombify_options"]) ){

        $list_numbering_reverse = isset($_POST["zombify_options"]["list_numbering_reverse"]) ? 1 : 0;

    } else {

        $list_numbering_reverse = ( isset( $_GET['post_id'] ) && (int)$_GET['post_id'] > 0 ) ? (int)get_post_meta( (int)$_GET['post_id'], "list_numbering_reverse", true ) : 0;

    }
    ?>
</div>