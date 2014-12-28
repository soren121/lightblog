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
        if($data['type'] == 'post' || $data['type'] == 'page')
        {
            $author_check = $this->dbh->prepare("
            SELECT
                author_id
            FROM :type
            WHERE :type_id = :id
            LIMIT 1
            ");

            $author_check->bindValue(":type", $data['type'].'s', PDO::PARAM_STR);
            $author_check->bindValue(":type_id", $data['type'].'_id', PDO::PARAM_STR);
            $author_check->bindParam(":id", $data['id'], PDO::PARAM_INT);

            if(!$author_check->execute())
            {
                return array("result" => "error", "response" => $author_check->errorInfo()[2]);
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
                $this->dbh->exec("DELETE FROM comments WHERE post_id=".(int)$data['id']);
                // Update comment number in the posts table
                $this->dbh->exec("UPDATE posts SET comments=0 WHERE post_id=".(int)$data['id']);

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
                :type
            WHERE :type_id = :id
        ");

        $delete->bindValue(":type", $data['type'].'s', PDO::PARAM_STR);
        $delete->bindValue(":type_id", $data['type'].'_id', PDO::PARAM_STR);
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
