
<!-- saved from url=(0069)http://lightblog.googlecode.com/svn-history/r447/trunk/admin/edit.php -->
<HTML><BODY><PRE style="word-wrap: break-word; white-space: pre-wrap;">&lt;?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/edit.php
	
	©2009-2010 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

*********************************************/

// Require config file
require('../config.php');
require(ABSPATH .'/Sources/Core.php');

if((int)$_GET['type'] == 1) { $type = 'post'; }
elseif((int)$_GET['type'] == 2) { $type = 'page'; }
elseif((int)$_GET['type'] == 3) { $type = 'category'; }

# Query for past content
$result = $dbh-&gt;query("SELECT * FROM ".($type == 'category' ? 'categorie' : $type)."s WHERE id=".(int)$_GET['id']) or die(sqlite_error_string($dbh-&gt;lastError));

# Get past data and set it
while($past = $result-&gt;fetchObject()) {
	$title = $past-&gt;title;
	if($type !== 'category') {
		$author = $past-&gt;author;
		$s_category = (int)$past-&gt;category;
		if($past-&gt;published == 1) {
			$ps_checked = 'checked="checked"';
		}
		if($past-&gt;comments == 1) {
			$cs_checked = 'checked="checked"';
		}
	}
	if($type == 'post') { $text = $past-&gt;post; }
	elseif($type == 'page') { $text = $past-&gt;page; }
	elseif($type == 'category') { $text  = $past-&gt;info; }
}

?&gt;
&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"&gt;
&lt;html xmlns="http://www.w3.org/1999/xhtml"&gt;
&lt;head&gt;
	&lt;meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /&gt;
	&lt;title&gt;Edit &lt;?php echo ucwords($type) ?&gt; - &lt;?php bloginfo('title') ?&gt;&lt;/title&gt;
	&lt;link rel="stylesheet" type="text/css" href="&lt;?php bloginfo('url') ?&gt;admin/style/style.css" /&gt;
	&lt;!--[if lte IE 7]&gt;&lt;style type="text/css"&gt;html.jqueryslidemenu { height: 1%; }&lt;/style&gt;&lt;![endif]--&gt;
	&lt;script type="text/javascript" src="&lt;?php bloginfo('url') ?&gt;Sources/jQuery.js"&gt;&lt;/script&gt;
	&lt;script type="text/javascript" src="&lt;?php bloginfo('url') ?&gt;Sources/jQuery.SlideMenu.js"&gt;&lt;/script&gt;
	&lt;script type="text/javascript" src="&lt;?php bloginfo('url') ?&gt;Sources/jQuery.Corners.js"&gt;&lt;/script&gt;
	&lt;script type="text/javascript" src="&lt;?php bloginfo('url') ?&gt;Sources/nicEdit.js"&gt;&lt;/script&gt; 
	&lt;script type="text/javascript"&gt;	
		$(document).ready(function(){
			$('.rounded').corner(); 
			$('.roundedt').corner("round top 10px"); 
			$('.roundedb').corner("round bottom 10px");
			new nicEditor({iconsPath:'&lt;?php bloginfo('url') ?&gt;Sources/nicEditorIcons.gif',xhtml:true}).panelInstance('wysiwyg');
		});
		$(function() {
			$('#edit').submit(function() {
				var inputs = [];
				var wysiwygtext = nicEditors.findEditor('wysiwyg').getContent();
				$('.ef', this).each(function() {
					if($(this).is(':checkbox') &amp;&amp; $(this).is(':not(:checked)')) {
						void(0);
					}
					else {
						inputs.push(this.name + '=' + escape(this.value));
					}
				})
				$('#wysiwyg', this).each(function() {
					inputs.push(this.name + '=' + unescape(wysiwygtext));
				})
				jQuery.ajax({
					data: inputs.join('&amp;'),
					type: "POST",
					url: this.getAttribute('action'),
					timeout: 2000,
					error: function() {
						$('#notifybox').text('Failed to submit &lt;?php echo $type ?&gt;.').css("background","#b20000").slideDown("normal");
						console.log("Failed to submit");
						alert("Failed to submit.");
					},
					success: function(r) {
						$('#notifybox').html('&lt;?php echo ucwords($type) ?&gt; edited. | &lt;' + 'a href="' + r + '"&gt;View &lt;?php echo $type ?&gt;&lt;/' + 'a&gt;').css("background", "#CFEBF7").slideDown("normal");
					}
				})
				return false;
			})
		});
	&lt;/script&gt;
