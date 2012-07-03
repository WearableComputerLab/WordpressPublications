jQuery(document).ready(function() {

	jQuery('#wcl_pdf_button').click(function() {
		window.send_to_editor = function(html) {
			imgurl = jQuery(html).attr('href');
			jQuery('#wcl_pdf').val(imgurl);
			tb_remove();
		}

		tb_show('', 'media-upload.php?TB_iframe=true');
		return false;
	});


});

jQuery(document).ready(function() {

	jQuery('#wcl_image_button').click(function() {
		window.send_to_editor = function(html) {
			imgurl = jQuery('img',html).attr('src');
			jQuery('#wcl_image').val(imgurl);
			tb_remove();


		}

		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	});



});
