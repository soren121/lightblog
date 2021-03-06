<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/Processors/Processor.Edit.php

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

class Edit extends Processor
{
    public function processor($data)
    {
        if(!in_array($data['type'], array('post', 'page')))
        {
            return array("result" => "error", "response" => "Invalid content type.");
        }

        $past_data = $this->dbh->prepare("
            SELECT
                *
            FROM {$data['type']}s
            WHERE {$data['type']}_id = :id");

        $past_data->bindParam(":id", $data['id'], PDO::PARAM_INT);

        if(!$past_data->execute())
        {
            $e = past_data->errorInfo();
            return array("result" => "error", "response" => $e[2]);
        }

        // Fetch previous data
        while($past = $past_data->fetchObject())
        {
            if($data['type'] == 'post')
            {
                $ptitle = $past->post_title;
                $ppublished = $past->published;
                $ptext = $past->post_text;
                $pcategory = $past->categories;
                $pcomments = $past->allow_comments;
            }
            elseif($data['type'] == 'page')
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

            // For posts only
            if($data['type'] == 'post')
            {
                // Check comments checkbox
                if(isset($data['comments']) && $data['comments'] == 1)
                {
                    $comments = 1;
                }
                else
                {
                    $comments = 0;
                }

                // Check category
                $category = (int)$data['category'];
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

            if($data['type'] == 'post')
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
                $edit = $this->dbh->prepare("
                    UPDATE
                        {$type}s
                    SET " . implode(', ', $base) . "
                    WHERE {$type}_id = :id
                ");

                $edit->bindParam(":id", $data['id'], PDO::PARAM_INT);

                if(!$edit->execute())
                {
                    $e = $edit->errorInfo();
                    return array("result" => "error", "response" => $e[2]);
                }

                // Create URL to return to jQuery
                $url = get_bloginfo('url')."?".$data['type']."=".$data['id'];

                // Return JSON-encoded response
                return array("result" => "success", "response" => $url);
            }
        }
    }
}

?>
