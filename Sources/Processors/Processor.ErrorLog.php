<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/Processors/Processor.ErrorLog.php

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

class ErrorLog extends TableSelection
{
    public function processor($data)
    {
        list($errorlog, $total) = $this->query($data, 'errors', 'error_id');

        if(!$errorlog->execute())
        {
            $e = $errorlog->errorInfo();
            return array("result" => "error", "response" => $e[2]);
        }

        $return = '';
        $i = 0;
        while($row = $errorlog->fetchObject())
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

            $return .= '<td><input type="checkbox" name="checked[]" value="'.$row->error_id.'" class="bf table" /></td>';
            $return .= '<td>'.errorsMapType($row->error_type).'</td>';
            $return .= '<td>
                <a href="'.get_bloginfo('url').'admin/error.php?id='.$row->error_id.'">'.(strlen($row->error_message) > 50 ? substr($row->error_message, 0, 47).'...' : $row->error_message).'</a>';
            $return .= '</td>';
            $return .= '<td>'.date('n/j/Y g:i:sA', $row->error_time).'</td>';
            $return .= '<td class="c"><img src="style/delete.png" alt="Delete" onclick="deleteItem('.$row->error_id.');" style="cursor:pointer;" /></td>';
            $return .= '</tr>';
        }

        return array("result" => "success", "response" => $return);
    }
}

?>
