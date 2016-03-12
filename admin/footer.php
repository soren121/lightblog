<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    admin/footer.php
    
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

function buildMenu($selected)
{
    $menu = array(
        "Dashboard" => array(
            "link" => "dashboard.php",
            "children" => false
        ),
        "Create" => array(
            "link" => "create.php?type=1",
            "children" => array(
                "Post" => "create.php?type=1",
                "Page" => "create.php?type=2",
                "Category" => "create-category.php"
            )
        ),
        "Manage" => array(
            "link" => "manage.php?type=1",
            "children" => array(
                "Post" => "manage.php?type=1",
                "Page" => "manage.php?type=2",
                "Comments" => "comments.php",
                "Categories" => "manage.php?type=3"
            )
        ),
        "Appearance" => array(
            "link" => "appearance.php",
            "children" => false
        ),
        "Users" => array(
            "link" => "users.php",
            "children" => array(
                "Manage Users" => "users.php",
                "Your Profile" => "profile.php?id=".user()->id(),
                "Add User" => "adduser.php",
                "Manage Roles" => "roles.php"
            )
        ),
        "Settings" => array(
            "link" => "settings.php",
            "children" => false
        ),
        "Maintenance" => array(
            "link" => "error-log.php",
            "children" => array(
                "Backup and Optimize" => "backup.php",
                "Error Log" => "error-log.php"
            )
        ),
    );

    foreach($menu as $topname => $attr)
    {
        $select = '';
        if(is_array($attr['children']) && in_array($selected, $attr['children']) || !is_array($attr['children']) && $attr['link'] == $selected)
        {
            $select = 'selected open';
        }
        echo '<li class="'.$select.'">';
        echo '<img src="style/'.strtolower($topname).'.png" class="nav-icon" alt="" />';
        if($attr['children'] === false)
        {
            echo '<a href="'.$attr['link'].'" class="nav-link single '.$select.'">'. l($topname).'</a>';
            echo '</li>';
        }
        else
        {
            echo '<a href="'.$attr['link'].'" class="nav-link">'.l($topname).'</a>';
            echo '<a href="#" class="nav-toggle"><img src="style/';
            if($select != '')
            {
                echo 'minus.png" alt="-" /></a>';
            }
            else
            {
                echo 'plus.png" alt="+" /></a>';
            }
            echo '<ul class="submenu '.$select.'">';
            foreach($attr['children'] as $name => $link)
            {
                echo '<li><a href="'.$link.'" class="nav-link ';
                if($link == $selected)
                {
                    echo 'selected';
                }
                echo '">'.l($name).'</a></li>';
            }
            echo '</ul></li>';
        }
        $select = null;
    }
}


?>

        <div id="navigation">
            <div id="user">
                <img id="gravatar" src="<?php echo gravatar() ?>" title="<?php echo l('Your Gravatar, from gravatar.com'); ?>" alt="Gravatar" />
                <div>
                    <strong class="role-<?php echo user()->role() ?>"><?php echo user()->displayName() ?></strong><br />
                    <a href="profile.php?id=<?php echo user()->id() ?>"><?php echo l('Your Profile'); ?></a> | <a href="login.php?logout"><?php echo l('Logout'); ?></a>
                </div>
                <div class="clear"></div>
            </div>
            <ul id="menu">
                <?php buildMenu((!isset($selected) ? $selected = basename($_SERVER['REQUEST_URI']) : $selected)) ?>
            </ul>
            <div id="footer">
                <p><?php echo l('Powered by LightBlog %s', LightyVersion('r')); ?></p>
            </div>
        </div>

        <div class="clear"></div>
    </div>

    <script type="text/javascript">
    //<![CDATA[
        $('.submenu:not(".selected")').hide();
        $('ul#menu > li > a.nav-link:not(.single)').width(160);
        $('.nav-toggle').show().click(function()
        {
            if(!$(this).closest('li').is('.open'))
            {
                $('.submenu:not(:hidden)').slideUp('fast').prev('.nav-toggle').children('img').attr('src', 'style/plus.png');
                $('ul#menu > li.open').removeClass('open');
                $(this).children('img').attr('src', 'style/minus.png').parent().next('ul').slideDown('fast').parent().addClass('open');
            }
            else
            {
                $(this).children('img').attr('src', 'style/plus.png').parent().next('ul').slideUp('fast').parent().removeClass('open');
            }
        });
    //]]>
    </script>
</body>
</html>
