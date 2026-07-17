<?php
/**
 * Reign options page - Lucide icon helper.
 *
 * Returns inline Lucide SVGs (stroke 1.75, currentColor) for the options-page
 * UI. Vendored inline so there is no Dashicons / font-icon dependency, per the
 * Wbcom UX foundation (Lucide only).
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'reign_options_icon' ) ) {

	/**
	 * Inner <path>/<rect>/<circle> markup for each supported Lucide icon.
	 *
	 * @return array<string, string>
	 */
	function reign_options_icon_paths() {
		return array(
			'image'        => '<rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>',
			'type'         => '<path d="M4 7V4h16v3"/><path d="M9 20h6"/><path d="M12 4v16"/>',
			'droplet'      => '<path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/>',
			'panel-top'    => '<rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/>',
			'panel-bottom' => '<rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 15h18"/>',
			'link'         => '<path d="M9 17H7A5 5 0 0 1 7 7h2"/><path d="M15 7h2a5 5 0 1 1 0 10h-2"/><line x1="8" x2="16" y1="12" y2="12"/>',
		);
	}

	/**
	 * Return an inline Lucide SVG string.
	 *
	 * @param string $name Icon name (see reign_options_icon_paths()).
	 * @param int    $size Pixel size for width/height.
	 * @return string Safe inline <svg>, or empty string for an unknown name.
	 */
	function reign_options_icon( $name, $size = 20 ) {
		$paths = reign_options_icon_paths();
		if ( ! isset( $paths[ $name ] ) ) {
			return '';
		}

		return sprintf(
			'<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
			(int) $size,
			$paths[ $name ]
		);
	}
}
