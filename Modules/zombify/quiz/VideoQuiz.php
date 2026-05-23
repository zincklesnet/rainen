<?php
/**
 * Zombify Video Quiz
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_VideoQuiz") ) {

    /**
     * Class Zombify_VideoQuiz
     */
    class Zombify_VideoQuiz extends Zombify_BaseQuiz
    {
        /**
         * Quiz view file
         *
         * @var string
         */
        public $view_path = 'video';

        /**
         * Quiz slug
         *
         * @var string
         */
        public $slug = 'video';

        /**
         * Zombify Video Quiz Structure
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
                    "field_visibility" => ( isset( zombify()->post_main_fields[ $this->slug ]["image"] ) ? zombify()->post_main_fields[ $this->slug ]["image"] : 'show' )
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
                "video" => [
                    "type" => "group",
                    "name" => "video",
                    "fields" => [
                        "original_source" => [
                            "type" => "field",
                            "name" => "original_source",
                            "label" => __("Via", "zombify"),
                            "field_type" => "checkbox",
                            "show_dependency" => [
                                "video/videofile",
                                "video/embed_url",
                            ]
                        ],
                        "video_credit" => [
                            "type" => "field",
                            "name" => "video_credit",
                            "label" => __("Source URL", "zombify"),
                            "field_type" => "url",
                            "show_dependency" => "video/original_source",
                            "rules" => [
                                "url" => "1"
                            ],
                        ],
                        "video_credit_text" => [
                            "type" => "field",
                            "name" => "video_credit_text",
                            "label" => __("Credit", "zombify"),
                            "field_type" => "text",
                            "show_dependency" => "video/original_source"
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
                        "video_description" => [
                            "type" => "field",
                            "name" => "video_description",
                            "label" => __("Description", "zombify"),
                            "field_type" => "textarea",
                        ],
                        "mediatype" => [
                            "type" => "field",
                            "name" => "mediatype",
                            "label" => __("Media type", "zombify"),
                            "field_type" => "radio",
                        ],
                        "videofile" => [
                            "type" => "field",
                            "name" => "videofile",
                            "label" => __("Video", "zombify"),
                            "field_type" => "video",
                            "field_format" => "video",
                            "rules" => [
                                "extensions" => "mp4, webm",
                                "maxSize" => zombify()->get_video_upload_max_size()
                            ],
                            "use_as_featured" => true,
                        ],
                        "video_external" => [
                            "type" => "field",
                            "name" => "video_external",
                            "label" => __("Paste Video URL", "zombify"),
                            "field_type" => "url",
                            "rules" => [
                                "url" => "1"
                            ],
                        ],
                    ]
                ]

            ];

        }
		
		public function updateFeaturedMedia( $data, $post_id, $attachment_id = null, $media_data = array() ){

			if( $data['video'][0]['mediatype'] == 'image' && !empty($data['video'][0]['videofile']) ){
				//set Featured Media based on uploaded video
				return parent::updateFeaturedMedia($data, $post_id, $data['video'][0]['videofile'], $media_data);
			} elseif($data['video'][0]['mediatype'] == 'embed' && !empty($data['video'][0]['embed_url'])) {
				//set Featured Media based on uploaded embed, we still need to find a way to provide thumbnail as attachment
				$media_data['media_id'] = false;
				$media_data["media_mime_type"] = 'embed/' . strtolower($data['video'][0]['embed_type']);
				$media_data["media_url"] = $data['video'][0]['embed_url'];
				return parent::updateFeaturedMedia($data, $post_id, null, $media_data);
			}
			parent::updateFeaturedMedia($data, $post_id, $attachment_id);
		}

    }

}