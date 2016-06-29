<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://fbgm.eu
 * @since      1.0.0
 *
 * @package    Clarity_Manga_Reader
 * @subpackage Clarity_Manga_Reader/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Clarity_Manga_Reader
 * @subpackage Clarity_Manga_Reader/admin
 * @author     George Florea Banus <george@fbgm>
 */
class Clarity_Manga_Reader_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since      1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Clarity_Manga_Reader_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Clarity_Manga_Reader_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/clarity-manga-reader-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Clarity_Manga_Reader_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Clarity_Manga_Reader_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$ext = array_map('trim', explode(',', get_option('cmr_allowed_extensions')));
		wp_enqueue_script( 'jquery-ui-progressbar' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/clarity-manga-reader-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'fine-uploade', plugin_dir_url( __FILE__ ) . 'js/fine-uploader.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'clarity', array(
			'ajaxurl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'clarity' ),
			'path'        => CMR_DIR_PATH . sanitize_title(get_the_title()) . '_' . get_the_id(),
			'manga_obj'   => get_post(get_the_id()),
			'btn_text'    => get_option('cmr_show_text'),
			'max_up_size' => get_option('cmr_max_up_size'),
			'allowed_ext' => $ext,
		));

	}


	/**
	 * This function checks if the manga title has changed,
	 * if it did it renames the folder where it's chapters and images are stored.
	 *
	 */
	public function check_values( $post_ID, $post_after, $post_before ) {
		$manga_title = sanitize_title( get_the_title() );

		if ( $post_before->post_title != $post_after->post_title )
		{
			$before = get_home_path() . CMR_DIR_NAME . '/' . $manga_title . '_' . $post_before->ID;
			$after  = get_home_path() . CMR_DIR_NAME . '/' . sanitize_title( $post_after->post_title ). '_' . $post_after->ID;
			rename( $before, $after );
		}
	}


	public function mt_change_manga_dir_name() {
	   if( $this->mt_check_title_change() == true ) {
		   rename( get_home_path() . MT_DIR_NAME . '/' . $manga_title, get_home_path() . MT_DIR_NAME . '/' . sanitize_title( $post_after->post_title ) );
	   }
	}

	/**
	 */
	function check_directory_name( $option,  $old_value,  $value ) {
	  	if ( $old_value != $value AND $old_value == CMR_DIR_NAME )
	  	{
	  		rename( get_home_path() . $old_value, get_home_path() . $value );
	  	}
	}

	/*
	 *
	 * AJAX responses
	 *
	 */
	function createChapter()
	{
		global $wpdb;
		$vars = array($_POST['number'], $_POST['id']);
		$chapter_number_exists = Manage::constructQuery(
			"SELECT * FROM {$wpdb->prefix}cmr_chapters WHERE chapter_number=%d AND manga_id=%d",
			$vars
		 );
		$single_manga_dir = CMR_DIR_PATH . sanitize_title($_POST['title'] . '_' . $_POST['id']);

		if( !is_numeric($_POST['number'])  )
		{
			$array['error'] = 'The chapter number has to be a number.';
			exit(json_encode($array));
		}

		if( $_POST['volume'] != NULL && !is_numeric($_POST['volume'])  )
		{
			$array['error'] = 'The chapter volume has to be a number.';
			exit(json_encode($array));
		}

		else if( !is_numeric($_POST['id']) )
		{
			$array['error'] = 'Something went wrong. Reload the page.';
			exit(json_encode($array));
		}

		else if( $_POST['number'] == NULL || $_POST['name'] == NULL )
		{
			$array['error'] = 'The chapter name and number are both required.';
			exit(json_encode($array));
		}

		else if(  $_POST['name'] != strip_tags($_POST['name']) )
		{
			$array['error'] = 'No html allowed.';
			exit(json_encode($array));
		}

		else if( !empty($chapter_number_exists) )
		{
			$array['error'] = 'Chapter number already exists.';
			exit(json_encode($array));
		}

		else
		{
			$wpdb->insert( $wpdb->prefix . 'cmr_chapters', array(
				'chapter_name'     => htmlentities($_POST['name'], ENT_QUOTES | ENT_HTML401),
				'chapter_number'   => $_POST['number'],
				'chapter_volume'   => $_POST['volume'],
				'manga_id'         => $_POST['id'],
			));
			$chapters = new manage();
			$array['chapter']     = $chapters->getChapter($wpdb->insert_id);
			$array['manga_title'] = sanitize_title($_POST['title']);
			$array['path']        = CMR_DIR_PATH;
			$array['admin_ajax']  = admin_url('admin-ajax.php');
			if(!file_exists($single_manga_dir))
			{
				mkdir($single_manga_dir);
			}
			exit(json_encode($array));
		}
		die();
	}


