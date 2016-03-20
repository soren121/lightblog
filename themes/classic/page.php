<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    themes/default/page.php
    
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

include('head.php');
include('sidebar.php');

?>

            <div id="content">
                <!-- Start the loop -->
                <?php $pages = new PageLoop(); $pages->obtain_page(); while($pages->has_pages()): ?>
                    <div class="postbox">
                        <h4 class="postnamealt">
                            <?php $pages->title() ?>
                        </h4>
                        <div class="post"><?php $pages->content() ?></div>
                    </div>
                    <!-- End the loop -->
                <?php endwhile; ?>
                <div class="clear"></div>
            </div>
<?php include('footer.php')?>
