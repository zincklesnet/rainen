<?php
$items_per_page = zf_get_items_per_page( $zf_shortcode_args );
$current_page   = zf_get_current_page( $zf_shortcode_args );

/* Set AMP scripts to load */
zombify()->amp()->set_scripts( array( 'amp-form', 'amp-bind', 'amp-analytics' ) );
?>
<div id="zfWrap">
	<ol class="zfListSet">
		<?php
		$index        = 1;
		$index        += ( $current_page - 1 ) * $items_per_page;
		$data["list"] = array_slice( $data["list"], ( $current_page - 1 ) * $items_per_page, $items_per_page );

		if ( isset( $data["list"] ) ) {
			foreach ( $data["list"] as $list ) {
				$postObj = get_post( $list["post_id"] );
				if ( $postObj->post_status != 'publish' ) {
					continue;
				} ?>
				<li class="zfSetItem">
					<h3 class="zfTitle zfTitleNum">
						<span class="zfNum"><?php echo $index; ?></span>
						<a href="<?php echo get_permalink( $list["post_id"] ); ?>"><?php echo $list["title"]; ?></a>
					</h3>
					<?php
					if ( ( $list["mediatype"] == 'image' && isset( zf_array_values( $list["image"] )[0]["attachment_id"] ) ) || ( $list["mediatype"] == 'embed' && $list["embed_url"] != '' ) ) {
						switch ( $list["mediatype"] ) {
							case "image":
								$zf_media_html = '';
								if ( isset( zf_array_values( $list["image"] )[0]["attachment_id"] ) ) {
									$zf_media_html = zombify_get_img_tag( zf_array_values( $list["image"] )[0]["attachment_id"], 'full' );
								}
								break;
							case "embed":
								$zf_media_html = sprintf( '<div class="zf-embedded-url">%s</div>', Zombify_BaseQuiz::renderEmbed( $list, true ) );
								break;
							default:
								$zf_media_html = '';

						} ?>
						<figure class="zfMedia <?php echo "zf" . $list["mediatype"]; ?>">
							<?php
							echo $zf_media_html;

							if ( isset( $list["image_credit"] ) && ! empty( $list["image_credit"] ) ) {
								?>
								<figcaption class="zfCaption">
									<cite class="zfCredit"><?php zf_showCredit( $list["image_credit"], $list["image_credit_text"] ); ?></cite>
									<?php if ( isset( $list["affiliate"] ) && $list["affiliate"] && $list["shop_url"] != '' ) {
										$shop_button_text = ! empty( $list['shop_button_text'] ) ? $list['shop_button_text'] : __( "Buy Now", "zombify" );
										?>
										<a class="zfBtnBuy" href="<?php echo esc_url( $list["shop_url"] ); ?>" target="_blank" rel="nofollow noopener">
											<?php esc_html_e( $shop_button_text, "zombify" ); ?>
										</a>
									<?php } ?>
								</figcaption>
							<?php } ?>

						</figure>
					<?php } ?>

					<div class="zf-item_meta zfClear">
						<span class="zfUser">
					    	<a class="zfUserImg" href="<?php echo get_author_posts_url( $postObj->post_author ); ?>">
								<?php echo get_avatar( $postObj->post_author ); ?>
					  		</a>
						    <a class="zfUserName" href="<?php echo get_author_posts_url( $postObj->post_author ); ?>">
								<?php echo get_the_author_meta( 'display_name', $postObj->post_author ); ?>
						    </a>
						    <time class="zfDate"><?php esc_html_e( "at", "zombify" ); ?>
							    <?php echo date_i18n( "h:i a F d, Y", strtotime( $postObj->post_date ) ); ?>
						    </time>
				  		</span>
						<?php
						if ( get_post_meta( get_the_ID(), "openlist_close_voting", true ) != 1 ) {
							?>
							<div class="zf-item-vote-box" zf-amp-class="'zf-item-vote-box ' + zfVoteClass<?php echo $list["post_id"]; ?>">
								<div class="zf-item-vote" data-zf-post-id="<?php echo $list["post_id"]; ?>" data-zf-post-parent-id="<?php echo get_the_ID(); ?>">
									<form method="post"
										  target="_blank"
										  id="zfVoteFormUp_<?php echo $list["post_id"]; ?>"
										  action-xhr="<?php echo admin_url( 'admin-ajax.php' ) ?>"
										  on="submit-success:AMP.setState({formResponse: event.response, zfVoteClass<?php echo $list["post_id"]; ?>: 'zf-voted zf-voted-up'}) , zfVoteCountUp_<?php echo $list["post_id"]; ?>.show"
										  class="zf-vote-form zf-vote-form-up">
										<input type="hidden" name="post_id" value="<?php echo $list["post_id"]; ?>">
										<input type="hidden" name="post_parent_id" value="<?php echo get_the_ID(); ?>">
										<input type="hidden" name="vote_type" value="up">
										<input type="hidden" name="action" value="zombify_post_vote">
										<input type="hidden" name="amp" value="1">
										<button class="zf-vote_btn zf-vote_up"
												role="button"
												tabindex="1" type="submit" value=""
												on="tap:AMP.setState({zfVoteClass<?php echo $list["post_id"]; ?>: 'zf-voting zf-voting-up'})">
											<amp-img src="<?php echo zombify()->plugin_url; ?>assets/images/amp/zf-arrow-up.svg"
													 width="22" height="22" alt="up">
											</amp-img>
										</button>
										<span id="zfVoteCountUp_<?php echo $list["post_id"]; ?>"
											  class="zf-vote_number zf-vote_number_up"
											  zf-amp-text="formResponse.votes<?php echo $list["post_id"]; ?>"
											  hidden>
										</span>
									</form>

									<span class="zf-vote_count zf-vote_count_main"
										  data-zf-post-id="<?php echo $list["post_id"]; ?>">
										<amp-img class="zf-ripple"
												 src="<?php echo zombify()->plugin_url; ?>assets/images/amp/zf-ripple.svg"
												 width="34" height="34" alt="up">
										</amp-img>
										<span class="zf-vote_number zf-vote_number_main">
										  <?php echo (int) get_post_meta( $list["post_id"], "zombify_post_rateing", true ); ?>
										</span>
									</span>

									<form method="post"
										  target="_blank"
										  id="zfVoteFormDown_<?php echo $list["post_id"]; ?>"
										  action-xhr="<?php echo admin_url( 'admin-ajax.php' ) ?>"
										  on="submit-success:AMP.setState({formResponse: event.response, zfVoteClass<?php echo $list["post_id"]; ?>: 'zf-voted zf-voted-down'}), zfVoteCountDown_<?php echo $list["post_id"]; ?>.show"
										  class="zf-vote-form zf-vote-form-down">
										<input type="hidden" name="post_id" value="<?php echo $list["post_id"]; ?>">
										<input type="hidden" name="post_parent_id" value="<?php echo get_the_ID(); ?>">
										<input type="hidden" name="vote_type" value="down">
										<input type="hidden" name="action" value="zombify_post_vote">
										<input type="hidden" name="amp" value="1">
										<button class="zf-vote_btn zf-vote_down"
												role="button"
												tabindex="1"
												type="submit"
												value=""
												on="tap:AMP.setState({zfVoteClass<?php echo $list["post_id"]; ?>: 'zf-voting zf-voting-down'})">
											<amp-img src="<?php echo zombify()->plugin_url; ?>assets/images/amp/zf-arrow-down.svg"
													 width="22" height="22" alt="up">
											</amp-img>
										</button>
										<span id="zfVoteCountDown_<?php echo $list["post_id"]; ?>"
											  class="zf-vote_number zf-vote_number_down"
											  zf-amp-text="formResponse.votes<?php echo $list["post_id"]; ?>"
											  hidden>
										</span>
									</form>
								</div>
							</div>
							<?php
						}
						?>
					</div>
					<?php if ( $list["description"] ) { ?>
						<div class="zf-list_description"><?php echo $list["description"]; ?></div>
					<?php } ?>
					<div class="zf-amp-leave-comment">
						<a class="zfBtnLg"
						   href="<?php echo get_permalink(); ?>#zombify-main-section"><?php _e( "Leave a Reply", "zombify" ) ?>
						</a>
					</div>
				</li>
				<?php
				$index ++;
			}
		} ?>
	</ol>
	<?php do_action( 'zombify_after_post_layout' ); ?>
</div>