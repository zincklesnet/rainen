<?php
/**
 * Zombify Story Quiz
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_StoryQuiz") ) {

    /**
     * Class Zombify_StoryQuiz
     */
    class Zombify_StoryQuiz extends Zombify_BaseQuiz
    {
        /**
         * Quiz view file
         *
         * @var string
         */
        public $view_path = 'story';

        /**
         * Quiz slug
         *
         * @var string
         */
        public $slug = 'story';

        public function getAliasGroups($filtered = true){
	
	        $zf_config = zombify()->get_config();
            $zombify_story_format_order = [];

            if( zf_get_option('zombify_story_format_order') ) {
                $zombify_story_format_order = json_decode( zf_get_option('zombify_story_format_order') );
            }

            $alias_groups = array(
                "text" => array( "label" => $zf_config["sub_groups_labels"]["text"], "icon" => "zf-icon-text_story"),
                "image" => array( "label" => $zf_config["sub_groups_labels"]["image"], "icon" => "zf-icon-image"),
                "embedd" => array( "label" => $zf_config["sub_groups_labels"]["embedd"], "icon" => "zf-icon-embed"),
                "link" => array( "label" => $zf_config["sub_groups_labels"]["link"], "icon" => "zf-icon-link"),
                "poll" => array( "label" => $zf_config["sub_groups_labels"]["poll"], "icon" => "zf-icon-type-poll"),
                "personality" => array( "label" => $zf_config["sub_groups_labels"]["personality"], "icon" => "zf-icon-type-personality"),
                "trivia" => array( "label" => $zf_config["sub_groups_labels"]["trivia"], "icon" => "zf-icon-type-trivia"),
                "story_list" => array( "label" => $zf_config["sub_groups_labels"]["story_list"], "icon" => "zf-icon-type-list"),
                "story_countdown" => array( "label" => $zf_config["sub_groups_labels"]["story_countdown"], "icon" => "zf-icon-type-countdown"),
                "audio" => array( "label" => $zf_config["sub_groups_labels"]["audio"], "icon" => "zf-icon-type-audio"),
                "video" => array( "label" => $zf_config["sub_groups_labels"]["video"], "icon" => "zf-icon-type-video"),
                "gif" => array( "label" => $zf_config["sub_groups_labels"]["gif"], "icon" => "zf-icon-type-gif"),
            );

            foreach( $alias_groups as $format_value => $format_data ) {

                if( zf_get_option('zombify_story_format_order') ) {
                    if( isset($zombify_story_format_order->$format_value) ) {
                        $alias_groups[$format_value]["order"] = $zombify_story_format_order->$format_value;
                    }
                }
            }

            if( zf_get_option('zombify_story_format_order') ) {
                uasort($alias_groups,function($a, $b) {
                    return isset($a['order']) && isset($b['order']) ? $a['order'] - $b['order'] : 0;
                });
            }

            $this->alias_groups = $alias_groups;

            return parent::getAliasGroups($filtered);

        }

        /**
         * Zombify Story Quiz Structure
         *
         * @return Array
         */
        public function structure(){

            return [
                "title" => [
                    "type" => "field",
                    "name" => "title",
                    "label" => __("Title", "zombify"),
                    "field_type" => "text",
                    "rules" => [
                        "required",
                    ]
                ],
                "image" => [
                    "type" => "field",
                    "name" => "image",
                    "label" => __("Add Thumbnail", "zombify"),
                    "field_type" => "file",
                    "rules" => [
                        "extensions" => "png, jpg, gif, jpeg, webp, avif",
                        "maxSize" => zf_max_upload_size() / 1024
                    ],
                    "use_as_featured" => true,
                ],
                "use_preface" => [
                    "type" => "field",
                    "name" => "use_preface",
                    "label" => __("Add preface", "zombify"),
                    "field_type" => "checkbox",
                    "field_visibility" => ( isset( zombify()->post_main_fields[ $this->slug ]["preface"] ) ? zombify()->post_main_fields[ $this->slug ]["preface"] : 'show' )
                ],
                "preface_description" => [
                    "type" => "field",
                    "name" => "preface_description",
                    "label" => __("Type something here", "zombify"),
                    "field_type" => "textarea",
                    "field_visibility" => ( isset( zombify()->post_main_fields[ $this->slug ]["preface"] ) ? zombify()->post_main_fields[ $this->slug ]["preface"] : 'show' )
                ],
                "use_excerpt" => [
                    "type" => "field",
                    "name" => "use_excerpt",
                    "label" => __("Add excerpt", "zombify"),
                    "field_type" => "checkbox",
                    "field_visibility" => ( isset( zombify()->post_main_fields[ $this->slug ]["excerpt"] ) ? zombify()->post_main_fields[ $this->slug ]["excerpt"] : 'show' )
                ],
                "excerpt_description" => [
                    "type" => "field",
                    "name" => "excerpt_description",
                    "label" => __("Type excerpt here", "zombify"),
                    "field_type" => "textarea",
                    "field_visibility" => ( isset( zombify()->post_main_fields[ $this->slug ]["excerpt"] ) ? zombify()->post_main_fields[ $this->slug ]["excerpt"] : 'show' )
                ],
                "description" => [
                    "type" => "field",
                    "name" => "description",
                    "label" => __("Description", "zombify"),
                    "field_type" => "textarea",
                    "field_visibility" => ( isset( zombify()->post_main_fields[ $this->slug ]["description"] ) ? zombify()->post_main_fields[ $this->slug ]["description"] : 'show' )
                ],
                "story" => [
                    "type" => "group",
                    "name" => "story",
                    "fields" => [
                        "text" => [
                            "type" => "group",
                            "name" => "text",
                            "fields" => [
                                "text_title" => [
                                    "type" => "field",
                                    "name" => "text_title",
                                    "label" => __("Paragraph Title", "zombify"),
                                    "field_type" => "text",
                                ],
                                "text_description" => [
                                    "type" => "field",
                                    "name" => "text_description",
                                    "label" => __("Description", "zombify"),
                                    "field_type" => "textarea",
                                ],
                            ]
                        ],

                        "image" => [
                            "type" => "group",
                            "name" => "image",
                            "fields" => [
                                "image_title" => [
                                    "type" => "field",
                                    "name" => "image_title",
                                    "label" => __("Image Title", "zombify"),
                                    "field_type" => "text",
                                ],
                                "image_caption" => [
                                    "type" => "field",
                                    "name" => "image_caption",
                                    "label" => __("Description", "zombify"),
                                    "field_type" => "textarea",
                                ],
                                "image_image" => [
                                    "type" => "field",
                                    "name" => "image_image",
                                    "label" => __("Browse Image", "zombify"),
                                    "field_type" => "file",
                                    "rules" => [
                                        "extensions" => "png, jpg, gif, jpeg, webp, avif",
                                        "maxSize" => zf_max_upload_size() / 1024
                                    ],
                                    "use_as_featured" => true,
                                ],
                                "image_image_credit" => [
                                    "type" => "field",
                                    "name" => "image_image_credit",
                                    "label" => __("Source URL", "zombify"),
                                    "field_type" => "url",
                                    "show_dependency" => "story/image/image_original_source",
                                    "rules" => [
                                        "url" => "1"
                                    ],
                                ],
                                "image_image_credit_text" => [
                                    "type" => "field",
                                    "name" => "image_image_credit_text",
                                    "label" => __("Credit", "zombify"),
                                    "field_type" => "text",
                                    "show_dependency" => "story/image/image_original_source"
                                ],
                                "image_original_source" => [
                                    "type" => "field",
                                    "name" => "image_original_source",
                                    "label" => __("Via", "zombify"),
                                    "field_type" => "checkbox",
                                    "show_dependency" => ["story/image/image_image", "story/image/image_image_file_url"]
                                ],
                            ]
                        ],

                        "link" => [
                            "type" => "group",
                            "name" => "link",
                            "fields" => [
                                "link_headline" => [
                                    "type" => "field",
                                    "name" => "link_headline",
                                    "label" => __("Paragraph Title", "zombify"),
                                    "field_type" => "text",
                                ],
                                "link_description" => [
                                    "type" => "field",
                                    "name" => "link_description",
                                    "label" => __("Description", "zombify"),
                                    "field_type" => "textarea",
                                ],
                                "link_link" => [
                                    "type" => "field",
                                    "name" => "link_link",
                                    "label" => __("Place Link Here", "zombify"),
                                    "field_type" => "url",
                                    "rules" => [
                                        "url" => "1"
                                    ],
                                ],
                            ]
                        ],

                        "embedd" => [
                            "type" => "group",
                            "name" => "embedd",
                            "fields" => [
                                "embed_title" => [
                                    "type" => "field",
                                    "name" => "embed_title",
                                    "label" => __("Embed Title", "zombify"),
                                    "field_type" => "text",
                                ],
                                "embed_url" => [
                                    "type" => "field",
                                    "name" => "embed_url",
                                    "label" => __("Embed / URL", "zombify"),
                                    "field_type" => "textarea",
                                ],
                                "embed_thumb" => [
                                    "type" => "field",
                                    "name" => "embed_thumb",
                                    "label" => __("Embed Thumbnail", "zombify"),
                                    "field_type" => "hidden",
                                    "use_as_featured" => true,
                                ],
                                "embed_type" => [
                                    "type" => "field",
                                    "name" => "embed_type",
                                    "label" => __("Embed Type", "zombify"),
                                    "field_type" => "hidden",
                                ],
                                "embed_variables" => [
                                    "type" => "field",
                                    "name" => "embed_variables",
                                    "label" => __("Embed Variables", "zombify"),
                                    "field_type" => "hidden",
                                ],
                                "embed_description" => [
                                    "type" => "field",
                                    "name" => "embed_description",
                                    "label" => __("Description", "zombify"),
                                    "field_type" => "textarea",
                                ],
                                "embed_credit" => [
                                    "type" => "field",
                                    "name" => "image_image_credit",
                                    "label" => __("Source URL", "zombify"),
                                    "field_type" => "url",
                                    "show_dependency" => "story/embedd/embed_original_source",
                                    "rules" => [
                                        "url" => "1"
                                    ],
                                ],
                                "embed_credit_text" => [
                                    "type" => "field",
                                    "name" => "image_image_credit_text",
                                    "label" => __("Credit", "zombify"),
                                    "field_type" => "text",
                                    "show_dependency" => "story/embedd/embed_original_source"
                                ],
                                "embed_original_source" => [
                                    "type" => "field",
                                    "name" => "image_original_source",
                                    "label" => __("Via", "zombify"),
                                    "field_type" => "checkbox",
                                    "show_dependency" => "story/embedd/embed_url"
                                ],
                            ]
                        ],

                        "poll" => [
                            "type" => "group",
                            "name" => "poll",
                            "fields" => [
                                "questions" => [
                                    "type" => "group",
                                    "name" => "questions",
                                    "alias_class" => "Poll",
                                    "alias_group" => "questions",
                                ],
                            ]
                        ],

                        "personality" => [
                            "type" => "group",
                            "name" => "personality",
                            "fields" => [
                                "results" => [
                                    "type" => "group",
                                    "name" => "results",
                                    "alias_class" => "Personality",
                                    "alias_group" => "results",
                                ],
                                "questions" => [
                                    "type" => "group",
                                    "name" => "questions",
                                    "alias_class" => "Personality",
                                    "alias_group" => "questions",
                                ],
                            ]
                        ],

                        "trivia" => [
                            "type" => "group",
                            "name" => "trivia",
                            "fields" => [
                                "results" => [
                                    "type" => "group",
                                    "name" => "results",
                                    "alias_class" => "Trivia",
                                    "alias_group" => "results",
                                ],
                                "questions" => [
                                    "type" => "group",
                                    "name" => "questions",
                                    "alias_class" => "Trivia",
                                    "alias_group" => "questions",
                                ],
                            ]
                        ],

                        "story_list" => [
                            "type" => "group",
                            "name" => "story_list",
                            "fields" => [
                                "list" => [
                                    "type" => "group",
                                    "name" => "list",
                                    "alias_class" => "List",
                                    "alias_group" => "list",
                                ],
                            ]
                        ],
						
                        "story_countdown" => [
                            "type" => "group",
                            "name" => "story_countdown",
                            "fields" => [
                                "list" => [
                                    "type" => "group",
                                    "name" => "list",
                                    "alias_class" => "Countdown",
                                    "alias_group" => "list",
                                ],
                            ]
                        ],
						
                        "audio" => [
                            "type" => "group",
                            "name" => "audio",
                            "fields" => [
                                "audio" => [
                                    "type" => "group",
                                    "name" => "audio",
                                    "alias_class" => "Audio",
                                    "alias_group" => "audio",
                                ],
                            ]
                        ],

                        "video" => [
                            "type" => "group",
                            "name" => "video",
                            "fields" => [
                                "video" => [
                                    "type" => "group",
                                    "name" => "video",
                                    "alias_class" => "Video",
                                    "alias_group" => "video",
                                ],
                            ]
                        ],

                        "gif" => [
                            "type" => "group",
                            "name" => "gif",
                            "fields" => [
                                "gif" => [
                                    "type" => "group",
                                    "name" => "gif",
                                    "alias_class" => "Gif",
                                    "alias_group" => "gif",
                                ],
                            ]
                        ],

                    ]
                ],


            ];

        }
		
		//to handle gif,video subtypes Featured Media
		public function updateFeaturedMedia( $data, $post_id, $attachment_id = null, $media_data = array() ){
			$zf_config = zombify()->get_config();
			$first_group_type = isset( $zf_config["post_sub_types"][$this->subtype]["first_group"] ) ? $zf_config["post_sub_types"][$this->subtype]["first_group"] : '';

			if( $first_group_type == 'gif') {
                /* If just uploaded handle it, otherwise handle earlier uploaded (with the index `1000`) */
				if( ! empty( $data["story"][0]["gif"][0]["gif"][0]["image_image"][0]["attachment_id"] ) || ! empty( $data["story"][0]["gif"][0]["gif"][0]["image_image"][1000]["attachment_id"] ) ) {
                    $first_group_attachment_id = ! empty( $data["story"][0]["gif"][0]["gif"][0]["image_image"][0]["attachment_id"] )
                        ? $data["story"][0]["gif"][0]["gif"][0]["image_image"][0]["attachment_id"]
                        : $data["story"][0]["gif"][0]["gif"][0]["image_image"][1000]["attachment_id"];

					return parent::updateFeaturedMedia($data, $post_id, $first_group_attachment_id, $media_data);
				}
			}
			if($first_group_type == 'video') {
				$mediatype = !empty( $data["story"][0]['video'][0]['video'][0]['mediatype'] ) ? $data["story"][0]['video'][0]['video'][0]['mediatype'] : '';
				$videofile = !empty( $data["story"][0]['video'][0]['video'][0]['videofile'] ) ? $data["story"][0]['video'][0]['video'][0]['videofile'] : '';
				$embed_url = !empty( $data["story"][0]['video'][0]['video'][0]['embed_url'] ) ? $data["story"][0]['video'][0]['video'][0]['embed_url'] : '';
				$embed_type = !empty( $data["story"][0]['video'][0]['video'][0]['embed_type'] ) ? $data["story"][0]['video'][0]['video'][0]['embed_type'] : '';
				if($mediatype == 'image' && !empty($videofile)) {
					//set Featured Media based on uploaded video
					return parent::updateFeaturedMedia($data, $post_id, $videofile, $media_data);
				} elseif($mediatype == 'embed' && !empty($embed_url)) {
					//set Featured Media based on uploaded embed, we still need to find a way to provide thumbnail as attachment
					$media_data['media_id'] = false;
					$media_data["media_mime_type"] = 'embed/' . strtolower($embed_type);
					$media_data["media_url"] = $embed_url;
					return parent::updateFeaturedMedia($data, $post_id, null, $media_data);
				}
			}
			
			parent::updateFeaturedMedia($data, $post_id, $attachment_id, $media_data);
		}

        /**
         * Set featured image for story
         *
         * @param int $post_id
         *
         * @return bool
         */
        public function setFeaturedImage( $post_id ) {

            $featured =  $this->getFeaturedImageByPriority();
            if( !empty($featured["attachment_id"]) ){
                /* If there is generated jpeg, set as featured image (e.g. for gif) */
                $attachment_jpeg_id = get_post_meta($featured["attachment_id"], 'zombify_jpeg_id', true);

                $thumbnail_id = (
                    $attachment_jpeg_id > 0
                    && wp_update_post( array('ID' => $attachment_jpeg_id, 'post_parent' => $post_id ) ) > 0
                )
                    ? $attachment_jpeg_id
                    : $featured["attachment_id"];
            } else {
                //todo:no sure if we need this case at all
                $thumbnail_id = $this->handleFeaturedImage( $featured, $post_id );
            }

            $this->setPostThumbnail( $thumbnail_id, $post_id );

            return true;

        }

    }

}