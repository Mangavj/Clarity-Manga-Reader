<?php

/**
 * The main settings content for our beautiful plugin.
 *
 */
 flush_rewrite_rules();
?>
<div class="wrap">
	<h2><?php _e('Clarity Manga Reader', 'clarity-manga-reader'); ?></h2>
	<div id="clarity-manga-reader">
		<form method="post" action="options.php">
			<?php settings_fields('cmr_settings'); ?>
			<?php do_settings_sections('cmr_settings'); ?>
			<?php submit_button('Save Changes'); ?>
		</form>
	</div>
</div>
