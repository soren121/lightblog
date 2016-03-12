<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/Processors/Processor.ApproveComment.php

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

class ApproveComment extends Processor
{
    public function processor($data)
    {
        if(permissions('EditComments'))
        {
            // Execute query to approve comment
            $approve = $this->dbh->prepare("UPDATE comments SET published=1 WHERE id=?");

            $approve->bindValue(1, $data['id'], PDO::PARAM_INT);

            if(!$approve->execute())
            {
                $e = $approve->errorInfo();
                return array("result" => "error", "response" => $e[2]);
            }

            return array('result' => 'success');
        }
    }
}

?>
