<?php /***************************************

	LightBlog 0.9
	SQLite blogging platform

	themes/default/head.php

	©2008-2012 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

******************************************/ ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title><?php bloginfo('title') ?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('themeurl') ?>/style/style.css" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('url') ?>feed.php"  title="RSS Feed" />
	<link rel="alternate" type="application/atom+xml" href="<?php bloginfo('url') ?>feed.php?type=atom"  title="Atom Feed" />
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Corners.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.rounded').corner('round 5px');
			$('#notifybox').click(function() { $(this).slideUp('normal'); });
		});
	</script>
</head>
<body>
	<div id="wrapper">
        <div id="header" class="rounded">
			<h3><a href="<?php bloginfo('url') ?>"><?php bloginfo('title') ?></a></h3>
		</div>
