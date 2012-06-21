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

function loadrow($type, $count = 10, $after = null)
{
	global $dbh;
	if($after != null)
	{
		$after = " WHERE id < ".(int)$after;
	}
	$result = @$dbh->query("SELECT * FROM ".sqlite_escape_string(strip_tags($type)).$after." ORDER BY id desc LIMIT 0, ".(int)$count) or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
	$return = '';
	$i = 0;
	while($row = $result->fetchObject())
	{
		$i++;
		if($i == $count)
		{
			$return .= '<tr id="'.$row->id.'" class="last">';	
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
				$return .= '<td>'.$row->category.'</td>';
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
		die(json_encode(array("result" => "success", "response" => loadrow($_POST['type'], $_POST['count'], $_POST['after']))));
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
						<p class="table-options">
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
								<?php echo loadrow($type) ?>
							</tbody>
						</table>
					</form>
					<p class="table-options">
						Showing <span id="row-start">1<span>-<span id="row-limit"><?php echo $total < 10 ? $total : 10 ?></span> out of <span id="row-total"><?php echo $total ?></span> <?php echo $type ?>.
					</p>
				<?php endif; endif; ?>
			</div>
		</div>

		<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>Sources/CLEditor/jQuery.CLEditor.css" />
		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Tablesorter.js"></script>
		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Tablesorter.Widgets.js"></script>
		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Metadata.js"></script>
		<script type="text/javascript">
		//<![CDATA[
			$('#manage').tablesorter(
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

			$(function()
			{
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
					
					$('#ajaxresponse').html('<img src="<?php bloginfo('url') ?>admin/style/new/loading.gif" alt="Saving" />');

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
						success: function(r)
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
									if(action == 'delete')
									{
										$('#ajaxresponse').html('<p><?php echo ucwords($type) ?> deleted.</p>');
										var last = $('tbody tr.last').attr('id');
										var count = $('.table:checked').size();
										$('.table:checked').parent('td').parent('tr').remove();
										jQuery.ajax(
										{
											data: "loadrow=true&type=<?php echo $type ?>&count=" + count + "&after=" + last + "&csrf_token=<?php userinfo('csrf_token') ?>",
											type: "POST",
											url: window.location,
											timeout: 2000,
											dataType: 'json',
											success: function(r)
											{
												if(r == null || r.result == 'error')
												{
													$('#ajaxresponse').html('<p>AJAX request failed;<br />failed to fetch new row.</p>').css("color","#E36868");
												}
												if(r.result == 'success')
												{
													$('#manage tbody').append(r.response);
													$('#manage').trigger('update', [true]);
													var rowtotal = $('span#row-total').text();
													var rowlimit = $('span#row-limit').text();
													$('span#row-total').text(rowtotal - 1);
													if(rowlimit > rowtotal)
													{
														$('span#row-limit').text(rowlimit - 1);
													}
												}
											}
										});
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
					})
					return false;
				})
			});
		//]]>
		</script>

<?php include('footer.php') ?>
