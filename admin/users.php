<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    admin/users.php

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
require(ABSPATH .'/Sources/Process.php');

$response = processForm($_POST);
if(isset($_POST['ajax'])) { die(json_encode($response)); }

if(!isset($_POST['page']))
{
    $response = processForm(array('form' => 'Users', 'csrf_token' => user()->csrf_token(), 'page' => 1));
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

$head_title = l('Manage Users');
$head_css = "table.css";

include('head.php');

$rowtotal = $GLOBALS['dbh']->query("SELECT COUNT(*) FROM users")->fetchColumn();

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
                <?php if(permissions('EditSettings')): ?>
                    <form action="<?php bloginfo('url') ?>admin/users.php?<?php echo http_build_query($_GET, '', '&amp;') ?>" method="post" id="bulk">
                        <div class="table-options">
                            <p style="float:left">
                                <select name="action" class="bf" style="width: 140px;">
                                    <option selected="selected" value="default"><?php echo l('Bulk Actions:'); ?></option>
                                    <option value="delete"><?php echo l('Delete'); ?></option>
                                </select>
                                <input class="bf" type="hidden" name="type" value="error" />
                                <input class="bf" type="hidden" name="form" value="BulkAction" />
                                <input class="bf" type="hidden" name="csrf_token" value="<?php echo user()->csrf_token() ?>" />
                                <input type="submit" class="bf" value="<?php echo l('Apply'); ?>" name="bulk" />
                            </p>
                            <p id="itemnum-container" style="float:right">
                                <label for="itemnum"><?php echo l('Users per Page'); ?></label>
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
                                    <th class="{sorter: false}"><input type="checkbox" id="select-all" title="<?php echo l('Select All/None'); ?>" /></th>
                                    <th><?php echo l('User'); ?></th>
                                    <th><?php echo l('Display Name'); ?></th>
                                    <th><?php echo l('Email'); ?></th>
                                    <th><?php echo l('Last IP Address'); ?></th>
                                    <th class="{sorter: false}"><?php echo l('Delete'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php echo $response ?>
                            </tbody>
                        </table>
                    </form>
                    <form action="<?php bloginfo('url') ?>admin/users.php?<?php echo http_build_query($_GET, '', '&amp;') ?>" method="post">
                        <div class="table-options" style="height:20px;">
                            <input type="hidden" name="csrf_token" value="<?php echo user()->csrf_token() ?>" />
                            <input type="hidden" name="form" value="ErrorLog" />
                            <input type="hidden" name="page" value="<?php echo $page ?>" />
                            <input type="submit" id="prev-link" name="prev" onclick="javascript:loadpage('prev');return false;" style="float:left;<?php echo ($page == 1) ? 'display:none;' : '' ?>" value="&laquo; <?php echo l('Newer Errors'); ?>" />
                            <input type="submit" id="next-link" name="next" onclick="javascript:loadpage('next');return false;" style="float:right;<?php echo (($page * 10) >= $rowtotal) ? 'display:none;' : '' ?>" value="<?php echo l('Older Errors'); ?> &raquo;" />
                            <div class="clear"></div>
                        </div>
                    </form>
                    <p class="table-info"><?php echo l('Showing <span id="row-start">%s</span> - <span id="row-limit">%s</span> out of <span id="row-total">%s</span> error(s).', $rowstart, $rowlimit, $rowtotal); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/js/jQuery.Tablesorter.js"></script>
        <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/js/jQuery.Tablesorter.Widgets.js"></script>
        <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/js/jQuery.Metadata.js"></script>
        <script type="text/javascript">form = 'Users'; csrf_token = '<?php echo user()->csrf_token() ?>';</script>
        <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/js/Table.js"></script>

<?php include('footer.php') ?>
