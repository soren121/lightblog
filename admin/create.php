<?php
/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/create.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

define('INLB', true);

// Require config file
require('../Sources/Core.php');
require(ABSPATH .'/Sources/Admin.php');
require(ABSPATH .'/Sources/Process.php');

if((int)$_GET['type'] == 1) { $type = 'post'; }
elseif((int)$_GET['type'] == 2) { $type = 'page'; }

function formCallback($response)
{
	if(!empty($response))
	{
		global $type;
		if($response['result'] == 'error')
		{
			return '<span class="result error">'. l('Failed to submit %s', $type). ';<br />'. $response['response']. '</span>';
		}
		elseif($response['result'] == 'success')
		{
			return '<a class="view" href="'. $response['response']. '">'. l('View %s', $type). ' &raquo;</a>';
		}
		else
		{
			return '<span class="result error">'. l('Failed to submit %s', $type). ';<br />'. l('No response from form processor.'). '</span>';
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

$head_title = l('Create '. ucwords($type));
$head_css = "create.css";

include('head.php');

?>

		<div id="contentwrapper">
			<div id="contentcolumn">
				<?php if(!isset($type)): ?>
					<p><?php echo l('The type of content to add was not specified. You must have taken a bad link. Please
					use the navigation to the left to choose the correct type.'); ?></p>
				<?php else: if(permissions('Create'.ucwords($type).'s')): ?>
					<form action="<?php bloginfo('url') ?>admin/create.php?type=<?php echo (int)$_GET['type'] ?>" method="post" id="create">
						<div>
							<label class="tfl" for="title"><?php echo l('Title'); ?></label><br />
							<input id="title" class="textfield cf" name="title" type="text" title="<?php echo l('Title'); ?>" /><br />
							<textarea class="cf" rows="12" cols="36" name="text" id="wysiwyg"></textarea><br />
							<input class="cf" type="hidden" name="type" value="<?php echo $type ?>" />
							<input class="cf" type="hidden" name="form" value="<?php echo l('Create'); ?>" />
							<input class="cf" type="hidden" name="csrf_token" value="<?php echo user()->csrf_token() ?>" />
						</div>
						<div class="settings">
							<?php if($type == 'post'): ?>
								<div style="float: left;margin-right: 30px;">
									<label for="category"><?php echo l('Category:'); ?></label>
									<select class="cf" id="category" name="category">
										<?php list_categories('option', null) ?>
									</select>
								</div>
								<div style="float: left;">
									<p>
										<label for="comments"><input class="cf" type="checkbox" name="comments" id="comments" checked="checked" value="1" /> <?php echo l('Allow Comments'); ?></label>
									</p>
							<?php elseif($type != 'category'): ?>
								<div style="float: left;">
							<?php endif; if($type != 'category'): ?>
									<p>
										<label for="published"><input class="cf" type="checkbox" name="published" id="published" checked="checked" value="1" /> <?php echo l('Published'); ?></label>
									</p>
								</div>
							<?php endif; ?>
							<input class="cf submit" name="create" type="submit" value="<?php echo l('Publish'); ?>" />
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
				bodyStyle: 'margin:10px; font:12pt Georgia,Times,serif; cursor:text'
			});
		//]]>
		</script>

<?php include('footer.php') ?>
