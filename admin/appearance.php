<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    admin/appearance.php

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

// Require config file
require('../Sources/Core.php');
require(ABSPATH .'/Sources/Admin.php');
require(ABSPATH .'/Sources/Process.php');

// $response = processForm($_POST);
// if(isset($_POST['ajax'])) { die(json_encode($response)); }
//
// $response = processForm(array('form' => 'Appearance', 'csrf_token' => user()->csrf_token()));
//
// if(isset($response['response']))
// {
//     $response = $response['response'];
// }

$theme_array = glob(ABSPATH .'/themes/*/theme.xml');
$themes = '';

foreach($theme_array as $theme_xml)
{
    $xml = simplexml_load_file($theme_xml);

    $themes .= '<div class="theme">';

    if(property_exists($xml, 'screenshot'))
    {
        $theme_directory = get_bloginfo('url').'themes/'.basename(dirname($theme_xml)).'/';
        $themes .= '<img src="'. $theme_directory . $xml->screenshot .'" alt="'. $xml->title .'" />';
    }
    else
    {
        $themes .= '<div class="no-screenshot">No image</div>';
    }

    $themes .= '<div class="theme-info"><p class="theme-headline"><span class="theme-title">'.$xml->title.'</span>';

    if(property_exists($xml, 'version'))
    {
        $themes .= '<span class="theme-version"> '.$xml->version.'</span>';
    }

    $themes .= '</p>';

    if(property_exists($xml, 'author'))
    {
        $themes .= '<p class="theme-author">Author: ';

        if(property_exists($xml, 'authoruri'))
        {
            $themes .= '<a href="'.$xml->authoruri.'">'.$xml->author.'</a>';
        }
        else
        {
            $themes .= $xml->author;
        }

        $themes .= '</span>';
    }

    if(property_exists($xml, 'license'))
    {
        $themes .= '<p class="theme-license">License: ';

        if(property_exists($xml, 'licenseuri'))
        {
            $themes .= '<a href="'.$xml->licenseuri.'">'.$xml->license.'</a>';
        }
        else
        {
            $themes .= $xml->license;
        }

        $themes .= '</span>';
    }

    $themes .= '</div></div>';
}

$head_title = l('Appearance');
$head_css = "appearance.css";

include('head.php');

?>

        <div id="contentwrapper">
            <div id="contentcolumn">
                <?php if(permissions('EditSettings')): ?>
                    <form action="<?php bloginfo('url') ?>admin/appearance.php" method="post" id="theme-gallery">
                        <div class="theme-options">
                            <span class="total">Showing <?php echo count($theme_array) ?> theme(s).</span>
                            <input type="submit" class="submit" value="<?php echo l('Save'); ?>">
                        </div>

                        <?php echo $themes ?>

                        <div class="clear"></div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

<?php include('footer.php') ?>
