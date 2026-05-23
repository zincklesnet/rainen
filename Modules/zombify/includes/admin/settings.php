<?php
/**
 * Zombify Admin Settings
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/*
* Add Zombify admin settings page in left menu
*/
add_action( 'admin_menu', 'zombify_admin_settings_menu' );
if ( ! function_exists( 'zombify_admin_settings_menu' ) ) {

	function zombify_admin_settings_menu() {
		add_menu_page(
			__( 'Zombify', 'zombify' ),
			__( 'Zombify', 'zombify' ),
			'manage_options',
			'zombify',
			'zombify_admin_settings_controller',
			'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8IS0tIENyZWF0b3I6IENvcmVsRFJBVyBYNiAtLT4NCjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMTYwbW0iIGhlaWdodD0iMTYwbW0iIHZlcnNpb249IjEuMSIgc2hhcGUtcmVuZGVyaW5nPSJnZW9tZXRyaWNQcmVjaXNpb24iIHRleHQtcmVuZGVyaW5nPSJnZW9tZXRyaWNQcmVjaXNpb24iIGltYWdlLXJlbmRlcmluZz0ib3B0aW1pemVRdWFsaXR5IiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCINCnZpZXdCb3g9IjAgMCAxNjAwMCAxNjAwMCINCiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+DQogPGcgaWQ9IkxheWVyX3gwMDIwXzEiPg0KICA8bWV0YWRhdGEgaWQ9IkNvcmVsQ29ycElEXzBDb3JlbC1MYXllciIvPg0KICA8cGF0aCBmaWxsPSIjMjAxRTFFIiBkPSJNNzI2MiA0NzI0YzEyNiw5OSAyNzksMTcxIDQwNSwxMzMgMjM3LC03MCAzMzMsLTE1MCAzMzUsLTQxOGwtMzYxIC0xMTcyIC03NyAtMjIzIDU3OCAtODNjOTcsMzI1IDE1Nyw1NzYgMzIwLDg0NCAxMDgsMTI2IDI3MywxNzIgNDIzLDE1NSAyMTcsLTI0IDI2MiwtMTE4IDMxMCwtMjkyIC0yNiwtMjQ4IC05NiwtNDk1IC0yMzUsLTc0MyA2NDYsNDMgMTk2NywzOTEgMjU1MSw2NjEgMzA3LDEwNCA1NjQsLTIzMyAxNTIsLTQ5NiAtNDUwLC0yNDAgLTEwMzIsLTQ0MSAtMTUyMywtNTkzIDg5NywtNzExIDIxNzksLTE1NzEgMzEzNCwtMjE3MWwyMiAyOTZjMTM2LDEwNTUgMzYxLDIyOTEgNzgyLDMyNjIgODgzLDIwMzcgMTY3MiwzMzA5IDE2ODgsNDgxOSAyNywyNDk5IC0xMjQ4LDU1NzggLTUzMzYsNjU5OGwtMTcyIC00NzYgMzk5IC0xNDkgOTcgLTE1NyAtMTc0IC0xMzYgLTQzMCAxMDUgLTExMSAtNDkxYzE1LDU3IDQ0MiwtNzEgNTYxLC05MGwxMjUgLTIxMSAtMTk2IC0xNzcgLTU2MSAxMTAgLTIzIC0yMzMgNTExIC0xNDcgODYgLTI0MyAtMjA5IC0yNDYgLTU1NSAxNjIgLTEwNCAtNDkzIC00NDAgLTg0IC05NiAyMDkgMTc0IDQ1NiAtNzg2IDIzMyAtMTA0IDE1NCAxNzIgMTIyIDc1OSAtODQgODIgMjE2Yy0yMzcsMTIxIC00NjksMTQ4IC03MzksMTcxbC0xMDQgMzAyIDE3MyAxODEgNzY2IC0xNDQgMTQ2IDQxNCAtOTI4IDE2MiAtNzggMjM1IDE1MyAxMzAgODc1IC0yMzEgODcgNTY1Yy0xMTUyLDIzMiAtMjQ1NywxMDIgLTMzODQsLTg0IC04MzgsLTE2OCAtMTY1OSwtMzkwIC0zMDc2LC0xNDA4IC0xOTM5LC0xMzk0IC0yNjY3LC0zNzI5IC0yMzAwLC02MDQzIDkwLC01NjkgMTgxLC0xMTQ5IC0xMDgsLTIzNjQgLTE2NywtOTM2IC0zNTAsLTE4OTQgLTY1MiwtMjc5NWw1MDUgMTM5YzExNDUsNDEyIDIyOTcsNTIzIDM0MzUsOTIybC0xMTI1IDEwMzdjLTMwMSw1MDkgMTc2LDc2OSA0MTQsNDc4IDQyNCwtNTY4IDgzOCwtOTAyIDE0MzcsLTEyNTJsMjgzIC0yMDMgMjU3IDUxMWMzMTQsMzEgNzQzLDc2IDc3MywtMzE1bC0yNDEgLTU4NmMxOTUsLTEyNSA2MjEsLTIxOCA3OTksLTI3MSAxOTUsNDcwIDM0MiwxMDExIDQ1NiwxNTE5em00MDE3IDM3OTBjLTI1MywtNDIgLTMxNCwtNzU2IC0zNzksLTEwMjIgLTE2NywtNzgxIC02MSwtOTc1IDkwLC0xMDE3IDIxMCwtNTggNTMzLDMwNCA1NDksODkzIDkxLDQ1OSAyNzIsMTA2MyAtMjYxLDExNDV6bTI3NDUgLTE0OTJjMTA5LDQzMiAtMTUzLDE1NzYgLTQyNSwxOTQzIC00MTgsNTY0IC05NTgsNzg3IC0xNTYxLDEwNzlsNTkzIDE2N2MyODQsMTA2IDM5OSwyMDYgMzIxLDMzOSAtMTQxLDI0MiAtNTE2LDI4MiAtMTM5MSw2IC04MzEsLTI2MyAtMTE2MiwtMjQwIC0xNjc0LC00NzkgLTc3NCwtMzYxIC0xMzk5LC0xMDE0IC0xNjMwLC0xODMwIC0yMzksLTg0NiAyMSwtMTc1MiA2MTQsLTI0MTEgMTY1NiwtMTgzOSA0NjA3LC05NzcgNTE1MywxMTg3em0tMzIzMiAtMTQ1MGMtMTA4MCwyMjUgLTE4NjAsMTMzNCAtMTc4NywyMTY4IDgwLDkxNCAxMTA5LDE4NzIgMjMyNCwxODM1IDExMTEsLTM0IDE4MTksLTExNTUgMTg0NCwtMjAyMiAyOCwtOTcwIC05ODQsLTIyNzEgLTIzODEsLTE5ODB6bS0yMjEzIDUxNjNjNzcsMjUzIDM5Myw2MzAgMTcxLDgyNSAtNTQ5LDQ4MiAtNjg4LC0yMjYgLTUxNSwtNDc0bDM0NCAtMzUxem0zODQgLTkzYzQyLDIxMSA5LDExMDEgNTI4LDc4OCA1NzAsLTM0NCAtMzQyLC02NzcgLTUzNiwtODAwbDkgMTF6bS01MjUzIC0xNTYwYzI1MywxNDQgNDg0LDE2NCA3MzIsMzQwbC0xNTggNDgwYy0xMjksMzIyIC0yNjAsNTkwIDIwNyw3MzUgNjMzLDE5NiA2NzAsLTQ0MiA4ODUsLTg3OCAxOTQsNjMgNDUzLDE0MCA2NDAsMTk0IDIwNCw1OSA1NTAsLTc5IDYxNSwtMjY5IDExNywtMzQyIC0yODksLTQ3OSAtNTEwLC01NjkgLTEzMCwtNTYgLTMxNCwtMTAwIC00NDEsLTE2MGwyMDggLTY0OGMxNDIsLTQzMSAtNjc0LC05MTcgLTk1OSwtMTU1bC0xNzkgNDY0Yy0yMDgsLTEwOSAtNTA2LC0xNzAgLTczMiwtMjQ0IC0yMjcsLTczIC03MTcsNDc2IC0zMDgsNzA4eiIvPg0KIDwvZz4NCjwvc3ZnPg0K'
		);
	}

}

