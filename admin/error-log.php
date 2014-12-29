<?php
/***********************************************

    LightBlog 0.9
    SQLite blogging platform

    admin/error-log.php

    ©2008-2014 The LightBlog Team. All
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

$response = processForm($_POST);
if(isset($_POST['ajax'])) { die(json_encode($response)); }

if(!isset($_POST['page']))
{
    $response = processForm(array('form' => 'ErrorLog', 'csrf_token' => user()->csrf_token(), 'page' => 1));
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

$head_title = l('Error Log');
$head_css = "table.css";

include('head.php');

$rowtotal = $GLOBALS['dbh']->query("SELECT COUNT(*) FROM error_log")->fetchColumn();

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
                    <form action="<?php bloginfo('url') ?>admin/error-log.php?<?php echo http_build_query($_GET, '', '&amp;') ?>" method="post" id="bulk">
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
                                <label for="itemnum"><?php echo l('Errors per Page'); ?></label>
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
                                    <th><?php echo l('Type'); ?></th>
                                    <th class="{sorter: false}"><?php echo l('Error'); ?></th>
                                    <th><?php echo l('Date'); ?></th>
                                    <th class="{sorter: false}"><?php echo l('Delete'); ?></th>
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
                            <input type="hidden" name="form" value="ErrorLog" />
                            <input type="hidden" name="page" value="<?php echo $page ?>" />
                            <input type="submit" id="prev-link" name="prev" onclick="javascript:loadpage('prev');return false;" style="float:left;<?php echo ($page == 1) ? 'display:none;' : '' ?>" value="&laquo; <?php echo l('Newer Errors'); ?>" />
                            <input type="submit" id="next-link" name="next" onclick="javascript:loadpage('next');return false;" style="float:right;<?php echo (($page * 10) >= $rowtotal) ? 'display:none;' : '' ?>" value="<?php echo l('Older Errors'); ?> &raquo;" />
                            <div class="clear"></div>
                        </div>
                    </form>
                    <p class="table-info"><?php echo l('Showing <span id="row-start">%s</span> - <span id="row-limit">%s</span> out of <span id="row-total">%s</span> errors.', $rowstart, $rowlimit, $rowtotal); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Tablesorter.js"></script>
        <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Tablesorter.Widgets.js"></script>
        <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Metadata.js"></script>
        <script type="text/javascript">type = 'error'; form = 'ErrorLog'; csrf_token = '<?php echo user()->csrf_token() ?>';</script>
        <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/Table.js"></script>

<?php include('footer.php') ?>
