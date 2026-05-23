'use strict';
module.exports = function (grunt) {

	// load all grunt tasks matching the `grunt-*` pattern
	// Ref. https://npmjs.org/package/load-grunt-tasks
	require('load-grunt-tasks')(grunt);
	grunt.initConfig(
		{

			// Check text domain
			checktextdomain: {
				options: {
					text_domain: ['buddypress-profanity', 'buddypress'], // Specify allowed domain(s)
					keywords: [ // List keyword specifications
						'__:1,2d',
						'_e:1,2d',
						'_x:1,2c,3d',
						'esc_html__:1,2d',
						'esc_html_e:1,2d',
						'esc_html_x:1,2c,3d',
						'esc_attr__:1,2d',
						'esc_attr_e:1,2d',
						'esc_attr_x:1,2c,3d',
						'_ex:1,2c,3d',
						'_n:1,2,4d',
						'_nx:1,2,4c,5d',
						'_n_noop:1,2,3d',
						'_nx_noop:1,2,3c,4d'
					]
				},
				target: {
					files: [{
						src: [
							'*.php',
							'**/*.php',
							'!node_modules/**',
							'!options/framework/**',
							'!tests/**'
						], // all php
						expand: true
					}]
				}
			},

			// Task for CSS minification
			cssmin: {
				admin: {
					files: [{
						expand: true,
						cwd: 'admin/css/', // Source directory for admin CSS files
						src: ['*.css', '!*.min.css', '!vendor/*.css'], // Minify all admin CSS files except already minified ones
						dest: 'admin/css/min/', // Destination directory for minified admin CSS
						ext: '.min.css', // Extension for minified files
					}],
				},
				wbcom: {
					files: [{
						expand: true,
						cwd: 'admin/wbcom/assets/css/', // Source directory for admin CSS files
						src: ['*.css', '!*.min.css', '!vendor/*.css'], // Minify all admin CSS files except already minified ones
						dest: 'admin/wbcom/assets/css/min/', // Destination directory for minified admin CSS
						ext: '.min.css', // Extension for minified files
					}],
				},
			},

			// Task for JavaScript minification
			uglify: {
				admin: {
					options: {
						mangle: false, // Prevents variable name mangling
					},
					files: [{
						expand: true,
						cwd: 'admin/js/', // Source directory for admin JS files
						src: ['*.js', '!*.min.js', '!vendor/*.js'], // Minify all admin JS files except already minified ones
						dest: 'admin/js/min/', // Destination directory for minified admin JS
						ext: '.min.js', // Extension for minified files
					}],
				},
				wbcom: {
					options: {
						mangle: false, // Prevents variable name mangling
					},
					files: [{
						expand: true,
						cwd: 'admin/wbcom/assets/js', // Source directory for admin JS files
						src: ['*.js', '!*.min.js', '!vendor/*.js'], // Minify all admin JS files except already minified ones
						dest: 'admin/wbcom/assets/js/min/', // Destination directory for minified admin JS
						ext: '.min.js', // Extension for minified files
					}],
				},
			},

			// Task for watching file changes
			watch: {
				adminCss: {
					files: ['admin/css/*.css'], // Watch for changes in admin CSS files
					tasks: ['cssmin:admin'], // Run admin CSS minification task
				},
				adminJs: {
					files: ['admin/js/*.js'], // Watch for changes in admin JS files
					tasks: ['uglify:admin'], // Run admin JS minification task
				},
				php: {
					files: ['**/*.php'], // Watch for changes in PHP files
					tasks: ['checktextdomain'], // Run text domain check
				},
			},

			// Task for generating RTL CSS
			rtlcss: {
				myTask: {
					options: {
						// Generate source maps
						map: { inline: false },
						// RTL CSS options
						opts: {
							clean: false
						},
						// RTL CSS plugins
						plugins: [],
						// Save unmodified files
						saveUnmodified: true,
					},
					files: [
						{
							expand: true,
							cwd: 'admin/css/', // Source directory for admin CSS
							src: ['**/*.min.css', '!vendor/**/*.css'], // Source files, excluding vendor CSS
							dest: 'admin/css/rtl/', // Destination directory for admin RTL CSS
							ext: '.rtl.css', // Extension for RTL files
							flatten: true // Prevents creating subdirectories
						},
						{
							expand: true,
							cwd: 'admin/wbcom/assets/css/', // Source directory for public CSS
							src: ['**/*.min.css', '!vendor/**/*.css'], // Source files, excluding vendor CSS
							dest: 'admin/wbcom/assets/css/rtl/', // Destination directory for public RTL CSS
							ext: '.rtl.css', // Extension for RTL files
							flatten: true // Prevents creating subdirectories
						}
					]
				}
			},
			shell: {
				wpcli: {
					command: 'wp i18n make-pot . languages/buddypress-profanity.pot',
				}
			}
		}
	);

	// Load the plugins
	grunt.loadNpmTasks('grunt-wp-i18n');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-checktextdomain');
	grunt.loadNpmTasks('grunt-rtlcss');
	grunt.loadNpmTasks('grunt-shell');

	// Register default tasks
	grunt.registerTask('default', ['cssmin', 'uglify', 'checktextdomain', 'rtlcss', 'shell', 'watch']);
};
