<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    admin/create.php

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

define('INLB', true);

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
                                        <?php list_categories('option', -1) ?>
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

        <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/js/Form.js"></script>

<?php include('footer.php') ?>
