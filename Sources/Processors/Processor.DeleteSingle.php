<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/Processors/Processor.DeleteSingle.php

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

class DeleteSingle extends Processor
{
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
