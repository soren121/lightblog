<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/error.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

// Require config file
require('../Sources/Core.php');
require(ABSPATH .'/Sources/Admin.php');

if(isset($_GET['id']))
{
	$request = $dbh->query('
				SELECT
					error_id AS id, error_time AS time, error_type AS type,
					error_message AS message, error_file AS filename, error_line AS line,
					error_url AS url
				FROM error_log
				WHERE error_id = '. ((int)$_GET['id']). '
				LIMIT 1');

	$error = $request->fetch(SQLITE_ASSOC);
}

$head_title = l('Error Info');
$head_css = "settings.css";
$selected = "error-log.php";

include('head.php');

?>
		<div id="contentwrapper">
			<div id="contentcolumn">
				<?php if(permissions('EditSettings')): if($request->numRows() == 0 || !isset($_GET['id'])): ?>
					<p><?php l('The error you are trying to view does not exist.'); ?></p>
				<?php else: ?>
					<h3><?php echo l('Error #%s', (int)$_GET['id']); ?></h3>
					<br />
					<table style="width: 100%;">
						<tr>
							<td><strong><?php echo l('Error Type:'); ?></strong></td>
							<td><?php echo errorsMapType($error['type']) ?></td>
							<td><strong><?php echo l('Time:'); ?></strong></td>
							<td><?php echo date('n/j/Y g:i:sA', $error['time']) ?></td>
						</tr>
						<tr>
							<td colspan="4"><strong><?php echo l('Message:'); ?></strong></td>
						</tr>
						<tr>
							<td colspan="4" style="padding: 4px;"><?php echo $error['message'] ?></td>
						</tr>
						<tr>
							<td colspan="4"><strong><?php echo l('File / Line:'); ?></strong></td>
						</tr>
						<tr>
							<td colspan="4"><?php echo l('%s on line %s', ABSPATH. '/'. $error['filename'], $error['line']); ?></td>
						</tr>
						<tr>
							<td colspan="4"><strong><?php echo l('URL:'); ?></strong></td>
						</tr>
						<tr>
							<td colspan="4"><?php echo $error['url'] ?></td>
						</tr>
					</table>
					<p style="text-align: right;"><?php echo l('Delete'); ?> | <a href="<?php bloginfo('url') ?>admin/error-log.php"><?php echo l('Back to Error Log'); ?> &raquo;</a></p>
				<?php endif; endif; ?>
			</div>
		</div>

<?php include('footer.php') ?>
