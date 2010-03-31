$.fn.hint = $.I(function(index, el) {
	var title = $(el).attr('title');
	function remove() {
		$(el).val('').css("color", "#000");
	}
	if(title) {
		$(el).on("blur", function () {
			if(el.value === '') {
				$(el).val(title).css("color", "#666");
			}
		})
		.on("focus", remove)
		.fire("blur");
		$(el.form).on("submit", remove);
		$(window).on("unload", remove);
	}
});