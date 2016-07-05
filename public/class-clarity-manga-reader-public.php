<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://fbgm.eu
 * @since      1.0.0
 *
 * @package    Clarity_Manga_Reader
 * @subpackage Clarity_Manga_Reader/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Clarity_Manga_Reader
 * @subpackage Clarity_Manga_Reader/public
 * @author     George Florea Banus <george@fbgm>
 */
class Clarity_Manga_Reader_Public {

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
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/clarity-manga-reader-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/clarity-manga-reader-public.js', array( 'jquery' ), $this->version, false );

	}


	public function cmr_chaptersListShortcode($attributes){
		global $post;
		$manage = new manage();
		$object = $manage->getMangaObject( $post->ID );
		$atts = shortcode_atts( array(
			'before' => '<div class="chapters-container">',
			'after'  => '</div>',
			'title'  => 'Chapters',
		), $attributes );
		if ($object) {
			$previous_volume = '';
			$contents = '<h2>' . $atts['title'] . '</h2>' . $atts['before'];

			foreach ( $object as $o ) {
				$has_images = $manage->getChapterImages( $o->id );
				if ( $has_images[0]->id ) {
					$reader = get_option('cmr_reader_page');
					$vol = '';
					if ( $current_volume != $previous_volume && $current_volume != 0 ) {
						$vol = '<div class="volume-number">Volume ' . $o->chapter_volume . '</div>';
					}
					$contents .= $vol;
					$contents .= '<div class="ch-link">';
					$contents .= '<a href="' . get_site_url() . '/';
					$contents .= $reader . '/' . $o->post_name;
					$contents .= '/chapter-' . $o->chapter_number;
					$contents .= '/page-1">Chapter ';
					$contents .= $o->chapter_number . ': ' . stripslashes( $o->chapter_name );
					$contents .= '</a></div>';
					$previous_volume = $current_volume;
				}
			}
			$contents .= $atts['after'];
			return $contents;
		}
		return;
	}

	public function cmr_read($atts){
		/* Setting the necessary variables */
		$manga_slug     = get_query_var( 'manga_name' );
		$current_img    = get_query_var( 'i' );
		$chapter_number = get_query_var( 'chapter_number' );
		$reader         = get_option('cmr_reader_page');
		$manage         = new manage();
		$manga_object   = $manage->readObject( $manga_slug, $chapter_number );
		$manga_id       = $manga_object[0]->manga_id;
		$ch_id          = $manga_object[0]->chapter_id;
		$chapters       = $manage->get_Chapters_With_Images( $manga_id );
		$manga_dir      = $manga_slug . '_' . $manga_id;

		/* How many images are in the chapter */
		$total_ch_images = count( $manga_object );

		/* How many chapters does the manga have */
		$total_chapters = count( $chapters );
		$contents = '';

		/*  */
		$ch_numbers = array();
		foreach ( $chapters as $chapter ) {
			$ch_numbers[] .= $chapter->chapter_number;
		}
		$key = array_search( $chapter_number, $ch_numbers );


		/*
		 * The logic for the next and previous links.
		 *
		 * If the current image is not the last in the chapter,
		 * go to the next image in that chapter.
		 *
		 */
		if ( $total_ch_images != $current_img ) {
			$previous_chapter = $chapter_number;
			$next_chapter = $chapter_number;
			$previous_img = $current_img - 1;
			$next_img = $current_img + 1;

			$next_page = site_url() . '/' . $reader . '/' . $manga_slug;
			$next_page .= '/' . 'chapter-' . $chapter_number . '/page-' . $next_img;

			/*
			 * If it's not the first image in a chapter
			 * go to previous image in the chapter.
			 *
			 */
			if ( $current_img != '1' ) {
				$previous_page = site_url() . '/' . $reader . '/' . $manga_slug;
				$previous_page .= '/' . 'chapter-' . $chapter_number . '/page-' . $previous_img;
			}
			/*
			 * If it's the first image
			 * of the first chapter, go to manga page.
			 *
			 */
			else if ( $current_img == '1' AND $key == '0' ) {
				$previous_page = site_url() . '?p=' . $manga_id;
			}
			/*
			 * If it's the first image
			 * of a chapter that's not the first,
			 * go to the last image of previous chapter.
			 *
			 */
			else {
				$previous_chapter = $chapters[--$key]->chapter_number;
				$ch_images = $manage->readObject($manga_slug, $previous_chapter);
				$previous_page = site_url() . '/' . $reader . '/' . $manga_slug;
				$previous_page .= '/' . 'chapter-' . $previous_chapter . '/page-' . count($ch_images);
			}
		}
		/*
		 * If the current image is the last in the chapter,
		 * go to the first image of the next chapter.
		 *
		 */
		else if ( $total_ch_images == $current_img AND $total_chapters != $key + 1 ) {
			$previous_chapter = $chapter_number;
			$next_chapter = $chapters[$key + 1]->chapter_number;
			$previous_img = $current_img - 1;
			$next_img = '1';

			$next_page = site_url() . '/' . $reader . '/' . $manga_slug;
			$next_page .= '/' . 'chapter-' . $next_chapter . '/page-' . $next_img;

			$previous_page = site_url() . '/' . $reader . '/' . $manga_slug;
			$previous_page .= '/' . 'chapter-' . $chapter_number . '/page-' . $previous_img;
		}
		/*
		 * If the current image is the last
		 * in the last chapter, go to the manga page.
		 *
		 */
		else if ( $total_ch_images == $current_img AND $total_chapters == $key + 1 ) {
			$previous_chapter = $chapter_number;
			$next_chapter = $chapter_number;
			$previous_img = $current_img - 1;
			$next_img = $current_img;

			$next_page = site_url() . '?p=' . $manga_id;

			$previous_page = site_url() . '/' . $reader . '/' . $manga_slug;
			$previous_page .= '/' . 'chapter-' . $chapter_number . '/page-' . $previous_img;
		}

		/* The image url */
		$img = $current_img;
		$img_url = site_url() . '/' . CMR_DIR_NAME . '/';
		$img_url .= $manga_dir . '/ch_' . $chapter_number . '/';
		$img_url .= $manga_object[--$img]->image_name;

		$contents .= '<div id="jump"></div>';
		$contents .= '<div id="' . $manga_slug . '" class="current-image">';
		$contents .= '<div id="manga-nav" class="manga-nav">';
		$contents .= '<span class="float-left">Reading ';
		$contents .= '<a href="' . site_url() . '?p=' . $manga_id . '" data-mid="' . $manga_id . '" id="manga-title">' . get_the_title( $manga_id ) . '</a></span>';
		$contents .= '<div class="float-right">';
		$contents .= '<select id="chapter-select">';
		foreach ( $chapters as $chapter ) {
			if ( $chapter->chapter_number == $chapter_number ) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			$contents .= $chapter->chapter_number;
			$contents .= '<option value="' . $chapter->chapter_number . '"' . $selected . '>Chapter ' . $chapter->chapter_number . '</option>';
		}
		$contents .= '</select>';
		$contents .= '<select id="page-select">';
		for ( $i = 1; $i <= $total_ch_images; $i++ ) {
			if ( $i == $current_img ) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			$contents .= '<option value="' . $i . '"' . $selected . '>Page ' . $i . '</option>';
		}
		$contents .= '</select>';
		$contents .= '</div>';
		$contents .= '<span class="float-right">';
		$contents .= '<a href="' . $previous_page . '" id="prev-page" data-ch="' . $previous_chapter . '" data-img="' . $previous_img . '"> Previous </a>';
		$contents .= '<a href="' . $next_page . '" id="next-page" data-ch="' . $next_chapter . '" data-img="' . $next_img . '"> Next </a>';
		$contents .= '</span>';
		$contents .= '</div>';
		$contents .= '<div class="manga-image-link"><a href="' . $next_page . '" class="img-next-page" id="img-next-page">';
		$contents .= '<img src="' . $img_url . '" class="manga-image" style="max-height: auto;text-align: center;margin: auto;">';
		$contents .= '</a></div>';
		$contents .= '</div>';
		if(get_option('cmr_js_navigation')) {
			wp_enqueue_script(  'clarity-reader-nav', plugin_dir_url( __FILE__ ) . 'js/clarity-reader-nav.js' );
			wp_localize_script(
				'clarity-reader-nav',
				'obj',
				array(
					'chapters'      => $chapters,
					'images'        => $manga_object,
					'all_manga_dir' => CMR_DIR_NAME,
					'site_url'      => site_url(),
					'title'         => $manga_slug,
					'read'          => $reader
				)
			);
		}
		return $contents;
	}

	public function cmr_add_chapter_list_content($content){
		if( get_option( 'cmr_display_chapters_list' ) && is_single() && get_post_type() == 'manga' ){
			$ch_list = '<div class="chapter-list">' . do_shortcode( '[cmr_chapters_list]') . '</div>';
			return $content . $ch_list;
		}
		return $content;
	}

    public function cmr_rewrite_rules() {
		$reader = get_option('cmr_reader_page');
        add_rewrite_rule('^' . $reader . '/(.*)/chapter-(.*)/page-(.*)', 'index.php?pagename=' . $reader . '&manga_name=$matches[1]&chapter_number=$matches[2]&i=$matches[3]', 'top');
    }

    public function cmr_query_vars($vars) {
        $vars[] .= 'manga_name';
        $vars[] .= 'chapter_number';
        $vars[] .= 'i';
        return $vars;
    }

    public function ajax_get_prev_chapter_images() {
        $manage = new manage();
        $prev_ch_images= $manage->readObject($_GET['manga_name'], $_GET['ch_number']);
		$array['prev_ch_images_number'] = count($prev_ch_images);
        exit(json_encode($array));
    }

}
