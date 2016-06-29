console.log(clarity.manga_obj);
function upload(i,j){
	var ext = clarity.allowed_ext;
	var up = new qq.FineUploaderBasic({
		button: document.getElementById('add-images-'+i),
		request: {
			params: {
				action: 'cmr_upload',
				folder: clarity.path,
				chapter_id: i,
				chapter_number: j
			},
			endpoint: ajaxurl,
		},
		validation: {
		//	allowedExtensions: ext,
			sizeLimit: clarity.max_up_size*1000*1000,
		},
		debug:true,
		autoUpload: false,
		callbacks: {
			onSubmit: function(id, fileName, response) {
				var element = document.createElement('div');
				element.setAttribute('id', 'q-image-'+id);
				element.setAttribute('class', 'q-image');
				document.getElementById('queue-list-'+i).appendChild(element);
				document.getElementById('q-image-'+id).innerHTML = '<span id="q-cancel-'+id+'" class="q-cancel"><span class="dashicons dashicons-trash"></span></span>';
				document.getElementById('q-image-'+id).innerHTML += this.getName(id);
				document.getElementById('q-cancel-'+id).onclick = function(){
					up.cancel(id);
					document.getElementById('q-image-'+id).remove();
				};
				document.getElementById('error-messages-'+i).innerHTML = '';
			},

			onAllComplete: function(succeeded, failed) {
				document.getElementById('progress-label-'+i).innerHTML
					= '<div class="q-succes">All files uploaded. <span id="dismiss-'+i+'" class="close-progress">[ X ]<span></div>';
				document.getElementById('dismiss-'+i).onclick = function(){
					document.getElementById('upload-progress-'+i).innerHTML = '';
				}

			},

			onError: function(id, name, errorReason, xhr) {
				document.getElementById('error-messages-'+i).innerHTML = errorReason;
				return;
			},

			onTotalProgress: function(tub, tb) {
				var percentage =  Math.round((parseInt(tub) * 100)/parseInt(tb));
				if ( percentage > 0 ) {
					document.getElementById('upload-progress-'+i).innerHTML
						= '<div class="progress-bar" style="width:'+percentage+'%;"><div id="progress-label-'+i+'" class="progress-label">'+percentage+'%</div></div>';
				}
			}
		}
	});
	document.getElementById('start-upload-'+i).onclick = function(){
		up.uploadStoredFiles();
		document.getElementById('queue-list-'+i).innerHTML = '';
	};
}

	(function ($) {
'use strict';


window.onload = function () {

    function createChapter() {
        $('#add-chapter').click(function () {

            var manga_id       = clarity.manga_obj.ID;
            var manga_title    = clarity.manga_obj.post_title;
            var chapter_name   = $('#chapter-name').val();
            var chapter_number = $('#chapter-number').val();
            var chapter_volume = $('#chapter-volume').val();

            $.ajax({
                method: 'post',
                dataType: 'json',
                url: ajaxurl,
                data: {
                    action: 'create_chapter',
                    name: chapter_name,
                    number: chapter_number,
                    volume: chapter_volume,
                    title: manga_title,
                    id: manga_id
                }
            }).done(function (data) {
                //console.log(data);
                var admin_ajax = data.admin_ajax;
                if (data.error === undefined)
                {
                    var wpmr_error = '';
                } else
                {
                    var wpmr_error = '<div class="errors">' + data.error + '</div>';
					$('.errors').remove();
					$('#chapter-form').prepend(wpmr_error);
					return;
                }
                var chapter_added  = '<div id="chapter-form">';
                    chapter_added += '<div class="input-box"><label for="chapter-name">Chapter Name:</label><input type="text" name="" id="chapter-name"></div>';
                    chapter_added += '<div class="input-box"><label for="chapter-number">Chapter Number:</label><input type="text" name="chapter-number" id="chapter-number"></div>';
                    chapter_added += '<div class="input-box"><label for="chapter-volume">Chapter Volume:</label><input type="text" name="chapter-volume" id="chapter-volume"></div>';
                    chapter_added += '<button type="button" id="add-chapter">Add</button>';
                    chapter_added += '</div>';
                $('#chapter-form').html(chapter_added);
                createChapter();

                // add the newly created chapter to the chapters list
                var chapter_id = data.chapter[0].id;
                var chapter_number = data.chapter[0].chapter_number;
                var chapter_name = data.chapter[0].chapter_name;

				var addimages     = '';
				var upimages      = '';
				var listimages    = '';
				var editchapter   = '';
				var deletechapter = '';
				if(clarity.btn_text){
					var addimages     = '<span class="btn-text">Add Images</span>';
					var upimages      = '<span class="btn-text">Start Upload</span>';
					var listimages    = '<span class="btn-text">List Images</span>';
					var editchapter   = '<span class="btn-text">Edit Chapter</span>';
					var deletechapter = '<span class="btn-text">Delete Chapter</span>';
				}

                var chapters = '<div id="cmr-manage-' + chapter_id + '" class="cmr-chapters__manage"><h3>Chapter ' + chapter_number + ' Added</h3>';
                    chapters += '<div class="chapter">';
	                    chapters += '<div class="chapter-text">Chapter Number: <strong>' + chapter_number + '</strong> | Chapter Name: <span class="ch-name ch-name-' + chapter_id + '">'+chapter_name+'</span></div>';
	                    chapters += '<span class="add button button-secondary add-images" id="add-images-' + chapter_id + '">'+addimages+'<span class="dashicons dashicons-plus-alt"></span></span>';
	                    chapters += '<span class="start button button-secondary" id="start-upload-' + chapter_id + '">'+upimages+'<span class="dashicons dashicons-upload"></span></span>';
	                    chapters += '<span class="load button button-secondary" id="load-images-' + chapter_id + '" data-chid="'+chapter_id+'">'+listimages+'<span class="dashicons dashicons-list-view"></span></span>';
	                    chapters += '<span class="edit button button-secondary" data-chid="' + chapter_id + '" title="Edit Chapter ' + chapter_number + '">'+editchapter+'<span class="dashicons dashicons-edit"></span></span>';
	                    chapters += '<span class="del button button-secondary" data-chid="' + chapter_id + '" title="Delete chapter ' + chapter_number + '">'+deletechapter+'<span class="dashicons dashicons-trash"></span></span>';

						chapters += '<div id="edit-chapter-form-'+chapter_id+'" class="edit-container">';
							chapters += '<div id="chapter-form">';
							chapters += '<label for="chapter-name-'+chapter_id+'">Change Chapter Name:</label>';
							chapters += '<input type="text" name="chapter-name-'+chapter_id+'" id="chapter-name-'+chapter_id+'" value="'+chapter_name+'">';
							chapters += '<button type="button" id="submit-edit-'+chapter_id+'">Submit</button>';
							chapters += '</div>';
						chapters += '</div><!-- edit-container --></div><!-- chapter -->';

						chapters += '<div class="ajax-data">';
		                    chapters += '<div id="upload-progress-' + chapter_id + '" class="upload-progress"></div>';
							chapters += '<div id="queue-list-' + chapter_id + '" class="queue-list"></div>';
		                    chapters += '<div id="list-images-' + chapter_id + '" class="list-images"></div>';
							chapters += '<div id="error-messages-' + chapter_id + '" class="error-messages"></div>';
						chapters += '</div><!-- ajax-data -->';

                    chapters += '</div><!-- cmr-manage -->';

                $('#added-chapters').prepend(chapters);
				upload(chapter_id,chapter_number);
            });

        });
    }
    createChapter();



	/* << Load Images */
    $('body').on('click', '.load', function () {
		var chid      = $(this).data('chid');
		var manga_id  = $('#the-post-id').val();
		$(this).replaceWith('<span id="close-images-' + chid + '" class="button button-secondary" data-chid="' + chid + '"><span class="dashicons dashicons-no-alt"></span></span>');
		$.ajax({
            method: 'post',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: 'load_images',
                chapter_id: chid,
                manga_id: manga_id
            }
        }).done(function (d) {
			//console.log(d);
            var count = d.chapter_images.length;
            for (var img = 0; img < count; img++)
            {
                var img_name = d.chapter_images[img].image_name;
                var img_id = d.chapter_images[img].id;
                var img_url = d.manga_dir_url + 'ch_' + d.chapter_number + '/' + img_name;

                var list_images = '<div id="image-' + d.chapter_images[img].id + '" class="ch-image">';
                list_images += '<span class="chapter-text">';
                list_images += '<a href="' + img_url + '" target="_blank">' + img_name + '</a>';
                list_images += '</span>';
                list_images += '<span data-imgid="' + img_id + '" class="delete_image" id="delete-image-' + img_id + '">delete image</span>';
                list_images += '</div>';
                $('#list-images-' + chid).append(list_images);
            }
            $('#filelist-' + chid).html('');

            $('#close-images-' + chid).click(function () {
                $('#list-images-' + chid).html('');
                $('#filelist-' + chid).html('');
                $('#close-images-' + chid).replaceWith('<span class="load button button-secondary" id="load-images-' + chid + '"  data-chid="' + chid + '"><span class="dashicons dashicons-list-view"></span></span>');
            });
        });
    });
	/* Load Images >> */

	/* << Delete Chapter */
    $('body').on('click', '.del', function () {
		var chid = $(this).data('chid');

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: 'delete_chapter',
                chapter_id: chid
            }
        }).done(function (data) {
            $('#cmr-manage-' + chid).replaceWith('');
        });
    });
	/* Delete Chapter >> */

	/* << Edit Chapter */
	$('body').on('click', '.edit', function () {
		var chid = $(this).data('chid');
		$('#edit-chapter-form-'+chid).toggle();
		$('#submit-edit-' + chid).click(function () {
			var manga_id = $('#the-post-id').val();
			var chapter_name = $('#chapter-name-'+chid).val();
			$.ajax({
				method: 'post',
				dataType: 'json',
				url: ajaxurl,
				data: {
					action: 'edit_chapter',
					name: chapter_name,
					id: manga_id,
					chapter_id: chid
				}
			}).done(function (data) {
				$('#ch-edited-'+chid).html('<strong>Chapter name changed</strong>');
				$('.ch-name-'+chid).html(chapter_name);
			});
		});
	});
	/* Edit Chapter >> */


	/* << Delete Image */
	$('body').on('click', '.delete_image', function () {
		var imgid = $(this).data('imgid');
		$.ajax({
			method: 'post',
			dataType: 'json',
			url: ajaxurl,
			data: {
				action: 'delete_image',
				image_id: imgid
			}
		}).done(function(data) {
			$('#image-' + imgid).replaceWith('');
		});
	});
	/* Delete Image >> */


