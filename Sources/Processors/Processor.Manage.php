<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/Processors/Processor.Manage.php

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

require("Processor.TableSelection.php");

class Manage extends TableSelection
{
    public function processor($data)
    {
        if(!in_array($data['type'], array('post', 'page')))
        {
            return array("result" => "error", "response" => "Invalid content type.");
        }

        list($manage, $total) = $this->query($data, $data['type'].'s', $data['type'].'_id');

        if(!$manage->execute())
        {
            $e = $manage->errorInfo();
            return array("result" => "error", "response" => $e[2]);
        }

        if($data['type'] == 'post')
        {
            $category_query = $this->dbh->query("SELECT * FROM post_categories");
            $categories_query = $this->dbh->query("SELECT category_id, full_name FROM categories");
            $category_ids = array();
            $categories = array();

            while($cat = $category_query->fetchObject())
            {
                $category_ids[$cat->post_id] = $cat->category_id;
            }
            while($cat = $categories_query->fetchObject())
            {
                $categories[$cat->category_id] = $cat->full_name;
            }
        }

        $return = '';
        $i = 0;

        $date_format = get_bloginfo('date_format');
        $time_format = get_bloginfo('time_format');

        while($row = $manage->fetchObject())
        {
            $i++;
            $return .= '<tr id="'.$row->{$data['type'].'_id'}.'"';

            if($i == $total)
            {
                $return .= ' class="last">';
            }
            elseif($i == 1)
            {
                $return .= ' class="first">';
            }
            else
            {
                $return .= '>';
            }

            $return .= '
                <td>
                    <input type="checkbox" name="checked[]" value="'.$row->{$data['type'].'_id'}.'" class="bf table" />
                </td>';

            $return .= '
                <td>
                    <a href="'.get_bloginfo('url').'?'.$data['type'].'='.$row->{$data['type'].'_id'}.'">'.$row->{$data['type'].'_title'}.'</a>';
                    if($row->published == 0) {
                        $return .= '
                        <span style="color:#E36868;">&mdash; Draft</span>';
                    }
            $return .= '
                </td>
                <td>'.$row->author_name.'</td>
                <td title="'. date($date_format.' \a\t '.$time_format, $row->{$data['type'].'_date'}) .'">'.date($date_format, $row->{$data['type'].'_date'}).'</td>';

            if($data['type'] == 'post')
            {
                $return .= '
                <td>
                    <a href="'.get_bloginfo('url').'?category='.$category_ids[$row->post_id].'">'.$categories[$category_ids[$row->post_id]].'</a>
                </td>';
            }
            if(permissions("EditOthers".ucwords($data['type'])."s") || permissions("Edit".ucwords($data['type'])."s") && user()->id() == $row->author_id)
            {
                $return .= '
                <td class="c">
                    <a href="edit.php?type='.(int)$_GET['type'].'&amp;id='.$row->{$data['type'].'_id'}.'">
                        <img src="style/edit.png" alt="Edit" style="border:0;" />
                    </a>
                </td>

                <td class="c">
                    <input type="submit" name="delete" value="'.$row->{$data['type'].'_id'}.'" class="bf table" />
                </td>';
            }
            else {
                $return .= '
                <td class="c">
                    <img class="disabled" src="style/edit.png" alt="" title="You aren\'t allowed to edit this '.$data['type'].'." />
                </td>
                <td class="c">
                    <img class="disabled" src="style/delete.png" alt="" title="You aren\'t allowed to delete this '.$data['type'].'." />
                </td>';
            }

            $return .= '</tr>';
        }

        return array("result" => "success", "response" => $return);
    }
}

?>
