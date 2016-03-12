<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    admin/manage.php

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

switch($_GET['type'])
{
    case 1:
        $type = 'post';
        break;
    case 2:
        $type = 'page';
        break;
    default:
        trigger_error("Invalid content type", E_USER_ERROR);
}

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

$rowtotal = $GLOBALS['dbh']->query("SELECT COUNT(*) FROM {$type}s")->fetchColumn();

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
                            <input type="submit" id="prev-link" name="prev" onclick="javascript:loadpage('prev');return false;" style="float:left;<?php echo ($page == 1) ? 'display:none;' : '' ?>" value="&laquo; Newer <?php echo ucwords($type) ?>s" />
                            <input type="submit" id="next-link" name="next" onclick="javascript:loadpage('next');return false;" style="float:right;<?php echo (($page * 10) >= $rowtotal) ? 'display:none;' : '' ?>" value="Older <?php echo ucwords($type) ?>s &raquo;" />
                            <div class="clear"></div>
                        </div>
                    </form>
                    <p class="table-info"><?php echo l('Showing <span id="row-start">%s</span> - <span id="row-limit">%s</span> out of <span id="row-total">%s</span> %s(s).', $rowstart, $rowlimit, $rowtotal, $type); ?></p>
                <?php endif; endif; ?>
            </div>
        </div>

        <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/js/jQuery.Tablesorter.js"></script>
        <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/js/jQuery.Tablesorter.Widgets.js"></script>
        <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/js/jQuery.Metadata.js"></script>
        <script type="text/javascript">type = '<?php echo $type ?>'; form = 'Manage'; csrf_token = '<?php echo user()->csrf_token() ?>';</script>
        <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/js/jQuery.Form.js"></script>
        <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/js/Table.js"></script>

<?php include('footer.php') ?>
