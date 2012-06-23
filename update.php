<?php

/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	update.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

define('INLB', true);
define('INDEVMODE', true);

// Shutdown Magic Quotes automatically
// Highly inefficient, but there isn't much we can do about it
if((function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc() == 1) || @ini_get('magic_quotes_sybase'))
{
	function stripslashes_gpc(&$value)
	{
		$value = stripslashes($value);
	}

	array_walk_recursive($_GET, 'stripslashes_gpc');
	array_walk_recursive($_POST, 'stripslashes_gpc');
	array_walk_recursive($_COOKIE, 'stripslashes_gpc');
	array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}

if(function_exists('get_magic_quotes_runtime') && @get_magic_quotes_runtime())
{
	@set_magic_quotes_runtime(false);
}

// Gets directory URL
function baseurl()
{
	$site_url = explode('/', $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	unset($site_url[count($site_url)-1]);

	$site_url = implode('/', $site_url);
	return 'http://'.$site_url.'/';
}

// Operates the side menu selectors
// First parameter specifies page
// Second parameter defines if we're changing the page or just reading it
function menuClass($item, $op = 0)
{
	static $cur_item = 1;

	if($op == 1)
	{
		$cur_item = (int)$item;
	}
	else
	{
		if($cur_item == $item)
		{
			echo "selected";
		}

		if($cur_item > $item)
		{
			echo "done";
		}

		if($cur_item < $item)
		{
			echo "notdone";
		}
	}
}

function remove_inserts($sql)
{
	// Since none of the text have semicolons we can do this safely.
	$commands = array();
	foreach(explode(';', $sql) as $command)
	{
		$command = trim($command);

		// Like the function name implies, no INSERTs. Well, except for a couple
		// tables.
		if(strtoupper(substr($command, 0, 6)) == 'INSERT' && strpos($command, 'INSERT INTO \'roles\'') === false && strpos($command, 'INSERT INTO \'role_permissions\'') === false)
		{
			continue;
		}

		$commands[] = $command;
	}

	return implode(';'. "\r\n", $commands);
}

function create_temp_db($sql)
{
	require(dirname(__FILE__). '/Sources/StringFunctions.php');

	// We need a random name for the database.
	$filename = randomString(mt_rand(32, 64)). '.db';

	$ndbh = new SQLiteDatabase($filename);

	// Execute the queries.
	$executed = $ndbh->queryExec($sql, $error_message);

	return array($ndbh, $filename, ($executed ? null : $error_message));
}

function sanitize_row($row)
{
	foreach($row as $key => $value)
	{
		if((string)$value == (string)(int)$value)
		{
			$value = (int)$value;
		}
		elseif((string)$value == (string)(float)$value)
		{
			$value = (float)$value;
		}
		else
		{
			$value = sqlite_escape_string($value);
		}

		$row[$key] = $value;
	}

	return $row;
}

function generate_query($table_name, $data)
{
	return 'INSERT INTO \''. $table_name. '\' VALUES(\''. implode('\', \'', sanitize_row($data)). '\')';
}

function copy_data($db, $ndb)
{
	// First off, for the categories.
	$request = $db->query('
		SELECT
			id, shortname, fullname, info
		FROM categories');

	while($row = $request->fetch(SQLITE_ASSOC))
	{
		$ndb->query(generate_query('categories', array($row['id'], $row['shortname'], $row['fullname'], $row['info'])));
	}

	// Then all the settings.
	$request = $db->query('
		SELECT
			variable, value
		FROM core');

	while($row = $request->fetch(SQLITE_ASSOC))
	{
		$ndb->query(generate_query('settings', array($row['variable'], $row['value'])));
	}

	// Comments, we want those!
	$request = $db->query('
		SELECT
			id, published, pid, name, email, website, date, text, spam
		FROM comments');

	$comment_count = array();
	while($row = $request->fetch(SQLITE_ASSOC))
	{
		$ndb->query(generate_query('comments', array($row['id'], $row['pid'], 'comment', $row['published'], 0, $row['name'], $row['email'], $row['website'], '', $row['date'], $row['text'], $row['spam'])));

		if($row['published'] == 1)
		{
			$comment_count[$row['pid']] = (isset($comment_count[$row['pid']]) ? $comment_count[$row['pid']] + 1 : 1);
		}
	}

	// We will do users next, we need their names to do a couple things right
	// in just a bit.
	$request = $db->query('
		SELECT
			id, username, password, email, displayname, role, ip, salt
		FROM users');

	$id_map = array();
	while($row = $request->fetch(SQLITE_ASSOC))
	{
		$id_map[strtolower($row['username'])] = (int)$row['id'];

		$ndb->query(generate_query('users', array($row['id'], $row['username'], $row['password'], $row['email'], $row['displayname'], $row['role'], $row['ip'], $row['salt'], 1, time())));
	}

	// Then their pages...
	$request = $db->query('
		SELECT
			id, title, page, date, author, published
		FROM pages');

	while($row = $request->fetch(SQLITE_ASSOC))
	{
		$ndb->query(generate_query('pages', array($row['id'], $row['title'], generate_shortname($row['id'], $row['title']), $row['date'], $row['published'], $row['author'], isset($id_map[strtolower($row['author'])]) ? $id_map[strtolower($row['author'])] : 0, $row['page'])));
	}

	// Finally, their posts. The most important, if I do say so myself :P.
	$request = $db->query('
		SELECT
			id, title, post, date, author, published, category, comments
		FROM posts');

	while($row = $request->fetch(SQLITE_ASSOC))
	{
		$ndb->query(generate_query('posts', array($row['id'], $row['title'], generate_shortname($row['id'], $row['title']), $row['date'], $row['published'], $row['author'], isset($id_map[strtolower($row['author'])]) ? $id_map[strtolower($row['author'])] : 0, $row['post'], $row['category'], $row['comments'], $row['comments'], isset($comment_count[$row['id']]) ? $comment_count[$row['id']] : 0)));
		$ndb->query(generate_query('post_categories', array($row['id'], $row['category'])));
	}
}

function generate_shortname($id, $name)
{
	$char_map = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$name_length = strlen($name);
	$name = strtolower($name);
	$shortname = '';
	$prev_char = null;
	for($index = 0; $index < $name_length; $index++)
	{
		$char = substr($name, $index, 1);

		// Is this an allowed character?
		if(strpos($char_map, $char) === false)
		{
			// No repeated -.
			if($prev_char !== null && $prev_char != '-')
			{
				$prev_char = '-';
				$shortname .= '-';
			}
		}
		else
		{
			$prev_char = $char;
			$shortname .= $char;
		}
	}

	return ((int)$id). '-'. trim($shortname, '-');
}

function update()
{
	require('config.php');

	@set_time_limit(3600);
	@ini_set('memory_limit', '64M');

	$dbh = new SQLiteDatabase( DBH );

	// We actually use the install.sql file this time...
	if(!is_readable('install.sql') && !chmod('install.sql', 0644))
	{
		return 'Failed to open install.sql. Please chmod it to 644 and try again.';
	}

	// Open, read, and close SQL file
	if(is_readable('install.sql'))
	{
		$sqlh = fopen('install.sql', 'r');
		$sql = fread($sqlh, filesize('install.sql'));
		fclose($sqlh);

		// Now to do some work...
		// Such as removing any INSERT's, as they aren't needed.
		$sql = remove_inserts($sql);

		// Now create a temporary database with the new SQL.
		list($ndbh, $filename, $error_message) = create_temp_db($sql);

		if($error_message !== null)
		{
			return 'Failed to write to the temporary database because: '.$error_message.'.';
		}

		// Alright, almost there. Now we need to copy the data over from the old
		// database to the new one.
		copy_data($dbh, $ndbh);

		// Okay, close the two databases and then rename it all.
		unset($dbh, $ndbh);

		$backup_name = dirname(DBH). '/backup-'. basename(DBH);
		rename(DBH, $backup_name);
		rename($filename, DBH);

		return null;
	}

	return 'An unknown error occurred.';
}

// Will process after Step 1
if(isset($_POST['update']))
{
	$return = update();
	// Check for errors
	if(!$return == null)
	{
		$error = $return;
		$page = null;
	}
	else
	{
		// Set new updater page
		$page = "finish";
		menuClass(2, 1);
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>LightBlog Updater</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<style type="text/css">
		body {
			background: #EDEDED;
			font-family: "Trebuchet MS", "Verdana", sans;
		}
		#wrapper {
			width: 870px;
			min-height: 540px;
			margin: 20px auto;
		}
		#header {
			height: 35px;
			background: #2B5EA1;
			color: #fff;
		}
		#header h3 {
			font-size: 1.2em;
			padding: 5px 0 0 8px;
		}
		#sidebar {
			background: #D6DFEB;
			width: 239px;
			float: left;
			min-height: 505px;
			border-left: 1px dotted #5E9BEB;
			border-right: 1px dotted #5E9BEB;
			border-bottom: 1px dotted #5E9BEB;
		}
		#sidebar ul {
			list-style-type: circle;
			font-size: 0.9em;
			margin-top: 25px;
		}
		#sidebar ul li {
			margin-bottom: 3px;
		}
		#sidebar ul li.selected {
			color: #000;
		}
		#sidebar ul li.done {
			color: #9BB1CF;
		}
		#sidebar ul li.notdone {
			color: #748CAB;
		}
		#content {
			width: 608px;
			min-height: 495px;
			float: left;
			background: #fff;
			border-bottom: 1px dotted #5E9BEB;
			border-right: 1px dotted #5E9BEB;
			padding: 5px 10px;
		}
		#content h2 {
			color: #78B1EB;
			margin: 10px 0 5px 10px;
		}
		#content p {
			margin: 10px 0 0 25px;
			font-size: .98em;
		}
		#content span#error {
			margin: 15px 0 0 25px;
			color: #9C0606;
			font-size: .9em;
		}
		form {
			margin: 20px 0 0 25px;
		}
		label {
			font-size: .9em;
			font-weight: bold;
		}
		input[type="submit"], button {
			padding: 3px 10px 3px 10px;
		}
		div.clear {
			clear: both;
		}
	</style>
