<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/FunctionReplacements.php

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
    Function: utf_substr

    Parameters:
        string $str
        int $start
        int $length
*/
function utf_substr($str, $start, $length = null)
{
    if($length === null)
    {
        return call_user_func(function_exists('mb_substr') ? 'mb_substr' : 'substr', $str, $start);
    }
    else 
    {
        call_user_func(function_exists('mb_substr') ? 'mb_substr' : 'substr', $str, $start, $length);
    }
}

/*
    Function: utf_strtoupper

    Parameters:
        string $str
*/
function utf_strtoupper($str)
{
    return call_user_func(function_exists('mb_strtoupper') ? 'mb_strtoupper' : 'strtoupper', $str);
}

/*
    Function: utf_strtolower

    Parameters:
        string $str
*/
function utf_strtolower($str)
{
    return call_user_func(function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower', $str);
}

/*
    Function: utf_strrpos

    Parameters:
        string $haystack
        string $needle
        int $offset
*/
function utf_strrpos($haystack, $needle, $offset = 0)
{
    return call_user_func(function_exists('mb_strrpos') ? 'mb_strrpos' : 'strrpos', $haystack, $needle, $offset);
}

/*
    Function: utf_strripos

    Parameters:
        string $haystack
        string $needle
        int $offset
*/
function utf_strripos($haystack, $needle, $offset = 0)
{
    return call_user_func(function_exists('mb_strripos') ? 'mb_strripos' : 'strripos', $haystack, $needle, $offset);
}

/*
    Function: utf_strpos

    Parameters:
        string $haystack
        string $needle
        int $offset
*/
function utf_strpos($haystack, $needle, $offset = 0)
{
    return call_user_func(function_exists('mb_strpos') ? 'mb_strpos' : 'strpos', $haystack, $needle, $offset);
}

/*
    Function: utf_strlen

    Parameters:
        string $str
*/
function utf_strlen($str)
{
    return call_user_func(function_exists('mb_strlen') ? 'mb_strlen' : 'strlen', $str);
}

/*
    Function: utf_stripos

    Parameters:
        string $haystack
        string $needle
        int $offset
*/
function utf_stripos($haystack, $needle, $offset = 0)
{
    return call_user_func(function_exists('mb_strpos') ? 'mb_stripos' : 'stripos', $haystack, $needle, $offset);
}

/*
    Function: utf_htmlspecialchars

    Parameters:
        string $str
*/
function utf_htmlspecialchars($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

?>
