<?php
/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/manage.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

// Require config file
require('../Sources/Core.php');
require(ABSPATH .'/Sources/Admin.php');
require(ABSPATH .'/Sources/Process.php');

if((int)$_GET['type'] == 1) { $type = 'post'; }
elseif((int)$_GET['type'] == 2) { $type = 'page'; }

$response = processForm($_POST);
if(isset($_POST['ajax'])) { die(json_encode($response)); }

if(!isset($_POST['page']))
{
	$response = processForm(array('form' => 'Manage', 'csrf_token' => user()->csrf_token(), 'type' => $type, 'page' => 1));
	$page = 1;
}
else
{
	if(isset($_POST['prev']))
	{
		$page = $_POST['page'] -= 1;
	}
	if(isset($_POST['next']))
	{
		$page = $_POST['page'] += 1;
	}
}

if(isset($response['response']))
{
	$response = $response['response'];
}

$head_title = "Manage ".ucwords($type)."s";
$head_css = "table.css";

include('head.php');

$rowtotal = $dbh->query("SELECT {$type}_id FROM {$type}s") or die(sqlite_error_string($dbh->lastError));
$rowtotal = $rowtotal->numRows();

$rowstart = (10 * $page) - 9;

if((10 * $page) >= $rowtotal)
{
	$rowlimit = $rowtotal;
}
else
{
	$rowlimit = $page * 10;
}

?>

		<div id="contentwrapper">
			<div id="contentcolumn">
				<?php if(permissions('EditPosts')): if(!isset($type)): ?>
					<p>The type of content to add was not specified. You must have taken a bad link. Please
					use the navigation bar above to choose the correct type.</p>
				<?php else: ?>
					<form action="<?php bloginfo('url') ?>admin/manage.php?<?php echo http_build_query($_GET, '', '&amp;') ?>" method="post" id="bulk">
						<div class="table-options">
							<p style="float:left">
								<select name="action" class="bf" style="width: 140px;">
									<option selected="selected" value="default">Bulk Actions:</option>
									<option value="delete">Delete</option>
									<option value="publish">Publish</option>
									<option value="unpublish">Un-publish</option>
								</select>
								<input class="bf" type="hidden" name="type" value="<?php echo $type ?>" />
								<input class="bf" type="hidden" name="form" value="BulkAction" />
								<input class="bf" type="hidden" name="csrf_token" value="<?php echo user()->csrf_token() ?>" />
								<input type="submit" class="bf" value="Apply" name="bulk" />
							</p>
							<p id="itemnum-container" style="float:right">
								<label for="itemnum"><?php echo ucwords($type) ?>s per page</label>
								<select id="itemnum" name="items" style="width: 60px;">
									<option value="10" selected="selected">10</option>
									<option value="20">20</option>
									<option value="50">50</option>
								</select>
							</p>
							<div class="clear"></div>
						</div>
						<table id="manage" cellspacing="0">
							<thead>
								<tr>
									<th class="{sorter: false}"><input type="checkbox" id="select-all" title="Select All/None" /></th>
									<th>Title</th>
									<th>Author</th>
									<th>Date</th>
									<?php if($type == 'post'): ?>
										<th>Category</th>
									<?php endif; ?>
									<th class="{sorter: false}">Edit</th>
									<th class="{sorter: false}">Delete</th>
								</tr>
							</thead>
							<tbody>
								<?php echo $response ?>
							</tbody>
						</table>
					</form>
					<form action="<?php bloginfo('url') ?>admin/manage.php?<?php echo http_build_query($_GET, '', '&amp;') ?>" method="post">
						<div class="table-options" style="height:20px;">
							<input type="hidden" name="csrf_token" value="<?php echo user()->csrf_token() ?>" />
							<input type="hidden" name="type" value="<?php echo $type ?>" />
							<input type="hidden" name="form" value="Manage" />
							<input type="hidden" name="page" value="<?php echo $page ?>" />
							<input type="submit" id="prev-link" name="prev" onclick="javascript:loadpage('prev');return false;" style="float:left;<?php echo ($page == 1) ? 'display:none;' : '' ?>" value="&laquo; Prev Page" />
							<input type="submit" id="next-link" name="next" onclick="javascript:loadpage('next');return false;" style="float:right;<?php echo (($page * 10) >= $rowtotal) ? 'display:none;' : '' ?>" value="Next Page &raquo;" />
							<div class="clear"></div>
						</div>
					</form>
					<p class="table-info">Showing <span id="row-start"><?php echo $rowstart ?></span> - <span id="row-limit"><?php echo $rowlimit ?></span> out of <span id="row-total"><?php echo $rowtotal ?></span> <?php echo $type ?>s.</p>
				<?php endif; endif; ?>
			</div>
		</div>

		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Tablesorter.js"></script>
		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Tablesorter.Widgets.js"></script>
		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Metadata.js"></script>
		<script type="text/javascript">
		//<![CDATA[
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
					data: "ajax=true&form=Manage&type=<?php echo $type ?>&count=" + count + "&before=" + last + "&page=" + page + "&csrf_token=<?php echo user()->csrf_token() ?>",
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
							$('#ajaxresponse').html('<p><?php echo ucwords($type) ?> deleted.</p>');
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
							$('#ajaxresponse').html('<p><?php echo ucwords($type) ?> updated.</p>');
							$('.table:checked').parent().next().children('span').remove();
						}
						if(action == 'unpublish')
						{
							$('.table:checked').parent().next().append(' <span style="color:#E36868;">&mdash; Draft</span>');
						}
						$('select[name=action]').val('default');
						$('.table:checked').attr('checked', 'false');
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

			function deleteItem(id, title)
			{
				var answer = confirm('Do you really want to delete ' + type + ' "' + title + '"?');
				if(answer)
				{
					$('#ajaxresponse').html('<img src="<?php bloginfo('url') ?>admin/style/new/loading.gif" alt="Saving" />');

					$('tr#' + id + ' > td:first').children(':checkbox').attr('checked', true);
					jQuery.ajax(
					{
						data: "ajax=true&form=DeleteSingle&csrf_token=<?php echo user()->csrf_token() ?>&type=<?php echo $type ?>&id=" + id,
						type: "POST",
						url: "<?php bloginfo('url') ?>Sources/ProcessAJAX.php",
						timeout: 2000,
						dataType: 'json',
						error: function()
						{
							$('#ajaxresponse').html('<p>Failed to delete ' + type + '.</p>').css("color","#E36868");
						},
						success: function(data)
						{
							deleterow_callback(data, true);
						}
					})
				}
			}
		//]]>
		</script>

<?php include('footer.php') ?>
