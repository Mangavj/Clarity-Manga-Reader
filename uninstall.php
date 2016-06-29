<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://fbgm.eu
 * @since      1.0.0
 *
 * @package    Clarity_Manga_Reader
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if( get_option( 'cmr_delete_tables' ) ){
	global $wpdb;
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cmr_chapters" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cmr_images" );
}

if( get_option( 'cmr_delete_files' ) ){
        $dir = get_home_path() . get_option('cmr_dir_name');
		if ( file_exists($dir) ) {
			$files = new RecursiveIteratorIterator(
			    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
			    RecursiveIteratorIterator::CHILD_FIRST
			);

			foreach ($files as $fileinfo) {
			    $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
			    $todo($fileinfo->getRealPath());
			}

			rmdir($dir);
		}
}

if( get_option( 'cmr_delete_tables' ) ){
	delete_option('cmr_enable_manga_post_type');
	delete_option('cmr_dir_name');
	delete_option('cmr_max_up_size');
	delete_option('cmr_allowed_extensions');
	delete_option('cmr_per_page');
	delete_option('cmr_show_text');
	delete_option('cmr_reader_page');
	delete_option('cmr_js_navigation');
	delete_option('cmr_display_chapters_list');
	delete_option('cmr_delete_tables');
	delete_option('cmr_delete_files');
	delete_option('cmr_delete_settings');
}
