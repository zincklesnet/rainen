<?php
/**
 * BP Edit Activity - Comment Editing Extension
 *
 * Adds modal-based editing functionality for activity comments
 * matching the same style as activity post editing
 *
 * @since 1.1.0
 * @package Buddypress_Edit_Activity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class for handling activity comment editing
 */
class BP_Edit_Activity_Comments {

    /**
     * Instance of this class
     */
    private static $instance = null;

    /**
     * Get the singleton instance
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->setup_hooks();
    }

    /**
     * Setup hooks for comment editing
     */
    private function setup_hooks() {
        // Add edit button to comments
        add_action( 'bp_activity_comment_options', array( $this, 'add_comment_edit_button' ) );

        // AJAX handlers for comment editing
        add_action( 'wp_ajax_bp_edit_activity_comment_get_content', array( $this, 'ajax_get_comment_content' ) );
        add_action( 'wp_ajax_bp_edit_activity_comment_save_content', array( $this, 'ajax_save_comment_content' ) );

        // Add modal template and scripts
        add_action( 'wp_footer', array( $this, 'add_comment_edit_modal' ), 99 );

        // Filter to add 'activity_comment' to editable types
        add_filter( 'bp_editable_types_activity', array( $this, 'add_comment_to_editable_types' ) );

        // Add edited indicator to comments
        add_filter( 'bp_activity_comment_content', array( $this, 'add_edited_indicator' ), 10, 2 );
    }

    /**
     * Add activity_comment to editable types
     */
    public function add_comment_to_editable_types( $types ) {
        $types[] = 'activity_comment';
        return $types;
    }

