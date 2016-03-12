<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/Processors/Processor.Create.php

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

class Create extends Processor
{
    public function processor($data)
    {
        if(!in_array($data['type'], array('post', 'page')))
        {
            return array("result" => "error", "response" => "Invalid content type.");
        }

        $type = $data['type'];
        if(permissions('Create'.ucwords($type).'s'))
        {
            // Has anything been submitted?
            if(empty($data['title']) || empty($data['text']))
            {
                return array("result" => "error", "response" => "you must give your ".$type." a title and content.");
            }

            // Require the HTML filter class
            require(ABSPATH .'/Sources/Class.htmLawed.php');

            // Grab the data from form and clean things up
            $title = utf_htmlspecialchars($data['title']);
            $text = htmLawed::hl($data['text'], array('safe' => 1, 'make_tag_strict' => 1, 'balance' => 1, 'keep_bad' => 3));

            // Check published checkbox
            if(isset($data['published']) && $data['published'] == 1)
            {
                $published = 1;
            }
            else
            {
                $published = 0;
            }

            // Check comments checkbox
            if(isset($data['comments']) && $data['comments'] == 1)
            {
                $comments = 1;
            }
            else
            {
                $comments = 0;
            }

            if(!function_exists('generate_shortname'))
            {
                require(ABSPATH. '/Sources/PostFunctions.php');
            }

            // Insert post/page into database
            if($type == 'post')
            {
                $create_post = $this->dbh->prepare("
                    INSERT INTO
                        posts
                            (post_title,
                            short_name,
                            post_date,
                            published,
                            author_name,
                            author_id,
                            post_text,
                            categories,
                            allow_comments,
                            allow_pingbacks,
                            comments)
                    VALUES(
                        :title,
                        :shortname,
                        :date,
                        :published,
                        :author,
                        :author_id,
                        :text,
                        :categories,
                        :comments,
                        0,
                        0
                    )
                ");

                $create_post->bindParam(":title", $title, PDO::PARAM_STR);
                $create_post->bindValue(":shortname", generate_shortname(0, $title), PDO::PARAM_STR);
                $create_post->bindValue(":date", time());
                $create_post->bindParam(":published", $published);
                $create_post->bindValue(":author", user()->displayName(), PDO::PARAM_STR);
                $create_post->bindValue(":author_id", user()->id(), PDO::PARAM_INT);
                $create_post->bindParam(":text", $text);
                $create_post->bindValue(":categories", $data['category']);
                $create_post->bindParam(":comments", $comments);

                if(!$create_post->execute())
                {
                    $e = $create_post->errorInfo();
                    return array("result" => "error", "response" => $e[2]);
                }

                $id = $this->dbh->lastInsertId();

                // Get the real short name.
                $update_shortname = $this->dbh->prepare("
                    UPDATE posts
                    SET short_name = :shortname
                    WHERE post_id = :id
                ");

                $update_shortname->bindValue(":shortname", generate_shortname($id, $title));
                $update_shortname->bindParam(":id", $id, PDO::PARAM_INT);

                if(!$update_shortname->execute())
                {
                    $e = $update_shortname->errorInfo();
                    return array("result" => "error", "response" => $e[2]);
                }

                $update_categories = $this->dbh->prepare("
                    INSERT INTO
                        post_categories
                            (post_id,
                            category_id)
                    VALUES(
                        :id,
                        :categories
                    )
                ");

                $update_categories->bindParam(":id", $id, PDO::PARAM_INT);
                $update_categories->bindParam(":categories", $data['category']);

                if(!$update_categories->execute())
                {
                    $e = $update_categories->errorInfo();
                    return array("result" => "error", "response" => $e[2]);
                }
            }
            else
            {
                $create_page = $this->dbh->prepare("
                    INSERT INTO
                        pages
                            (page_title,
                            short_name,
                            page_date,
                            published,
                            author_name,
                            author_id,
                            page_text)
                    VALUES(
                        :title,
                        ' ',
                        :date,
                        :published,
                        :author,
                        :author_id,
                        :text
                    )
                ");

                $create_page->bindParam(":title", $title, PDO::PARAM_STR);
                $create_page->bindValue(":date", time());
                $create_page->bindParam(":published", $published);
                $create_page->bindValue(":author", user()->displayName(), PDO::PARAM_STR);
                $create_page->bindValue(":author_id", user()->id(), PDO::PARAM_INT);
                $create_page->bindParam(":text", $text);

                if(!$create_page->execute())
                {
                    $e = $create_page->errorInfo();
                    return array("result" => "error", "response" => $e[2]);
                }

                $id = $this->dbh->lastInsertId();
            }

            // Create URL to return to jQuery
            $url = get_bloginfo('url')."?".$type."=".$id;

            // Return JSON-encoded response
            return array("result" => "success", "response" => $url);
        }
    }
}

?>
