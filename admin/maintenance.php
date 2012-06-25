<?php
/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/maintenance.php

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

$area = !empty($_GET['area']) && $_GET['area'] == 'errors' ? 'errors' : 'system';

if($area == 'errors' && !empty($_POST['ajax']))
{
	header('Content-Type: text/json; charset=utf-8');

	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== user()->csrf_token()) {
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}

	$response = array('result' => '', 'response' => '');

	// Did we get an error message to delete?
	if(empty($_POST['id']))
	{
		$response['result'] = 'error';
		$response['response'] = 'No error message ID supplied.';
	}
	else
	{
		// Delete the error message, or attempt to do so anyways.
		$dbh->query('
			DELETE FROM error_log
			WHERE error_id = '. (int)$_POST['id']);

		if($dbh->changes() > 0)
		{
			$response['result'] = 'success';
		}
		else
		{
			$response['result'] = 'error';
			$response['response'] = 'That error message does not exist.';
		}
	}


	die(json_encode($response));
}
elseif($area == 'errors' && !empty($_GET['delete']))
{
	if(isset($_GET['sid']) && $_GET['sid'] == $_SESSION['csrf_token'])
	{
		$dbh->query('
			DELETE FROM error_log'. ($_GET['delete'] != 'all' ? '
			WHERE error_id = '. ((int)$_GET['delete']) : ' WHERE 1'));

		if($dbh->changes() > 0)
		{
			$_SESSION['error_deleted'] = true;
		}
		else
		{
			$_SESSION['error_deleted'] = false;
		}
	}

	header('HTTP/1.1 307 Temporary Redirect');
	header('Location: '. $_SERVER['PHP_SELF']. '?area=errors');
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $area == 'errors' ? 'Error Log' : 'System Maintenance' ?> - <?php bloginfo('title') ?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/style.css" />
	<!--[if lte IE 7]><style type="text/css">html.jqueryslidemenu { height: 1%; }</style><![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript">
		function deleteItem(id)
		{
			if(confirm('Do you really want to remove this error message?')) {
				jQuery.ajax({
					data: "ajax=true&csrf_token=<?php echo user()->csrf_token() ?>&id=" + id,
					type: "POST",
					url: "<?php echo $_SERVER['PHP_SELF']. '?area=errors' ?>",
					timeout: 3000,
					error: function() {
						$('#notifybox').text('Failed to delete the error message.').css("background","#E36868").css("border-color","#a40000").slideDown("normal");
					},
					success: function(r) {
						if(r.result == 'success') {
							var tr = '#tr' + id;
							$(tr).hide();
						}
						else {
							$('#notifybox').text('Failed to delete the error message; ' + r.response).css("background","#E36868").css("border-color","#a40000").slideDown("normal");
						}
					}
				})
			}
		}
	</script>
</head>

<body>
	<div id="wrapper">
		<div id="header" class="roundedt">
			<a href="<?php bloginfo('url') ?>"><?php bloginfo('title') ?></a>
		</div>
		<?php include('menu.php'); ?>
		<div id="content">
			<?php
if(permissions('AccessMaintenance'))
{
	// Perhaps they are viewing the error log?
	if($area == 'errors' && empty($_GET['id']))
	{
		// How many errors are there?
		$request = $dbh->query('
			SELECT
				COUNT(*)
			FROM error_log');

		$error_count = $request->fetchSingle();

		// Which page are you on?
		$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;

		// Generate the pagination.
		$pagination = advancedPagination('error_log', $_SERVER['PHP_SELF'].'?area=errors', $page, 10);

		// Load'em up!
		$request = $dbh->query('
			SELECT
				error_id, error_type, error_time, error_message
			FROM error_log
			ORDER BY error_id DESC
			LIMIT '. (($page - 1) * 10). ', 10');

		$display_count = $request->numRows();
?>
			<h2 class="title"><img class="textmid" src="style/manage.png" alt="" />Error Log</h2>
<?php
if(array_key_exists('error_deleted', $_SESSION))
{
	echo '
			<p style="color: ', ($_SESSION['error_deleted'] ? 'green' : 'red'), '; text-align: center;">', ($_SESSION['error_deleted'] ? 'The error was successfully deleted.' : 'The error could not be deleted.'), '</p>';

	unset($_SESSION['error_deleted']);
}
?>
			<div id="notifybox" style="margin:3px 0 -3px 5px;width:588px;"></div>
			<p style="margin-top: 10px;"><?php if($error_count > 0) { ?>Displaying <strong><?php echo ((($page - 1) * 10) + 1) ?> - <?php echo ((($page - 1) * 10) + $display_count)?></strong> of <strong><?php echo $error_count ?></strong> errors.<?php } else { ?>No errors to display.<?php } ?></p>
			<table class="managelist" style="width: 98% !important;">
			<!-- Add table headings -->
				<tr>
					<th class="managelist" style="width: 80px;">Type</th>
					<th class="managelist">Message</th>
					<th class="managelist" style="width: 150px;">Date</th>
					<th class="managelist" style="width: 20px;">Delete</th>
				</tr>
			<!-- Start row loop -->
<?php
while($row = $request->fetch(SQLITE_ASSOC))
{
?>
			<tr id="tr<?php echo $row['error_id'] ?>">
				<td><?php echo errorsMapType($row['error_type']) ?></td>
				<td><a href="<?php echo $_SERVER['PHP_SELF']. '?area=errors&amp;id='. $row['error_id'] ?>"><?php echo strlen($row['error_message']) > 50 ? substr($row['error_message'], 0, 47). '...' : $row['error_message'] ?></a></td>
				<td><?php echo date('n/j/Y g:i:sA', $row['error_time']) ?></td>
				<td class="c"><img src="style/delete.png" alt="Delete" onclick="deleteItem(<?php echo $row['error_id'] ?>);" style="cursor:pointer;" /></td>
			</tr>
<?php
}
?>
			<!-- End row loop -->
			</table>
<?php
			echo '<div style="float: left;">'. $pagination. '</div>
			<div style="float: right;"><p style="margin-top: 5px; margin-right: 5px;"><a href="'. $_SERVER['PHP_SELF']. '?area=errors&amp;delete=all&amp;sid='.user()->csrf_token(). '" onclick="return confirm(\'Are you sure you want to remove all error messages?\');">Delete All</a></p></div><div style="clear: both;"></div>';
	}
	// ... or maybe they want some more information about an error.
	elseif($area == 'errors' && !empty($_GET['id']))
	{
		// We need to see whether this error even exists.
		$request = $dbh->query('
			SELECT
				error_id AS id, error_time AS time, error_type AS type,
				error_message AS message, error_file AS filename, error_line AS line,
				error_url AS url
			FROM error_log
			WHERE error_id = '. ((int)$_GET['id']). '
			LIMIT 1');

		// Does the error not exist?
		if($request->numRows() == 0)
		{
?>
			<h2 class="title"><img class="textmid" src="style/manage.png" alt="" />Error Not Found</h2>
			<p>Sorry, but the error you are attempting to view does not exist.</p>
<?php
		}
		else
		{
			// Good, the error exists. So we can display it.
			$error = $request->fetch(SQLITE_ASSOC);
?>
			<h2 class="title"><img class="textmid" src="style/manage.png" alt="" />Viewing Error #<?php echo $error['id'] ?></h2>
			<table style="width: 100%;">
				<tr>
					<td><strong>Error Type:</strong></td>
					<td><?php echo errorsMapType($error['type']) ?></td>
					<td><strong>Time:</strong></td>
					<td><?php echo date('n/j/Y g:i:sA', $error['time']) ?></td>
				</tr>
				<tr>
					<td colspan="4"><strong>Message:</strong></td>
				</tr>
				<tr>
					<td colspan="4" style="padding: 4px;"><?php echo $error['message'] ?></td>
				</tr>
				<tr>
					<td colspan="4"><strong>File / Line:</strong></td>
				</tr>
				<tr>
					<td colspan="4"><?php echo ABSPATH. '/'. $error['filename']. ' on line '. $error['line']; ?></td>
				</tr>
				<tr>
					<td colspan="4"><strong>URL:</strong></td>
				</tr>
				<tr>
					<td colspan="4"><?php echo $error['url'] ?></td>
				</tr>
			</table>
			<p style="text-align: right;"><a href="<?php echo $_SERVER['PHP_SELF']. '?area=errors&amp;delete='. $error['id']. '&amp;sid='.user()->csrf_token(); ?>" onclick="return confirm('Are you sure?');">Delete</a> | <a href="<?php echo $_SERVER['PHP_SELF']. '?area=errors'; ?>">Back to Error Log &raquo;</a></p>
<?php
		}
	}
	// System maintenance, then.
	else
	{

	}
}
?>
		</div>
		<div id="footer" class="roundedb">
			Powered by LightBlog <?php LightyVersion() ?>
	    </div>
	</div>
</body>
</html>
