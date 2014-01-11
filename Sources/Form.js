$('form').ajaxForm(
{
	dataType: 'json',
	data: {
		ajax : 'true'
	},
	timeout: 2000,
	beforeSubmit: function() {
		$('#ajaxresponse').html('<img src="style/new/loading.gif" alt="Saving" />');
		return true;
	},
	error: function() {
		$('#ajaxresponse').html('<span class="result">AJAX request failed;<br />(Client failure/not JSON-encoded).</span>');
	},
	success: function(data) {
		$('#ajaxresponse').html(data.response);
	}
});