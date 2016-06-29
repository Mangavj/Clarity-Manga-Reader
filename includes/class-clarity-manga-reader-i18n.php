<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://fbgm.eu
 * @since      1.0.0
 *
 * @package    Clarity_Manga_Reader
 * @subpackage Clarity_Manga_Reader/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Clarity_Manga_Reader
 * @subpackage Clarity_Manga_Reader/includes
 * @author     George Florea Banus <george@fbgm>
 */
class Clarity_Manga_Reader_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'clarity-manga-reader',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
