<?php
/***********************************************

    LightBlog 0.9
    SQLite blogging platform

    admin/appearance.php

    ©2008-2014 The LightBlog Team. All
    rights reserved. Released under the
    GNU General Public License 3. For
    all licensing information, please
    see the LICENSE.txt document
    included in this distribution.

***********************************************/

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

    $themes .= '<span>'.$xml->title.'</span>';

    $themes .= '</div>';
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
