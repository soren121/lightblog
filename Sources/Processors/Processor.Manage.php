<?php
/*********************************************

    LightBlog 0.9
    SQLite blogging platform

    Sources/Processors/Processor.Manage.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

*********************************************/

class Manage
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = $GLOBALS['dbh'];
    }

    public function processor($data)
    {
        if(!in_array($data['type'], array('post', 'page')))
        {
            return array("result" => "error", "response" => "Invalid content type.");
        }

        $where = array();
        if(isset($data['prev']))
        {
            $data['page'] -= 1;
        }
        if(isset($data['next']))
        {
            $data['page'] += 1;
        }
        if($data['page'] != 0)
        {
            if(!isset($data['count']))
            {
                $data['count'] = 10;
            }

            $data['page'] = (int)(($data['page'] - 1) * $data['count']);
        }
        elseif($data['before'] != 0)
        {
            array_push($where, $data['type']."_id < ".(int)$data['before']);
            $data['start'] = 0;
        }

        if(!empty($where))
        {
            $where = implode(' AND ', $where);
        }
        else
        {
            $where = "1";
        }

        $total = $this->dbh->query("
            SELECT
                COUNT(*)
            FROM {$data['type']}s
        ")->fetchColumn();

        $manage = $this->dbh->prepare("
            SELECT
                *
            FROM {$data['type']}s
            WHERE {$where}
            ORDER BY {$data['type']}_id desc
            LIMIT :page , :count
        ");

        $manage->bindParam(":page", $data['page'], PDO::PARAM_INT);
        $manage->bindParam(":count", $data['count'], PDO::PARAM_INT);

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
                <td>'.date('n/j/Y', $row->{$data['type'].'_date'}).'</td>';

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
                    <img src="style/edit-d.png" alt="" title="You aren\'t allowed to edit this '.$data['type'].'." />
                </td>
                <td class="c">
                    <img src="style/delete-d.png" alt="" title="You aren\'t allowed to delete this '.$data['type'].'." />
                </td>';
            }

            $return .= '</tr>';
        }
        return array("result" => "success", "response" => $return);
    }
}

?>
