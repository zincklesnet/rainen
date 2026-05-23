<?php
/**
 * Zombify Audio Quiz
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_AudioQuiz") ) {

    /**
     * Class Zombify_AudioQuiz
     */
    class Zombify_AudioQuiz extends Zombify_BaseQuiz
    {
        /**
         * Quiz view file
         *
         * @var string
         */
        public $view_path = 'audio';

        /**
         * Quiz slug
         *
         * @var string
         */
        public $slug = 'audio';

        /**
         * Zombify Audio Quiz Structure
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
                "audio" => [
                    "type" => "group",
                    "name" => "audio",
                    "fields" => [
                        "original_source" => [
                            "type" => "field",
                            "name" => "original_source",
                            "label" => __("Via", "zombify"),
                            "field_type" => "checkbox",
                            "show_dependency" => [
                                "audio/videofile",
                                "audio/embed_url",
                            ]
                        ],
                        "audio_credit" => [
                            "type" => "field",
                            "name" => "audio_credit",
                            "label" => __("Source URL", "zombify"),
                            "field_type" => "url",
                            "show_dependency" => "audio/original_source",
                            "rules" => [
                                "url" => "1"
                            ],
                        ],
                        "audio_credit_text" => [
                            "type" => "field",
                            "name" => "audio_credit_text",
                            "label" => __("Credit", "zombify"),
                            "field_type" => "text",
                            "show_dependency" => "audio/original_source"
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
                        "audio_description" => [
                            "type" => "field",
                            "name" => "audio_description",
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
                            "label" => __("Audio", "zombify"),
                            "field_type" => "video",
                            "field_format" => "audio",
                            "rules" => [
                                "extensions" => "mp3",
                                "maxSize" => zombify()->get_audio_upload_max_size()
                            ],
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

    }

}