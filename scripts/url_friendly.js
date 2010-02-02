function url_friendly(text, len) {
	if (len == null) {
		len = 40;
	}
	var url = text
		.toLowerCase()
		.replace(/^\s+|\s+$/g, "")
		.replace(/[_|\s]+/g, "_")
		.replace(/[^a-z0-9_]+/g, "")
		.substring(0,len)
		.replace(/[_]+/g, "_")
		.replace(/^_+|_+$/g, "")
		;
	return url;
}