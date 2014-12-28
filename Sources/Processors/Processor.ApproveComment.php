<?php
/*********************************************

    LightBlog 0.9
    SQLite blogging platform

    Sources/Processors/Processor.ApproveComment.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

*********************************************/

class ApproveComment
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = $GLOBALS['dbh'];
    }

    public function processor($data)
    {
        if(permissions('EditComments'))
        {
            // Execute query to approve comment
            $approve = $this->dbh->prepare("UPDATE comments SET published=1 WHERE id=?");

            $approve->bindParam(1, $data['id'], PDO::PARAM_INT);

            if(!$approve->execute())
            {
                return array("result" => "error", "response" => $approve->errorInfo()[2]);
            }

            return array('result' => 'success');
        }
    }
}

?>
