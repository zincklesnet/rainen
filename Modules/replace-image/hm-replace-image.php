<?php
/**
 * Plugin Name: Replace Image
 * Description: Upload a new version of an image without deleting the old image attachment, so that references to the image remain intact.
 * Version: 1.1.11
 * Author: WP Zone
 * Author URI: https://wpzone.co/?utm_source=replace-image&utm_medium=link&utm_campaign=wp-plugin-author-uri
 * License: GNU General Public License version 3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */
 
/*
Replace Image
Copyright (C) 2024  WP Zone

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.

=====

This plugin contains material based on WordPress. For WordPress
licensing and copyright details, see ./license/wp-license.txt.
*/

define('WPZ_REPLACE_IMAGE_VERSION', '1.1.10');

add_action('admin_menu', 'hm_replace_image_admin_menu');
function hm_replace_image_admin_menu() {
	add_submenu_page('tools.php', 'Replace Image', 'Replace Image', 'upload_files', 'hm_replace_image', 'hm_replace_image_page');
}

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'hm_replace_image_action_links');
function hm_replace_image_action_links($links) {
	array_unshift($links, '<a href="'.esc_url(get_admin_url(null, 'tools.php?page=hm_replace_image')).'">Instructions</a>');
	return $links;
}

function hm_replace_image_page() {
	echo('
		<div class="wrap">
			<h2>Replace Image</h2>
			<p>To use this tool, click the Replace Image button on the Attachment Details screen for the image you wish to replace (e.g. by clicking on the image in the Media Library). Then upload or select the image to use as a replacement. Do not choose an image already in use elsewhere, since the replacement image will be moved to overwrite the original image.</p>
			<h3>Important:</h3>
			<p>In order for this plugin to work correctly, please ensure the following:</p>
			<ol>
				<li><strong>Disable all WordPress caching plugins during development.</strong></li>
				<li>
					<strong>Disable your browser\'s cache.</strong><br /><br />
					In Google Chrome, hit F12 to open Developer Tools, click the Network tab, and ensure that &quot;Disable Cache&quot; is checked. Leave the Developer Tools window open during development to keep this setting in effect.<br /><br />
					<img src="'.esc_url(plugins_url('images/disable-cache-chrome.png', __FILE__)).'" alt="Disabling the cache in Google Chrome" /><br /><br />
					In Mozilla Firefox, hit F12, click the gearwheel icon, and ensure that &quot;Disable Cache (when toolbox is open)&quot; is checked. Leave the toolbox window open during development to keep this setting in effect.<br /><br />
					<img src="'.esc_url(plugins_url('images/disable-cache-firefox.png', __FILE__)).'" alt="Disabling the cache in Mozilla Firefox" />
				</li>
			</ol><br />
	');
	$potent_slug = 'replace-image';
	include(__DIR__.'/plugin-credit.php');
	echo('
		</div>
	');
}


add_action('admin_enqueue_scripts', 'hm_replace_image_enqueue_scripts');
function hm_replace_image_enqueue_scripts() {
	wp_enqueue_script('hm-replace-image', plugins_url('js/hm-replace-image.js', __FILE__), [], WPZ_REPLACE_IMAGE_VERSION, true);
}

add_action('edit_attachment', 'hm_replace_image_edit_attachment');
function hm_replace_image_edit_attachment($postId) {
	if (!empty($_POST['replaceWith']) && is_numeric($_POST['replaceWith']) && !empty($_POST['replaceWithNonce']) && wp_verify_nonce($_POST['replaceWithNonce'], 'hm_replace_image') && current_user_can('edit_post', $postId)) {

		$uploadDir = wp_upload_dir();
		$newFile = $uploadDir['basedir'].'/'.get_post_meta($_POST['replaceWith'], '_wp_attached_file', true);
		
		// Make sure the new file exists before proceeding
		if (!is_file($newFile)) {
			return false;
		}
		
		// Delete the old attachment's files
		hm_replace_image_delete_attachment($postId);
		
		$oldFile = $uploadDir['basedir'].'/'.get_post_meta($postId, '_wp_attached_file', true);
		if (!file_exists(dirname($oldFile)))
			wp_mkdir_p(dirname($oldFile));

		global $wp_filesystem;
		if (WP_Filesystem() && $wp_filesystem->copy($newFile, $oldFile)) {
			$meta = wp_generate_attachment_metadata($postId, $oldFile);
			wp_update_attachment_metadata($postId, $meta);

			if (current_user_can('delete_post', $_POST['replaceWith'])) {
				wp_delete_attachment($_POST['replaceWith'], true);
			}
		}
	}
}

/** Code copied from WordPress wp-includes/post.php and modified **/
function hm_replace_image_delete_attachment( $post_id ) {
	$meta = wp_get_attachment_metadata( $post_id );
	$backup_sizes = get_post_meta( $post_id, '_wp_attachment_backup_sizes', true );
	$file = get_attached_file( $post_id );

	if ( is_multisite() )
		delete_transient( 'dirsize_cache' );

	$uploadpath = wp_get_upload_dir();

	if ( ! empty($meta['thumb']) ) {
		$thumbfile = str_replace(basename($file), $meta['thumb'], $file);
		/** This filter is documented in wp-includes/functions.php */
		$thumbfile = apply_filters( 'wp_delete_file', $thumbfile );
		@ unlink( path_join($uploadpath['basedir'], $thumbfile) );
	}

	// Remove intermediate and backup images if there are any.
	if ( isset( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
		foreach ( $meta['sizes'] as $size => $sizeinfo ) {
			$intermediate_file = str_replace( basename( $file ), $sizeinfo['file'], $file );
			/** This filter is documented in wp-includes/functions.php */
			$intermediate_file = apply_filters( 'wp_delete_file', $intermediate_file );
			@ unlink( path_join( $uploadpath['basedir'], $intermediate_file ) );
		}
	}

	if ( is_array($backup_sizes) ) {
		foreach ( $backup_sizes as $size ) {
			$del_file = path_join( dirname($meta['file']), $size['file'] );
			/** This filter is documented in wp-includes/functions.php */
			$del_file = apply_filters( 'wp_delete_file', $del_file );
			@ unlink( path_join($uploadpath['basedir'], $del_file) );
		}
	}
	wp_delete_file( $file );
}
/** End code copied from WordPress **/






add_filter('attachment_fields_to_edit', 'hm_replace_image_attachment_fields', 10, 2);
function hm_replace_image_attachment_fields($fields, $attachment) {
	if (current_user_can('edit_post', $attachment->ID)) {
	wp_enqueue_media();
	$fields['hm_image_replace'] = array();
	$fields['hm_image_replace']['label'] = '';
	$fields['hm_image_replace']['input'] = 'html';
	$fields['hm_image_replace']['html'] = '
		<button type="button" class="button-secondary button-large" onclick="hm_replace_image();">Replace Image</button>
		<button type="button" class="button-secondary button-large" onclick="'.(class_exists('WpzAiImageLab') ? 'hm_replace_image();window.jQuery&&setTimeout(function(){window.jQuery(\'button[id=\\\'menu-item-wpzai\\\']:visible:first\').click();}, 500);' : 'alert(\'This feature requires the free AI Image Lab plugin.\');window.open(\''.esc_url(admin_url('plugin-install.php?s=AI%20Image%20Lab%20by%20WP%20Zone&tab=search&type=term')).'\');').'">Replace with AI Image</button>
		<input type="hidden" id="hm_replace_image_with_fld" name="replaceWith" />
		'.wp_nonce_field('hm_replace_image', 'replaceWithNonce', false, false).'
		<p><strong>Warning:</strong> Replacing this image with another one will permanently delete the current image file, and the replacement image will be moved to overwrite this one. <a href="'.esc_url(get_admin_url(null, 'tools.php?page=hm_replace_image')).'" target="_blank">Instructions</a></p>
	';
	}

	return $fields;
}


add_filter('wp_calculate_image_srcset', 'hm_replace_image_calculate_image_srcset');
function hm_replace_image_calculate_image_srcset($sources) {
	if (is_admin()) {
		foreach ($sources as $size => $source) {
			$source['url'] .= (strpos($source['url'], '?') === false ? '?' : '&').'_t='.time();
			$sources[$size] = $source;
		}
	}
	return $sources;
}

add_filter('wp_get_attachment_image_src', 'hm_replace_image_get_attachment_image_src');
function hm_replace_image_get_attachment_image_src($attr) {
	if (is_admin() && !empty($attr[0])) {
		$attr[0] .= (strpos($attr[0], '?') === false ? '?' : '&').'_t='.time();
	}
	return $attr;
}

add_filter('wp_prepare_attachment_for_js', 'hm_replace_image_prepare_attachment_for_js');
function hm_replace_image_prepare_attachment_for_js($response) {
	if (is_admin()) {
		if (strpos($response['url'], '?') !== false)
			$response['url'] .= (strpos($response['url'], '?') === false ? '?' : '&').'_t='.time();
		if (isset($response['sizes'])) {
			foreach ($response['sizes'] as $sizeName => $size) {
				$response['sizes'][$sizeName]['url'] .= (strpos($size['url'], '?') === false ? '?' : '&').'_t='.time();
			}
		}
	}
	return $response;
}


/* Review/donate notice */

register_activation_hook(__FILE__, 'hm_replace_image_first_activate');
function hm_replace_image_first_activate() {
	$pre = 'hm_replace_image';
	$firstActivate = get_option($pre.'_first_activate');
	if (empty($firstActivate)) {
		update_option($pre.'_first_activate', time());
	}
}
if (is_admin() && get_option('hm_replace_image_rd_notice_hidden') != 1 && time() - get_option('hm_replace_image_first_activate') >= (14*86400)) {
	add_action('admin_notices', 'hm_replace_image_rd_notice');
	add_action('wp_ajax_hm_replace_image_rd_notice_hide', 'hm_replace_image_rd_notice_hide');
}
function hm_replace_image_rd_notice() {
	$pre = 'hm_replace_image';
	$slug = 'replace-image';
	echo('
		<div id="'.esc_attr($pre).'_rd_notice" class="updated notice is-dismissible"><p>Does the <strong>Replace Image</strong> plugin make your life easier?
		Please support our free plugin by <a href="'.esc_url('https://wordpress.org/support/view/plugin-reviews/'.$slug).'" target="_blank">writing a review</a>!
		Thanks!</p></div>
		<script>jQuery(document).ready(function($){$(\'#'.sanitize_key($pre).'_rd_notice\').on(\'click\', \'.notice-dismiss\', function(){jQuery.post(ajaxurl, {action:\'hm_replace_image_rd_notice_hide\'})});});</script>
	');
}
function hm_replace_image_rd_notice_hide() {
	$pre = 'hm_replace_image';
	update_option($pre.'_rd_notice_hidden', 1);
}


?>