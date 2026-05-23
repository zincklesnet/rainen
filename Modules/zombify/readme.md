Zombify Plugin Documentation
==============================
This documentation will describe the structure and functionality of the Zombify Plugin.

### Files Structure
```
/assets/            * Assets files like css, scss, js, images, fonts etc...
/config/            * Configuration files
/controllers/       * Admin and Public Controllers
/includes/          * Functions, Hooks, Libraries, Extensions etc...
/languages/         * .pot file for translations
/quiz/              * Post types objects with fields hierarchy, structure, validation rules stc...
/site-helpers/      * Node modules
/views/             * HTML view templates
/zombify.php        * Main plugin file
```

### Configuration file
File location: /config/config.php
```
<?php
	$zf_config = [
		'zf_post_types' => [
			"personality" => array(
                'name'          => esc_html__("Personality quiz", "zombify"),
                'description'   => esc_html__("Series of questions that intends to reveal something about the personality", "zombify"),
                'order'         => 1,
                'excerpt'       => 0,
                'preface'       => 0,
                'show'          => 1,
            ),
            "trivia" => array(
                'name'          => esc_html__("Trivia quiz", "zombify"),
                'description'   => esc_html__("Series of questions with right and wrong answers that intends to check knowledge", "zombify"),
                'order'         => 2,
                'excerpt'       => 0,
                'preface'       => 0,
                'show'          => 1,
            ),
           ...
           ...
           ...
		],
        'zf_editor' => [
            'zf_editor_adv_height'              => 250,
            'zf_editor_lt_height'               => 175,
            'zf_editor_adv_char_counter_max'    => 25000,
            'zf_editor_lt_char_counter_max'     => 25000,
            'zf_editor_paragraphs'              => [
                'N'     => 'Normal',
                'H1'    => 'Heading 1',
                'H2'    => 'Heading 2',
            ],
            'zf_editor_adv_toolbar'             => ['paragraphFormat','bold', 'italic', 'underline', 'strikeThrough', 'align', 'formatOL', 'formatUL', 'insertTable', 'insertImage', 'insertLink', 'undo', 'redo', 'quote', 'html'],
            'zf_editor_lt_toolbar'              => ['bold', 'italic', 'underline', 'strikeThrough', 'align', 'formatOL', 'formatUL', 'insertLink', 'undo', 'redo'],
            'zf_editor_adv_image_default_width' => 0,
            'zf_editor_lt_image_default_width'  => 0,
            'zf_editor_adv_placeholder'         => '',
            'zf_editor_lt_placeholder'          => '',
            'zf_editor_loader'                  => plugins_url('zombify') . '/assets/images/loading.gif',
            'zf_editor_paste_plain'             => false,
        ],
        'zf_excerpt_characters_limit' => 165,
        'sub_groups_labels' => array(
            "text" => __("Text", "zombify"),
            "image" => __("Image", "zombify"),
            "embedd" => __("Embed", "zombify"),
            "link" => __("Link", "zombify"),
            "poll" => __("Poll", "zombify"),
            "personality" => __("Personality Quiz", "zombify"),
            "trivia" => __("Trivia", "zombify"),
            "story_list" => __("Listicle", "zombify"),
            "audio" => __("Audio", "zombify"),
            "video" => __("Video", "zombify"),
            "gif" => __("Gif", "zombify"),
        ),
        "post_sub_types" => array(
            "main" => array(
                "name"     => esc_html__("Story", "zombify"),
                "description"     => esc_html__("Formatted Text with Embeds and Visuals", "zombify"),
                "formats"   => array(),
                "icon" => "story",
                'excerpt'       => 0,
                'preface'       => 0,
                'show'          => 0,
                'first_group'   => '',
                'order'         => 1,
            ),
            "personality" => array(
                "name"     => esc_html__("Personality quiz", "zombify"),
                "description"     => "",
                "formats"   => array(),
                "icon" => "personality",
                'excerpt'       => 0,
                'preface'       => 0,
                'show'          => 0,
                'first_group'   => 'personality',
                'order'         => 2,
            ),
            ...
            ...
            ...
        ),
	];
?>
```
In this configuration file we have ['zf_post_types'] which contains the configuration of each post type, it must be added this way ...
```
"example_post_type_slug" => array(
    'name'          => esc_html__("Example post type name", "zombify"),         * Post type name
    'description'   => esc_html__("Example post type description", "zombify"),  * Post type description
    'order'         => 1,                                                       * Post type order in types listing
    'excerpt'       => 0,                                                       * Use excerpt or not
    'preface'       => 0,                                                       * Use preface or not
    'show'          => 1,                                                       * Show or hide the post type
),
```
In story post type we have sub groups, which labels are described in ["sub_groups_labels"] ...
```
'sub_groups_labels' => array(
    "text" => __("Text", "zombify"),
    "image" => __("Image", "zombify"),
    ...
),
```
For changing the all post types to have the story type functionality, we have created ["post_sub_types"] where we can create new post types which will extend all the story functionality, having it's additional configurations. A post sub type with "main" slug is required, never remove it.
```
"example_sub_type" => array(
    "name"     => esc_html__("Example subtype name", "zombify"),                * Subtype name
    "description"     => esc_html__("Example subtype description", "zombify"),  * Subtype description
    "formats"   => array(),                                                     *
    "icon" => "story",                                                          * Icon in types list
    'excerpt'       => 0,                                                       * Use excerpt or not
    'preface'       => 0,                                                       * Use preface or not
    'show'          => 0,                                                       * Show or hide the post type
    'first_group'   => '',                                                      * Which group must be the first opened by default
    'order'         => 1,                                                       * Order number in post types list
),
```

