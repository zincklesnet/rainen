<?php
/*
 * Check for upgrades
 */
add_action( 'plugins_loaded', 'zf_may_be_upgrade' );
add_action( 'upgrader_pre_install', 'zf_may_be_upgrade' );
function zf_may_be_upgrade() {

	$data              = zombify()->get_plugin_data();
	$installed_version = get_site_option( 'zombify_plugin_ver', 0 );
	if ( ! $installed_version ) {
		$installed_version = $data->version;
		update_site_option( 'zombify_plugin_ver', $installed_version );
	}

	if ( version_compare( $data->version, $installed_version ) > 0 ) {

		zombify_migrate();

		update_site_option( 'zombify_plugin_ver', $data->version );

	}

}

/*
 * Migration function.
 */
function zombify_migrate() {

	global $wpdb;

	// Upgrade Poll data

	$posts = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "postmeta` AS `pm1` WHERE `pm1`.`meta_key` = 'zombify_data' AND `pm1`.`meta_value` != '' AND `pm1`.`post_id` IN ( SELECT `pm2`.`post_id` FROM `" . $wpdb->prefix . "postmeta` AS `pm2` WHERE `pm2`.`meta_key` = 'zombify_data_type' AND `pm2`.`meta_value` = 'poll' )" );

	if ( is_array( $posts ) && count( $posts ) > 0 ) {

		foreach ( $posts as $post ) {

			$vote = $wpdb->get_row( "SELECT * FROM `" . $wpdb->prefix . "postmeta` AS `pm` WHERE `pm`.`meta_key` = 'zombify_poll_results' AND `pm`.`post_id` = " . $post->post_id );

			if ( isset( $vote->meta_value ) && $vote->meta_value != '' ) {

				$vote_arr = unserialize( $vote->meta_value );

				if ( ! isset( $vote_arr["answers"] ) ) {
					$vote_arr["answers"] = array();
				}
				if ( ! isset( $vote_arr["groups"] ) ) {
					$vote_arr["groups"] = array();
				}

			} else {

				$vote_arr = array(
					"total"   => 0,
					"answers" => array(),
					"groups"  => array(),
				);

			}

			$data = zf_decode_data( $post->meta_value );

			if ( isset( $data["questions"] ) && is_array( $data["questions"] ) && count( $data["questions"] ) > 0 ) {

				$total = 0;

				foreach ( $data["questions"] as $question_index => $question ) {

					if ( ! isset( $question["question_id"] ) ) {

						$data["questions"][ $question_index ]["question_id"] = md5( time() . rand( 0, 1000000 ) );

					}

					$answers_total = 0;

					if ( isset( $data["questions"][ $question_index ]["answers"] ) ) {
						foreach ( $data["questions"][ $question_index ]["answers"] as $answer_index => $answer ) {

							if ( ! isset( $answer["answer_id"] ) ) {

								$data["questions"][ $question_index ]["answers"][ $answer_index ]["answer_id"] = md5( time() . rand( 0, 1000000 ) );

							}

							if ( ! isset( $vote_arr["answers"][ $data["questions"][ $question_index ]["answers"][ $answer_index ]["answer_id"] ] ) ) {

								$vote_arr["answers"][ $data["questions"][ $question_index ]["answers"][ $answer_index ]["answer_id"] ] = 0;

							}

							$answers_total += $vote_arr["answers"][ $data["questions"][ $question_index ]["answers"][ $answer_index ]["answer_id"] ];

							$total += $vote_arr["answers"][ $data["questions"][ $question_index ]["answers"][ $answer_index ]["answer_id"] ];

						}
					}

					if ( ! isset( $vote_arr["groups"][ $data["questions"][ $question_index ]["question_id"] ] ) ) {

						$vote_arr["groups"][ $data["questions"][ $question_index ]["question_id"] ] = 0;

					}

					$vote_arr["groups"][ $data["questions"][ $question_index ]["question_id"] ] = $answers_total;

				}

				$vote_arr["total"] = $total;

				$data = zf_encode_data( $data );

				$wpdb->query( "UPDATE `" . $wpdb->prefix . "postmeta` SET `" . $wpdb->prefix . "postmeta`.`meta_value` = '" . addslashes( $data ) . "' WHERE `" . $wpdb->prefix . "postmeta`.`meta_id`=" . $post->meta_id );

				$wpdb->query( "UPDATE `" . $wpdb->prefix . "postmeta` SET `" . $wpdb->prefix . "postmeta`.`meta_value` = '" . addslashes( serialize( $vote_arr ) ) . "' WHERE `" . $wpdb->prefix . "postmeta`.`meta_key` = 'zombify_poll_results' AND `" . $wpdb->prefix . "postmeta`.`post_id` = " . $post->post_id );

			}

		}

	}


	// Upgrade Story data

	$posts = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "postmeta` AS `pm1` WHERE `pm1`.`meta_key` = 'zombify_data' AND `pm1`.`meta_value` != '' AND `pm1`.`post_id` IN ( SELECT `pm2`.`post_id` FROM `" . $wpdb->prefix . "postmeta` AS `pm2` WHERE `pm2`.`meta_key` = 'zombify_data_type' AND `pm2`.`meta_value` = 'story' )" );

	if ( is_array( $posts ) && count( $posts ) > 0 ) {

		foreach ( $posts as $post ) {

			$data = zf_decode_data( $post->meta_value );

			if ( ! isset( $data["story"] ) ) {
				continue;
			}

			reset( $data["story"] );
			$first_key = key( $data["story"] );

			if ( isset( $data["story"][ $first_key ]["group_format"] ) ) {

				$newdata = array();

				$newdata["title"]               = isset( $data["title"] ) ? $data["title"] : '';
				$newdata["description"]         = isset( $data["description"] ) ? $data["description"] : '';
				$newdata["preface_description"] = isset( $data["preface_description"] ) ? $data["preface_description"] : '';
				if ( isset( $data["image"] ) ) {
					$newdata["image"] = $data["image"];
				}

				$newdata["story"] = array();

				foreach ( $data["story"] as $story ) {

					if ( isset( $story["group_format"] ) && in_array( $story["group_format"], array(
							"text",
							"image",
							"link",
							"embed"
						) ) ) {

						$story_group = array();

						switch ( $story["group_format"] ) {

							case "text":

								$story_group["text"]                        = array();
								$story_group["text"][0]                     = array();
								$story_group["text"][0]["text_title"]       = isset( $story["text_title"] ) ? $story["text_title"] : '';
								$story_group["text"][0]["text_description"] = isset( $story["text_description"] ) ? zf_purify_kses( htmlspecialchars_decode( $story["text_description"] ) ) : '';

								break;

							case "link":

								$story_group["link"]                        = array();
								$story_group["link"][0]                     = array();
								$story_group["link"][0]["link_headline"]    = isset( $story["link_headline"] ) ? $story["link_headline"] : '';
								$story_group["link"][0]["link_description"] = isset( $story["link_description"] ) ? zf_purify_kses( htmlspecialchars_decode( $story["link_description"] ) ) : '';
								$story_group["link"][0]["link_link"]        = isset( $story["link_link"] ) ? $story["link_link"] : '';

								break;

							case "embed":

								$story_group["embedd"]                             = array();
								$story_group["embedd"][0]                          = array();
								$story_group["embedd"][0]["embed_title"]           = isset( $story["embed_title"] ) ? $story["embed_title"] : '';
								$story_group["embedd"][0]["embed_url"]             = isset( $story["embed_url"] ) ? $story["embed_url"] : '';
								$story_group["embedd"][0]["embed_thumb"]           = isset( $story["embed_thumb"] ) ? $story["embed_thumb"] : '';
								$story_group["embedd"][0]["embed_description"]     = isset( $story["embed_description"] ) ? zf_purify_kses( htmlspecialchars_decode( $story["embed_description"] ) ) : '';
								$story_group["embedd"][0]["embed_credit"]          = isset( $story["embed_credit"] ) ? $story["embed_credit"] : '';
								$story_group["embedd"][0]["embed_credit_text"]     = isset( $story["embed_credit_text"] ) ? $story["embed_credit_text"] : '';
								$story_group["embedd"][0]["embed_original_source"] = isset( $story["embed_original_source"] ) ? $story["embed_original_source"] : '';

								break;

							case "image":

								$story_group["image"]                               = array();
								$story_group["image"][0]                            = array();
								$story_group["image"][0]["image_title"]             = isset( $story["image_title"] ) ? $story["image_title"] : '';
								$story_group["image"][0]["image_caption"]           = isset( $story["image_caption"] ) ? zf_purify_kses( htmlspecialchars_decode( $story["image_caption"] ) ) : '';
								$story_group["image"][0]["image_image"]             = isset( $story["image_image"] ) ? $story["image_image"] : '';
								$story_group["image"][0]["image_image_credit"]      = isset( $story["image_image_credit"] ) ? $story["image_image_credit"] : '';
								$story_group["image"][0]["image_image_credit_text"] = isset( $story["image_image_credit_text"] ) ? $story["image_image_credit_text"] : '';
								$story_group["image"][0]["image_original_source"]   = isset( $story["image_original_source"] ) ? $story["image_original_source"] : '';

								break;

						}

						$newdata["story"][] = $story_group;

					}

				}

				$data = zf_encode_data( $newdata );

				$wpdb->query( "UPDATE `" . $wpdb->prefix . "postmeta` SET `" . $wpdb->prefix . "postmeta`.`meta_value` = '" . addslashes( $data ) . "' WHERE `" . $wpdb->prefix . "postmeta`.`meta_id`=" . $post->meta_id );

			}


		}

	}


	/**
	 * upgrade audio data
	 */
	$posts = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "postmeta` AS `pm1` WHERE `pm1`.`meta_key` = 'zombify_data' AND `pm1`.`meta_value` != '' AND `pm1`.`post_id` IN ( SELECT `pm2`.`post_id` FROM `" . $wpdb->prefix . "postmeta` AS `pm2` WHERE `pm2`.`meta_key` = 'zombify_data_type' AND `pm2`.`meta_value` = 'audio' )" );

	if ( is_array( $posts ) && count( $posts ) > 0 ) {

		foreach ( $posts as $post ) {

			$data = zf_decode_data( $post->meta_value );

			if ( ! isset( $data["audio"] ) ) {

				$data["audio"]    = array();
				$data["audio"][0] = array();

				$data["audio"][0]["embed_url"]         = isset( $data["embed_url"] ) ? $data["embed_url"] : '';
				$data["audio"][0]["embed_thumb"]       = isset( $data["embed_thumb"] ) ? $data["embed_thumb"] : '';
				$data["audio"][0]["audio_description"] = isset( $data["audio_description"] ) ? zf_purify_kses( htmlspecialchars_decode( $data["audio_description"] ) ) : '';
				$data["audio"][0]["mediatype"]         = isset( $data["mediatype"] ) ? $data["mediatype"] : '';
				$data["audio"][0]["videofile"]         = isset( $data["videofile"] ) ? $data["videofile"] : '';
				$data["audio"][0]["video_external"]    = isset( $data["video_external"] ) ? $data["video_external"] : '';

				if ( isset( $data["embed_url"] ) ) {
					unset( $data["embed_url"] );
				}
				if ( isset( $data["embed_thumb"] ) ) {
					unset( $data["embed_thumb"] );
				}
				if ( isset( $data["audio_description"] ) ) {
					unset( $data["audio_description"] );
				}
				if ( isset( $data["mediatype"] ) ) {
					unset( $data["mediatype"] );
				}
				if ( isset( $data["videofile"] ) ) {
					unset( $data["videofile"] );
				}
				if ( isset( $data["video_external"] ) ) {
					unset( $data["video_external"] );
				}


				$data = zf_encode_data( $data );

				$wpdb->query( "UPDATE `" . $wpdb->prefix . "postmeta` SET `" . $wpdb->prefix . "postmeta`.`meta_value` = '" . addslashes( $data ) . "' WHERE `" . $wpdb->prefix . "postmeta`.`meta_id`=" . $post->meta_id );

			}

		}

	}

	/**
	 * upgrade video data
	 */
	$posts = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "postmeta` AS `pm1` WHERE `pm1`.`meta_key` = 'zombify_data' AND `pm1`.`meta_value` != '' AND `pm1`.`post_id` IN ( SELECT `pm2`.`post_id` FROM `" . $wpdb->prefix . "postmeta` AS `pm2` WHERE `pm2`.`meta_key` = 'zombify_data_type' AND `pm2`.`meta_value` = 'video' )" );

	if ( is_array( $posts ) && count( $posts ) > 0 ) {

		foreach ( $posts as $post ) {

			$data = zf_decode_data( $post->meta_value );

			if ( ! isset( $data["video"] ) ) {

				$data["video"]    = array();
				$data["video"][0] = array();

				$data["video"][0]["embed_url"]         = isset( $data["embed_url"] ) ? $data["embed_url"] : '';
				$data["video"][0]["embed_thumb"]       = isset( $data["embed_thumb"] ) ? $data["embed_thumb"] : '';
				$data["video"][0]["video_description"] = isset( $data["video_description"] ) ? zf_purify_kses( htmlspecialchars_decode( $data["video_description"] ) ) : '';
				$data["video"][0]["mediatype"]         = isset( $data["mediatype"] ) ? $data["mediatype"] : '';
				$data["video"][0]["videofile"]         = isset( $data["videofile"] ) ? $data["videofile"] : '';
				$data["video"][0]["video_external"]    = isset( $data["video_external"] ) ? $data["video_external"] : '';

				if ( isset( $data["embed_url"] ) ) {
					unset( $data["embed_url"] );
				}
				if ( isset( $data["embed_thumb"] ) ) {
					unset( $data["embed_thumb"] );
				}
				if ( isset( $data["video_description"] ) ) {
					unset( $data["video_description"] );
				}
				if ( isset( $data["mediatype"] ) ) {
					unset( $data["mediatype"] );
				}
				if ( isset( $data["videofile"] ) ) {
					unset( $data["videofile"] );
				}
				if ( isset( $data["video_external"] ) ) {
					unset( $data["video_external"] );
				}


				$data = zf_encode_data( $data );

				$wpdb->query( "UPDATE `" . $wpdb->prefix . "postmeta` SET `" . $wpdb->prefix . "postmeta`.`meta_value` = '" . addslashes( $data ) . "' WHERE `" . $wpdb->prefix . "postmeta`.`meta_id`=" . $post->meta_id );

			}

		}

	}

	/**
	 * upgrade trivia data
	 * set correct option one level higher
	 */
	$posts = $wpdb->get_results( "SELECT * FROM `" . $wpdb->prefix . "postmeta` AS `pm1` WHERE `pm1`.`meta_key` = 'zombify_data' AND `pm1`.`meta_value` != '' AND `pm1`.`post_id` IN ( SELECT `pm2`.`post_id` FROM `" . $wpdb->prefix . "postmeta` AS `pm2` WHERE `pm2`.`meta_key` = 'zombify_data_type' AND `pm2`.`meta_value` = 'trivia' )" );

	if ( is_array( $posts ) && count( $posts ) > 0 ) {

		foreach ( $posts as $post ) {

			$data = zf_decode_data( $post->meta_value );

			if ( $data ) {

				if ( isset( $data["questions"] ) && is_array( $data["questions"] ) ) {

					foreach ( $data["questions"] as $question_index => $question ) {

						$correct_index = false;

						if ( isset( $question["answers"] ) && is_array( $question["answers"] ) ) {

							foreach ( $question["answers"] as $answer_index => $answer ) {

								if ( isset( $answer["correct"] ) ) {

									if ( $answer["correct"] == 1 ) {

										$correct_index = $answer_index;

									}

									unset( $data["questions"][ $question_index ]["answers"][ $answer_index ]["correct"] );

								}

							}

						}

						if ( $correct_index !== false ) {

							$data["questions"][ $question_index ]["correct"] = $correct_index;

						}

					}

				}

				$data = zf_encode_data( $data );

				$wpdb->query( "UPDATE `" . $wpdb->prefix . "postmeta` SET `" . $wpdb->prefix . "postmeta`.`meta_value` = '" . addslashes( $data ) . "' WHERE `" . $wpdb->prefix . "postmeta`.`meta_id`=" . $post->meta_id );

			}


		}

	}

	// Clodconvert migration
	if ( function_exists( "boombox_get_theme_option" ) ) {

		if ( get_option( "zombify_cloudconvert_api_key" ) === false ) {
			update_option( "zombify_cloudconvert_api_key", boombox_get_theme_option( 'settings_gif_control_cloudconvert_app_key' ) );
		}

	}
}