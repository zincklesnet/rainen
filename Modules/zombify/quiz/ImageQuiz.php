<?php
/**
 * Zombify Image Quiz
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_ImageQuiz") ) {

    /**
     * Class Zombify_ImageQuiz
     */
    class Zombify_ImageQuiz extends Zombify_BaseQuiz
    {
        /**
         * Quiz view file
         *
         * @var string
         */
        public $view_path = 'image';

        /**
         * Quiz slug
         *
         * @var string
         */
        public $slug = 'image';

        /**
         * Zombify Image Quiz Structure
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
                "image_image" => [
                    "type" => "field",
                    "name" => "image_image",
                    "label" => __("Browse Image", "zombify"),
                    "field_type" => "file",
                    "rules" => [
                        "extensions" => "png, jpg, gif, jpeg, webp, avif",
                        "maxSize" => zf_max_upload_size() / 1024
                    ],
                    "use_as_featured" => true
                ],
                "original_source" => [
                    "type" => "field",
                    "name" => "original_source",
                    "label" => __("Via", "zombify"),
                    "field_type" => "checkbox",
                    "show_dependency" => ["image_image", "image_image_file_url"]
                ],
                "image_credit" => [
                    "type" => "field",
                    "name" => "image_credit",
                    "label" => __("Source URL", "zombify"),
                    "field_type" => "url",
                    "show_dependency" => "original_source",
                    "rules" => [
                        "url" => "1"
                    ],
                ],
                "image_credit_text" => [
                    "type" => "field",
                    "name" => "image_credit_text",
                    "label" => __("Credit", "zombify"),
                    "field_type" => "text",
                    "show_dependency" => "original_source"
                ],
                "image_description" => [
                    "type" => "field",
                    "name" => "image_description",
                    "label" => __("Description", "zombify"),
                    "field_type" => "textarea",
                ]

            ];

        }

    }

}