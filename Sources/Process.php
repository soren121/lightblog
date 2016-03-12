<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/Process.php

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

function processForm($data)
{
    if(!empty($data) && isset($data['form']))
    {
        if(!isset($data['csrf_token']) || $data['csrf_token'] !== user()->csrf_token())
        {
            if($data['form'] != 'Comment')
            {
                return array('result' => 'error', 'response' => 'CSRF token incorrect or missing.');
            }
        }
        if(file_exists(ABSPATH .'/Sources/Processors/Processor.'.$data['form'].'.php'))
        {
            require(ABSPATH .'/Sources/Processors/Processor.php');
            require(ABSPATH .'/Sources/Processors/Processor.'.$data['form'].'.php');
            $class = new $data['form']();
            return $class->processor($data);
        }
        else
        {
            return array('result' => 'error', 'response' => 'Form processor "'.$data['form'].'.php" does not exist.');
        }
    }
    else
    {
        return;
    }
}

?>
