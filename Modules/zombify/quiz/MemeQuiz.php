<?php
/**
 * Zombify Meme Quiz
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_MemeQuiz") ) {

    /**
     * Class Zombify_MemeQuiz
     */
    class Zombify_MemeQuiz extends Zombify_BaseQuiz
    {
        /**
         * Quiz view file
         *
         * @var string
         */
        public $view_path = 'meme';

        /**
         * Quiz slug
         *
         * @var string
         */
        public $slug = 'meme';

        /**
         * Zombify Meme Quiz Structure
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
                    "show_dependency" => "image_image"
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
                ],
                "settings" => [
                    "type" => "field",
                    "name" => "settings",
                    "label" => __("Settings", "zombify"),
                    "field_type" => "hidden",
                ],
                "readyimage" => [
                    "type" => "field",
                    "name" => "readyimage",
                    "label" => __("Ready image", "zombify"),
                    "field_type" => "hidden",
                ],
                "meme_template" => [
                    "type" => "field",
                    "name" => "meme_template",
                    "label" => __("Meme template", "zombify"),
                    "field_type" => "hidden",
                ]

            ];

        }

        /**
         * Set featured image of post
         *
         * @param int $post_id
         *
         * @return bool
         */
        public function setFeaturedImage( $post_id ){

            $upload_dir = wp_upload_dir();

            $img = $this->data["readyimage"];

            $img_data = explode( ',', $img );

            if( isset($img_data[1]) && base64_encode(base64_decode($img_data[1])) === $img_data[1] ) { // Checking if string is base64 encoded

                $decoded = base64_decode($img_data[1]);

                $filename = 'readyimage_' . $post_id . '.jpg';

                $file_data_up_path = $upload_dir['path'] . '/' . $filename;

                file_put_contents($file_data_up_path, $decoded);

                zf_insert_attachment($post_id, $file_data_up_path, 'image/png', true);

                $this->data["readyimage"] = $upload_dir['url'] . '/' . basename($file_data_up_path);

            }

            return true;

        }

        /**
         * Save original image for meme template
         *
         * @return void
         */
        public static function saveMemeTemplateImage( $data, $post_id ) {

            if( isset($data['meme_template']) && $data['meme_template'] !== '' ) {

                $image_path         = zf_modify_meme_template_url( $data['meme_template'] );
                $url_file_data      = zf_get_file_by_url($image_path, false, true);
                $file_data_up_path  = $url_file_data["uploaded"]["file"];
                $mime_type          = $url_file_data["type"];
	            if (empty($url_file_data['attachment_id'])) {
	                $attach_data = zf_insert_attachment( $post_id, $file_data_up_path, $mime_type, false );
	                $attach_id   = $attach_data["id"];
	            } else {
		            $attach_id = $url_file_data['attachment_id'];
	            }

                zf_save_downloaded_attachment( $post_id, $attach_id, $image_path );

            }

        }

    }

}