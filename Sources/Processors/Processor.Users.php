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

require("Processor.TableSelection.php");

class Users extends TableSelection
{
    public function processor($data)
    {
        list($users, $total) = $this->query($data, 'users', 'user_id');

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
