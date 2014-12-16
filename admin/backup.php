<?php
/*********************************************

    LightBlog 0.9
    SQLite blogging platform

    admin/backup.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

*********************************************/

// Require config file
require('../Sources/Core.php');
require(ABSPATH .'/Sources/Admin.php');

if(!empty($_REQUEST['backup']) && permissions('EditSettings'))
{
    if(ob_get_length() > 0)
    {
        ob_clean();
        ob_start(function_exists('ob_gzhandler') ? 'ob_gzhandler' : null);
    }

    header('Content-Disposition: attachment; filename="'. basename(ABSPATH). '-backup-'. date('m-d-Y'). '.sql"');

    echo '----', "\r\n";
    echo '-- ', l('Backup generated by LightBlog on %s', date('m-d-Y g:i:sA')), "\r\n";
    echo '----', "\r\n\r\n";

    // We need to get a list of everything in the database (the tables,
    // indexes, etc.)
    $request = $dbh->query('
        SELECT
            type, name, tbl_name, sql
        FROM sqlite_master
        ORDER BY tbl_name ASC, type DESC');

    $structure = array();
    $views = array();
    while($row = $request->fetch(SQLITE_ASSOC))
    {
        // All views at the end, just to be safe.
        if($row['type'] == 'view')
        {
            $views[] = $row;
        }
        else
        {
            $structure[$row['tbl_name']][] = $row;
        }
    }

    // We will need lots of time to do this!
    @set_time_limit(0);

    // We group the tables and indexes by table name so we can output the
    // structure correctly.
    foreach($structure as $tbl_name => $items)
    {
        // Output the structure first (table first, then any possible indexes).
        foreach($items as $item)
        {
            echo $item['sql']. ';', "\r\n". ($item['tbl_name'] == $item['name'] ? "\r\n" : '');
        }

        // Now for the content... Though make sure it is a table (and not a view).
        if(isset($items[0]['type']) && $items[0]['type'] == 'table')
        {
            echo "\r\n";

            $request = $dbh->query('
                SELECT
                    *
                FROM \''. sqlite_escape_string($tbl_name). '\'');

            while($row = $request->fetch(SQLITE_ASSOC))
            {
                echo 'INSERT INTO \'', sqlite_escape_string($tbl_name), '\' (\'', implode('\', \'', array_keys($row)), '\') VALUES(', implode(', ', backup_row($row)), ');', "\r\n";
            }

            echo "\r\n";
        }
    }

    // Now for the views.
    foreach($views as $item)
    {
        echo $item['sql']. ";\r\n\r\n";
    }

    exit;
}

function backup_row($row)
{
    $data = array();
    foreach($row as $value)
    {
        if((string)$value == (string)(int)$value)
        {
            $data[] = (int)$value;
        }
        elseif((string)$value == (string)(float)$value)
        {
            $data[] = (float)$value;
        }
        else
        {
            $data[] = '\''. sqlite_escape_string($value). '\'';
        }
    }

    return $data;
}

if(!empty($_POST['optimize']) && permissions('EditSettings'))
{
    // What size is the database right now?
    $filesize = filesize(DBH);

    // Now VACUUM.
    $dbh->query('
        VACUUM');

    $new_filesize = filesize(DBH);
    $improvement = round((($filesize - $new_filesize) / $filesize) * 100, 2);
}

$head_title = "Backup &amp; Optimize";
$head_css = "settings.css";

include('head.php');

?>
        <div id="contentwrapper">
            <div id="contentcolumn">
                <?php if(permissions('EditSettings')): ?>
                    <h3><?php echo l('Backup Database'); ?></h3>
                    <p><?php echo l('It is recommended that the database be backed up periodically in case disaster should strike. In order to restore a database from a backup a tool such as <a href="http://phpliteadmin.googlecode.com/" target="_blank">phpLiteAdmin</a> must be utilized.'); ?></p>
                    <form action="<?php bloginfo('url') ?>admin/backup.php" method="post" id="settings">
                        <div class="setting">
                            <input type="hidden" name="csrf_token" value="<?php echo user()->csrf_token() ?>" />
                            <input type="submit" class="submit" name="backup" value="<?php echo l('Backup'); ?>" />
                            <div class="clear"></div>
                        </div>
                    </form>
                <?php endif; ?>
                <?php if(permissions('EditSettings')): ?>
                    <h3><?php echo l('Optimize'); ?></h3>
                    <p><?php echo l('Optimizing the database periodically can reduce the size of the database itself along with making the website faster.'); ?></p>
                <?php
                if(isset($improvement))
                {
                    echo '
                    <p style="text-align: center; color: green; margin: 5px 0;">', l('Database Optimized (size decreased by %s%%)', $improvement), '</p>';
                }
                ?>
                    <form action="<?php bloginfo('url') ?>admin/backup.php" method="post" id="settings">
                        <div class="setting">
                            <input type="hidden" name="csrf_token" value="<?php echo user()->csrf_token() ?>" />
                            <input type="submit" class="submit" name="optimize" value="<?php echo l('Optimize'); ?>" />
                            <div class="clear"></div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

<?php include('footer.php') ?>
