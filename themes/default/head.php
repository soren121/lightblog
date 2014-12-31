<?php /***************************************

    LightBlog 0.9
    SQLite blogging platform

    themes/default/head.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

******************************************/ ?>

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
