<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/Processors/Processor.BulkAction.php

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

class BulkAction extends Processor
{
    public function processor($data)
    {
        if(!in_array($data['type'], array('post', 'page', 'error', 'user')))
        {
            return array("result" => "error", "response" => "Invalid content type.");
        }

        if(permissions('EditOthersPosts'))
        {
            if($data['checked'] != '' && !array_key_exists('delete', $data))
            {
                function sanitize_ids(&$ids, $index)
                {
                    $ids = (int)$ids;
                }

                array_walk($data['checked'], 'sanitize_ids');

                $in = implode(',', $data['checked']);

                switch($data['action'])
                {
                    case 'delete':
                        $action = $this->dbh->prepare("DELETE FROM {$data['type']}s WHERE {$data['type']}_id IN ({$in})");
                        break;
                    case 'publish':
                        $action = $this->dbh->prepare("UPDATE {$data['type']}s SET published=".time()." WHERE {$data['type']}_id IN ({$in})");
                        break;
                    case 'unpublish':
                        $action = $this->dbh->prepare("UPDATE {$data['type']}s SET published=0 WHERE {$data['type']}_id IN ({$in})");
                }
            }
            elseif(array_key_exists('delete', $data))
            {
                $action = $this->dbh->prepare("DELETE FROM {$data['type']}s WHERE {$data['type']}_id = :id");
                $action->bindValue(":id", $data['delete'], PDO::PARAM_INT);
            }

            if(!$action->execute())
            {
                $action->errorInfo();
                return array("result" => "error", "response" => $e[2]);
            }
            else
            {
                return array("result" => "success");
            }
        }
    }
}

?>