</head>

<body>
	<div id="wrapper">
		<div id="header">
			<h3>LightBlog Updater</h3>
		</div>
		<div id="sidebar">
			<ul>
				<li class="<?php menuClass(1); ?>">Step 1: Update</li>
				<li class="<?php menuClass(2); ?>">Step 2: Finish</li>
			</ul>
			<div class="clear"></div>
		</div>
		<div id="content">
			<?php if(!isset($page) || $page == null): $disable = null; $page = null; ?>
				<h2>Updating your database</h2>
				<p>This updater <strong>only</strong> works for LightBlog sites being upgraded from version<br />
				   0.9.3 to 0.9.4. You will need to update your LightBlog site's database <br />
				   before you can continue to use your site. Click the Update button below to <br />
				   start the process. Please note that if you have a lot of content on your site<br />
				   it will take more time, so please be patient. DO NOT click the back button<br />
				   or close your browser before the update is complete.</p>
				 <p><strong>Note:</strong> This process can take a few minutes.</p>
				<form action="<?php echo basename($_SERVER['SCRIPT_FILENAME']); ?>" method="post">
					<div>
						<input type="submit" name="update" value="Update Database" onclick="setTimeout('this.disabled=true', 250);" />
					</div>
				</form>
				<br /><span id="error"><?php if(!isset($error)){$error=null;}echo $error; ?></span>
			<?php endif; if($page == 'finish'): ?>
				<h2>You're done!</h2>
				<p>Click the Continue button to go on to your updated blog! :)</p>
				<p>A backup copy of your previous database was kept and may be deleted (containing &quot;backup-&quot; in the name) if everything was properly copied.</p>
				<button onclick="window.location='<?php echo baseurl(); ?>'">Continue</button>
			<?php endif; ?>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
</body>
</html>
