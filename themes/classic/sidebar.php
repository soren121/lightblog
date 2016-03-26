<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    themes/default/sidebar.php
    
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

?>

            <div id="sidebar">
                <div>
                    <h4>Pages</h4>
                    <ul>
                        <?php list_pages() ?>
                    </ul>
                </div>

                <div>
                    <h4>Categories</h4>
                    <ul>
                        <?php list_categories() ?>
                    </ul>
                </div>

                <div>
                    <h4>Archives</h4>
                    <ul>
                        <?php list_archives() ?>
                    </ul>
                </div>

                <div>
                    <h4>Meta</h4>
                    <ul>
                        <li><a href="<?php bloginfo('url') ?>admin/">Site Admin</a></li>
                        <li><a href="http://github.com/soren121/lightblog" rel="nofollow">LightBlog Home</a></li>
                    </ul>
                </div>

                <div>
                    <h4>Feeds</h4>
                    <ul>
                        <li><img src="<?php bloginfo('themeurl') ?>/style/rss.png" alt="" class="feed" /><a href="<?php bloginfo('url') ?>feed.php">RSS Feed</a></li>
                        <li><img src="<?php bloginfo('themeurl') ?>/style/atom.png" alt="" class="feed" /><a href="<?php bloginfo('url') ?>feed.php?type=atom">Atom Feed</a></li>
                    </ul>
                </div>
                <div class="clear"></div>
            </div>
