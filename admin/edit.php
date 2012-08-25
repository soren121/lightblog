<?php
/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/edit.php

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

if((int)$_GET['type'] == 1)
{
	$type = 'post';
}
elseif((int)$_GET['type'] == 2)
{
	$type = 'page';
}

if(isset($_GET['id']))
{
	$id = (int)$_GET['id'];
}

function formCallback($response)
{
	if(!empty($response))
	{
		global $type;

		if($response['result'] == 'error')
		{
			return '<span class="result error">'. l('Failed to edit %s', $type). ';<br />'. $response['response']. '</span>';
		}
		elseif($response['result'] == 'success')
		{
			if(isset($response['response']))
			{
				return '<a class="view" href="'. $response['response']. '">'. l('View %s', $type). ' &raquo;</a>';
			}
		}
		else
		{
			return '<span class="result error">'. l('Failed to edit %s', $type). ';<br />'. l('No response from form processor.'). '</span>';
		}
	}
}

$head_response = formCallback(processForm($_POST));
if(isset($_POST['ajax']))
{
	die(json_encode(array(
										'response' => $head_response
									)));
}

$head_title = l('Edit '. ucwords($type));
$head_css = "create.css";
$selected = "manage.php?type=". (int)$_GET['type'];

include('head.php');

// Query for past content
$result = $dbh->query("
	SELECT
		*
	FROM {$type}s
	WHERE {$type}_id= ". (int)$_GET['id']) or die(sqlite_error_string($dbh->lastError));

while($past = $result->fetchObject())
{
	$author_id = $past->author_id;
	if($past->published != 0)
	{
		$ps_checked = 'checked="checked"';
	}
	if($type == 'post')
	{
		$title = $past->post_title;
		$text = $past->post_text;
		$s_category = $past->categories;
		if($past->allow_comments == 1)
		{
			$cs_checked = 'checked="checked"';
		}
	}
	elseif($type == 'post')
	{
		$title = $past->page_title;
		$text = $past->page_text;
	}
}

?>

		<div id="contentwrapper">
			<div id="contentcolumn">
				<?php if(permissions('EditOthersPosts') || permissions('EditPosts') && $author_id == user()->id()): if(!isset($type)): ?>
					<p><?php echo l('The type of content to add was not specified. You must have taken a bad link. Please
					use the navigation bar above to choose the correct type.'); ?></p>
				<?php else: ?>
					<form action="<?php bloginfo('url') ?>admin/edit.php?<?php echo http_build_query($_GET, '', '&amp;') ?>" method="post" id="edit">
						<div>
							<label class="tfl" for="title"><?php echo l('Title'); ?></label><br />
							<input id="title" class="textfield ef" name="title" type="text" title="<?php echo l('Title'); ?>" value="<?php echo $title ?>" /><br />
							<textarea class="ef" rows="12" cols="36" name="text" id="wysiwyg"><?php echo $text ?></textarea><br />
							<input class="ef" type="hidden" name="type" value="<?php echo $type ?>" />
							<input class="ef" type="hidden" name="id" value="<?php echo $id ?>" />
							<input class="ef" type="hidden" name="form" value="<?php echo l('Edit'); ?>" />
							<input class="ef" type="hidden" name="csrf_token" value="<?php echo user()->csrf_token() ?>" />
						</div>
						<div class="settings">
							<?php if($type == 'post'): ?>
								<div style="float: left;margin-right: 30px;">
									<label for="category"><?php echo l('Category:'); ?></label>
									<select class="ef" id="category" name="category">
										<?php list_categories('option', null, $s_category) ?>
									</select>
								</div>
								<div style="float: left;">
									<p>
										<label for="comments"><input class="ef" type="checkbox" name="comments" id="comments" <?php echo @$cs_checked ?> value="1" /> <?php echo l('Allow Comments'); ?></label>
									</p>
							<?php elseif($type != 'category'): ?>
								<div style="float: left;">
							<?php endif; if($type != 'category'): ?>
									<p>
										<label for="published"><input class="ef" type="checkbox" name="published" id="published" <?php echo @$cs_checked ?> value="1" /> <?php echo l('Published'); ?></label>
									</p>
								</div>
							<?php endif; ?>
							<input class="ef submit" name="edit" type="submit" value="<?php echo l('Save'); ?>" />
							<div class="clear"></div>
						</div>
					</form>
				<?php endif; endif; ?>
			</div>
		</div>

		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/Form.js"></script>
		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.CLEditor.js"></script>
		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.CLEditor.XHTML.js"></script>
		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.CLEditor.AdvancedTable.js"></script>
		<script type="text/javascript">
		//<![CDATA[
			$('#wysiwyg').cleditor({
				width: '100%',
				height: '320px',
				bodyStyle: 'margin:10px; font:11pt Georgia,Times,serif; cursor:text'
			});
		//]]>
		</script>

<?php include('footer.php') ?>