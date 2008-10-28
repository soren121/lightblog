<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>{$main_title}{if !empty($title)}- {$title}{/if}</title>
	<link rel="stylesheet" type="text/css" href="{$theme_dir}style.css" />
	<script type="text/javascript" src="{$sources_dir}jquery.js"></script>
	<script type="text/javascript" src="{$sources_dir}jquery.form.js"></script>
	<script type="text/javascript" src="{$sources_dir}jquery.wysiwyg.js"></script>
	<script type="text/javascript" src="{$sources_dir}vx.js"></script>
	<script type="text/javascript"> 
	$(document).ready(function() { 
		$('.ajaxform').ajaxForm(function() { 
            alert("Thank you!"); 
        }); 
		$('.wysiwyg').wysiwyg();
	});
  </script> 
</head>
<body>

   <!-- Begin Wrapper -->
   <div id="wrapper">
   
         <!-- Begin Header -->
         <div id="header">
		 
		       This is the Header		 
			   
		 </div>		 
		 <!-- End Header -->
		 <!-- Begin Faux Columns -->
		 <div id="faux">
