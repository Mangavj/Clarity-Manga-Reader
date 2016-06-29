<?php
/* Back-end Settings Section */
add_settings_section(
	'cmr_s1',
	__('Main Settings', 'clarity-manga-reader'),
	array($this, 'cmr_s1_display'),
	'cmr_settings'
);

/* Front-end Settings Section */
add_settings_section(
	'cmr_s2',
	__('Front-end Settings', 'clarity-manga-reader'),
	array($this, 'cmr_s2_display'),
	'cmr_settings'
);

/* Uninstall Settings Section */
add_settings_section(
	'cmr_s3',
	__('Uninstall Settings (don\'t touch this unless you want to remove the plugin for good)', 'clarity-manga-reader'),
	array($this, 'cmr_s3_display'),
	'cmr_settings'
);


/* Add Back-end Settings Fields
 *

/* Enable Manga Post Type Setting */
add_settings_field(
	/* id */
	'cmr_enable_manga_post_type',
	/* title */
	'<span class="cmr-enable-manga-post-type" title="' . __('Enable Manga Post Type', 'clarity-manga-reader') . '">
	<label for="cmr_enable_manga_post_type">' . __('Enable Manga Post Type', 'clarity-manga-reader') . '</label>
	</span>',
	/* callback */
	array($this, 'cmr_enable_manga_post_type'),
	/* page */
	'cmr_settings',
	/* section */
	'cmr_s1'
);
/* Set Manga Directory Name Setting */
add_settings_field(
	/* id */
	'cmr_dir_name',
	/* title */
	'<span class="cmr-directory" title="' . __('Set Manga Directory Name', 'clarity-manga-reader') . '">
	<label for="cmr_dir_name">' . __('Set Manga Directory Name', 'clarity-manga-reader') . '</label>
	</span>',
	/* callback */
	array($this, 'cmr_set_dir_name'),
	/* page */
	'cmr_settings',
	/* section */
	'cmr_s1'
);
/* Set Max Upload Size Setting */
add_settings_field(
	/* id */
	'cmr_max_up_size',
	/* title */
	'<span class="cmr-max-up-size" title="' . __('Set Max Upload Size', 'clarity-manga-reader') . '">
	<label for="cmr_max_up_size">' . __('Set Max Upload Size', 'clarity-manga-reader') . '</label>
	</span>',
	/* callback */
	array($this, 'cmr_max_up_size'),
	/* page */
	'cmr_settings',
	/* section */
	'cmr_s1'
);
/* Set Allowed File Types Setting */
add_settings_field(
	/* id */
	'cmr_allowed_extensions',
	/* title */
	'<span class="cmr-allowed-extensions" title="' . __('Set Allowed File Types', 'clarity-manga-reader') . '">
	<label for="cmr_allowed_extensions">' . __('Set Allowed File Types', 'clarity-manga-reader') . '</label>
	</span>',
	/* callback */
	array($this, 'cmr_allowed_extensions'),
	/* page */
	'cmr_settings',
	/* section */
	'cmr_s1'
);
/* Set Number of chapters to show per page Setting */
add_settings_field(
	/* id */
	'cmr_per_page',
	/* title */
	'<span class="cmr-per-page" title="' . __('Number of chapters to show per page', 'clarity-manga-reader') . '">
	<label for="cmr_per_page">' . __('Number of chapters to show per page', 'clarity-manga-reader') . '</label>
	</span>',
	/* callback */
	array($this, 'cmr_chapters_per_page'),
	/* page */
	'cmr_settings',
	/* section */
	'cmr_s1'
);
/* Enable text for chapter buttons Setting */
add_settings_field(
	/* id */
	'cmr_show_text',
	/* title */
	'<span class="cmr-show-text" title="' . __('Display text for chapter buttons', 'clarity-manga-reader') . '">
	<label for="cmr_show_text">' . __('Display text for chapter buttons', 'clarity-manga-reader') . '</label>
	</span>',
	/* callback */
	array($this, 'cmr_enable_show_text'),
	/* page */
	'cmr_settings',
	/* section */
	'cmr_s1'
);


/* Add Front-end Settings Fields
 *
 * Select page for the reader Setting
 */
