<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    install.php

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

// This should be set to false when it is being distributed.
define('INDEVMODE', true);

// An array of files which the installer requires to operate.
$required_files = array(
    'Sources/FunctionReplacements.php', 'Sources/StringFunctions.php',
    'config-example.php', 'install.sql',
);

foreach($required_files as $filename)
{
    if(!file_exists($filename))
    {
        die('The file \''. $filename. '\' was not found and is required by the installer.');
    }
}

// Get some extra functions
require(dirname(__FILE__). '/Sources/FunctionReplacements.php');
require(dirname(__FILE__). '/Sources/StringFunctions.php');

// Adds trailing slash if needed
function endslash($path)
{
    if(substr($path, -1, 1) != DIRECTORY_SEPARATOR)
    {
        $path .= DIRECTORY_SEPARATOR;
    }

    return $path;
}

// Gets current directory URL
function baseurl()
{
    $site_url = explode('/', $_SERVER['SERVER_NAME']. $_SERVER['REQUEST_URI']);
    unset($site_url[count($site_url) - 1]);

    $site_url = implode('/', $site_url);
    $site_url = 'http://'. $site_url. '/';

    return $site_url;
}

$accordion_options = [null, null, null, null];

function set_accordion(&$accordion_options, $highest)
{
    for($i = count($accordion_options) - 1; $i >= 0; $i--)
    {
        if($i > $highest)
        {
            $accordion_options[$i] = 'disabled';
        }
        else if($i == $highest)
        {
            $accordion_options[$i] = 'checked';
        }
    }
}

function database_setup()
{
    $database_path = $_POST['database-path'];
    
    if(empty($_POST['database-path']))
    {
        return 'No database path given.';
    }

    // Create database path
    $database_abspath = endslash($database_path). randomString(rand(16, 32)). '.db';
    if(!is_dir($database_path))
    {
        if(!file_exists($database_path))
        {
            if(!@mkdir($database_path, 0760, true))
            {
                return 'Unable to create directory. Please create it manually, chmod it to 760, and try again.';
            }
        }
    }
    else
    {
        if(!is_writable($database_path))
        {
            return 'Database path is not writable. Please chmod it to 760 and try again.';
        }
    }

    // Open, read, and close SQL file
    if(is_readable('install.sql'))
    {
        $sqlh = fopen('install.sql', 'r');
        $sql = fread($sqlh, filesize('install.sql'));
        fclose($sqlh);
    }
    else
    {
        // Attempt to make it readable
        if(!@chmod('install.sql', 0644))
        {
            return 'Failed to open \'install.sql\'. Please <abbr title="change permissions">chmod it</abbr> to 644 and try again.';
        }
    }

    // Create, write to, and close database
    try
    {
        $dbh = new PDO('sqlite:' . $database_abspath);
    }
    catch(PDOException $e)
    {
        return 'Failed to create the database. Please <abbr title="change permissions">chmod</abbr> its directory to 760 and try again.';
    }

    if($dbh->exec($sql) === false)
    {
        $e = $dbh->errorInfo();
        return 'Failed to write to the database because: '.$e[2];
    }

    unset($dbh);

    // Open, read, and close example config file
    if(is_readable('config-example.php'))
    {
        $excfgh = fopen('config-example.php', 'r');
        $excfg = fread($excfgh, filesize('config-example.php'));
        fclose($excfgh);
    }
    else
    {
        if(!@chmod('config-example.php', 0644))
        {
            return 'Failed to open \'config-example.php\'. Please <abbr title="change permissions">chmod it</abbr> to 644 and try again.';
        }
        else
        {
            $excfgh = fopen('config-example.php', 'r');
            $excfg = fread($excfgh, filesize('config-example.php'));
            fclose($excfgh);
        }
    }

    // Prepare config file data
    $excfg = str_replace(array("absolute path to database here", 'name of login cookie'), array($database_abspath, 'lb'. mt_rand(100, 9999)), $excfg);

    // Now attempt to open config.php where we will store it.
    $cfgh = fopen('config.php', 'w');

    // But check to see if we couldn't open it by chance.
    if(empty($cfgh))
    {
        return 'Failed to create \'config.php\'. Please <abbr title="Change permissions">chmod the directory</abbr> to 644 and try again.';
    }

    flock($cfgh, LOCK_EX);
    fwrite($cfgh, $excfg);
    flock($cfgh, LOCK_UN);
    fclose($cfgh);
    
    return true;
}

