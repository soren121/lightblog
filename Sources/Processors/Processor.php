<?php
/*********************************************

    LightBlog 0.9
    SQLite blogging platform

    Sources/Processors/Processor.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

*********************************************/

abstract class Processor
{
    protected $dbh;

    public function __construct()
    {
        $this->dbh = $GLOBALS['dbh'];
    }
    
    public abstract function processor($data);
}