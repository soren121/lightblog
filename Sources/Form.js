$('form').submit(function() {
	$('#ajaxresponse').html('<img src="style/new/loading.gif" alt="Saving" />');
	var inputs = [];
	$(':input', this).each(function() {
		if($(this).is(':checkbox, :radio') && $(this).is(':not(:checked)')) {
			void(0);
		}
		else {
			inputs.push(this.name + '=' + this.value);
		}
	});

	jQuery.ajax({
		data: 'ajax=true&' + inputs.join('&'),
		type: "POST",
		url: $(this).attr('action'),
		timeout: 2000,
		dataType: 'json',
		error: function() {
			$('#ajaxresponse').html('<span class="result">AJAX request failed;<br />(Client failure/not JSON-encoded).</span>');
		},
		success: function(r) {
			$('#ajaxresponse').html(r.response);
		}
	})
	return false;
});