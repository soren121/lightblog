<?php session_start();define("Light", true);require('config.php');require('admin/corefunctions.php');
$result06 = sqlite_query($handle,"SELECT title, id, post, date FROM posts ORDER BY id desc LIMIT 15") or die("SQLite query error: code 06<br>".sqlite_error_string(sqlite_last_error($handle)));
// LightBlog 0.9
// Written by soren121 <soren121@northsalemcrew.net>
// Released under the GNU GPL v3
// http://tcn.110mb.com/

while ($line = sqlite_fetch_array($result06, SQLITE_ASSOC))
        {
            $return[] = $line;
        }

$now = date("D, d M Y H:i:s O");

$output = "<?xml version=\"1.0\"?>
            <rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">
                <channel>
                    <title>$site_name</title>
                    <link>".$site_url."rss.php</link>
                    <description>RSS Feed for $site_name</description>
                    <language>en-us</language>
                    <lastBuildDate>$now</lastBuildDate>
                    <docs>http://cyber.law.harvard.edu/rss/rss.html</docs>
                    <generator>LightBlog 0.9</generator>
                    <atom:link href=\"".$site_url."rss.php\" rel=\"self\" type=\"application/rss+xml\" />
            ";
            
foreach ($return as $line)
{
	date_default_timezone_set('UTC');
    $output .= "<item><title>".htmlentities($line['title'])."</title>
                    <link>".$site_url."post.php?id=".htmlentities($line['id'])."</link>
                    <pubDate>".date("D, d M Y H:i:s O", htmlentities($line['date']))."</pubDate>
                    <description>".htmlentities(strip_tags($line['post']))."</description>
                    <guid isPermaLink=\"true\">".$site_url."post.php?id=".htmlentities($line['id'])."</guid>
                    <comments>".$site_url."post.php?id=".htmlentities($line['id'])."</comments>
                </item>";
}
$output .= "</channel></rss>";
header("Content-Type: application/rss+xml");
echo $output;
?>
