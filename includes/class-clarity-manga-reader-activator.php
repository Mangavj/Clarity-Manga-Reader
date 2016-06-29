<?php

/**
 * Fired during plugin activation
 *
 * @link       http://fbgm.eu
 * @since      1.0.0
 *
 * @package    Clarity_Manga_Reader
 * @subpackage Clarity_Manga_Reader/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Clarity_Manga_Reader
 * @subpackage Clarity_Manga_Reader/includes
 * @author     George Florea Banus <george@fbgm>
 */
class Clarity_Manga_Reader_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$cmr_chapters = $wpdb->prefix . 'cmr_chapters';
		$cmr_images = $wpdb->prefix . 'cmr_images';

		// create the chapters database table
		if($wpdb->get_var( "show tables like '$cmr_chapters'" ) != $cmr_chapters)
		{
			$sql = "CREATE TABLE " . $cmr_chapters . " (
			`id`             int(9) NOT NULL AUTO_INCREMENT,
			`manga_id`       int(9) NOT NULL,
			`chapter_name`   varchar(255) NOT NULL,
			`chapter_number` mediumint(9) NOT NULL,
			`chapter_volume` mediumint(9) NOT NULL,
			UNIQUE KEY id (id)
			);";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}

		// create the images database table
		if($wpdb->get_var( "show tables like '$cmr_images'" ) != $cmr_images)
		{
			$sql = "CREATE TABLE " . $cmr_images . " (
			`id`         int(9) NOT NULL AUTO_INCREMENT,
			`chapter_id` int(9) NOT NULL,
			`image_name` varchar(255) NOT NULL,
			UNIQUE KEY id (id)
			);";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		if( !file_exists(get_home_path() . '/wp-manga') ){
			mkdir(get_home_path() . '/wp-manga');
		}

		add_option( 'cmr_dir_name', 'wp-manga' );
		add_option( 'cmr_js_navigation', 1 );
		add_option( 'cmr_per_page', 10 );
		add_option( 'cmr_show_text', 1 );
		add_option( 'cmr_display_chapters_list', 1 );
		add_option( 'cmr_reader_page', 'read' );

		add_option( 'cmr_max_up_size', 2 );
		add_option( 'cmr_allowed_extensions', 'jpg, jpeg, png' );

		if (get_page_by_title('Read') == NULL) {
			$read = array(
				'post_title'    => 'Read',
				'post_content'  => '[cmr_read]',
				'post_type'     => 'page',
				'post_status'   => 'publish',
			);
			wp_insert_post( $read );
		}
	}

}