    /**
     * Check if user can edit comment
     */
    public function can_edit_comment( $comment_id = 0 ) {
        if ( ! is_user_logged_in() ) {
            return false;
        }

        // Get comment data
        $comment = new BP_Activity_Activity( $comment_id );

        if ( ! $comment->id || $comment->type !== 'activity_comment' ) {
            return false;
        }

        // Check if user is admin or comment author
        if ( current_user_can( 'manage_options' ) || (int) $comment->user_id === bp_loggedin_user_id() ) {
            // Check time limit (use same settings as main activity)
            $edit_option = bp_get_option( '_bp_enable_edit_option', true );
            if ( ! $edit_option ) {
                return false;
            }

            // For non-admins, check time limit
            if ( ! current_user_can( 'manage_options' ) ) {
                $duration_setting = bp_get_option( '_bp_edit_activity_duration', 'forever' );
                $duration = $this->get_duration_seconds( $duration_setting );

                if ( $duration > 0 ) {
                    $comment_time = strtotime( $comment->date_recorded );
                    $current_time = time();
                    $diff = abs( $current_time - $comment_time );

                    if ( $diff >= $duration ) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Get duration in seconds from setting
     */
    private function get_duration_seconds( $setting ) {
        switch ( $setting ) {
            case 'thirty_days':
            case '30_days':
                return 30 * DAY_IN_SECONDS;
            case 'seven_days':
            case '7_day':
                return WEEK_IN_SECONDS;
            case 'one_day':
            case '1_day':
                return DAY_IN_SECONDS;
            case 'one_hour':
            case '1_hour':
                return HOUR_IN_SECONDS;
            case 'ten_minutes':
            case '10_minutes':
                return 10 * MINUTE_IN_SECONDS;
            case 'forever':
            default:
                return 0;
        }
    }

    /**
     * Add edit button to comment options
     */
    public function add_comment_edit_button() {
        global $activities_template;

        // Get current comment ID from context
        $comment_id = bp_get_activity_comment_id();

        if ( ! $comment_id ) {
            return;
        }

        if ( $this->can_edit_comment( $comment_id ) ) {
            if ( class_exists( 'BuddyPress' ) && isset( buddypress()->buddyboss ) ) {
                ?>
                <div class="generic-button">
                    <a href="#" class="bp-secondary-action bp-edit-comment" data-comment-id="<?php echo esc_attr( $comment_id ); ?>">
                        <div class="generic-button">
                            <span class="bp-screen-reader-text"><?php esc_html_e( 'Edit', 'buddypress-edit-activity' ); ?></span>
                            <span class="edit-label"><?php esc_html_e( 'Edit', 'buddypress-edit-activity' ); ?></span>
                        </div>
                    </a>
                </div>
                <?php
            } else {
                ?>
                <div class="generic-button">
                    <a href="#" class="bp-secondary-action bp-edit-comment" data-comment-id="<?php echo esc_attr( $comment_id ); ?>">
                        <div class="generic-button"><?php esc_html_e( 'Edit', 'buddypress-edit-activity' ); ?></div>
                    </a>
                </div>
                <?php
            }
        }
    }

    /**
     * AJAX handler to get comment content for editing
     */
    public function ajax_get_comment_content() {
        check_ajax_referer( 'buddypress-edit-activity', 'ajax_nonce' );

        $comment_id = isset( $_POST['comment_id'] ) ? (int) $_POST['comment_id'] : 0;

        if ( ! $comment_id || ! $this->can_edit_comment( $comment_id ) ) {
            wp_send_json_error( array(
                'message' => __( 'You cannot edit this comment.', 'buddypress-edit-activity' )
            ) );
        }

        $comment = new BP_Activity_Activity( $comment_id );

        // Clean content for editing
        $content = stripslashes( $comment->content );
        $content = wp_strip_all_tags( $content );

        wp_send_json_success( array(
            'content' => $content,
            'comment_id' => $comment_id
        ) );
    }

    /**
     * AJAX handler to save edited comment content
     */
    public function ajax_save_comment_content() {
        check_ajax_referer( 'buddypress-edit-activity', 'ajax_nonce' );

        $comment_id = isset( $_POST['comment_id'] ) ? (int) $_POST['comment_id'] : 0;
        $content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';

        if ( ! $comment_id || ! $this->can_edit_comment( $comment_id ) ) {
            wp_send_json_error( array(
                'message' => __( 'You cannot edit this comment.', 'buddypress-edit-activity' )
            ) );
        }

        if ( empty( trim( $content ) ) ) {
            wp_send_json_error( array(
                'message' => __( 'Comment content cannot be empty.', 'buddypress-edit-activity' )
            ) );
        }

        $comment = new BP_Activity_Activity( $comment_id );

        // Apply filters similar to main activity editing
        $content = apply_filters( 'bp_activity_comment_content_before_save', $content, $comment_id );

        // Update comment content
        $comment->content = $content;
        $saved = $comment->save();

        if ( ! $saved ) {
            wp_send_json_error( array(
                'message' => __( 'Failed to save comment.', 'buddypress-edit-activity' )
            ) );
        }

        // Mark as edited
        bp_activity_update_meta( $comment_id, '_bp_comment_edited', true );
        bp_activity_update_meta( $comment_id, '_bp_comment_edited_time', current_time( 'mysql' ) );

        // Get updated comment HTML - just the inner content, not the wrapper
        ob_start();
        echo bp_activity_filter_kses( $content );
        ?>
        <span class="bp-comment-edited-indicator"><?php esc_html_e( '(edited)', 'buddypress-edit-activity' ); ?></span>
        <?php
        $html = ob_get_clean();

        wp_send_json_success( array(
            'message' => __( 'Comment updated successfully.', 'buddypress-edit-activity' ),
            'content' => $html,
            'raw_content' => $content
        ) );
    }

    /**
     * Add edited indicator to edited comments
     */
    public function add_edited_indicator( $content, $comment = null ) {
        if ( ! $comment ) {
            global $activities_template;
            if ( isset( $activities_template->activity->current_comment ) ) {
                $comment = $activities_template->activity->current_comment;
            }
        }

        if ( $comment && isset( $comment->id ) ) {
            $is_edited = bp_activity_get_meta( $comment->id, '_bp_comment_edited', true );
            if ( $is_edited ) {
                $content .= ' <span class="bp-comment-edited-indicator">' . __( '(edited)', 'buddypress-edit-activity' ) . '</span>';
            }
        }

        return $content;
    }

    /**
     * Add modal template and JavaScript for comment editing
     */
    public function add_comment_edit_modal() {
        if ( ! is_user_logged_in() || ! bp_is_active( 'activity' ) ) {
            return;
        }
        ?>
        <!-- Comment Edit Modal (using exact same markup as activity modal) -->
        <div id="bp-edit-comment-wrapper" style="display:none">
            <div class="bp-activity-edit-model-wrap">
                <div class="modal-wrapper">
                    <div class="modal-container">
                        <header class="bp-model-header">
                            <h4>
                                <span class="target_name"><?php echo esc_html__( 'Edit comment', 'buddypress-edit-activity' ); ?></span>
                            </h4>
                            <a class="bp-model-close-button" href="#">
                                <span class="dashicons dashicons-no-alt"></span>
                            </a>
                        </header>

                        <?php do_action( 'bp_before_edit_comment_template' ); ?>

                        <form id="frm-bp-edit-comment" method="POST">
                            <input type="hidden" name="bp_edit_comment_nonce" value="<?php echo esc_attr( wp_create_nonce( 'bp-edit-comment') ); ?>">
                            <input type="hidden" id="bp_edit_comment_id" name="comment_id" value="">
                            <div class="field ac-textarea">
                                <textarea class="bp-suggestions" id="bp-edit-comment-content" cols="50" rows="4" style="resize: vertical;" name="comment_content"></textarea>
                            </div>
                            <div id="bp-edit-additional-comment-content" class="bp_edit_additional_comment_content">
                                <?php do_action( 'bp_edit_comment_fields' ); ?>
                            </div>

                            <input type="submit" name="update_comment" value="<?php esc_html_e( 'Update comment', 'buddypress-edit-activity' ); ?>" />
                        </form>

                        <?php do_action( 'bp_after_edit_comment_template' ); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript for comment editing -->
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Handle edit comment button click - use modal like activity editing
            $(document).on('click', '.bp-edit-comment', function(e) {
                e.preventDefault();

                var $button = $(this);
                var commentId = $button.data('comment-id');

                // Get comment content via AJAX
                $.ajax({
                    url: bp_edit_activity.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'bp_edit_activity_comment_get_content',
                        comment_id: commentId,
                        ajax_nonce: bp_edit_activity.ajax_nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show modal exactly like activity modal
                            var $form_wrapper = $('#bp-edit-comment-wrapper');
                            $form_wrapper.find('input[name="comment_id"]').val(commentId);
                            $form_wrapper.find('textarea').val(response.data.content);
                            $form_wrapper.show();

                            // Focus textarea after a slight delay
                            setTimeout(function() {
                                $('#bp-edit-comment-content').focus();
                            }, 100);
                        } else {
                            alert(response.data.message);
                        }
                    }
                });
            });

            // Handle modal close - same as activity modal
            $(document).on('click', '#bp-edit-comment-wrapper .bp-model-close-button', function(e) {
                e.preventDefault();
                $(this).closest('#bp-edit-comment-wrapper').hide();
            });

            // Handle form submission
            $(document).on('submit', '#frm-bp-edit-comment', function(e) {
                e.preventDefault();

                var commentId = $('#bp_edit_comment_id').val();
                var newContent = $('#bp-edit-comment-content').val();

                if (!newContent.trim()) {
                    alert('<?php esc_html_e( 'Comment cannot be empty.', 'buddypress-edit-activity' ); ?>');
                    return;
                }

                // Save via AJAX
                $.ajax({
                    url: bp_edit_activity.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'bp_edit_activity_comment_save_content',
                        comment_id: commentId,
                        content: newContent,
                        ajax_nonce: bp_edit_activity.ajax_nonce
                    },
                    beforeSend: function() {
                        $('#frm-bp-edit-comment input[type="submit"]').prop('disabled', true).val('<?php esc_html_e( 'Updating...', 'buddypress-edit-activity' ); ?>');
                    },
                    success: function(saveResponse) {
                        if (saveResponse.success) {
                            // Primary selector: use the standard BuddyPress comment ID
                            var $comment = $('#acomment-' + commentId);

                            if ($comment.length === 0) {
                                // Fallback: find by edit button
                                $comment = $('.bp-edit-comment[data-comment-id="' + commentId + '"]').closest('li.acomment-item, li[id^="acomment-"]');
                            }

                            if ($comment.length > 0) {
                                // Find the content div within the comment
                                var $contentDiv = $comment.find('.acomment-content').first();

                                if ($contentDiv.length > 0) {
                                    // Update the content
                                    $contentDiv.html(saveResponse.data.content);
                                    console.log('Comment #' + commentId + ' updated successfully');
                                } else {
                                    // Try alternative selectors for content
                                    $contentDiv = $comment.find('[class*="comment-content"]').first();
                                    if ($contentDiv.length > 0) {
                                        $contentDiv.html(saveResponse.data.content);
                                        console.log('Comment updated via alternative selector');
                                    } else {
                                        console.warn('Could not find content div, reloading page...');
                                        location.reload();
                                    }
                                }
                            } else {
                                console.warn('Could not find comment element #acomment-' + commentId + ', reloading page...');
                                location.reload();
                            }

                            // Close modal
                            $('#bp-edit-comment-wrapper').hide();
                            $('#bp-edit-comment-content').val('');
                            $('#bp_edit_comment_id').val('');

                            // Show success message
                            if (saveResponse.data.message) {
                                console.log(saveResponse.data.message);
                            }
                        } else {
                            alert(saveResponse.data.message);
                        }
                    },
                    error: function() {
                        alert('<?php esc_html_e( 'Error saving comment.', 'buddypress-edit-activity' ); ?>');
                    },
                    complete: function() {
                        $('#frm-bp-edit-comment input[type="submit"]').prop('disabled', false).val('<?php esc_html_e( 'Update comment', 'buddypress-edit-activity' ); ?>');
                    }
                });
            });

            // Click outside to close - same as activity modal
            $(document).on('click', '.bp-activity-edit-model-wrap', function(event) {
                if ($(event.target).hasClass('bp-activity-edit-model-wrap')) {
                    $('#bp-edit-comment-wrapper .bp-model-close-button').trigger('click');
                }
            });

            // ESC key to close - same as activity modal
            $(document).on('keydown', function(event) {
                if (event.keyCode === 27) {
                    $('#bp-edit-comment-wrapper .bp-model-close-button').trigger('click');
                }
            });
        });
        </script>

        <!-- Minimal CSS - uses existing activity modal styles -->
        <style type="text/css">
        /* Edited indicator styling to match activity edited text */
        .bp-comment-edited-indicator,
        .acomment-content .bp-comment-edited-indicator {
            font-size: 14px;
            color: #767676;
            font-style: normal;
            margin-left: 5px;
        }
        </style>
        <?php
    }
}

// Initialize the class
add_action( 'bp_loaded', function() {
    BP_Edit_Activity_Comments::get_instance();
} );