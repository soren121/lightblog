<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    admin/head.php

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

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $head_title ?> // <?php bloginfo('title') ?> &mdash; LightBlog</title>
    <link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/assets/css/main.css" />
    <link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/assets/css/<?php echo $head_css ?>" />
    <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/js/jQuery.js"></script>
</head>
<body>
    <div id="maincontainer">
        <div id="header">
            <img id="logo" src="assets/images/logotype-min.svg" />
            <div>  
                <h2 id="blogtitle"><a href="<?php bloginfo('url') ?>"><?php bloginfo('title') ?></a></h2>
                <h2 id="section"><?php echo $head_title ?></h2>
                <div id="ajaxresponse"><?php echo (!isset($head_response) ? '' : $head_response) ?></div>
            </div>
        </div>
