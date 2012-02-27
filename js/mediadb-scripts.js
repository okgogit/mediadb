$(document).ready(function() {
	
/**
 * Vertical Align Function
 * 
 *   Vertically aligns an element within its containing element.
 *       Example: $('#content').vAlign();
 *
 *   Credit: http://snipplr.com/view.php?codeview&id=12566.  
 */
(function ($) {
$.fn.vAlign = function() {
	return this.each(function(i){
		var ah = $(this).height();
		var ph = $(this).parent().height();
		var mh = (ph - ah) / 2;
	$(this).css('margin-top', mh);
	});
};
})(jQuery);

// Scripts to handle code validation form
$("#validcodeform").submit(function (e) {
	if ( $("#code-input").val().length>0) {
		$.ajax({
			url: '/wp-content/plugins/mediadb/js/codecheck.php',
			type: "POST",
			async: false,
			cache: false,
			dataType: 'json',
			data:  { 'code' : $('#code-input').val(),
				 'user_id' : $('#user-id').val() },
			success: function (data) {
				console.log(data);
				if (data.status == "valid") {
					console.log('valid!');
					$('#code-input').attr('disabled',true);
					$('#code-input').css('background-color','#E1F3FD');
					$('#code-input').css('color','#000');
					$('#code-submit').hide();
					$('#code-block').append('<strong> Code validated!</strong>');
				} 
				else {
					console.log('invalid!');
				}
			}
		});
	} else {
		return true;
 	}
	return false;
});

// Script to handle media selection form
$("#mediaselectform").submit(function (e) {
	console.log('in mediaselectform submit');
	var selectedMedia = $('#media-selection option:selected').val();
	console.log(selectedMedia);
        if ( selectedMedia !== '' ) {
		$.download('/wp-content/plugins/mediadb/js/mediadb_download.php', { 'media_id' : selectedMedia }, 'post');
        }
	else {
		console.log('there was no media selected');
	} 
        return false;
});

/**
* download
* 
* This plugin provides a way to request binary data that simulates an AJAX 
* call.  This work-around is necessary b/c jquery.ajax doesn't currently
* support binary data as a return, see the info on dataTypes in the 
* jQuery.ajax documentation.  This solution comes from here:
*  http://filamentgroup.com/lab/jquery_plugin_for_requesting_ajax_like_file_downloads/.
*/
jQuery.download = function(url, data, method){
	//url and data options required
	if( url && data ){ 
		//data can be string of parameters or array/object
		data = typeof data == 'string' ? data : jQuery.param(data);
		//split params into form inputs
		var inputs = '';
		jQuery.each(data.split('&'), function(){ 
			var pair = this.split('=');
			inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
		});
		//send request
		jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>')
		.appendTo('body').submit().remove();
	};
};

// vertically align the code input fields within containing div
$('label[for="code-input"]').vAlign();
$('#code-input').vAlign();
$('#valid-code').vAlign();	


});
