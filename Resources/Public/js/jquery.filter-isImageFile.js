/*! jQuery :isImageFile filter - v0.0.2 - 2014-02-23
*
* Copyright (c) 2013-2014 Jonathan Heilmann;
*
* CHANGELOG
* 0.0.2:	-added jpeg to filter
* 0.0.1:	-initial release
*
*/
jQuery.noConflict();
(function($) {
	$.extend($.expr[':'], {
		isImageFile: function(obj){
			var $this = $(obj);
			var file = $this.attr('href');
			if(file == null) {return false;} // Return false if the path is empty
			file.toLowerCase();	// Convert to lower case
			var extension = file.substr((file.lastIndexOf('.')+1)); // Get extension of file
			switch(extension) {
				case 'jpeg':
				case 'jpg':
				case 'png':
				case 'gif':
					// Got an image - return true
					return true;
				break;
				default:
					// No image found - return false
				return false;
			}
		}
	});
})(jQuery);