<?php ?>
<script>
(function ( $ ) {
	$(document).ready(function () {
		

        var testervar = $('[id^="titlediv"]').find('#title');
        window.setInterval(function () {
            if (testervar.val().length > 0)
            {
                $('#add_manga_chapters').css('display', 'block');
                $('#get_manga_chapters').css('display', 'block');
            } else {
                $('#add_manga_chapters').css('display', 'none');
                $('#get_manga_chapters').css('display', 'none');
            }
        }, 900);

        $('#publish').click(function () {
            if (testervar.val().length < 1)
            {
                $('[id^=\"titlediv\"]').css('background', '#F96');
                setTimeout($('#ajax-loading').css('visibility', 'hidden'), 100);
                alert('Manga title is required');
                setTimeout($('#publish').removeClass('button-primary-disabled'), 100);
                return false;
            }
        });
	}); // document ready
}( jQuery ));
</script>
