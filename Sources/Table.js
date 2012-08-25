$('#itemnum-container, #select-all').show();

if(window.location.hash == '')
{
	window.location.hash = '#page=1';
}

$('table').tablesorter(
{
	widgets:['zebra', 'resizable'],
	widgetOptions:
	{
		zebra: ["normal-row", "alt-row"]
	}
});

$('input#select-all').click(function()
{
	var checked = this.checked;
	$("input:checkbox.bf").each(function()
	{
		this.checked = checked;
	})
});

$('#itemnum').change(function()
{
	count = $('#itemnum > option:selected').val();
	loadpage('reset');
});

function ucwords(str)
{
	// http://kevin.vanzonneveld.net
	// +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	// +   improved by: Waldo Malqui Silva
	// +   bugfixed by: Onno Marsman
	// +   improved by: Robin
	// +      input by: James (http://www.james-bell.co.uk/)
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	return (str + '').replace(/^([a-z])|\s+([a-z])/g, function($1)
	{
		return $1.toUpperCase();
	});
}

function pagination_callback()
{
	var hash = window.location.hash;
	var page = Number(hash.substr(6, 1));
	var count = $('#itemnum > option:selected').val();
	var rowtotal = Number($('span#row-total').text());
	rowstart = (count * page) - count + 1;
	$('span#row-start').text(rowstart);
	if(rowstart == 1)
	{
		$('#prev-link').hide();
	}
	else
	{
		$('#prev-link').show();
	}
	if((count * page) >= rowtotal)
	{
		$('span#row-limit').text(rowtotal);
		$('#next-link').hide();
	}
	else
	{
		$('#next-link').show();
		$('span#row-limit').text(count * page);
	}
}

function loadrow_js(count, clear, page)
{
	function callback(r, clear)
	{
		if(r == null || r.result == 'error')
		{
			$('#ajaxresponse').html('<p>AJAX request failed;<br />failed to fetch new row(s).</p>').css("color","#E36868");
			return false;
		}
		if(r.result == 'success')
		{
			if(clear == true)
			{
				$('table tbody').html(r.response);
			}
			else
			{
				$('table tbody').append(r.response);
			}
			$('table').trigger('update', [true]);
		}
	}
	var last = $('tbody tr.last').attr('id');
	jQuery.ajax(
	{
		data: "ajax=true&form=" + form + "&type=" + type + "&count=" + count + "&before=" + last + "&page=" + page + "&csrf_token=" + csrf_token,
		type: "POST",
		url: window.location,
		timeout: 2000,
		dataType: 'json',
		success: function(data)
		{
			callback(data, clear);
		}
	});
}

function loadpage(type)
{
	$('#ajaxresponse').html('<img src="style/new/loading.gif" alt="Loading" />');

	var hash = window.location.hash;
	var page = Number(hash.substr(6, 1));
	var count = $('#itemnum > option:selected').val();
	var rowstart = Number($('span#row-start').text());
	var rowlimit = Number($('span#row-limit').text());
	var rowtotal = Number($('span#row-total').text());

	if(type == 'prev')
	{
		if(page == 1)
		{
			$('#ajaxresponse').empty();
			return;
		}
		page -= 1;
		loadrow_js(count, true, page);
		window.location.hash = '#page=' + String(page);
		$('#next-link').show();
	}
	if(type == 'next')
	{
		if(page >= (rowtotal / count))
		{
			$('#ajaxresponse').empty();
			$('#next-link').hide();
			return;
		}
		page += 1;
		loadrow_js(count, true, page);
		window.location.hash = '#page=' + String(page);
		$('#prev-link').show();
	}
	if(type == 'initial')
	{
		if(page != 1)
		{
			loadrow_js(count, true, page);
		}
	}
	if(type == 'reset')
	{
		window.location.hash = '#page=1';
		page = 1;
		loadrow_js(count, true, page);
	}

	pagination_callback();
	$('#ajaxresponse').empty();
}

loadpage('initial');

function deleterow_callback(r, single)
{
	var type = $('span#type').text();
	if(r == null)
	{
		$('#ajaxresponse').html('<p>AJAX request failed.</p>').css("color","#E36868");
	}
	else
	{
		if(r.result == 'success')
		{
			var action = $('select[name=action]').val();
			if(action == 'delete' || single == true)
			{
				$('#ajaxresponse').html('<p>' + ucwords(type) + '(s) deleted.</p>');
				var checked = $('.table:checked').size();
				$('.table:checked').parent('td').parent('tr').remove();
				var rowtotal = Number($('span#row-total').text());
				var rowlimit = Number($('span#row-limit').text());
				$('span#row-total').text(rowtotal - checked);
				if(rowlimit < rowtotal)
				{
					$('span#row-limit').text(rowlimit - checked);
				}
				var hash = window.location.hash;
				var page = Number(hash.substr(6, 1));
				if($('#searchTable tbody').children().length == 0 && page > 1)
				{
					loadpage('prev');
					$('#next-link').hide();
					return;
				}
				loadrow_js(checked, false, 0);
				$('table').trigger('update', [true]);
				pagination_callback();
			}
			else
			{
				$('#ajaxresponse').html('<p>' + ucwords(type) + '(s) updated.</p>');
				$('.table:checked').parent().next().children('span').remove();
			}
			if(action == 'unpublish')
			{
				$('.table:checked').parent().next().append(' <span style="color:#E36868;">&mdash; Draft</span>');
			}
			$('select[name=action]').val('default');
		}
		else
		{
			$('#ajaxresponse').html('<p>AJAX request failed;<br />' + r.response + '</p>').css("color","#E36868");
		}
	}
}

$('#bulk').submit(function()
{
	if($('#bulk select').val() == 'default')
	{
		return false;
	}

	var inputs = [];
	$('.bf', this).each(function()
	{
		if($(this).is(':checkbox') && $(this).is(':not(:checked)'))
		{
			void(0);
		}
		else
		{
			inputs.push(this.name + '=' + this.value);
		}
	});

	$('#ajaxresponse').html('<img src="style/new/loading.gif" alt="Saving" />');

	jQuery.ajax(
	{
		data: 'ajax=true&' + inputs.join('&'),
		type: "POST",
		url: $(this).attr('action'),
		timeout: 2000,
		error: function()
		{
			$('#ajaxresponse').html('AJAX request failed.').css("color","#E36868");
		},
		dataType: 'json',
		success: function(data)
		{
			deleterow_callback(data, false);
		}
	})
	return false;
});