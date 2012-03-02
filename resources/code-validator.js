jQuery(document).ready(function () {
	jQuery("#your-profile").submit(function (e) {
		if (jQuery("#code").val().length>0) {
			console.log(jQuery('#code').val());
			jQuery.ajax({
				async: false,
 				url: 'codecheck.php?code='+escape(jQuery("#code").val()),
 				dataType: 'json',
 				success: function (data) {
 				if (data.status=="valid") {
 					console.log('valid');
 					return true;
 				} else {
 					var gosubmit = confirm('The database code you entered could not be verified. Do you want to submit the form anyway?');
 					if (gosubmit) {
						return true;
					} else {
						e.preventDefault();
							return false;
						}
				}
			}});
		} else {
			return true;
 		}
 	});
});

