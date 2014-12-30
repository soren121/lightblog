<?php
/*********************************************

    LightBlog 0.9
    SQLite blogging platform

    Sources/Processors/Processor.ErrorLog.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

*********************************************/

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
