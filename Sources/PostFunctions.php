<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/PostFunctions.php

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

if(!defined('INLB'))
{
    die('Nice try...');
}

/*
    Function: generate_shortname

    Parameters:
        int $id
        string $name
*/
function generate_shortname($id, $name)
{
    $char_map = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $name_length = utf_strlen($name);
    $name = utf_strtolower($name);
    $shortname = '';
    $prev_char = null;
    for($index = 0; $index < $name_length; $index++)
    {
        $char = utf_substr($name, $index, 1);

        // Is this an allowed character?
        if(utf_strpos($char_map, $char) === false)
        {
            // No repeated -.
            if($prev_char !== null && $prev_char != '-')
            {
                $prev_char = '-';
                $shortname .= '-';
            }
        }
        else
        {
            $prev_char = $char;
            $shortname .= $char;
        }
    }

    return ((int)$id). '-'. trim($shortname, '-');
}
?>
