<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    themes/default/main.php
    
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
                <?php $posts = new PostLoop(); $posts->obtain_posts($page, 8); if($posts->has_posts(true) > 0): while($posts->has_posts()): ?>
                <div class="postbox">
                    <h4 class="postname">
                        <a class="postname" href="<?php $posts->permalink() ?>"><?php $posts->title() ?></a>
                    </h4>
                    <div class="post"><?php $posts->content('Read More &#187;') ?></div>
                    <div class="postdata">
                        <span class="postdata">
                            <img src="<?php bloginfo('themeurl') ?>/style/date.png" alt="" />
                            <?php $posts->date() ?>
                        </span>
                        <span class="postdata">
                            <img src="<?php bloginfo('themeurl') ?>/style/user.png" alt="" />
                            <?php $posts->author() ?>
                        </span>
                        <span class="postdata">
                            <img src="<?php bloginfo('themeurl') ?>/style/comment.png" alt="" />
                            <a href="<?php $posts->permalink() ?>#commentform"><?php grammarFix($posts->commentNum(), 'Comment', 'Comments') ?></a>
                        </span><br />
                        <span class="postdata">
                            <img src="<?php bloginfo('themeurl') ?>/style/category.png" alt="" />
                            <?php $posts->category() ?>
                        </span>
                    </div>
                </div>
                <?php endwhile; $posts->pagination(); else: ?>
                <p>Sorry, no posts matched your criteria.</p>
                <?php endif; ?>

               <div class="clear"></div>
            </div>
            
<?php include('footer.php')?>