&lt;/head&gt;

&lt;body&gt;
	&lt;div id="wrapper"&gt;
		&lt;div id="header" class="roundedt"&gt;
			&lt;a href="&lt;?php bloginfo('url') ?&gt;"&gt;&lt;?php bloginfo('title') ?&gt;&lt;/a&gt;	 
		&lt;/div&gt;
		&lt;?php include('menu.php'); ?&gt;
		&lt;div id="content"&gt;
			&lt;?php if($type !== 'category' &amp;&amp;  permissions(2) || $type !== 'category' &amp;&amp;  permissions(1) &amp;&amp; $author === userFetch('displayname','r') || $type === 'category' &amp;&amp; permissions(2)): if(!isset($type)): ?&gt;
			&lt;p&gt;The type of content to add was not specified. You must have taken a bad link. Please
			use the navigation bar above to choose the correct type.&lt;/p&gt;
			&lt;?php else: ?&gt;
			&lt;h2 class="title"&gt;&lt;img class="textmid" src="style/manage.png" alt="" /&gt;Edit &lt;?php echo ucwords($type) ?&gt;&lt;/h2&gt;
			&lt;div id="notifybox"&gt;&lt;/div&gt;
			&lt;form action="&lt;?php bloginfo('url') ?&gt;Sources/ProcessAJAX.php" method="post" id="edit"&gt;
				&lt;div style="float:left;width:480px;margin-top:3px;"&gt;
					&lt;label class="tfl" for="title"&gt;Title&lt;/label&gt;
					&lt;input class="textfield ef" name="title" type="text" id="title" title="Title" value="&lt;?php echo stripslashes($title) ?&gt;" /&gt;
					&lt;label class="tfl" for="wysiwyg"&gt;&lt;?php echo $type == 'category' ? 'Info' : 'Body'; ?&gt;&lt;/label&gt;
					&lt;textarea rows="12" cols="36" name="text" id="wysiwyg"&gt;&lt;?php echo stripslashes($text) ?&gt;&lt;/textarea&gt;
					&lt;input class="ef" type="hidden" name="type" value="&lt;?php echo $type ?&gt;" /&gt;
					&lt;input class="ef" type="hidden" name="id" value="&lt;?php echo (int)$_GET['id'] ?&gt;" /&gt;
				&lt;/div&gt;
				&lt;div class="settings" style="float:left;width:170px;margin:16px 0 10px;padding:15px;"&gt;
					&lt;?php if($type == 'post'): ?&gt;
						&lt;label for="category"&gt;Category:&lt;/label&gt;&lt;br /&gt;
						&lt;select class="ef" id="category" name="category"&gt;
							&lt;?php list_categories($s_category) ?&gt;
						&lt;/select&gt;&lt;br /&gt;&lt;br /&gt;
						&lt;label for="comments"&gt;Comments on?&lt;/label&gt;
						&lt;input class="ef" type="checkbox" name="comments" id="comments" &lt;?php echo $cs_checked; ?&gt; value="1" /&gt;&lt;br /&gt;
					&lt;?php endif; if($type != 'category'): ?&gt;
						&lt;label for="published"&gt;Published?&lt;/label&gt;
						&lt;input class="ef" type="checkbox" name="published" id="published" &lt;?php echo $ps_checked; ?&gt; value="1" /&gt;&lt;br /&gt;&lt;br /&gt;
					&lt;?php endif; ?&gt;
					&lt;input class="ef submit" name="edit" type="submit" value="Save" /&gt;
				&lt;/div&gt;
				&lt;div style="clear:both;"&gt;&lt;/div&gt;
			&lt;/form&gt;
			&lt;?php endif; endif; ?&gt;
		&lt;/div&gt;
		&lt;div id="footer" class="roundedb"&gt;		
			Powered by LightBlog &lt;?php LightyVersion() ?&gt;    
	    &lt;/div&gt;
	&lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;
</PRE></BODY></HTML>