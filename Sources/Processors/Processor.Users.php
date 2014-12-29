<?php
/*********************************************

    LightBlog 0.9
    SQLite blogging platform

    Sources/Processors/Processor.Users.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

*********************************************/

class Users
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = $GLOBALS['dbh'];
    }

    public function processor($data)
    {
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
            array_push($where, "error_id < ".(int)$data['before']);
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
            FROM users
        ")->fetchColumn();

        $users = $this->dbh->prepare("
            SELECT
                *
            FROM users
            WHERE {$where}
            ORDER BY user_id desc
            LIMIT :page , :count
        ");

        $users->bindParam(":page", $data['page'], PDO::PARAM_INT);
        $users->bindParam(":count", $data['count'], PDO::PARAM_INT);

        if(!$users->execute())
        {
            $e = $users->errorInfo();
            return array("result" => "error", "response" => $e[2]);
        }

        $return = '';
        $i = 0;
        while($row = $users->fetchObject())
        {
            $i++;
            $return .= '<tr id="'.$row->error_id.'"';

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

            $return .= '<td><input type="checkbox" name="checked[]" value="'.$row->user_id.'" class="bf table" /></td>';
            $return .= '<td><span class="role-'.$row->user_role.'">'.$row->user_name.'</span></td>';
            $return .= '<td>'.$row->display_name.'</td>';
            $return .= '<td>'.$row->user_email.'</td>';
            $return .= '<td>'.$row->user_ip.'</td>';
            $return .= '<td class="c"><img src="style/delete.png" alt="Delete" onclick="deleteItem('.$row->user_id.');" style="cursor:pointer;" /></td>';
            $return .= '</tr>';
        }
        return array("result" => "success", "response" => $return);
    }
}

?>
