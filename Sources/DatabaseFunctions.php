<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/DatabaseFunctions.php

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

/*
    Function: get_bloginfo

    Returns the value of a given row.

    Parameters:

        var - Row to obtain value from.
        reload - Reload the settings again, forcibly.

    Returns:

        The value of the given row.
*/
function get_bloginfo($var, $reload = false)
{
    // Global the database handle
    global $dbh;

    // If this is the first time bloginfo's been called...
    if(!isset($GLOBALS['bloginfo_data']) || !empty($reload))
    {
        $result = $dbh->query("
            SELECT
                variable, value
            FROM settings
        ");

        // Let's make an array!
        $GLOBALS['bloginfo_data'] = array();

        // For each row, set a key with the value
        while($row = $result->fetchObject())
        {
            $GLOBALS['bloginfo_data'][$row->variable] = $row->value;
        }

        if(!isset($GLOBALS['bloginfo_data']['themeurl']))
        {
            // Set the theme URL
            $GLOBALS['bloginfo_data']['themeurl'] = $GLOBALS['bloginfo_data']['url'].'themes/'.$GLOBALS['bloginfo_data']['theme'];
        }
    }

    return array_key_exists($var, $GLOBALS['bloginfo_data']) ? $GLOBALS['bloginfo_data'][$var] : false;
}

function bloginfo($var)
{
    echo get_bloginfo($var);
}

function get_roles($role = null)
{
    global $dbh;
    static $rolequery = null;
    if($rolequery === null)
    {
        $result = $dbh->query("
            SELECT
                *
            FROM roles
            ORDER BY role_id desc
        ");

        $roles = array();
        while($row = $result->fetchObject())
        {
            $roles[$row->role_id] = $row->role_name;
        }
    }
    if(!is_null($role))
    {
        return $roles[$role];
    }
    else
    {
        return $roles;
    }
}

?>
