function cd(delUrl, message) {
	if (message == null) {
		message = "Are you sure you would like to delete this item?";
	}
	jQuery.facebox('<div>' + message + '</div><div class="facebox_buttons"><a class="button button_delete" href="' + delUrl + '" class="delete"><span>Delete</span></a><a class="button button_cancel" href="javascript:;" onclick="jQuery(document).trigger(\'close.facebox\')"><span>Cancel</span></a><div style="clear: both;"></div></div>');
}