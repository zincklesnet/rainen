<?php

if (!class_exists('ADL_LP_shortcode')):
	/**
	 * Class ADL_team_enqueue.
	 * It enqueue all scripts and styles needed by the plugin
	 */
	class ADL_LP_shortcode {

		public function __construct() {
			add_shortcode( 'adl_lp_popup', array($this, 'adl_legal_popup'));

		}
        

		/**
		 *It returns the popups for adl legal page as a shortcode callback
		 * @param array $atts   The shortcode attributes passed to the function
		 * @return mixed It returns the popups content
		 */
		public function adl_legal_popup( $atts  ) {
			ob_start();

			global $ADL_LP, $wpdb;
			$id = shortcode_atts( array(
				'id' => ''
			), $atts);
			// enqueue required styles and script for the modal popups
			wp_enqueue_style( 'bootstrap-modal-style');
			wp_enqueue_script( 'bootstrap-modal-script');
			//func defined in DB class
			$popup = $ADL_LP->get_popup($id); // get the template from the database to output
			$ready_popup_content =  apply_filters('adl_popup_content', $ADL_LP->replace_shortcode_with_content(@$popup->content)); // return modified content
			
			//get popup settings 
			$popup_options = get_option('adl_lp_popup'); // array of options
			// validate and sanitize data of popup options
			$disabled_pop_notice_title = (!empty($popup_options['disabled_pop_notice_title'])) ? absint($popup_options['disabled_pop_notice_title']) : 0;
			$popup_notice_title = (!empty($popup_options['popup_notice_title'])) ? sanitize_text_field($popup_options['popup_notice_title']) : 'Please read the following terms and agree to see the content';
			$agreement_text = (!empty($popup_options['agreement_text'])) ? sanitize_text_field($popup_options['agreement_text']) : 'I understand and I agree.';
			$accept_btn_text = (!empty($popup_options['accept_btn_text'])) ? sanitize_text_field($popup_options['accept_btn_text']) : 'Accept';
			$popup_width = (!empty($popup_options['popup_width'])) ? sanitize_text_field($popup_options['popup_width']) : '90%';
			$popup_height = (!empty($popup_options['popup_height'])) ? sanitize_text_field($popup_options['popup_height']) : '90vh';
			$user_can_close_popup = (!empty($popup_options['user_can_close_popup'])) ? absint($popup_options['user_can_close_popup']) : 0;

			?>
			<!-- Modal HTML -->
			<div id="adlPopup" class="modal fade">
				<div class="modal-dialog" style="width: <?= $popup_width ?>; height: <?= $popup_height ?> ; overflow: auto;">
					<div class="modal-content">
					<?php if ( !$disabled_pop_notice_title ) {?>
                        <div class="modal-header">
                            <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
                            <h4 class="modal-title"> <?= esc_html($popup_notice_title); ?></h4>
                        </div>
					<?php } ?>
						
						<div class="modal-body">
							<?= $ready_popup_content; ?>
                        </div>
						<div class="modal-footer">
							<div style="display: block;">
                                    <p >
                                    <label>
                                    <input type="checkbox" name="lp_accept" id="lp_accept" value="1" onclick="jQuery('#accept_term').toggle();"> <?= $agreement_text; ?>
                                    </label>
                                        <button type="button" id="accept_term" class="btn btn-primary" data-dismiss="modal" style="display: none;"><?= $accept_btn_text; ?></button>
                                    </p>
                            </div>
						</div>
					</div>
				</div>
			</div>
            <script>
                (function ($) {
                    $(document).ready(function () {
                        var popup = $('#adlPopup');
                        popup.modal(
                            <?php if ( !$user_can_close_popup ) { ?>
                            {
                                backdrop: 'static',
                                keyboard: false
                            }
                        <?php } ?>
                       );
                    });

                })(jQuery);
            </script>

			<?php
			return ob_get_clean();
		}
	}


endif;

