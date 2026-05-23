<?php
/**
 * Zombify Poll Quiz
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_PollQuiz") ) {

    /**
     * Class Zombify_PollQuiz
     */
    class Zombify_PollQuiz extends Zombify_BaseQuiz
    {
        /**
         * Quiz view file
         *
         * @var string
         */
        public $view_path = 'poll';

        /**
         * Quiz slug
         *
         * @var string
         */
        public $slug = 'poll';

        /**
         * Zombify Poll Quiz Structure
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
                "questions" => [
                    "type" => "group",
                    "name" => "questions",
                    "fields" => [
                        "question_id" => [
                            "type" => "field",
                            "name" => "question_id",
                            "label" => __("Question ID", "zombify"),
                            "field_type" => "uniqueid",
                        ],
                        "question" => [
                            "type" => "field",
                            "name" => "question",
                            "label" => __("Question", "zombify"),
                            "field_type" => "text",
                            "rules" => [
                                "required"
                            ]
                        ],
                        "mediatype" => [
                            "type" => "field",
                            "name" => "mediatype",
                            "label" => __("Media type", "zombify"),
                            "field_type" => "radio",
                        ],
                        "image" => [
                            "type" => "field",
                            "name" => "image",
                            "label" => __("Browse Image", "zombify"),
                            "field_type" => "file",
                            "rules" => [
                                "extensions" => "png, jpg, gif, jpeg, webp, avif",
                                "maxSize" => zf_max_upload_size() / 1024
                            ],
                            "use_as_featured" => true,
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
                        "description" => [
                            "type" => "field",
                            "name" => "description",
                            "label" => __("Description", "zombify"),
                            "field_type" => "textarea",
                        ],
                        "answers_format" => [
                            "type" => "field",
                            "name" => "answers_format",
                            "label" => __("Answers format", "zombify"),
                            "field_type" => "radio",
                        ],
                        "original_source" => [
                            "type" => "field",
                            "name" => "original_source",
                            "label" => __("Via", "zombify"),
                            "field_type" => "checkbox",
                            "show_dependency" => [
                                "questions/image",
                                "questions/image_file_url",
                                "questions/embed_url",
                            ]
                        ],
                        "image_credit" => [
                            "type" => "field",
                            "name" => "image_credit",
                            "label" => __("Source URL", "zombify"),
                            "field_type" => "url",
                            "show_dependency" => "questions/original_source",
                            "rules" => [
                                "url" => "1"
                            ],
                        ],
                        "image_credit_text" => [
                            "type" => "field",
                            "name" => "image_credit_text",
                            "label" => __("Credit", "zombify"),
                            "field_type" => "text",
                            "show_dependency" => "questions/original_source"
                        ],
                        "answers" => [
                            "type" => "group",
                            "name" => "answers",
                            "fields" => [
                                "answer_id" => [
                                    "type" => "field",
                                    "name" => "answer_id",
                                    "label" => __("Answer ID", "zombify"),
                                    "field_type" => "uniqueid",
                                ],
                                "answer_text" => [
                                    "type" => "field",
                                    "name" => "answer_text",
                                    "label" => __("Answer text", "zombify"),
                                    "field_type" => "textarea",
                                    "rules" => [
                                        "required"
                                    ]
                                ],
                                "image" => [
                                    "type" => "field",
                                    "name" => "image",
                                    "label" => __("Browse Image", "zombify"),
                                    "field_type" => "file",
                                    "rules" => [
                                        "extensions" => "jpeg, png, gif, jpg, webp, avif",
                                        "maxSize" => zf_max_upload_size() / 1024
                                    ]
                                ],
                                "image_credit" => [
                                    "type" => "field",
                                    "name" => "image_credit",
                                    "label" => __("Source URL", "zombify"),
                                    "field_type" => "url",
                                    "rules" => [
                                        "url" => "1"
                                    ],
                                ],
                            ]
                        ]
                    ]
                ]
            ];

        }

    }

}