<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    admin/adduser.php

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

function formCallback($response)
{
    if(!empty($response))
    {
        if($response['result'] == 'error')
        {
            return '<span class="result error">'. l('Failed to add user'). ';<br />'. $response['response']. '</span>';
        }
        elseif($response['result'] == 'success')
        {
            return '<span class="result">'. $response['response']. '</span>';
        }
        else
        {
            return '<span class="result error">'. l('Failed to add user'). ';<br />'. l('No response from form processor.'). '</span>';
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

if(permissions('AddUsers'))
{
    $role_options = '';
    foreach(get_roles() as $role_id => $role)
    {
        $role_options .= '<option value="'.$role_id.'">'.$role.'</option>';
    }
}

$head_title = l('Add User');
$head_css = "settings.css";

include('head.php');
?>

        <div id="contentwrapper">
            <div id="contentcolumn">
                <?php if(permissions('AddUsers')): ?>
                    <form action="<?php bloginfo('url') ?>admin/adduser.php" method="post" id="adduser">
                        <div class="setting">
                            <div class="label">
                                <label for="username"><?php echo l('Username'); ?></label>
                            </div>
                            <div class="input">
                                <input type="text" name="username" id="username" value="" autocomplete="off" />
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="setting even">
                            <div class="label">
                                <label for="password"><?php echo l('Password'); ?></label>
                            </div>
                            <div class="input">
                                <input type="password" name="password" id="password" value="" />
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="setting">
                            <div class="label">
                                <label for="vpassword"><?php echo l('Password (again)'); ?></label>
                            </div>
                            <div class="input">
                                <input type="password" name="vpassword" id="vpassword" value="" />
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="setting even">
                            <div class="label">
                                <label for="email"><?php echo l('Email'); ?></label>
                            </div>
                            <div class="input">
                                <input type="text" name="email" id="email" value="" />
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="setting">
                            <div class="label">
                                <label for="displayname"><?php echo l('Display Name'); ?></label>
                                <p><?php echo l('This will be used to identify the user around the site.'); ?></p>
                            </div>
                            <div class="input">
                                <input type="text" name="displayname" id="displayname" value="" />
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="setting even">
                            <div class="label">
                                <label for="role"><?php echo l('Role'); ?></label>
                            </div>
                            <div class="input">
                                <select name="role" id="role">
                                    <?php echo $role_options ?>
                                </select>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="setting">
                            <input type="hidden" name="csrf_token" value="<?php echo user()->csrf_token() ?>" />
                            <input type="hidden" name="form" value="AddUser" />
                            <input type="submit" class="submit" name="addusersubmit" value="Add User" />
                            <div class="clear"></div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <script type="text/javascript">
        //<![CDATA[
            $(function() {
                $('form').submit(function() {
                    $('#ajaxresponse').html('<img src="style/loading.gif" alt="<?php echo l('Saving'); ?>" />');
                    var inputs = [];
                    $(':input', this).each(function() {
                        if($(this).is(':checkbox, :radio') && $(this).is(':not(:checked)')) {
                            void(0);
                        }
                        else {
                            inputs.push(this.name + '=' + this.value);
                        }
                    });

                    jQuery.ajax({
                        data: 'ajax=true&' + inputs.join('&'),
                        type: "POST",
                        url: $(this).attr('action'),
                        timeout: 2000,
                        error: function() {
                            $('#ajaxresponse').html('<span class="result" style="color: red; border-color: red;"><?php echo l('Failed to create user'); ?>;<br /><?php echo l('(jQuery failure).'); ?></span>');
                        },
                        dataType: 'json',
                        success: function(r) {
                            $('#ajaxresponse').html(r.response);
                            $('#username').val('');
                            $('#password').val('');
                            $('#vpassword').val('');
                            $('#email').val('');
                            $('#displayname').val('');
                            $('#role').val(1);
                        }
                    })
                    return false;
                })
            });
        //]]>
        </script>

<?php include('footer.php') ?>
