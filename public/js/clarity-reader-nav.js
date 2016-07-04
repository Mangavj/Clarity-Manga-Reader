(function ($) {
	$(function () {
	$(document).ready(function () {
	console.log(obj);
		var chapters        =  obj.chapters;
		var images          =  obj.images;
		var all_manga_dir   =  obj.all_manga_dir;
		var mid             = $( '#manga-title' ).attr( 'data-mid' );
		var url             = window.location.href;
		var re              = new RegExp('(.*)\/' + obj.read + '\/+(.*)+\/chapter-+(.*)+\/page-+(.*)+\/');
		var site_url        = url.replace( re, '$1' );
		var manga_slug      = url.replace( re, '$2' );
		var current_chapter = url.replace( re, '$3' );
		var current_image   = url.replace( re, '$4' );/**/
		var title = obj.title;

		$("select#chapter-select").on("change", function () {
			window.location.href = obj.site_url + '/' + obj.read + '/' + manga_slug + '/chapter-'  + $("select#chapter-select").val() + '/page-1/';
		});
		$("select#page-select").on("change", function () {
		   window.location.href = obj.site_url + '/' + obj.read + '/' + manga_slug + '/chapter-' + $("select#chapter-select").val() + "/page-" + $("select#page-select").val();
		});


		function arraySearch(arr,val) {
			for (var i=0; i<arr.length; i++)
				if (arr[i] === val)
					return i;
			return false;
		}
		var ch_numbers = [];
		for (i=0; i<chapters.length; i++){
			ch_numbers.push(chapters[i].chapter_number);
		}



		function getNextPage(page, goto) {
			var url             = window.location.href;
			var re              = new RegExp('(.*)\/' + obj.read + '\/+(.*)+\/chapter-+(.*)+\/page-+(.*)+\/');
			var site_url        = url.replace( re, '$1' );
			var manga_slug      = url.replace( re, '$2' );
			var current_chapter = url.replace( re, '$3' );
			var current_image   = url.replace( re, '$4' );

			/* first image, first chapter */
			if ( current_image === '1' && current_chapter === '1' ) {
				if( goto === 'next' ){
					if(images.length > 1 ) {
						var next_img = parseInt(page) - 1;
						$('.manga-image').attr('src', site_url + '/' + all_manga_dir + '/' + title + '_' + mid + '/ch_' + current_chapter + '/' + images[next_img].image_name);
						history.pushState( null, null, site_url + '/'+obj.read+'/' + manga_slug + '/chapter-' + current_chapter + '/page-' + page + '/' );
						$( 'select#page-select option' ).removeAttr( 'selected' );
						$( 'select#page-select option[value="' + page + '"]' ).attr( 'selected', 'selected' );
					} else {
						window.location = site_url + '?p=' + mid;
					}
				} else if ( goto === 'prev' ) {
					window.location = site_url + '?p=' + mid;
				}
			}

			/* first image */
			else if ( current_image === '1' && current_chapter !== '1' ) {
				console.log(page);
				if( goto === 'next' ){
					var next_img = parseInt(page) - 1;
					$('.manga-image').attr('src', site_url + '/' + all_manga_dir + '/' + title + '_' + mid + '/ch_' + current_chapter + '/' + images[next_img].image_name);
					history.pushState( null, null, site_url + '/'+obj.read+'/' + manga_slug + '/chapter-' + current_chapter + '/page-' + page + '/' );
					$( 'select#page-select option' ).removeAttr( 'selected' );
					$( 'select#page-select option[value="' + page + '"]' ).attr( 'selected', 'selected' );
				} else if ( goto === 'prev' ) {
					var prev_chapter = parseInt(arraySearch(ch_numbers, current_chapter))-1;
					$.ajax({
						type: 'get',
						dataType: 'json',
						url: ajaxurl,
						data: {
							action: 'ajax_get_prev_chapter_images',
							manga_name: manga_slug,
							ch_number: chapters[prev_chapter].chapter_number
						},
						success: function (data) {
							console.log(data);
							window.location = site_url + '/'+obj.read+'/' + manga_slug + '/' + 'chapter-' + chapters[prev_chapter].chapter_number + '/page-' + data.prev_ch_images_number;
						}
					});
				}
			}

			/* last image */
			else if ( parseInt(current_image) === images.length && parseInt(arraySearch(ch_numbers, current_chapter))+1 !== chapters.length ) {
				console.log();
				if( goto === 'next' ){
					var next_chapter = parseInt(arraySearch(ch_numbers, current_chapter))+1;
					window.location = site_url + '/'+obj.read+'/' + manga_slug + '/' + 'chapter-' + chapters[next_chapter].chapter_number + '/page-1';
				} else if ( goto === 'prev' ) {
					var next_img = parseInt(page) - 1;
					$('.manga-image').attr('src', site_url + '/' + all_manga_dir + '/' + manga_slug + '_' + mid + '/ch_' + current_chapter + '/' + images[next_img].image_name);
					history.pushState( null, null, site_url + '/'+obj.read+'/' + manga_slug + '/chapter-' + current_chapter + '/page-' + page + '/' );
					$( 'select#page-select option' ).removeAttr( 'selected' );
					$( 'select#page-select option[value="' + page + '"]' ).attr( 'selected', 'selected' );
				}

			}

			/* last image, last chapter */
			else if ( parseInt(current_image) === images.length && parseInt(arraySearch(ch_numbers, current_chapter))+1 === chapters.length ) {
				console.log('last image, last chapter');
				if( goto === 'next' ){
					window.location = site_url + '?p=' + mid;
				} else if ( goto === 'prev' ) {
					var next_img = parseInt(page) - 1;
					$('.manga-image').attr('src', site_url + '/' + all_manga_dir + '/' + title + '_' + mid + '/ch_' + current_chapter + '/' + images[next_img].image_name);
					history.pushState( null, null, site_url + '/'+obj.read+'/' + manga_slug + '/chapter-' + current_chapter + '/page-' + page + '/' );
					$( 'select#page-select option' ).removeAttr( 'selected' );
					$( 'select#page-select option[value="' + page + '"]' ).attr( 'selected', 'selected' );
				}
			}

			/* next image */
			else {
				var next_img = parseInt(page) - 1;
				var img_src = site_url + '/' + all_manga_dir + '/' + title + '_' + mid + '/ch_' + current_chapter + '/' + images[next_img].image_name;

				$('.manga-image').attr('src', img_src);
				history.pushState( null, null, site_url + '/'+obj.read+'/' + manga_slug + '/chapter-' + current_chapter + '/page-' + page + '/' );
				$( 'select#page-select option' ).removeAttr( 'selected' );
				$( 'select#page-select option[value="' + page + '"]' ).attr( 'selected', 'selected' );

			}
		}

		$('#img-next-page, #next-page, #prev-page').attr('href', '#');
		$( document ).on( 'click', '#img-next-page, #next-page', function (event) {
			event.preventDefault();
			nextPage();
		});
		$( document ).on( 'click', '#prev-page', function (event) {
			event.preventDefault();
			prevPage();
		});

		/* Preloading images
		 * a - the current image
		 * b - the number of images to preload
		 */

		function crmPreload(x, y){
			var imagesarray = [];
			for(l = 0; l < y; l++){
				var pre = parseInt( x ) + l;
				if ( typeof images[pre] === 'undefined' ) {
					var img = '';
				} else {

					imagesarray.push(site_url + '/' + all_manga_dir + '/' + title + '_' + mid + '/ch_' + current_chapter + '/' + images[pre].image_name);
				}

			}
			function preloadimages(obj, cb) {
				var loaded = 0;
				var toload = 0;
				var images = obj instanceof Array ? [] : {};

				for (var i in obj) {
					toload++;
					images[i] = new Image();
					images[i].src = obj[i];
					images[i].onload  = load;
					images[i].onerror = load;
					images[i].onabort = load;
				}

				function load() {
					if (++loaded >= toload) cb(images);
				}
			}
			function preloadCallback(){
				console.log();
			}

			preloadimages(imagesarray, preloadCallback);

		}
		crmPreload(current_image, 5);

		function nextPage()
		{
			var url             = window.location.href;
			var re              = new RegExp('(.*)\/' + obj.read + '\/+(.*)+\/chapter-+(.*)+\/page-+(.*)+\/');
			var current_image   = url.replace( re, '$4' );
			$('html, body').animate({
				scrollTop: $("#jump").offset().top
			}, 200);

			crmPreload(current_image, 5);
			getNextPage(parseInt(current_image) + 1, 'next');

			return false;
		}

		function prevPage()
		{
			var url             = window.location.href;
			var re              = new RegExp('(.*)\/' + obj.read + '\/+(.*)+\/chapter-+(.*)+\/page-+(.*)+\/');
			var current_image   = url.replace( re, '$4' );
			$('html, body').animate({
				scrollTop: $("#jump").offset().top
			}, 200);
			getNextPage(parseInt(current_image) - 1, 'prev');
			return false;
		}

		window.addEventListener( 'popstate', function (e) {
			var url             = window.location.href;
			var re              = new RegExp('(.*)\/' + obj.read + '\/+(.*)+\/chapter-+(.*)+\/page-+(.*)+\/');
			var site_url        = url.replace( re, '$1' );
			var manga_slug      = url.replace( re, '$2' );
			var current_chapter = url.replace( re, '$3' );
			var current_image   = url.replace( re, '$4' );

			var next_img = parseInt(current_image) - 1;
			$('.manga-image' ).attr('src', site_url + '/' + all_manga_dir + '/' + title + '_' + mid + '/ch_' + current_chapter + '/' + images[next_img].image_name);
			$('select#page-select option').removeAttr('selected');
			$('select#page-select option[value="' + current_image + '"]').attr('selected', 'selected');
			$('select#chapter-select option').removeAttr('selected');
			$('select#chapter-select option[value="' + current_chapter + '"]').attr('selected', 'selected');
		});

	});
	});
})( jQuery );
