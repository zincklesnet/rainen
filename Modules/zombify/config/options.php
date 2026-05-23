<?php
$post_tags = array();

foreach( zombify()->get_post_types() as $ptype => $ptype_label )
    $post_tags[ $ptype ] = $ptype;

$zf_options = array(
    "zombify_post_tags" => $post_tags
);

return $zf_options;