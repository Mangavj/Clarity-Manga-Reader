<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://fbgm.eu
 * @since      1.0.0
 *
 * @package    Clarity_Manga_Reader
 * @subpackage Clarity_Manga_Reader/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Clarity_Manga_Reader
 * @subpackage Clarity_Manga_Reader/includes
 * @author     George Florea Banus <george@fbgm>
 */
class Clarity_Manga_Reader {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Clarity_Manga_Reader_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'clarity-manga-reader';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Clarity_Manga_Reader_Loader. Orchestrates the hooks of the plugin.
	 * - Clarity_Manga_Reader_i18n. Defines internationalization functionality.
	 * - Clarity_Manga_Reader_Admin. Defines all hooks for the admin area.
	 * - Clarity_Manga_Reader_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-clarity-manga-reader-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-clarity-manga-reader-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/manage.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-clarity-manga-reader-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-clarity-manga-reader-public.php';

		$this->loader = new Clarity_Manga_Reader_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Clarity_Manga_Reader_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Clarity_Manga_Reader_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Clarity_Manga_Reader_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'plugin_action_links_' . CMR_PLUGIN_BASENAME, $plugin_admin, 'cmr_pp_settings_link' );

		$this->loader->add_action( 'admin_enqueue_scripts' , $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts' , $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'post_updated'          , $plugin_admin, 'check_values', 10, 3 );
		$this->loader->add_action( 'delete_post'           , $plugin_admin, 'wpmr_delete_manga_dir', 10 );
		$this->loader->add_action( 'init'                  , $plugin_admin, 'register_manga_post_type' );
		$this->loader->add_action( 'add_meta_boxes'        , $plugin_admin, 'add_chapter_meta_box' );
		$this->loader->add_action( 'admin_menu'            , $plugin_admin, 'cmr_add_settings_menu');
		$this->loader->add_action( 'admin_init'            , $plugin_admin, 'settings_api_init' );
		$this->loader->add_action( 'update_option'         , $plugin_admin, 'check_directory_name', 10, 3 );
		/* AJAX actions */
		$this->loader->add_action( 'wp_ajax_cmr_upload'    , $plugin_admin, 'cmr_upload');
		$this->loader->add_action( 'wp_ajax_create_chapter', $plugin_admin, 'createChapter' );
		$this->loader->add_action( 'wp_ajax_delete_chapter', $plugin_admin, 'deleteChapter' );
		$this->loader->add_action( 'wp_ajax_edit_chapter'  , $plugin_admin, 'editChapter' );
		$this->loader->add_action( 'wp_ajax_load_images'   , $plugin_admin, 'loadImages' );
		$this->loader->add_action( 'wp_ajax_delete_image'  , $plugin_admin, 'deleteImage' );
		$this->loader->add_action( 'wp_ajax_cmr_pagination', $plugin_admin, 'cmr_pagination' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Clarity_Manga_Reader_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'the_content',  $plugin_public, 'cmr_add_chapter_list_content' );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'init',        $plugin_public, 'cmr_rewrite_rules' );
		$this->loader->add_filter( 'query_vars',  $plugin_public, 'cmr_query_vars' );

		$this->loader->add_action( 'wp_ajax_ajax_get_prev_chapter_images',        $plugin_public, 'ajax_get_prev_chapter_images' ); // ajax for logged in users
		$this->loader->add_action( 'wp_ajax_nopriv_ajax_get_prev_chapter_images', $plugin_public, 'ajax_get_prev_chapter_images' ); // ajax for not logged in users


		$this->loader->add_shortcode( 'cmr_chapters_list', $plugin_public, 'cmr_chaptersListShortcode' );
		$this->loader->add_shortcode( 'cmr_read', $plugin_public, 'cmr_read' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Clarity_Manga_Reader_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