add_settings_field(
	/* id */
	'cmr_reader_page',
	/* title */
	'<span class="cmr-reader-page" title="' . __('Select page for the reader', 'clarity-manga-reader') . '">
	<label for="cmr_reader_page">' . __('Select page for the reader', 'clarity-manga-reader') . '</label>
	</span>',
	/* callback */
	array($this, 'cmr_set_reader_page'),
	/* page */
	'cmr_settings',
	/* section */
	'cmr_s2'
);
/* Enable Javascript Navigation Setting */
add_settings_field(
	/* id */
	'cmr_js_navigation',
	/* title */
	'<span class="cmr-directory" title="' . __('Enable Javascript Navigation', 'clarity-manga-reader') . '">
	<label for="cmr_js_navigation">' . __('Enable Javascript Navigation', 'clarity-manga-reader') . '</label>
	</span>',
	/* callback */
	array($this, 'cmr_activate_js_navigation'),
	/* page */
	'cmr_settings',
	/* section */
	'cmr_s2'
);
add_settings_field(
   /* id */
   'cmr_display_chapters_list',
   /* title */
   '<span class="cmr-display-chapter-list" title="' . __('Display Chapters List After The Content', 'clarity-manga-reader') . '">
   <label for="cmr_display_chapters_list">' . __('Display Chapters List After The Content', 'clarity-manga-reader') . '</label>
   </span>',
   /* callback */
   array($this, 'cmr_display_chapters_list'),
   /* page */
   'cmr_settings',
   /* section */
   'cmr_s2'
);

/* Add Uninstall Settings Fields
 *
 * Delete Tables Setting
 */
add_settings_field(
	/* id */
	'cmr_delete_tables',
	/* title */
	'<span class="cmr-delete-tables" title="' . __('Delete Tables', 'clarity-manga-reader') . '">
	<label for="cmr_delete_tables">' . __('Delete Tables', 'clarity-manga-reader') . '</label>
	</span>',
	/* callback */
	array($this, 'cmr_delete_tables'),
	/* page */
	'cmr_settings',
	/* section */
	'cmr_s3'
);
/* Delete Files Setting */
add_settings_field(
   /* id */
   'cmr_delete_files',
   /* title */
   '<span class="cmr-delete-files" title="' . __('Delete Files', 'clarity-manga-reader') . '">
   <label for="cmr_delete_files">' . __('Delete Files', 'clarity-manga-reader') . '</label>
   </span>',
   /* callback */
   array($this, 'cmr_delete_files'),
   /* page */
   'cmr_settings',
   /* section */
   'cmr_s3'
);
/* Delete Plugin Settings Setting */
add_settings_field(
   /* id */
   'cmr_delete_settings',
   /* title */
   '<span class="cmr-delete-settings" title="' . __('Delete Files', 'clarity-manga-reader') . '">
   <label for="cmr_delete_settings">' . __('Delete Files', 'clarity-manga-reader') . '</label>
   </span>',
   /* callback */
   array($this, 'cmr_delete_settings'),
   /* page */
   'cmr_settings',
   /* section */
   'cmr_s3'
);

/* cmr_s1 */
register_setting( 'cmr_settings', 'cmr_enable_manga_post_type', array($this, 'cmr_checkbox_validation') );
register_setting( 'cmr_settings', 'cmr_dir_name', 'sanitize_file_name' );
register_setting( 'cmr_settings', 'cmr_max_up_size', 'sanitize_text_field' );
register_setting( 'cmr_settings', 'cmr_allowed_extensions', 'sanitize_text_field' );
register_setting( 'cmr_settings', 'cmr_per_page', 'sanitize_text_field' );
register_setting( 'cmr_settings', 'cmr_show_text', array($this, 'cmr_checkbox_validation') );
/* cmr_s2 */
register_setting( 'cmr_settings', 'cmr_reader_page', 'sanitize_text_field' );
register_setting( 'cmr_settings', 'cmr_js_navigation', array($this, 'cmr_checkbox_validation') );
register_setting( 'cmr_settings', 'cmr_display_chapters_list', array($this, 'cmr_checkbox_validation') );
/* cmr_s3 */
register_setting( 'cmr_settings', 'cmr_delete_tables', array($this, 'cmr_checkbox_validation') );
register_setting( 'cmr_settings', 'cmr_delete_files', array($this, 'cmr_checkbox_validation') );
register_setting( 'cmr_settings', 'cmr_delete_settings', array($this, 'cmr_checkbox_validation') );
