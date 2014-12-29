<?php
/*********************************************

    LightBlog 0.9
    SQLite blogging platform

    Sources/Processors/Processor.DeleteSingle.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

*********************************************/

class DeleteSingle
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = $GLOBALS['dbh'];
    }

    public function processor($data)
    {
        if(!in_array($data['type'], array('post', 'page', 'user')))
        {
            return array("result" => "error", "response" => "Invalid content type.");
        }

        if($data['type'] == 'post' || $data['type'] == 'page')
        {
            $author_check = $this->dbh->prepare("
            SELECT
                author_id
            FROM {$data['type']}s
            WHERE {$data['type']}_id = :id
            LIMIT 1
            ");

            $author_check->bindParam(":id", $data['id'], PDO::PARAM_INT);

            if(!$author_check->execute())
            {
                $e = $author_check->errorInfo();
                return array("result" => "error", "response" => $e[2]);
            }
        }

        switch($data['type'])
        {
            case 'post':
                if(!permissions('EditOthersPosts') || permissions('EditPosts') && $author_check->fetchColumn() == user()->id())
                {
                    return array("result" => "error", "response" => "You're not allowed to delete this post.");
                }

                // Delete comments associated with this post
                $delete_comments = $this->dbh->prepare("
                    DELETE FROM
                        comments
                    WHERE post_id = ?
                ");

                $delete_comments->bindParam(1, $data['id'], PDO::PARAM_INT);
                if(!$delete_comments->execute())
                {
                    $e = $delete_comments->errorInfo();
                    return array("result" => "error", "response" => $e[2]);
                }

                // Update comment number in the posts table
                $update_comments = $this->dbh->prepare("
                    UPDATE
                        posts
                    SET comments = 0
                    WHERE post_id = ?
                ");

                $update_comments->bindParam(1, $data['id'], PDO::PARAM_INT);
                if(!$update_comments->execute())
                {
                    $e = $update_comments->errorInfo();
                    return array("result" => "error", "response" => $e[2]);
                }

                break;
            case 'page':
                if(!permissions('EditOthersPages') || permissions('EditPages') && $author_check->fetchColumn() == user()->id())
                {
                    return array("result" => "error", "response" => "You're not allowed to delete this page.");
                }

                break;
            case 'user':
                if(!permissions('EditOtherUsers') || (int)$data['id'] == user()->id())
                {
                    return array("result" => "error", "response" => "You're not allowed to delete this user.");
                }

                break;
            case default:
                return array("result" => "error", "response" => "Invalid content type.");
        }

        // Execute query to delete post/page/category
        $delete = $this->dbh->prepare("
            DELETE FROM
                {$data['type']}s
            WHERE {$data['type']}_id = :id
        ");

        $delete->bindParam(":id", $data['id'], PDO::PARAM_INT);

        if(!$delete->execute())
        {
            return array("result" => "error", "response" => "Delete query failed.");
        }

        else
        {
            return array("result" => "success");
        }
    }
}

?>
