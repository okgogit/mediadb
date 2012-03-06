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

	// remove previous form message if there is one
	$('#validcodeform-msg').remove();

	// if user has entered a code, check to see if it is valid.
	if ( $("#code-input").val().length>0 || $('#code-input').val() !== 'enter valid code' ) {
		$.ajax({
			url: mediadbAjax.pluginURL+'/resources/codecheck.php',
			type: "POST",
			async: false,
			cache: false,
			dataType: 'json',
			data:  { 'code' : $('#code-input').val(),
				 'user_id' : $('#user-id').val() },
			success: function (data) {
				//console.log('success');
				//console.log('data:'+data.status);
				if (data.status == 'valid') {
					$('#code-input').attr('disabled',true);
					$('#code-input').css('background-color','#E1F3FD');
					$('#code-input').css('color','#000');
					$('#code-submit').hide();
					$('#code-block').append('<span id="validcodeform-msg"><strong> Code validated!</strong></span>');
					$('#mediaselectform').slideToggle();
				} 
				if (data.status == 'invalid') {
					$('#code-block').append('<span id="validcodeform-msg"><strong> Invalid code</strong></span>');
				}
				if (data.status == 'already used') {
					$('#code-block').append('<span id="validcodeform-msg"><strong> Already used.</strong></span>');
				}
			},
			error: function (xhr, textStatus) {
				console.log(textStatus);
				console.log(xhr);
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
		$.download(mediadbAjax.pluginURL+'/resources/mediadb_download.php', { 'media_id' : selectedMedia }, 'post');
        }
	else {
		alert('Please select something to download from the list.');
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


// ** Misc code for page styles ** //

// vertically align the code input fields within containing div
$('label[for="code-input"]').vAlign();
$('#code-input').vAlign();
$('#valid-code').vAlign();	

// if the user has not entered a code disable the mediaselection form
if ( $('#valid-code').length == 0 ) { // the user has not entered a valid code
	$('#mediaselectform').hide();
}

});
