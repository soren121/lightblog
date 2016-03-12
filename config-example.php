<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    config.php

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

// Constant: DBH
// Defines the path to the SQLite database
define('DBH', 'absolute path to database here');

define('LBCOOKIE', 'name of login cookie');

/*-------DO NOT EDIT BELOW THIS LINE!---------*/

// Define absolute path of main directory here
if(!defined('ABSPATH')) {
    // Constant: ABSPATH
    // Defines the absolute path to LightBlog's main directory
    define('ABSPATH', dirname(__FILE__));
}

?>
