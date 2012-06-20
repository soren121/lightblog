<?php

/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/head.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $title ?> // <?php bloginfo('title') ?> &mdash; LightBlog</title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/new/main.css" />
	<!--[if lte IE 7]>
		<link rel="stylesheet" type="text/css" href="style/ie-fixes.css" />
	<![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
</head>
<body>
	<div id="maincontainer">	
		<div id="header">
			<h1><a href="<?php bloginfo('url') ?>"><?php bloginfo('title') ?></a><span> // <?php echo $title ?></span></h1>
			<div id="ajaxresponse"></div>
			<div class="clear"></div>
		</div>