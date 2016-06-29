<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://fbgm.eu
 * @since             1.0.0
 * @package           Clarity_Manga_Reader
 *
 * @wordpress-plugin
 * Plugin Name:       Clarity Manga Reader
 * Plugin URI:        fbgm.eu
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            George Florea Banus
 * Author URI:        http://fbgm.eu
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       clarity-manga-reader
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once(ABSPATH . 'wp-admin/includes/file.php');

define( 'CMR_URL'        , plugin_dir_url( __FILE__ ) );
define( 'CMR_PLUGIN_DIR' , plugin_dir_path( __FILE__ ) );
define( 'CMR_PLUGIN_BASENAME'   , plugin_basename(__FILE__) );

define( 'CMR_DIR_PATH'   , get_home_path() . get_option('cmr_dir_name') . '/' );
define( 'CMR_DIR_NAME'   , get_option('cmr_dir_name') );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-clarity-manga-reader-activator.php
 */
function activate_clarity_manga_reader() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-clarity-manga-reader-activator.php';
	Clarity_Manga_Reader_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-clarity-manga-reader-deactivator.php
 */
function deactivate_clarity_manga_reader() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-clarity-manga-reader-deactivator.php';
	Clarity_Manga_Reader_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_clarity_manga_reader' );
register_deactivation_hook( __FILE__, 'deactivate_clarity_manga_reader' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-clarity-manga-reader.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_clarity_manga_reader() {

	$plugin = new Clarity_Manga_Reader();
	$plugin->run();

}
run_clarity_manga_reader();
