<?php
/**
 * Zombify Gif Quiz
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !class_exists("Zombify_GifQuiz") ) {

    /**
     * Class Zombify_GifQuiz
     */
    class Zombify_GifQuiz extends Zombify_BaseQuiz
    {
        /**
         * Quiz view file
         *
         * @var string
         */
        public $view_path = 'gif';

        /**
         * Quiz slug
         *
         * @var string
         */
        public $slug = 'gif';

        /**
         * Zombify Gif Quiz Structure
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
                "gif" => [
                    "type" => "group",
                    "name" => "gif",
                    "fields" => [
                        "image_image" => [
                            "type" => "field",
                            "name" => "image_image",
                            "label" => __("Browse File", "zombify"),
                            "field_type" => "file",
                            "rules" => [
                                "extensions" => "gif,mp4",
                                "maxSize" => zf_get_option("zombify_max_upload_size") / 1024
                            ],
                            "use_as_featured" => true
                        ],
                        "original_source" => [
                            "type" => "field",
                            "name" => "original_source",
                            "label" => __("Via", "zombify"),
                            "field_type" => "checkbox",
                            "show_dependency" => array("gif/image_image", "gif/image_image_file_url")
                        ],
                        "image_credit" => [
                            "type" => "field",
                            "name" => "image_credit",
                            "label" => __("Source URL", "zombify"),
                            "field_type" => "url",
                            "show_dependency" => "gif/original_source",
                            "rules" => [
                                "url" => "1"
                            ],
                        ],
                        "image_credit_text" => [
                            "type" => "field",
                            "name" => "image_credit_text",
                            "label" => __("Credit", "zombify"),
                            "field_type" => "text",
                            "show_dependency" => "gif/original_source"
                        ],
                        "image_description" => [
                            "type" => "field",
                            "name" => "image_description",
                            "label" => __("Description", "zombify"),
                            "field_type" => "textarea",
                        ]
                    ]
                ]

            ];

        }

		
		public function updateFeaturedMedia( $data, $post_id, $attachment_id = null, $media_data = array() ){
			//set Featured Media based on uploaded GIF
			if( isset( $data["gif"][0]["image_image"][0]["attachment_id"] ) || isset( $data["gif"][0]["image_image"][1000]["attachment_id"] ) ) {
                /* If just uploaded handle it, otherwise handle earlier uploaded (with the index `1000`) */
				$attachment_id = isset( $data["gif"][0]["image_image"][0]["attachment_id"] )
                    ? $data["gif"][0]["image_image"][0]["attachment_id"]
                    : $data["gif"][0]["image_image"][1000]["attachment_id"];
				$gif_url = wp_get_attachment_url( $attachment_id );
				$media_data["zombify_gif_id"]   = $attachment_id;
				$media_data["zombify_gif_url"]  = $gif_url;
				
				return parent::updateFeaturedMedia($data, $post_id, $attachment_id, $media_data);
			}
			parent::updateFeaturedMedia($data, $post_id, $attachment_id, $media_data);
		}

        /**
         * Set featured image of post GIF former `child` image
         *
         * @param int $post_id
         *
         * @return bool
         */
		 //todo: we need to handle this for story subtypes as well
        public function setFeaturedImage( $post_id ){

            $thumbnail_id = 0;
			$featured =  $this->getFeaturedImageByPriority( );
			
			if ('gif/image_image' == implode('/', $featured['path'])) {
				//todo: can we have a case, when zombify_jpeg_id is not defined?
				$thumbnail_id = get_post_meta($featured["attachment_id"], 'zombify_jpeg_id', true);
				if ($thumbnail_id) {
					wp_update_post( array('ID' => $thumbnail_id, 'post_parent' => $post_id ));
				}
			} else {
				//handle featured image in native functionality
				if( !empty($featured["attachment_id"]) ){
					$thumbnail_id = $featured["attachment_id"];
				} else {
					//todo:no sure if we need this case at all
					$thumbnail_id = $this->handleFeaturedImage( $featured, $post_id );
				}
			}
			
			$this->setPostThumbnail( $thumbnail_id, $post_id );

            return true;

        }

    }

}