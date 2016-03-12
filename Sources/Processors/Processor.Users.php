<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/Processors/Processor.Users.php

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
            $return .= '<tr id="'.$row->user_id.'"';

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