/*
	<div class="cmr-pagination">
		<span href="#" id="cmr-prev" class="button button-secondary" data-page="0" data-is="prev">Previous</span>
		<span href="#" id="cmr-next" class="button button-secondary" data-page="2" data-is="next">Next</span>
	</div>
		/* << Prev/Next Page */

		function cmrPagination(direction, page){

			$.ajax({
				method: 'post',
				dataType: 'json',
				url: ajaxurl,
				data: {
					action: 'cmr_pagination',
					manga_id: clarity.manga_id,
					page: page
				}
			}).done(function(data) {
				console.log(data);

				$('.chapters').html('');
					for( var i = 0; i < data.length; i++ ){
					var chapter_id = data[i].id;
					var chapter_number = data[i].chapter_number;
					var chapter_name = data[i].chapter_name;

					var chapters = '<div id="cmr-manage-' + chapter_id + '" class="cmr-chapters__manage">';
						chapters += '<div class="chapter">';
							chapters += '<div class="chapter-text">Chapter Number: <strong>' + chapter_number + '</strong> | Chapter Name: <span class="ch-name ch-name-' + chapter_id + '">'+chapter_name+'</span></div>';
							chapters += '<span class="add button button-secondary add-images" id="add-images-' + chapter_id + '"><span class="dashicons dashicons-plus-alt"></span></span>';
							chapters += '<span class="start button button-secondary" id="start-upload-' + chapter_id + '"><span class="dashicons dashicons-upload"></span></span>';
							chapters += '<span class="load button button-secondary" id="load-images-' + chapter_id + '" data-chid="'+chapter_id+'"><span class="dashicons dashicons-list-view"></span></span>';
							chapters += '<span class="edit button button-secondary" data-chid="' + chapter_id + '" title="Edit Chapter ' + chapter_number + '"><span class="dashicons dashicons-edit"></span></span>';
							chapters += '<span class="del button button-secondary" data-chid="' + chapter_id + '" title="Delete chapter ' + chapter_number + '"><span class="dashicons dashicons-trash"></span></span>';

							chapters += '<div id="edit-chapter-form-'+chapter_id+'" class="edit-container">';
								chapters += '<div id="chapter-form">';
								chapters += '<label for="chapter-name-'+chapter_id+'">Change Chapter Name:</label>';
								chapters += '<input type="text" name="chapter-name-'+chapter_id+'" id="chapter-name-'+chapter_id+'" value="'+chapter_name+'">';
								chapters += '<button type="button" id="submit-edit-'+chapter_id+'">Submit</button>';
								chapters += '</div>';
							chapters += '</div><!-- edit-container --></div><!-- chapter -->';

							chapters += '<div class="ajax-data">';
								chapters += '<div id="upload-progress-' + chapter_id + '" class="upload-progress"><div id="progress-label-' + chapter_id + '" class="progress-label"></div></div>';
								chapters += '<div id="list-images-' + chapter_id + '" class="list-images"></div>';
								chapters += '<div id="container"><div id="filelist-' + chapter_id + '"></div></div>';
							chapters += '</div><!-- ajax-data -->';

						chapters += '</div><!-- cmr-manage -->';

					$('.chapters').append(chapters);
					upload(chapter_id,chapter_number);
				}
			});
		}

		$('.cmr-pagination').on('click', '#cmr-next', function(){
			$('#cmr-prev').removeClass('disabled');
			var next = $('#cmr-next').attr('data-page');
			var prev = $('#cmr-prev').attr('data-page');
			var max = $('#cmr-next').attr('data-max');
			cmrPagination('next', next);
			$('#cmr-next').attr('data-page', parseInt(next)+1);
			$('#cmr-prev').attr('data-page', parseInt(prev)+1);
			if (next === max){
				$('#cmr-next').addClass('disabled');
				return;
			}

		});

		$('.cmr-pagination').on('click', '#cmr-prev', function(){
			$('#cmr-next').removeClass('disabled');
			var next = $('#cmr-next').attr('data-page');
			var prev = $('#cmr-prev').attr('data-page');
			cmrPagination('prev', prev);
			$('#cmr-next').attr('data-page', parseInt(next)-1);
			$('#cmr-prev').attr('data-page', parseInt(prev)-1);
			if (prev === '1'){
				$('#cmr-prev').addClass('disabled');
				return;
			}
		});


		/* Prev/Next Page >> */




}; // document ready
})(jQuery);
