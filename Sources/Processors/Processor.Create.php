<?php
/*********************************************

    LightBlog 0.9
    SQLite blogging platform

    Sources/Processors/Processor.Create.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

*********************************************/

class Create
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = $GLOBALS['dbh'];
    }

    public function processor($data)
    {
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
                    return array("result" => "error", "response" => $create_post->errorInfo()[2]);
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
                    return array("result" => "error", "response" => $update_shortname->errorInfo()[2]);
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
                    return array("result" => "error", "response" => $update_categories->errorInfo()[2]);
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
                    return array("result" => "error", "response" => $create_page->errorInfo()[2]);
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
