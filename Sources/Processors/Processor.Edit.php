<?php
/*********************************************

    LightBlog 0.9
    SQLite blogging platform

    Sources/Processors/Processor.Edit.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

*********************************************/

class Edit
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = $GLOBALS['dbh'];
    }

    public function processor($data)
    {
        if(!in_array($_POST['type'], ['post', 'page']))
        {
            return array("result" => "error", "response" => "Invalid content type.");
        }

        $past_data = $this->dbh->prepare("
            SELECT
                *
            FROM :type
            WHERE :type_id = :id");

        $past_data->bindValue(":type", $_POST['type'].'s', PDO::PARAM_STR);
        $past_data->bindValue(":type_id", $_POST['type'].'_id', PDO::PARAM_STR);
        $past_data->bindParam(":id", $_POST['id'], PDO::PARAM_INT);

        if(!$past_data->execute())
        {
            return array("result" => "error", "response" => $past_data->errorInfo()[2]);
        }

        // Fetch previous data
        while($past = $past_data->fetchObject())
        {
            if($_POST['type'] == 'post')
            {
                $ptitle = $past->post_title;
                $ppublished = $past->published;
                $ptext = $past->post_text;
                $pcategory = $past->categories;
                $pcomments = $past->allow_comments;
            }
            elseif($_POST['type'] == 'page')
            {
                $ptitle = $past->page_title;
                $ppublished = $past->published;
                $ptext = $past->page_text;
            }
            $author_id = $past->author_id;
        }

        $past_data->closeCursor();

        if(permissions('EditOthersPosts') || permissions('EditPosts') && $author_id == user()->id())
        {
            // Require the HTML filter class
            require(ABSPATH .'/Sources/Class.htmLawed.php');

            // Grab the data from form and escape the text
            $title = utf_htmlspecialchars($_POST['title']);
            $text = htmLawed::hl($_POST['text'], array('safe' => 1, 'make_tag_strict' => 1, 'balance' => 1, 'keep_bad' => 3));

            // Check published checkbox
            if(isset($_POST['published']) && $_POST['published'] == 1)
            {
                $published = 1;
            }
            else
            {
                $published = 0;
            }

            // For posts only
            if($_POST['type'] == 'post')
            {
                // Check comments checkbox
                if(isset($_POST['comments']) && $_POST['comments'] == 1)
                {
                    $comments = 1;
                }
                else
                {
                    $comments = 0;
                }

                // Check category
                $category = (int)$_POST['category'];
            }

            // Set a base query to modify
            $base = array();

            if($ptitle !== $title)
            {
                array_push($base, $type."_title='".sqlite_escape_string($title)."'");
            }

            if($ptext !== $text)
            {
                array_push($base, $type."_text='".sqlite_escape_string($text)."'");
            }

            if((int)$ppublished !== $published)
            {
                array_push($base, "published='".(int)$published);
            }

            if($_POST['type'] == 'post')
            {
                if((int)$pcategory !== $category)
                {
                    array_push($base, "categories='".(int)$category);
                }

                if((int)$pcomments !== $comments)
                {
                    array_push($base, "allow_comments='".(int)$comments);
                }
            }

            // If nothing's changed, then we don't need to do anything
            if(empty($base))
            {
                return array("result" => "success");
            }
            else
            {
                // Execute modified query
                $sql_edit = @$this->dbh->exec("UPDATE {$type}s SET " . implode(', ', $base) . " WHERE {$type}_id=".$id) or die(json_encode(array("result" => "error", "response" => $type.sqlite_error_string($this->dbh->lastError()))));

                if($sql_edit == 0)
                {
                    return array("result" => "error", "response" => sqlite_error_string($dbh->lastError()));
                }

                // Create URL to return to jQuery
                $url = get_bloginfo('url')."?".$type."=".$id;

                // Return JSON-encoded response
                return array("result" => "success", "response" => $url);
            }
        }
    }
}

?>