/*
	function wpmr_delete_manga_dir( $mid ) {
	    rmdir( CMR_DIR_PATH . get_post($mid)->post_name );
	}
*/
	/*
	 *
	 *
	 *
	 */
	function loadImages()
	{
		global $wpdb;

		$array['chapter_images'] = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'cmr_images WHERE chapter_id=' . $_POST['chapter_id'], OBJECT );
		$array['chapter_number'] = $wpdb->get_var( 'SELECT chapter_number FROM ' . $wpdb->prefix . 'cmr_chapters WHERE id='.$_POST['chapter_id'] );
		$array['manga_dir_url'] = site_url() . '/' . CMR_DIR_NAME . '/' . sanitize_title( get_the_title( $_POST['manga_id'] ) ) .'_' . $_POST['manga_id']  . '/';
		exit(json_encode($array));
		die();
	}

	/*
	 *
	 *
	 *
	 */
	function deleteImage()
	{
		$image_id = $_POST['image_id'];
		$manage = new manage();
		$delete = $manage->deleteImage($image_id);
		die();
	}

	/*
	 *
	 *
	 *
	 */
	function editChapter()
	{
		global $wpdb;
		$chapter_id = $_POST['chapter_id'];
		$new_name   = $_POST['name'];
		$manage = new manage();
		$edit = $wpdb->update(
			$wpdb->prefix . 'cmr_chapters',
			array(
				'chapter_name' => $new_name	// string
			),
			array( 'ID' => $chapter_id )
		);
		return $edit;
		die();
	}

	/*
	 *
	 *
	 *
	 */
	function deleteChapter()
	{
		$chapter_id = $_POST['chapter_id'];
		$manage = new manage();
		$delete = $manage->deleteChapter($chapter_id);
		die();
	}

		/******************************************
		*** Register the manga post type **********
		*******************************************/
		public function register_manga_post_type() {
			if( get_option('cmr_enable_manga_post_type') == 1 ){
				$labels = array(
					'name'               => _x( 'Manga', 'post type general name', 'wp-manga-reader' ),
					'singular_name'      => _x( 'Manga', 'post type singular name', 'wp-manga-reader' ),
					'menu_name'          => _x( 'Manga', 'admin menu', 'wp-manga-reader' ),
					'name_admin_bar'     => _x( 'Manga', 'add new on admin bar', 'wp-manga-reader' ),
					'add_new'            => _x( 'Add New', 'Manga', 'wp-manga-reader' ),
					'add_new_item'       => __( 'Add New Manga', 'wp-manga-reader' ),
					'new_item'           => __( 'New Manga', 'wp-manga-reader' ),
					'edit_item'          => __( 'Edit Manga', 'wp-manga-reader' ),
					'view_item'          => __( 'View Manga', 'wp-manga-reader' ),
					'all_items'          => __( 'All Manga', 'wp-manga-reader' ),
					'search_items'       => __( 'Search Manga', 'wp-manga-reader' ),
					'parent_item_colon'  => __( 'Parent Manga:', 'wp-manga-reader' ),
					'not_found'          => __( 'No manga found.', 'wp-manga-reader' ),
					'not_found_in_trash' => __( 'No manga found in Trash.', 'wp-manga-reader' )
				);

				$args = array(
					'labels'             => $labels,
					'description'        => __( 'Description.', 'wp-manga-reader' ),
					'public'             => true,
					'publicly_queryable' => true,
					'show_ui'            => true,
					'show_in_menu'       => true,
					'query_var'          => true,
					'rewrite'            => array( 'slug' => 'manga' ),
					'capability_type'    => 'post',
					'has_archive'        => true,
					'hierarchical'       => false,
					'menu_position'      => null,
					'supports'           => array( 'editor', 'title', 'thumbnail', 'comments' )
				);

				register_post_type( 'manga', $args );
			}
		}


	/**
	 * Add chapters metabox.
	 */
	function add_chapter_meta_box() {
		add_meta_box(
			'manga_chapters',
			__( 'Manage Manga Chapters', 'manga_chapters_textdomain' ),
			array( $this, 'add_chapter_meta_box_callback'),
			'manga'
		);
	}

	/**
	 * Prints the metabox content.
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	function add_chapter_meta_box_callback( $post )	{
		global $post;
		global $wpdb;
		if ( $post->post_title == '' ) {
			echo '<div class="manga-has-no-title">';
			echo 'There has to be a title before you can add chapters. Add a title and save, either as a draft or publish.<br><br>';
			echo 'Also do not change title while adding chapters, it will mess with folder names.<br>';
			echo 'If you want to change title after you already added a chapter save, then change title, save again and then you cand add more chapters.</div>';
		} else {
			?>
			<div id="cmr-chapters">
				<h3>Add Chapters</h3>
				<input id="the-post-slug" type="hidden" value="<?php echo sanitize_title(get_the_title()) ?>">
				<input id="the-post-id" type="hidden" value="<?php echo $post->ID ?>">

				<div id="chapter-form">
					<div class="input-box"><label for="chapter-name">Chapter Name:</label><input type="text" name="chapter-name" id="chapter-name"></div>
					<div class="input-box"><label for="chapter-number">Chapter Number:</label><input type="text" name="chapter-number" id="chapter-number"></div>
					<div class="input-box"><label for="chapter-volume">Chapter Volume:</label><input type="text" name="chapter-volume" id="chapter-volume"></div>
					<button type="button" id="add-chapter">Add</button>
				</div>
				<div id="added-chapters"></div>
			</div>
			<?php
			$vars = array($post->ID, get_option('cmr_per_page'), 0);
			$chapters = Manage::constructQuery("
				SELECT * FROM {$wpdb->prefix}cmr_chapters WHERE manga_id=%d GROUP BY chapter_number LIMIT %d OFFSET %d",
				$vars
			);
			$i = 0;
			$addimages     = '';
			$upimages      = '';
			$listimages    = '';
			$editchapter   = '';
			$deletechapter = '';
			if(get_option('cmr_show_text')){
				$addimages     = '<span class="btn-text">Add Images</span>';
				$upimages      = '<span class="btn-text">Start Upload</span>';
				$listimages    = '<span class="btn-text">List Images</span>';
				$editchapter   = '<span class="btn-text">Edit Chapter</span>';
				$deletechapter = '<span class="btn-text">Delete Chapter</span>';
			}
			?>
			<div id="chapters-container">
			<h3>Chapters List</h3>
			<?php echo $this->cmr_pagination_display(); ?>
			<div class="chapters">
			<?php

			foreach($chapters as $chapter)
			{
			?>
				<div id="cmr-manage-<?php echo $chapter->id ?>" class="cmr-chapters__manage">
					<div class="chapter">

						<div class="chapter-text">
							Chapter Number: <strong><?php echo +$chapter->chapter_number ?></strong> | Chapter Name: <span class="ch-name ch-name-<?php echo $chapter->id ?>"><?php echo $chapter->chapter_name ?></span>
						</div>

						<span class="add button button-secondary"  data-chid="<?php echo $chapter->id ?>" data-chnum="<?php echo $chapter->chapter_number ?>" title="Add Images" id="add-images-<?php echo $chapter->id ?>" >
							<?php echo $addimages; ?><span class="dashicons dashicons-plus-alt"></span>
						</span>

						<span class="up button button-secondary"   data-chid="<?php echo $chapter->id ?>" title="Start Upload" id="start-upload-<?php echo $chapter->id ?>">
							<?php echo $upimages; ?><span class="dashicons dashicons-upload"></span>
						</span>

						<span class="load button button-secondary" data-chid="<?php echo $chapter->id ?>" title="List Images">
							<?php echo $listimages; ?><span class="dashicons dashicons-list-view"></span>
						</span>

						<span class="edit button button-secondary" data-chid="<?php echo $chapter->id ?>" title="Edit Chapter <?php echo $chapter->chapter_number ?>">
							<?php echo $editchapter; ?><span class="dashicons dashicons-edit"></span>
						</span>

						<span class="del button button-secondary"  data-chid="<?php echo $chapter->id ?>" title="Delete Chapter <?php echo $chapter->chapter_number ?>">
							<?php echo $deletechapter; ?><span class="dashicons dashicons-trash"></span>
						</span>

						<div id="edit-chapter-form-<?php echo $chapter->id ?>" class="edit-container">
							<div id="chapter-form">
								<label for="chapter-name-<?php echo $chapter->id ?>">Change Chapter Name:</label>
								<input type="text" name="chapter-name-<?php echo $chapter->id ?>" id="chapter-name-<?php echo $chapter->id ?>" value="<?php echo $chapter->chapter_name ?>">
								<button type="button" id="submit-edit-<?php echo $chapter->id ?>">Submit</button>
							</div>
							<div id="ch-edited-<?php echo $chapter->id ?>"></div>
						</div>

					</div>

					<div class="ajax-data">
						<div id="upload-progress-<?php echo $chapter->id ?>" class="upload-progress"></div>
						<div id="queue-list-<?php echo $chapter->id ?>" class="queue-list"></div>
						<div id="list-images-<?php echo $chapter->id ?>" class="list-images"></div>
						<div id="error-messages-<?php echo $chapter->id ?>" class="error-messages"></div>
					</div>
					<script>
					upload(<?php echo $chapter->id; ?>, <?php echo $chapter->chapter_number; ?>);
					</script>
				</div>
			<?php
				$i++;
			}
			include plugin_dir_path( __FILE__ ) . 'js/check.title.php';
			?>

			</div><!-- chapters -->
			</div>
			<?php
		}
	}


	public function cmr_upload() {
		// Include the upload handler class
		require_once ( __DIR__ . "/../includes/uploadhandler.php");
		$uploader = new UploadHandler();
		// Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
		$ext = array_map('trim', explode(',', get_option('cmr_allowed_extensions')));
		$uploader->allowedExtensions = $ext; // all files types allowed by default
		// Specify max file size in bytes.
		$uploader->sizeLimit = null;
		// Specify the input name set in the javascript.
		$uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default
		// If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
		$uploader->chunksFolder = "chunks";
		$method = $_SERVER["REQUEST_METHOD"];
		if ($method == "POST") {
			header("Content-Type: text/plain");
			// Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
			// For example: /myserver/handlers/endpoint.php?done
			if (isset($_GET["done"])) {
				$result = $uploader->combineChunks(dirname(__FILE__)."/files//".$_POST['chapter_id']);
			}
			// Handles upload requests
			else {
				if ( !is_numeric($_POST['chapter_id']) ) {
					die('Chapter ID is not a number');
				}
				// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
				$result = $uploader->handleUpload($_POST['folder'] . '/ch_' . $_POST['chapter_number']);
				// To return a name used for uploaded file you can use the following line.
				$image_name = sanitize_file_name($uploader->getUploadName());
				$result["uploadName"] = $image_name;
				global $wpdb;
				$wpdb->insert( $wpdb->prefix . 'cmr_images', array('image_name' => $image_name, 'chapter_id' => $_POST['chapter_id']));
			}
			//die(json_encode($result));
			die(json_encode($result));
		}
		// for delete file requests
		else if ($method == "DELETE") {
			$result = $uploader->handleDelete(dirname(__FILE__)."/files");
			die(json_encode($result));
		}
		else {
			header("HTTP/1.0 405 Method Not Allowed");
		}
			/*
			*/
	}


	/*
	 * Settings methods
	 */

	/*
	 * Add submenu to manga menu
	 */
	public function cmr_add_settings_menu() {
		add_options_page('Manga Settings', 'Manga Settings', 'edit_posts', 'cmr_settings', array( $this, 'cmr_settings_page' ) );
	}

	/*
	 * Settings page markup
	 */
	public function cmr_settings_page() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings-page.php';
	}

	/*
	 * Add settings fields and sections
	 */
	public function settings_api_init(){
		include 'partials/settings.php';
	}

	/*
	 * cmr_dir_name field markup
	 */
	public function cmr_enable_manga_post_type() {
		?>
		<input type="checkbox" value="1" <?php checked( get_option('cmr_enable_manga_post_type'), 1 ) ?> id="cmr_enable_manga_post_type" name="cmr_enable_manga_post_type">
		<div class="field-info">
			<?php _e('Leave disabled if there already is a manga post type.'); ?>
		</div>
		<?php
	}

	/*
	 * cmr_dir_name field markup
	 */
	public function cmr_set_reader_page() {
		?>
		<select id="cmr_reader_page" name="cmr_reader_page">
		<?php
		$pages = get_pages();
		for( $i = 0; $i < count($pages); $i++ ){

			if ($i == 0 && !get_option('cmr_reader_page')) {
				echo '<option disabled="disabled" selected="selected">Choose a page</option>';
			}
			echo '<option value="'.$pages[$i]->post_name.'" '.selected( get_option('cmr_reader_page'), $pages[$i]->post_name, true).'>'.$pages[$i]->post_title.'</option>';
		}
		?></select>
		<div class="field-info">
			Select a page to be used to display the images.
			You'll have to add the <strong>[cmr_read]</strong> shortcode to it.<br>
			Make sure to update this if you change the page url.
		</div>
		<?php
 	}

	/*
	 * cmr_dir_name field markup
	 */
	public function cmr_set_dir_name() {
		?>
		<input type="text" value="<?php echo get_option('cmr_dir_name') ?>" id="cmr_dir_name" name="cmr_dir_name" >
		<div class="field-info">
			This is the directory where the manga are saved.
			You can find it in the wordpress root folder.<br>
			Don't name it manga, it will cause problems.
		</div>
		<?php
	}

	/*
	 * cmr_max_up_size field markup
	 */
	public function cmr_max_up_size() {
		?>
		<input type="text" value="<?php echo get_option('cmr_max_up_size') ?>" id="cmr_max_up_size" name="cmr_max_up_size" >
		<div class="field-info">
			Max file size allowed, in megabytes.<br>Limited by the "upload_max_filesize", "post_max_size" php.ini directives.
		</div>
		<?php
	}

	/*
	 * cmr_allowed_extensions field markup
	 */
	public function cmr_allowed_extensions() {
		?>
		<input type="text" value="<?php echo get_option('cmr_allowed_extensions') ?>" id="cmr_allowed_extensions" name="cmr_allowed_extensions" >
		<div class="field-info">
			Comma separated list of extensions, no dots.
		</div>
		<?php
	}

	/*
	 * cmr_js_navigation field markup
	 */
	public function cmr_activate_js_navigation() {
		?>
		<input type="checkbox" value="1" <?php checked( get_option('cmr_js_navigation'), 1 ) ?> id="cmr_js_navigation" name="cmr_js_navigation">
		<div class="field-info">
			Lets you change the image without a page refresh.
		</div>
		<?php
 	}


	public function cmr_enable_show_text(){
		?>
		<input type="checkbox" value="1" <?php checked( get_option('cmr_show_text'), 1 ) ?> id="cmr_show_text" name="cmr_show_text" >
		<div class="field-info">
			If enabled displays text and icons for the chapter buttons on a manga page.
		</div>
		<?php
	}

	/*
	 * cmr_dir_name field markup
	 */
	public function cmr_chapters_per_page() {
		?>
		<input type="text" value="<?php echo get_option('cmr_per_page') ?>" id="cmr_per_page" name="cmr_per_page">
		<div class="field-info">
			How many chapters to show per page in the backend.
		</div>
		<?php
	}

	public function cmr_display_chapters_list(){
		?>
		<input type="checkbox" value="1" <?php checked( get_option('cmr_display_chapters_list'), 1 ) ?> id="cmr_display_chapters_list" name="cmr_display_chapters_list" >
		<div class="field-info">
			If enabled displays the chapters list after the content.<br>
			If disabled you have to add the <strong>[cmr_chapters_list]</strong> shortcode to the content of every manga.<br><br>
			Enabling won't remove existing shortcode from the content.<br>
			It's recommended to use "echo do_shortcode( '[cmr_chapters_list]' )" in your theme's php files, <strong>if you know what you're doing</strong>, else just enable this option.
		</div>
		<?php
	}

	public function cmr_delete_tables(){
		?>
		<input type="checkbox" value="1" <?php checked( get_option('cmr_delete_tables'), 1 ) ?> id="cmr_delete_tables" name="cmr_delete_tables" >
		<div class="field-info">
			If enabled, when the plugin is deleted, it deletes the database tables created by the plugin.
		</div>
		<?php
	}

	public function cmr_delete_files(){
		?>
		<input type="checkbox" value="1" <?php checked( get_option('cmr_delete_files'), 1 ) ?> id="cmr_delete_files" name="cmr_delete_files" >
		<div class="field-info">
			If enabled, when the plugin is deleted, it deletes the files uploaded through the plugin.
		</div>
		<?php
	}

	public function cmr_delete_settings(){
		?>
		<input type="checkbox" value="1" <?php checked( get_option('cmr_delete_settings'), 1 ) ?> id="cmr_delete_settings" name="cmr_delete_settings" >
		<div class="field-info">
			If enabled, when the plugin is deleted, it deletes the plugin's settings from the database.
		</div>
		<?php
	}

	/* Settings input validation */

	public function cmr_checkbox_validation($input){
		if($input == '1' || $input == NULL){
			return $input;
		}
		return;
	}

	public function cmr_s1_display() {

	}

	public function cmr_s2_display() {

	}

	public function cmr_s3_display() {

	}

	public function cmr_pagination(){
		global $wpdb;
		$manga_id = $_POST['manga_id'];
		$limit = get_option('cmr_per_page');
		$offset = ($_POST['page'] - 1) * $limit;
		$results = Manage::constructQuery(
			"SELECT * FROM {$wpdb->prefix}cmr_chapters WHERE manga_id=%d GROUP BY chapter_number LIMIT %d OFFSET %d", array($manga_id, $limit, $offset)
		);
		die(json_encode($results));
	}

	public function cmr_pagination_display(){
		global $wpdb;
		global $post;
		$limit = get_option('cmr_per_page');
		$results = Manage::constructQuery(
			"SELECT * FROM {$wpdb->prefix}cmr_chapters WHERE manga_id=%d", array($post->ID)
		);
		$totalPages = ceil(count($results)/$limit);
		$out = '<div class="cmr-pagination">
			<span href="#" id="cmr-prev" class="disabled button button-secondary" data-page="0">Previous</span>
			<span href="#" id="cmr-next" class="button button-secondary" data-page="2" data-max="'.$totalPages.'">Next</span>
		</div>';
		if($totalPages == 1 || $totalPages == 0){
			return;
		}
		return $out;
		}

	public function cmr_pp_settings_link( $links ) {
		$settings = array(
			'<a href="' . admin_url( 'options-general.php?page=cmr_settings' ) . '">' . __( 'Settings', 'clarity-manga-reader' ) . '</a>',
		);
		return array_merge( $links, $settings );
	}

}