### Quiz type objects
Files location: /quiz/{PostTypeSlug}Quiz.php
Each post type has it's unique structure and hierarchy, and it is represented in this files. All these classes must be extended from Zombify_BaseQuiz abstract class.
All these classes must contain publicly available "slug" property
```
public $slug = 'example_post_type_slug';
```
Also for representing the hierarchy of the post type, we must have here a publicly available "structure()" function. Here is an example from "List" post type:
```
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
        ...
        ...
        ...
        "list"=>[
            "type" => "group",
            "name" => "list",
            "fields"=> [
                "title" => [
                    "type" => "field",
                    "name" => "title",
                    "label" => __("Title", "zombify"),
                    "field_type" => "text",
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
                    "use_as_featured" => true
                ],
                ...
                ...
                ...
            ]

        ]

    ];

}
```
In this structure we can have fields and groups of fields.
##### Fields
A field is a separate element (text, image, embed, video etc) which is represented with it properties for input type, validation rules, name, label etc...
For example:
```
"example_field_slug" => [
    "type" => "field",                               * type represents that this array element is a field
    "name" => "example_field_slug",                  * the slug of the field, which is the same as the index of this array element
    "label" => __("Example field label", "zombify"), * Label of the field
    "field_type" => "text",                          * Field type in HTML. It can be also textarea, url, hidden, checkbox, file etc...
    "show_dependency" => "path/to/another/field",    * Show the field when another field will have content
    "rules" => [                                     * Validation rules for the field
        "required", 
    ],
    "use_as_featured" => true,                       * if this field is an image, then it can be used as featured image, if the featured image is not uploaded
    "field_visibility" => 'show'                     * Show the field or not, it can have show/hidden values
],
```

##### Group of fields
We have groups of fields for having some separate box of fields with same business logic which can be repeated multiple times. For example one item in a List is a group with some fields, it can be repeated multiple times, so the list will have multiple items.
```
"example_group_slug"=>[
    "type" => "group",                  * type represents that this array element is a group of fields
    "name" => "example_group_slug",     * the slug of the group, it is the same as the index of current array element
    "fields"=> [
        ...
        ...                             * Fields which are described before
        ...
    ]
]
```
One post type can have multiple groups. Also the groups can have multiple subgroups, so the hierarchy is implemented and parsed recursively.

##### Groups aliases
If we have to use a post type as a sub group of another one, we shouldn't copy the functionality, we can just use aliases of groups.
```
"example_slug_of_group" => [
    "type" => "group",
    "name" => "example_slug_of_group",
    "fields" => [
        "example_slug_of_aliased_group" => [
            "type" => "group",
            "name" => "example_slug_of_aliased_group",
            "alias_class" => "ExampleAliasClass",
            "alias_group" => "example_slug_of_aliased_group",
        ],
    ]
],
```