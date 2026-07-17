<?php
/**
 * Messages dropdown inner content — used by both the initial render and AJAX refresh.
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

global $wpdb;

if ( class_exists( 'BP_Better_Messages' ) && function_exists( 'Better_Messages' ) ) {
	?>
	<div class="dropdown-item-wrapper">
		<?php echo Better_Messages()->functions->get_threads_html( get_current_user_id() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<div class="dropdown-footer">
		<a href="<?php echo esc_url( trailingslashit( bp_loggedin_user_domain() . 'messages/' ) ); ?>" class="button read-more"><?php esc_html_e( 'View All Messages', 'reign' ); ?></a>
	</div>
	<?php
} else {
	if ( bp_has_message_threads(
		array(
			'user_id'  => bp_loggedin_user_id(),
			'type'     => 'unread',
			'per_page' => 10,
			'max'      => 10,
		)
	) ) :
		?>
		<div class="dropdown-item-wrapper">
			<?php
			while ( bp_message_threads() ) :
				bp_message_thread();

				global $messages_template;

				$recipients       = array();
				$recipient_names  = array();
				$excerpt          = '';
				$last_message_id  = 0;
				$first_message_id = 0;

				foreach ( array_reverse( $messages_template->thread->messages ) as $message ) {
					if ( '' !== wp_strip_all_tags( $message->message ) ) {
						$messages_template->thread->last_message_content = $message->message;
						$excerpt                                          = wp_strip_all_tags( bp_create_excerpt( $messages_template->thread->last_message_content, 50, array( 'ending' => '&hellip;' ) ) );
						$last_message_id                                  = (int) $message->id;
						$messages_template->thread->thread_id             = $message->thread_id;
						$messages_template->thread->last_sender_id        = $message->sender_id;
					}
				}
				if ( '' === $excerpt ) {
					$thread_messages = BP_Messages_Thread::get_messages( bp_get_message_thread_id(), null, 99999999 );
					foreach ( $thread_messages as $thread_message ) {
						$excerpt = wp_strip_all_tags( do_shortcode( $thread_message->message ) );
						if ( '' !== $excerpt ) {
							$last_message_id                                 = (int) $thread_message->id;
							$messages_template->thread->last_message_id      = $thread_message->id;
							$messages_template->thread->thread_id            = $thread_message->thread_id;
							$messages_template->thread->last_message_subject = $thread_message->subject;
							$messages_template->thread->last_message_content = $thread_message->message;
							$messages_template->thread->last_sender_id       = $thread_message->sender_id;
							$messages_template->thread->last_message_date    = $thread_message->date_sent;
							$excerpt = wp_strip_all_tags( bp_create_excerpt( $messages_template->thread->last_message_content, 50, array( 'ending' => '&hellip;' ) ) );
							break;
						}
					}
				}

				$group_id = bp_messages_get_meta( $last_message_id, 'group_id', true );
				if ( 0 === $last_message_id && ! $group_id ) {
					if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
						$first_message           = BP_Messages_Thread::get_first_message( bp_get_message_thread_id() );
						$group_message_thread_id = bp_messages_get_meta( $first_message->id, 'group_message_thread_id', true );
						$group_id                = (int) bp_messages_get_meta( $first_message->id, 'group_id', true );
					}
				}

				$group_name                = '';
				$group_avatar              = '';
				$group_link                = '';
				$group_message_users       = '';
				$group_message_type        = '';
				$group_message_thread_type = '';
				$group_message_fresh       = '';
				$is_deleted_group          = 0;

				if ( ! empty( $group_id ) ) {
					$group_message_users       = bp_messages_get_meta( $last_message_id, 'group_message_users', true );
					$group_message_type        = bp_messages_get_meta( $last_message_id, 'group_message_type', true );
					$group_message_thread_type = bp_messages_get_meta( $last_message_id, 'group_message_thread_type', true );
					$group_message_fresh       = bp_messages_get_meta( $last_message_id, 'group_message_fresh', true );
					$message_from              = bp_messages_get_meta( $last_message_id, 'message_from', true );

					if ( bp_is_active( 'groups' ) ) {
						$group_name = bp_get_group_name( groups_get_group( $group_id ) );
						if ( empty( $group_name ) ) {
							$group_link = 'javascript:void(0);';
						} elseif ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) {
							$group_link = bp_get_group_url( groups_get_group( $group_id ) );
						} else {
							$group_link = bp_get_group_permalink( groups_get_group( $group_id ) );
						}

						if ( function_exists( 'bp_disable_group_avatar_uploads' ) && bp_disable_group_avatar_uploads() && function_exists( 'bb_get_buddyboss_group_avatar' ) ) {
							$group_avatar = bb_get_buddyboss_group_avatar();
						} else {
							$group_avatar = bp_core_fetch_avatar(
								array(
									'item_id'    => $group_id,
									'object'     => 'group',
									'type'       => 'full',
									'avatar_dir' => 'group-avatars',
									/* translators: %s: Group name. */
									'alt'        => sprintf( __( 'Group logo of %s', 'reign' ), $group_name ),
									'title'      => $group_name,
									'html'       => false,
								)
							);
						}
					} else {
						$prefix       = apply_filters( 'bp_core_get_table_prefix', $wpdb->base_prefix );
						$groups_table = $prefix . 'bp_groups';
						$group_name   = $wpdb->get_var( $wpdb->prepare( "SELECT `name` FROM `{$groups_table}` WHERE `id` = %d;", $group_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $groups_table is built from $wpdb->base_prefix, not user input.
						$group_link   = 'javascript:void(0);';

						if ( ! empty( $group_name ) && ( ! function_exists( 'bp_disable_group_avatar_uploads' ) || ( function_exists( 'bp_disable_group_avatar_uploads' ) && ! bp_disable_group_avatar_uploads() ) ) ) {
							$directory                = 'group-avatars';
							$avatar_size              = '-bpfull';
							$legacy_group_avatar_name = '-groupavatar-full';
							$legacy_user_avatar_name  = '-avatar2';
							$avatar_folder_dir        = bp_core_avatar_upload_path() . '/' . $directory . '/' . $group_id;
							$avatar_folder_url        = bp_core_avatar_url() . '/' . $directory . '/' . $group_id;

							if ( file_exists( $avatar_folder_dir ) ) {
								$group_avatar = '';
								$av_dir = opendir( $avatar_folder_dir );
								if ( $av_dir ) {
									$avatar_files = array();
									$avatar_file  = readdir( $av_dir );
									while ( false !== $avatar_file ) {
										if ( 2 < strlen( $avatar_file ) ) {
											$avatar_files[] = $avatar_file;
										}
										$avatar_file = readdir( $av_dir );
									}
									if ( 0 < count( $avatar_files ) ) {
										foreach ( $avatar_files as $key => $value ) {
											if ( strpos( $value, $avatar_size ) !== false ) {
												$group_avatar = $avatar_folder_url . '/' . $avatar_files[ $key ];
											}
										}
										if ( ! isset( $group_avatar ) ) {
											foreach ( $avatar_files as $key => $value ) {
												if ( strpos( $value, $legacy_user_avatar_name ) !== false ) {
													$group_avatar = $avatar_folder_url . '/' . $avatar_files[ $key ];
												}
											}
											if ( ! isset( $group_avatar ) ) {
												foreach ( $avatar_files as $key => $value ) {
													if ( strpos( $value, $legacy_group_avatar_name ) !== false ) {
														$group_avatar = $avatar_folder_url . '/' . $avatar_files[ $key ];
													}
												}
											}
										}
									}
									closedir( $av_dir );
								}
							}
						} elseif ( function_exists( 'bb_attachments_get_default_profile_group_avatar_image' ) && ( function_exists( 'bp_disable_group_avatar_uploads' ) && ! bp_disable_group_avatar_uploads() ) ) {
							$group_avatar = bb_attachments_get_default_profile_group_avatar_image( array( 'object' => 'group' ) );
						} elseif ( function_exists( 'bb_get_buddyboss_group_avatar' ) && ( function_exists( 'bp_disable_group_avatar_uploads' ) && bp_disable_group_avatar_uploads() ) ) {
							$group_avatar = bb_get_buddyboss_group_avatar();
						}
					}

					$is_deleted_group = ( empty( $group_name ) ) ? 1 : 0;
					$group_name       = ( empty( $group_name ) ) ? __( 'Deleted Group', 'reign' ) : $group_name;
				}

				$is_group_thread = 0;
				if ( (int) $group_id > 0 ) {
					if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
						$first_message           = BP_Messages_Thread::get_first_message( bp_get_message_thread_id() );
						$group_message_thread_id = bp_messages_get_meta( $first_message->id, 'group_message_thread_id', true );
						$group_id                = (int) bp_messages_get_meta( $first_message->id, 'group_id', true );
						$message_users           = bp_messages_get_meta( $first_message->id, 'group_message_users', true );
						$message_type            = bp_messages_get_meta( $first_message->id, 'group_message_type', true );
						$message_from            = bp_messages_get_meta( $first_message->id, 'message_from', true );

						if ( 'group' === $message_from && bp_get_message_thread_id() === (int) $group_message_thread_id && 'all' === $message_users && 'open' === $message_type ) {
							$is_group_thread = 1;
						}
					}
				}

				$recipients       = array();
				$other_recipients = array();
				$current_user_data = false;
				if ( is_array( $messages_template->thread->recipients ) ) {
					foreach ( $messages_template->thread->recipients as $recipient ) {
						if ( empty( $recipient->is_deleted ) ) {
							$is_you         = bp_loggedin_user_id() === $recipient->user_id;
							$recipient_data = array(
								'avatar'    => esc_url(
									bp_core_fetch_avatar(
										array(
											'item_id' => $recipient->user_id,
											'object'  => 'user',
											'type'    => 'thumb',
											'width'   => BP_AVATAR_THUMB_WIDTH,
											'height'  => BP_AVATAR_THUMB_HEIGHT,
											'html'    => false,
										)
									)
								),
								'user_link' => bp_core_get_userlink( $recipient->user_id, false, true ),
								'user_name' => bp_core_get_user_displayname( $recipient->user_id ),
								'is_you'    => $is_you,
							);
							$recipients[] = $recipient_data;
							if ( ! $is_you ) {
								$other_recipients[] = $recipient_data;
							} else {
								$current_user_data = $recipient_data;
							}
						}
					}
				}
				$include_you = count( $other_recipients ) >= 2;
				$first_three = array_slice( $other_recipients, 0, 3 );
				if ( count( $first_three ) === 0 ) {
					$include_you = true;
				}
				?>
				<div class="dropdown-item unread" data-thread-id="<?php echo esc_attr( bp_get_message_thread_id() ); ?>">
					<span class="rg-full-link">
						<a href="<?php bp_message_thread_view_link( bp_get_message_thread_id() ); ?>">
							<?php bp_message_thread_subject(); ?>
						</a>
					</span>
					<div class="notification-item-content">
						<div class="item-avatar">
							<?php
							if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
								if ( function_exists( 'bp_messages_get_avatars' ) && ! empty( bp_messages_get_avatars( bp_get_message_thread_id(), get_current_user_id() ) ) ) {
									$avatars = bp_messages_get_avatars( bp_get_message_thread_id(), get_current_user_id() );
									?>
									<div class="notification-avatar">
										<a href="<?php bp_message_thread_view_link( bp_get_message_thread_id() ); ?>">
											<?php
											if ( count( $avatars ) > 1 ) {
												echo '<div class="thread-multiple-avatar">';
											}
											foreach ( $avatars as $avatar ) {
												echo '<img src="' . esc_url( $avatar['url'] ) . '" alt="' . esc_attr( $avatar['name'] ) . '" loading="lazy" decoding="async" />';
											}
											if ( count( $avatars ) > 1 ) {
												echo '</div>';
											}
											?>
										</a>
									</div>
									<?php
								} elseif ( $is_group_thread ) {
									?>
									<div class="notification-avatar">
										<a href="<?php bp_message_thread_view_link( bp_get_message_thread_id() ); ?>">
											<img src="<?php echo esc_url( $group_avatar ); ?>" alt="" loading="lazy" decoding="async">
										</a>
									</div>
									<?php
								} else {
									?>
									<div class="notification-avatar">
										<?php
										if ( count( $other_recipients ) > 1 ) {
											if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) :
												?>
												<a href="<?php echo esc_url( bp_members_get_user_url( $messages_template->thread->last_sender_id ) ); ?>">
													<?php bp_message_thread_avatar(); ?>
												</a>
											<?php else : ?>
												<a href="<?php echo esc_url( bp_core_get_user_domain( $messages_template->thread->last_sender_id ) ); ?>">
													<?php bp_message_thread_avatar(); ?>
												</a>
												<?php
											endif;
										} else {
											$recipient = ! empty( $first_three[0] ) ? $first_three[0] : $current_user_data;
											?>
											<a href="<?php echo esc_url( $recipient['user_link'] ); ?>">
												<img class="avatar" src="<?php echo esc_url( $recipient['avatar'] ); ?>" alt="<?php echo esc_attr( $recipient['user_name'] ); ?>" />
											</a>
											<?php
										}
										?>
									</div>
									<?php
								}
							} else {
								bp_message_thread_avatar( 'type=thumb&width=30&height=30' );
							}
							?>
						</div>
						<div class="item-info">
							<div class="dropdown-item-title message-subject ellipsis">
								<?php
								if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
									if ( $is_group_thread ) {
										?>
										<span class="notification-users">
											<a href="<?php esc_url( bp_message_thread_view_link( bp_get_message_thread_id() ) ); ?>">
												<?php echo esc_html( ucwords( $group_name ) ); ?>
											</a>
										</span>
										<?php
									} else {
										?>
										<span class="notification-users">
											<a href="<?php bp_message_thread_view_link( bp_get_message_thread_id() ); ?>">
												<?php
												$r_list  = (array) $messages_template->thread->recipients;
												$r_names = array();
												foreach ( $r_list as $r ) :
													if ( bp_loggedin_user_id() !== (int) $r->user_id ) :
														$r_name = bp_core_get_user_displayname( $r->user_id );
														if ( empty( $r_name ) ) :
															$r_name = __( 'Deleted User', 'reign' );
														endif;
														if ( bp_is_active( 'moderation' ) ) {
															if ( bp_moderation_is_user_suspended( $r->user_id ) ) {
																$r_name = __( 'Suspended Member', 'reign' );
															} elseif ( bp_moderation_is_user_blocked( $r->user_id ) ) {
																$r_name = __( 'Blocked Member', 'reign' );
															}
														}
														$r_names[] = $r_name ? ucwords( $r_name ) : '';
													endif;
												endforeach;
												echo esc_html( ! empty( $r_names ) ? implode( ', ', $r_names ) : '' );
												?>
											</a>
										</span>
										<?php
									}
								} else {
									?>
									<a href="<?php bp_message_thread_view_link( bp_get_message_thread_id(), bp_loggedin_user_id() ); ?>" class="color-primary"><?php bp_message_thread_subject(); ?></a>
									<?php
								}
								?>
							</div>
							<p class="mute"><?php bp_message_thread_last_post_date(); ?></p>
						</div>
					</div>
					<div class="actions">
						<a class="mark-read action-mark-message-read primary" href="#" data-thread-id="<?php echo esc_attr( bp_get_message_thread_id() ); ?>" data-bp-tooltip-pos="left" data-bp-tooltip="<?php esc_attr_e( 'Mark as Read', 'reign' ); ?>">
							<i class="fa-regular fa-eye-slash"></i>
						</a>
					</div>
				</div>
				<?php
			endwhile;
			?>
		</div>
	<?php else : ?>
		<div class="alert-message">
			<div class="alert alert-warning" role="alert"><?php esc_html_e( 'No messages found.', 'reign' ); ?></div>
		</div>
	<?php endif; ?>
	<div class="dropdown-footer">
		<a href="<?php echo esc_url( trailingslashit( bp_loggedin_user_domain() . bp_get_messages_slug() . '/inbox' ) ); ?>" class="button read-more"><?php esc_html_e( 'All Messages', 'reign' ); ?></a>
	</div>
	<?php
}
