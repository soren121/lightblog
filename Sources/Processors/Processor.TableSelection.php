<?php
/*********************************************

    LightBlog 0.9
    SQLite blogging platform

    Sources/Processors/Processor.TableSelection.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

*********************************************/

class TableSelection
{
    protected $dbh;

    public function __construct()
    {
        $this->dbh = $GLOBALS['dbh'];
    }

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
