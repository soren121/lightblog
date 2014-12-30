<?php
/*********************************************

    LightBlog 0.9
    SQLite blogging platform

    Sources/Processors/Processor.Comment.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

*********************************************/

class Comment
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = $GLOBALS['dbh'];
    }

    public function processor($data)
    {
        // We need to make sure that the post exists and allows comments!
        $comments_allowed = $this->dbh->prepare("
            SELECT
                allow_comments
            FROM posts
            WHERE post_id = ?
            LIMIT 1");

        $comments_allowed->bindValue(1, $_POST['comment_pid'], PDO::PARAM_INT);

        if(!$comments_allowed->execute())
        {
            return array("result" => "error", "response" => "Failed to query database.");
        }

        $row = $comments_allowed->fetch(PDO::FETCH_NUM);

        if($row[0] == 1)
        {
            // If they're a logged in user, we will set these ourselves.
            if(user()->is_logged())
            {
                // We will remove any effects of htmlspecialchars, as they will be
                // reapplied in a bit.
                $_POST['commenter_name'] = htmlspecialchars_decode(user()->userName(), ENT_QUOTES);
                $_POST['commenter_email'] = htmlspecialchars_decode(user()->email(), ENT_QUOTES);

                // TODO: auto-fill website
            }

            if(utf_strlen($_POST['commenter_name']) && utf_strlen($_POST['commenter_email']) > 0)
            {
                // Require the HTML filter class
                require(ABSPATH .'/Sources/Class.htmLawed.php');

                // Do they want us to remember them?
                if(!empty($_POST['remember_me']))
                {
                    setcookie(LBCOOKIE. '_cname', $_POST['commenter_name'], time() + 2592000, '/');
                    setcookie(LBCOOKIE. '_cemail', $_POST['commenter_email'], time() + 2592000, '/');
                    setcookie(LBCOOKIE. '_curl', !empty($_POST['commenter_website']) && is_url($_POST['commenter_website']) ? $_POST['commenter_website'] : '', time() + 2592000, '/');
                }

                $comment_submit = $this->dbh->prepare("
                    INSERT INTO
                        comments
                    (
                        post_id,
                        published,
                        commenter_id,
                        commenter_name,
                        commenter_email,
                        commenter_website,
                        commenter_ip,
                        comment_date,
                        comment_text
                     )
                    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $comment_submit->bindValue(1, $_POST['comment_pid'], PDO::PARAM_INT);
                $comment_submit->bindValue(2, (get_bloginfo('comment_moderation') == 'none' ? 1 : 0));
                $comment_submit->bindValue(3, user()->id(), PDO::PARAM_INT);
                $comment_submit->bindValue(4, utf_htmlspecialchars($_POST['commenter_name']), PDO::PARAM_STR);
                $comment_submit->bindValue(5, utf_htmlspecialchars($_POST['commenter_email']), PDO::PARAM_STR);
                $comment_submit->bindValue(6, (is_url($_POST['commenter_website']) ? utf_htmlspecialchars($_POST['commenter_website']) : ''), PDO::PARAM_STR);
                $comment_submit->bindValue(7, user()->ip());
                $comment_submit->bindValue(8, time());
                $comment_submit->bindValue(9, htmLawed::hl($_POST['comment_text'], array('safe' => 1, 'elements' => 'a, b, strong, i, em, li, ol, ul, br, span, u, s, img, abbr, blockquote, strike, code')));

                if(!$comment_submit->execute())
                {
                    return array("result" => "error", "response" => "Failed to submit comment to database.");
                }

                if(get_bloginfo('comment_moderation') == 'approval')
                {
                    // Set message
                    return array("result" => "success", "response" => "Your comment will appear as soon as it is approved by a moderator.");
                }
                else
                {
                    // Increment the posts comment count, then.
                    $comment_index_update = $this->dbh->prepare("
                        UPDATE posts
                        SET comments = comments + 1
                        WHERE post_id = ?
                    ");

                    $comment_index_update->bindValue(1, $_POST['comment_pid'], PDO::PARAM_INT);

                    if(!$comment_index_update->execute())
                    {
                        return array("result" => "error", "response" => "Failed to query database.");
                    }
                    return array("result" => "success");
                }
            }
        }
    }
}

?>
