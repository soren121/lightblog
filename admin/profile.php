<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    admin/profile.php

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
            return '<span class="result error">'. l('Failed to update profile'). ';<br />'. $response['response']. '</span>';
        }
        elseif($response['result'] == 'success')
        {
            return '<span class="result">'. l('Profile updated.'). '</span>';
        }
        else
        {
            return '<span class="result error">'. l('Failed to update profile'). ';<br />'. l('No response from form processor.'). '</span>';
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

if(permissions('EditOtherUsers') || (int)$_GET['id'] == user()->id())
{
    $userquery = $dbh->prepare("
        SELECT
            user_role
        FROM users
        WHERE user_id = ?");

    $userquery->bindParam(1, $_GET['id'], PDO::PARAM_INT);
    $userquery->execute();
    $user_role = $userquery->fetch(PDO::FETCH_NUM);

    $role_options = '';
    foreach(get_roles() as $role_id => $role)
    {
        $select = '';
        if((int)$_GET['id'] == user()->id() && $role_id == user()->role())
        {
            $select = 'selected="selected"';
        }
        elseif($user_role[0] == $role_id)
        {
            $select = 'selected="selected"';
        }
        $role_options .= '<option value="'.$role_id.'" '.$select.'>'.$role.'</option>';
    }
}

$head_title = l('Edit Profile');
$head_css = "settings.css";

if((int)$_GET['id'] != user()->id())
{
    $head_title = l('Edit User');
    $selected = "users.php";
}

include('head.php');

?>

        <div id="contentwrapper">
            <div id="contentcolumn">
                <?php if(isset($_GET['id'])): if(permissions('EditOtherUsers') || (int)$_GET['id'] == user()->id()): ?>
                    <form action="<?php bloginfo('url') ?>admin/profile.php" method="post" id="settings">
                        <div class="setting">
                            <div class="label">
                                <label for="password"><?php echo l('New Password'); ?></label>
                            </div>
                            <div class="input">
                                <input type="password" name="password" id="password" />
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="setting even">
                            <div class="label">
                                <label for="vpassword"><?php echo l('New Password (again)'); ?></label>
                            </div>
                            <div class="input">
                                <input type="password" name="vpassword" id="vpassword" />
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="setting">
                            <div class="label">
                                <label for="email"><?php echo l('Email Address'); ?></label>
                            </div>
                            <div class="input">
                                <input type="text" name="email" id="email" value="<?php echo user()->email() ?>" />
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="setting even">
                            <div class="label">
                                <label for="displayname"><?php echo l('Display Name'); ?></label>
                            </div>
                            <div class="input">
                                <input type="text" name="displayname" id="displayname" value="<?php echo user()->displayName() ?>" />
                            </div>
                            <div class="clear"></div>
                        </div>

                        <?php if(permissions('EditRoles')): ?>
                            <div class="setting">
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
                        <?php endif; ?>

                        <div class="setting <?php if(permissions('EditRoles')): echo 'even'; endif; ?>">
                            <div class="label">
                                <label for="cpassword"><?php echo l('Current Password'); ?></label>
                                <p><?php echo l('Required for security purposes.'); ?></p>
                            </div>
                            <div class="input">
                                <input type="password" name="cpassword" id="cpassword" />
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="setting <?php if(!permissions('EditRoles')): echo 'even'; endif; ?>">
                            <input type="hidden" name="uid" value="<?php echo (int)$_GET['id'] ?>" />
                            <input type="hidden" name="csrf_token" value="<?php echo user()->csrf_token() ?>" />
                            <input type="hidden" name="form" value="Profile" />
                            <input type="submit" class="submit" name="editprofile" value="<?php echo l('Save'); ?>" />
                            <div class="clear"></div>
                        </div>
                    </form>
                <?php endif; endif; ?>
            </div>
        </div>

        <script type="text/javascript">
        //<![CDATA[
            $('div.setting:odd').addClass('even');

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
                            $('#ajaxresponse').html('<span class="result"><?php echo l('Failed to save settings'); ?>;<br /><?php echo l('(jQuery failure).'); ?></span>');
                        },
                        dataType: 'json',
                        success: function(r) {
                            $('#ajaxresponse').html(r.response);
                        }
                    })
                    return false;
                })
            });
        //]]>
        </script>

<?php include('footer.php') ?>
