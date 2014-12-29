<?php
/*********************************************

    LightBlog 0.9
    SQLite blogging platform

    Sources/Processors/Processor.BulkAction.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

*********************************************/

class BulkAction
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = $GLOBALS['dbh'];
    }

    public function processor($data)
    {
        if(!in_array($data['type'], ['post', 'page']))
        {
            return array("result" => "error", "response" => "Invalid content type.");
        }

        if(permissions('EditOthersPosts'))
        {
            if($data['checked'] != '' && !array_key_exists('delete', $data))
            {
                switch($data['action'])
                {
                    case 'delete':
                        $action = $this->dbh->prepare("DELETE FROM {$data['type']}s WHERE {$data['type']}_id IN (:in)");
                        break;
                    case 'publish':
                        $action = $this->dbh->prepare("UPDATE {$data['type']}s SET published=".time()." WHERE {$data['type']}_id IN (:in)");
                        break;
                    case 'unpublish':
                        $action = $this->dbh->prepare("UPDATE {$data['type']}s SET published=0 WHERE {$data['type']}_id IN (:in)");
                }
                $action->bindValue(":in", implode(',', $data['checked']), PDO::PARAM_STR);
            }
            elseif(array_key_exists('delete', $data))
            {
                $action = $this->dbh->prepare("DELETE FROM {$data['type']}s WHERE {$data['type']}_id = :in");
                $action->bindValue(":in", $data['delete'], PDO::PARAM_INT);
            }

            if(!$action->execute())
            {
                return array("result" => "error", "response" => $action->errorInfo()[2]);
            }
            else
            {
                return array("result" => "success");
            }
        }
    }
}

?>