/**
 * Register settings
 */
add_action( 'admin_init', 'zf_register_settings' );
if ( ! function_exists( 'zf_register_settings' ) ) {

	function zf_register_settings() {

		register_setting( 'zf-settings-group', 'zombify_frontend_page' );
		register_setting( 'zf-settings-group', 'zombify_post_create_page' );
		register_setting( 'zf-settings-group', 'zombify_max_upload_size' );
		register_setting( 'zf-settings-group', 'zombify_max_upload_mp4_size' );
		register_setting( 'zf-settings-group', 'zombify_max_upload_mp3_size' );
		register_setting( 'zf-settings-group', 'zombify_disable_mp3_upload' );
		register_setting( 'zf-settings-group', 'zombify_disable_mp4_upload' );
		register_setting( 'zf-settings-group', 'zf_tags_limit' );
		register_setting( 'zf-settings-group', 'zf_categories_limit' );
		register_setting( 'zf-settings-group', 'zombify_allowed_cats' );
		register_setting( 'zf-settings-group', 'zombify_contributor_can_submit' );
		register_setting( 'zf-settings-group', 'zombify_disable_meme_templates' );
		register_setting( 'zf-settings-group', 'zf_disable_congratulations_popup' );
		register_setting( 'zf-settings-group', 'zombify_media_width_equal_post_width' );

		register_setting( 'zf-settings-group-branding', 'zombify_logo' );
		register_setting( 'zf-settings-group-branding', 'zombify_branding_color' );
		register_setting( 'zf-settings-group-branding', 'zombify_color_mode' );

		register_setting( 'zf-settings-group-formats', 'zombify_active_formats' );
		register_setting( 'zf-settings-group-formats', 'zombify_post_categroies' );
		register_setting( 'zf-settings-group-formats', 'zombify_post_tags' );
		register_setting( 'zf-settings-group-formats', 'zombify_post_savetype' );
		register_setting( 'zf-settings-group-formats', 'zombify_types_order' );
		register_setting( 'zf-settings-group-formats', 'zombify_subtypes_order' );

		register_setting( 'zf-settings-group-formats-story', 'zombify_story_formats' );
		register_setting( 'zf-settings-group-formats-story', 'zombify_story_format_order' );

		register_setting( 'zf-settings-group-cloudconvert', 'zombify_cloudconvert_api_key' );
		register_setting( 'zf-settings-group-cloudconvert', 'zombify_facebook_app_id' );
		register_setting( 'zf-settings-group-cloudconvert', 'zombify_facebook_app_secret' );
		register_setting( 'zf-settings-group-cloudconvert', 'zombify_twitch_app_id' );
		register_setting( 'zf-settings-group-cloudconvert', 'zombify_twitch_app_secret' );

	}

}