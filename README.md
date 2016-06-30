# Clarity Manga Reader
Clarity Manga Reader is WordPress plugin that turns your site into a manga reader.

## Installation
* Download zip
* Go to your dashboard > Plugins > Add New > Upload Plugin and upload the plugin
* Activate
* Go to settings page (Settings > Manga Settings) and enable the manga post type if not already available
* If you had a Read page before installing the plugin you'll need to select a page 
for the reader (on the settings page) and add the **[cmr_read]** shortcode.

## Usage
* After enabling the manga post type open the add new page, add a title for the manga and save draft.
* Now you have the form to add chapters (chapter name, number and volume, name and number being required)
* Add a name and a number click add and the chapter is added through ajax
* Now you can upload the images by clicking the add images button, then click start upload
* Publish your post/manga and view it, you should have a list of chapters after post content

## Shortcodes
Currently there are two shortcodes:

* **[cmr_read]** - should be added to the page you want to use for displaying the images (deafult is Read)
* **[cmr_chapters_list]** - adds the chapters list of a manga, 
by default the list is added after the manga content (what you add in the editor when creating the manga), 
but you can disable this behavior and add the shortcode directly in the editor, 
recommended is to add it in your theme's php file `<?php echo do_shortcode('[cmr_chapters_list]'); ?>` or leave it as it is.
