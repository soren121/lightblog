<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/manage.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

// Require config file
require('../config.php');
require(ABSPATH .'/Sources/Core.php');
require(ABSPATH .'/Sources/Admin.php');

if((int)$_GET['type'] == 1) { $type = 'posts'; }
elseif((int)$_GET['type'] == 2) { $type = 'pages'; }
elseif((int)$_GET['type'] == 3) { $type = 'categories'; }

function loadrow($type, $count = 10, $before = 0, $start = 0)
{
	global $dbh;
	if($start != 0)
	{
		$where = '';
		$start = (int)(($start - 1) * $count);
	}
	else
	{
		$where = " WHERE id < ".(int)$before;
		$start = 0;
	}
	$result = @$dbh->query("SELECT * FROM ".sqlite_escape_string(strip_tags($type)).$where." ORDER BY id desc LIMIT ".(int)$start.", ".(int)$count) or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
	$category = @$dbh->query("SELECT id,fullname FROM categories ORDER BY id asc");
	$categories = array();
	while($cat = $category->fetchObject())
	{
		$categories[$cat->id] = $cat->fullname;
	}
	$return = '';
	$i = 0;
	while($row = $result->fetchObject())
	{
		$i++;
		if($i == $result->numRows())
		{
			$return .= '<tr id="'.$row->id.'" class="last">';	
		}
		elseif($i == 1)
		{
			$return .= '<tr id="'.$row->id.'" class="first">';	
		}
		else
		{
			$return .= '<tr id="'.$row->id.'">';
		}
		$return .= '<td><input type="checkbox" name="checked[]" value="'.$row->id.'" class="bf table" /></td>';
		if($type == 'categories')
		{
			$return .= '<td>'.$row->fullname.'</td>
			<td>'.implode(' ', array_slice(explode(' ', $row->info), 0, 8)).'</td>';
		}
		else {
			$return .= '<td>
				<a href="'.get_bloginfo('url').'?'.substr($type, 0, -1).'='.$row->id.'">'.$row->title.'</a>';
				if($row->published != 1) {
					$return .= ' <span style="color:#E36868;">&mdash; Draft</span>';
				}
			$return .= '</td>
			<td>'.$row->author.'</td>
			<td>'.date('n/j/Y', $row->date).'</td>';
			if($type == 'posts')
			{
				$return .= '<td><a href="'.$row->category.'">'.$categories[$row->category].'</a></td>';
			}
		}
		if(($type !== 'categories') && (permissions(1) && get_userinfo('displayname') == $row->author) || (permissions(2)))
		{
			$return .= '<td class="c"><a href="edit.php?type='.(int)$_GET['type'].'&amp;id='.$row->id.'"><img src="style/edit.png" alt="Edit" style="border:0;" /></a></td>
			<td class="c"><img src="style/delete.png" alt="Delete" onclick="deleteItem('.$row->id.', \''.addcslashes(($type == 'categories') ? $row->fullname : $row->title, '\'').'\');" style="cursor:pointer;" /></td>';
		}
		else {
			$return .= '<td class="c"><img src="style/edit-d.png" alt="" title="You aren\'t allowed to edit this '.utf_substr($type, 0, -1).'." /></td>
			<td class="c"><img src="style/delete-d.png" alt="" title="You aren\'t allowed to delete this '.utf_substr($type, 0, -1).'." /></td>';
		}
		$return .= '</tr>';
	}
	return $return;
}

if(isset($_POST['loadrow']))
{
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])
	{
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else
	{
		die(json_encode(array("result" => "success", "response" => loadrow($_POST['type'], $_POST['count'], $_POST['before'], $_POST['start']))));
	}
}

$title = "Manage ".ucwords($type);
$selected = basename($_SERVER['REQUEST_URI']);

include('head.php');

$total = $dbh->query("SELECT id FROM $type") or die(sqlite_error_string($dbh->lastError));
$total = $total->numRows();

?>

		<div id="contentwrapper">
			<div id="contentcolumn">
				<?php if($type !== 'categories' && permissions(1) || $type === 'categories' && permissions(2)): if(!isset($type)): ?>
					<p>The type of content to add was not specified. You must have taken a bad link. Please
					use the navigation bar above to choose the correct type.</p>
				<?php else: ?>
					<form action="<?php bloginfo('url') ?>Sources/ProcessAJAX.php" method="post" id="bulk">
						<div class="table-options">
							<p style="float:left">
								<select name="action" class="bf" style="width: 140px;">
									<option selected="selected" value="default">Bulk Actions:</option>
									<option value="delete">Delete</option>
									<?php if($type != 'categories'): ?>
										<option value="publish">Publish</option>
										<option value="unpublish">Un-publish</option>
									<?php endif; ?>
								</select>
								<input class="bf" type="hidden" name="type" value="<?php echo $type ?>" />
								<input class="bf" type="hidden" name="csrf_token" value="<?php userinfo('csrf_token') ?>" />
								<input type="submit" class="bf" value="Apply" name="bulk" />
							</p>
							<p style="float:right">
								<label for="itemnum"><?php echo ucwords($type) ?> per page</label>
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
									<?php if($type != 'categories'): ?>
										<th>Title</th>
										<th>Author</th>
										<th>Date</th>
										<?php if($type == 'posts'): ?>
											<th>Category</th>
										<?php endif; ?>
										<th class="{sorter: false}">Edit</th>
										<th class="{sorter: false}">Delete</th>
									<?php else: ?>
										<th>Category</th>
										<th>Info</th>
										<th class="{sorter: false}">Edit</th>
										<th class="{sorter: false}">Delete</th>
									<?php endif; ?>
								</tr>
							</thead>
							<tbody>
								<tr id="1" class="last">
									<td colspan="7">&nbsp;</td>
								</tr>
							</tbody>
						</table>
					</form>
					<div class="table-options" style="height:20px;">
						<a id="prev-link" href="javascript:loadpage('prev');" style="float:left;">&laquo; Prev Page</a>
						<a id="next-link" href="javascript:loadpage('next');" style="float:right;">Next Page &raquo;</a>
						<div class="clear"></div>
					</div>
					<p class="table-info">Showing <span id="row-start">1</span> - <span id="row-limit"></span> out of <span id="row-total"><?php echo $total ?></span> <?php echo $type ?>.</p>
				<?php endif; endif; ?>
			</div>
		</div>

		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Tablesorter.js"></script>
		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Tablesorter.Widgets.js"></script>
		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Metadata.js"></script>
		<script type="text/javascript">
		//<![CDATA[
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
			
			function loadrow_js(count, clear, start)
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
					data: "loadrow=true&type=<?php echo $type ?>&count=" + count + "&before=" + last + "&start=" + start + "&csrf_token=<?php userinfo('csrf_token') ?>",
					type: "POST",
					cache: false,
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
					loadrow_js(count, true, page);
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
					data: inputs.join('&'),
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
				var type = 'category';
				if('<?php echo $type ?>' != 'categories')
				{
					type = '<?php echo $type ?>';
					type = type.substr(0, type.length - 1);
				}
				var answer = confirm('Do you really want to delete ' + type + ' "' + title + '"?');
				if(answer)
				{
					$('#ajaxresponse').html('<img src="<?php bloginfo('url') ?>admin/style/new/loading.gif" alt="Saving" />');
					
					$('tr#' + id + ' > td:first').children(':checkbox').attr('checked', true);
					jQuery.ajax(
					{
						data: "delete=true&csrf_token=<?php userinfo('csrf_token') ?>&type=<?php echo $type ?>&id=" + id,
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
