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
	e.preventDefault();
	console.log('in mediaselectform submit');
	var selectedMedia = $('#media-selection option:selected').val();
	console.log(selectedMedia);
        if ( selectedMedia !== '' ) {
                $.ajax({
                        url: '/wp-content/plugins/mediadb/js/mediadb_download.php',
                        type: "POST",   
                        async: false,
                        cache: false,   
                        dataType: 'json',
                        data:  { 'media_id' : selectedMedia },
                        success: function (data) {
				console.log('success');
                        }
                });
        } else {
                return true;
        }
        return false;
});

// vertically align the code input fields within containing div
$('label[for="code-input"]').vAlign();
$('#code-input').vAlign();
$('#valid-code').vAlign();	


});
