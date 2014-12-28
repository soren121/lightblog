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
        if(permissions('EditOthersPosts'))
        {
            if($data['checked'] != '' && !array_key_exists('delete', $data))
            {
                switch($data['action'])
                {
                    case 'delete':
                        $action = $this->dbh->prepare("DELETE FROM :type WHERE :type_id IN (:in)");
                        break;
                    case 'publish':
                        $action = $this->dbh->prepare("UPDATE :type SET published=".time()." WHERE :type_id IN (:in)");
                        break;
                    case 'unpublish':
                        $action = $this->dbh->prepare("UPDATE :type SET published=0 WHERE :type_id IN (:in)");
                }
                $action->bindParam(":in", implode(',', $data['checked']), PDO::PARAM_STR);
            }
            elseif(array_key_exists('delete', $data))
            {
                $action = @$this->dbh->exec("DELETE FROM :type WHERE :type_id = :in");
                $action->bindParam(":in", $data['delete'], PDO::PARAM_INT);
            }

            $action->bindParam(":type", strip_tags($data['type'])."s");
            $action->bindParam(":type_in", strip_tags($data['type'])."_id");

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
