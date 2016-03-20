<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/Processors/Processor.TableSelection.php

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

abstract class TableSelection extends Processor
{
    public function query($data, $table, $id)
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
            array_push($where, $id." < ".(int)$data['before']);
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
            FROM {$table}
        ")->fetchColumn();

        $table = $this->dbh->prepare("
            SELECT
                *
            FROM {$table}
            WHERE {$where}
            ORDER BY {$id} desc
            LIMIT :page , :count
        ");

        $table->bindParam(":page", $data['page'], PDO::PARAM_INT);
        $table->bindParam(":count", $data['count'], PDO::PARAM_INT);

        return array($table, $total);
    }
}

?>
