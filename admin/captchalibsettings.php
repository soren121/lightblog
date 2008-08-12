<?php
session_start();

require_once('captchalib.php');

/*
*****CONFIGURATION STARTS*****
*/
//Background Image
$config['BackgroundImage'] = "includes/white.png";

//Background Color- HEX
$config['BackgroundColor'] = "173D7F";

//image height - same as background image
$config['Height']=30;

//image width - same as background image
$config['Width']=100;

//text font size
$config['Font_Size']=24;

//text font style
$config['Font']="includes/captchafont.ttf";

//text angle to the left
$config['TextMinimumAngle']=15;

//text angle to the right
$config['TextMaximumAngle']=45;

//Text Color - HEX
$config['TextColor']='FFFFFF';

//Number of Captcha Code Character
$config['TextLength']=6;

//Background Image Transparency
$config['Transparency']=50;

/*
*******CONFIGURATION ENDS******
*/



//Create a new instance of the captcha
$captcha = new SimpleCaptcha($config);

//Save the code as a session dependent string
$_SESSION['string'] = $captcha->Code;


?>