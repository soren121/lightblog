<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    themes/default/head.php
    
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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php bloginfo('title') ?></title>
    <link rel="stylesheet" type="text/css" href="<?php bloginfo('themeurl') ?>/style/style.css" />
    <link rel="alternate" type="application/rss+xml" href="<?php bloginfo('url') ?>feed.php"  title="RSS Feed" />
    <link rel="alternate" type="application/atom+xml" href="<?php bloginfo('url') ?>feed.php?type=atom"  title="Atom Feed" />
    <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/js/jQuery.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#notifybox').click(function() { $(this).slideUp('normal'); });
        });
    </script>
</head>
<body>
    <div id="wrapper">
        <div id="header" class="rounded">
            <h3><a href="<?php bloginfo('url') ?>"><?php bloginfo('title') ?></a></h3>
        </div>
