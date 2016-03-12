<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    themes/default/post.php
    
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
                <?php $posts = new PostLoop(); $posts->obtain_post(); if($posts->has_posts(true) > 0): while($posts->has_posts()): ?>
                <div class="postbox">
                    <h4 class="postnamealt">
                        <?php $posts->title() ?>
                    </h4>
                    <div class="post"><?php $posts->content() ?></div>
                    <div class="postdata">
                        <span class="postdata">
                            <img src="<?php bloginfo('themeurl') ?>/style/date.png" alt="" />
                            <?php $posts->date('F j, Y') ?>
                        </span>
                        <span class="postdata">
                            <img src="<?php bloginfo('themeurl') ?>/style/user.png" alt="" />
                            <?php $posts->author() ?>
                        </span>
                        <span class="postdata">
                            <img src="<?php bloginfo('themeurl') ?>/style/category.png" alt="" />
                            <?php $posts->category() ?>
                        </span>
                    </div>
                </div>
                <?php endwhile; else: ?>
                <p>Sorry, no posts are available for display.</p>
                <?php endif; ?>

                <!-- Check if comments are enabled -->
                <?php $comment = new CommentLoop(); if($comment->allowed()): ?>
                <h4 class="commenthead"><?php grammarFix($comment->count(), 'Comment', 'Comments') ?></h4><br />
                <!-- Start comment loop -->
                <?php while($comment->has_comments()): ?>
                    <?php $comment->list_comments() ?>
                <?php endwhile; ?>
                <!-- End comment loop -->

                <h4 class="commentform-title">Post a comment</h4><br />
                <?php $comment->messageHook('<div id="notifybox">') ?>
                <form action="#commentform" method="post" id="commentform">
                    <?php if(user()->is_logged()): ?>
                    <p>You are commenting as <strong><?php echo user()->name(); ?></strong>.</p>
                    <?php else: ?>
                    <p><input name="commenter_name" type="text" id="cfname" maxlength="100" value="<?php commenter_name(); ?>" />
                    <label for="cfname"><small>Name (required)</small></label></p>
                    <p><input name="commenter_email" type="text" id="cfemail" maxlength="255" value="<?php commenter_email(); ?>" />
                    <label for="cfemail"><small>Email (required)</small></label></p>
                    <p><input name="commenter_website" type="text" id="cfwebsite" maxlength="255" value="<?php commenter_website(); ?>" />
                    <label for="cfwebsite"><small>Website</small></label></p>
                    <?php endif; ?>
                    <p><textarea cols="41" rows="10" name="comment_text" id="wysiwyg"></textarea></p>
                    <?php $comment->formHook(); if(user()->is_guest()): ?>
                    <p style="float: left;"><label title="Remember your name, email address and website"><input name="remember_me" value="1" type="checkbox" style="width: 15px !important;" checked="checked" /> Remember me</label></p>
                    <?php endif; ?>
                    <p style="float: right;"><input name="comment_submit" type="submit" value="Submit" id="cfsubmit" /></p>
                    <div style="clear: both;">
                    </div>
                </form>
                <?php else: ?>
                <!-- If comments are disabled, this message is shown -->
                <p>Comments have been disabled on this post.</p>
                <!-- End message -->
                <?php endif; ?>
                <div class="clear"></div>
            </div>
<?php include('footer.php')?>