function blog_setup()
{
    // Require config file
    // We need it to open the database
    require('config.php');

    // Match passwords
    if($_POST['password'] !== $_POST['password-repeat'])
    {
        return "Passwords don't match. Please try again.";
    }

    // Generate password hash
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Open database
    $dbh = new PDO('sqlite:'.DBH);
    $dbh->beginTransaction();

    $admin = $dbh->prepare("
        INSERT INTO users
        (
            user_name,
            user_pass,
            user_email,
            display_name,
            user_role,
            user_ip,
            user_activated,
            user_created
        )
        VALUES(
            :username,
            :pass,
            :email,
            :displayname,
            1,
            :ip_addr,
            1,
            :time
        )
    ");

    $admin->bindParam(":username", $_POST['username'], PDO::PARAM_STR);
    $admin->bindParam(":pass", $password_hash, PDO::PARAM_STR);
    $admin->bindParam(":email", $_POST['email'], PDO::PARAM_STR);
    $admin->bindParam(":displayname", $_POST['display-name'], PDO::PARAM_STR);
    $admin->bindParam(":ip_addr", $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
    $admin->bindValue(":time", time());

    $admin->execute();

    $settings = $dbh->prepare("
        INSERT INTO settings VALUES(:var, :val);
    ");

    $settings->bindValue(":var", "title");
    $settings->bindParam(":val", $_POST['blog-title'], PDO::PARAM_STR);

    $settings->execute();

    $settings->bindValue(":var", "url");
    $settings->bindParam(":val", $_POST['blog-url'], PDO::PARAM_STR);

    $settings->execute();

    if(!$dbh->commit())
    {
        $dbh->rollBack();
        return "Failed to commit changes.";
    }

    // Shut off database connection
    unset($dbh);
    
    return true;
}

if(isset($_POST['completed'])) 
{
    // But make sure we're not in development mode.
    if(!defined('INDEVMODE') || !INDEVMODE)
    {
        @unlink(__FILE__);
    }

    header('HTTP/1.1 307 Temporary Redirect');
    header('Location: '. baseurl());

    exit;
}
if(isset($_POST['user-ok']))
{
    if(true !== $return = blog_setup()) 
    {
        set_accordion($accordion_options, 2);
    }
    else
    {
        set_accordion($accordion_options, 3);
    }
}
else if(isset($_POST['database-ok']))
{
    if(true !== $return = database_setup()) 
    {
        set_accordion($accordion_options, 1);
    }
    else
    {
        set_accordion($accordion_options, 2);
    }
}
else if(isset($_POST['requirements-ok']))
{
    set_accordion($accordion_options, 1);
}
else 
{
    set_accordion($accordion_options, 0);
}

// Determine requirements
$requirements = [
    'php' => [
        'check' => version_compare(phpversion(), "5.4", ">="),
        'statuses' => ['Yes', 'Too old']
    ],
    'pdo_sqlite' => [
        'check' => extension_loaded('pdo_sqlite'),
        'statuses' => ['Enabled', 'Not enabled']
    ],
    'config' => [
        'check' => file_exists('./config.php') || is_writable('.'),
        'statuses' => ['Yes', 'No']
    ]
];

$requirements_met = true;
foreach($requirements as &$req)
{
    if($req['check'])
    {
        $req['html'] = '<td class="status-okay">' . $req['statuses'][0] . '</td>';
    }
    else {
        $req['html'] = '<td class="status-fail">' . $req['statuses'][1] . '</td>';
        $requirements_met = false;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Lightblog Installer</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="admin/assets/css/normalize.css" />
    <link rel="stylesheet" type="text/css" href="admin/assets/css/installer.css" />
</head>
<body>
    <div id="container">
        <header>
            <img src="admin/assets/images/logotype-min.svg" alt="Lightblog" />
            <h1>Installer</h1>
        </header>
        
        <main>
            <section id="requirements">
                <input class="accordion-input" id="accordion-requirements" name="accordion" type="radio" <?php echo $accordion_options[0] ?> />
                <label for="accordion-requirements">Step 1: Runtime Requirements</label>
                
                <div class="accordion-content">             
                    <p>Before we get started, the installer needs to determine if your server can run Lightblog properly.</p>
                      
                    <table>
                        <tr>
                            <td>PHP, 5.4+</td>
                            <?php echo $requirements['php']['html'] ?>
                        </tr>
                        
                        <tr>
                            <td>PHP extension: pdo_sqlite</td>
                            <?php echo $requirements['pdo_sqlite']['html'] ?>
                        </tr>
                        
                        <tr>
                            <td>Base directory is writable, or config.php exists</td>
                            <?php echo $requirements['config']['html'] ?>
                        </tr>
                    </table>
                    
                    
                    <?php if($requirements_met): ?>
                        <p>All requirements have been met.</p>
                        
                        <form method="post">
                            <input class="submit" name="requirements-ok" type="submit" value="Continue" />
                        </form>
                    <?php else: ?>
                        <p>The errors above must be corrected before you can continue.</p>
                    <?php endif; ?>
                </div>
            </section>
            
            <section id="database-setup">
                <input class="accordion-input" id="accordion-database-setup" name="accordion" type="radio" <?php echo $accordion_options[1] ?> />
                <label for="accordion-database-setup">Step 2: Database Configuration</label>
                <div class="accordion-content">
                    <p>
                        The installer will now try to setup your SQLite database. This
                        database will hold all of your blog's information, including
                        password hashes and other sensitive data.
                    </p>
                    <p>
                        The installer will create a database file with a randomly-generated
                        filename in the path that you select. We recommend you
                        place the database outside of your web root if possible, or
                        in a non-public-readable folder. If the path does not exist,
                        the installer will try to create it.
                    </p>
                    
                    <form method="post">
                        <fieldset>
                            <p>
                                <label for="database-driver">Database driver</label>
                                <select id="database-driver" name="database-driver" disabled>
                                    <option value="sqlite">SQLite</option>
                                </select>
                            </p>
                            
                            <p>
                                <label for="database-path">Database path</label>
                                <input type="text" name="database-path" id="database-path" value="<?php echo dirname(__FILE__) ?>" required />
                            </p>
                            
                            <p>
                                <input class="submit" name="database-ok" type="submit" value="Continue" />
                            </p>
                        </fieldset>
                    </form>
                </div>
            </section>
            
            <section id="user-setup">
                <input class="accordion-input" id="accordion-user-setup" name="accordion" type="radio" <?php echo $accordion_options[2] ?> />
                <label for="accordion-user-setup">Step 3: Configure Blog &amp; Admin User</label>
                <div class="accordion-content">
                    <form method="post" id="form-user-setup">                   
                        <fieldset class="float">
                            <h3>Admin User</h3>
                            
                            <p>
                                <label>Username</label>
                                <input type="text" id="username" name="username" required />
                            </p>
                            
                            <p>
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required />
                            </p>
                            
                            <p>
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" required />
                            </p>
                            
                            <p>
                                <label for="password-repeat">Repeat Password</label>
                                <input type="password" id="password-repeat" name="password-repeat" required />
                            </p>
                            
                            <p>
                                <label for="display-name">Display Name</label>
                                <input type="text" id="display-name" name="display-name" required />
                            </p>
                        </fieldset>
                        
                        <fieldset class="float">
                            <h3>Blog Options</h3>
                            
                            <p>
                                <label for="blog-title">Blog Title</label>
                                <input type="text" id="blog-title" name="blog-title" required />
                            </p>
                            
                            <p>
                                <label for="blog-url">Blog URL</label>
                                <input type="text" id="blog-url" name="blog-url" value="<?php echo baseurl() ?>" required />
                            </p>
                        </fieldset>
                        
                        <fieldset class="float" style="clear: both">
                            <input class="submit" name="user-ok" type="submit" value="Continue" />
                        </fieldset>
                    </form>
                </div>
            </section>
            
            <section id="completion">
                <input class="accordion-input" id="accordion-completion" name="accordion" type="radio" <?php echo $accordion_options[3] ?> />
                <label for="accordion-completion">Step 4: Install Complete</label>
                <div class="accordion-content">
                    <h2>You're done!</h2>
                    <p>Click the button below to see your new blog!</p>
                    
                    <form method="post">
                        <fieldset>
                            <input type="submit" name="completed" value="Take me to my blog" />
                        </fieldset>
                    </form>
                </div>
            </section>
        </main>
    </div>
    
    <script type="text/javascript">
        function validatePassword(e)
        {
            var password = e.target.form['password'];
            var passwordRepeat = e.target;
            if(password.value !== passwordRepeat.value)
            {
                passwordRepeat.setCustomValidity('Password does not match.');
            }
            else
            {
                passwordRepeat.setCustomValidity('');
            }
        }
        
        function disableAndWait(e)
        {
            var submit = e.target.querySelector("input[type=submit]");
            submit.value = "Please wait...";
            // Wait to disable the button so that it still gets submitted
            setTimeout(function(){ submit.disabled = true }, 100);
        }
    
        document.getElementById("password-repeat").addEventListener("input", validatePassword);
        for(var i = 0; i < document.forms.length; i++) 
            document.forms[i].addEventListener("submit", disableAndWait, false);
    </script>
</body>
</html>
