<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    admin/error.php

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

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
                FROM errors
                WHERE error_id = '. ((int)$_GET['id']). '
                LIMIT 1');

    $error = $request->fetch(PDO::FETCH_ASSOC);
}

$head_title = l('Error Info');
$head_css = "settings.css";
$selected = "error-log.php";

include('head.php');

?>
        <div id="contentwrapper">
            <div id="contentcolumn">
                <?php if(permissions('EditSettings')): if(!$error || !isset($_GET['id'])): ?>
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
