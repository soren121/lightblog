<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/settings.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

// Require config file
require('../Sources/Core.php');
require(ABSPATH .'/Sources/Admin.php');
require(ABSPATH .'/Sources/Process.php');

function formCallback($response)
{
	if(!empty($response))
	{
		if($response['result'] == 'error')
		{
			return '<span class="result error">'. l('Failed to save settings'). ';<br />'. $response['response']. '</span>';
		}
		elseif($response['result'] == 'success')
		{
			return '<span class="result">'. l('Settings saved.'). '</span>';
		}
		else
		{
			return '<span class="result error">'. l('Failed to save settings'). ';<br />'. l('No response from form processor.'). '</span>';
		}
	}
}

$head_response = formCallback(processForm($_POST));
if(isset($_POST['ajax']))
{
	die(json_encode(array(
										'response' => $head_response
									)));
}

// Now prepare what we're going to display.
$timezones = array(
	-12.0 => '(UTC -12:00) Eniwetok, Kwajalein',
	-11.0 => '(UTC -11:00) Midway Island, Samoa',
	-10.0 => '(UTC -10:00) Hawaii',
	-9.0 => '(UTC -9:00) Alaska',
	-8.0 => '(UTC -8:00) Pacific Time (US & Canada)',
	-7.0 => '(UTC -7:00) Mountain Time (US & Canada)',
	-6.0 => '(UTC -6:00) Central Time (US & Canada), Mexico City',
	-5.0 => '(UTC -5:00) Eastern Time (US & Canada), Bogota, Lima',
	-4.0 => '(UTC -4:00) Atlantic Time (Canada), Caracas, La Paz',
	-3.5 => '(UTC -3:30) Newfoundland',
	-3.0 => '(UTC -3:00) Brazil, Buenos Aires, Georgetown',
	-2.0 => '(UTC -2:00) Mid-Atlantic',
	-1.0 => '(UTC -1:00 hour) Azores, Cape Verde Islands',
	0.0 => '(UTC) Western Europe Time, London, Lisbon, Casablanca',
	1.0 => '(UTC +1:00 hour) Brussels, Copenhagen, Madrid, Paris',
	2.0 => '(UTC +2:00) Kaliningrad, South Africa',
	3.0 => '(UTC +3:00) Baghdad, Riyadh, Moscow, St. Petersburg',
	3.5 => '(UTC +3:30) Tehran',
	4.0 => '(UTC +4:00) Abu Dhabi, Muscat, Baku, Tbilisi',
	4.5 => '(UTC +4:30) Kabul',
	5.0 => '(UTC +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent',
	5.5 => '(UTC +5:30) Bombay, Calcutta, Madras, New Delhi',
	5.75 => '(UTC +5:45) Kathmandu',
	6.0 => '(UTC +6:00) Almaty, Dhaka, Colombo',
	7.0 => '(UTC +7:00) Bangkok, Hanoi, Jakarta',
	8.0 => '(UTC +8:00) Beijing, Perth, Singapore, Hong Kong',
	9.0 => '(UTC +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk',
	9.5 => '(UTC +9:30) Adelaide, Darwin',
	10.0 => '(UTC +10:00) Eastern Australia, Guam, Vladivostok',
	11.0 => '(UTC +11:00) Magadan, Solomon Islands, New Caledonia',
	12.0 => '(UTC +12:00) Auckland, Wellington, Fiji, Kamchatka'
);

$timezone_options = '';
$selected_timezone = get_bloginfo('timezone');
foreach($timezones as $offset => $name)
{
	if($selected_timezone == $offset)
	{
		$timezone_options .= '<option value="'.$offset.'" selected="selected">'. $name. '</option>';
	}
	else
	{
		$timezone_options .= '<option value="'.$offset.'">'. $name. '</option>';
	}
}

$date = array('F j, Y' => '', 'm/j/Y' => '', 'Y/m/j' => '', 'j/m/Y' => '', 'custom' => '');
$db_date = get_bloginfo('date_format');
if(!empty($db_date) && array_key_exists($db_date, $date))
{
	$date[$db_date] = 'checked="checked"';
	$date['custom_field'] = key($date);
}
else
{
	$date['custom'] = 'checked="checked"';
	$date['custom_field'] = $db_date;
}

$time = array('g:i a' => '', 'g:i A' => '', 'H:i' => '', 'custom' => '');
$db_time = get_bloginfo('time_format');
if(!empty($db_time) && array_key_exists($db_time, $time))
{
	$time[$db_time] = 'checked="checked"';
	$time['custom_field'] = key($time);
}
else
{
	$time['custom'] = 'checked="checked"';
	$time['custom_field'] = $db_time;
}

$head_title = l('General Settings');
$head_css = "settings.css";

include('head.php');

?>
		<div id="contentwrapper">
			<div id="contentcolumn">
				<?php if(permissions('EditSettings')): ?>
					<form action="<?php bloginfo('url') ?>admin/settings.php" method="post" id="settings">
						<div class="setting">
							<div class="label">
								<label for="title"><?php echo l('Blog Title'); ?></label>
							</div>
							<div class="input">
								<input type="text" name="title" id="title" value="<?php bloginfo('title') ?>" />
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting even">
							<div class="label">
								<label for="url"><?php echo l('LightBlog Address (URL)'); ?></label>
							</div>
							<div class="input">
								<input type="text" name="url" id="url" value="<?php echo utf_htmlspecialchars(get_bloginfo('url')); ?>" />
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting">
							<div class="label">
								<label for="timezone"><?php echo l('Time Zone'); ?></label>
							</div>
							<div class="input">
								<select name="timezone" id="timezone">
									<?php echo $timezone_options ?>
								</select>
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting even">
							<div class="label">
								<label><?php echo l('Date Format'); ?></label>
								<p>
									<?php echo l('For information on how to format a custom date or time, refer to <a href="http://php.net/manual/en/function.date.php" target="_blank">the PHP date() documentation</a>.'); ?>
								</p>
							</div>
							<div class="input">
								<p>
									<input type="radio" name="date" id="M-D-Y" value="F j, Y" <?php echo $date['F j, Y'] ?> />
									<label for="M-D-Y" title="Format: F j, Y"><?php echo date('F j, Y') ?></label>
								</p>
								<p>
									<input type="radio" name="date" id="m-D-Y" value="m/j/Y" <?php echo $date['m/j/Y'] ?> />
									<label for="m-D-Y" title="Format: m/j/Y"><?php echo date('m/j/Y') ?></label>
								</p>
								<p>
									<input type="radio" name="date" id="Y-M-D" value="Y/m/j" <?php echo $date['Y/m/j'] ?> />
									<label for="Y-M-D" title="Format: Y/m/j"><?php echo date('Y/m/j') ?></label>
								</p>
								<p>
									<input type="radio" name="date" id="D-M-Y" value="j/m/Y" <?php echo $date['j/m/Y'] ?> />
									<label for="D-M-Y" title="Format: j/m/Y"><?php echo date('j/m/Y') ?></label>
								</p>
								<p>
									<input type="radio" name="date" id="custom-date" value="custom" <?php echo $date['custom'] ?> />
									<label for="custom-date"><?php echo l('Custom:');?> </label>
									<input type="text" name="custom_date" id="custom-date-field" value="<?php echo $date['custom_field'] ?>" />
								</p>
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting">
							<div class="label">
								<label><?php echo l('Time Format'); ?></label>
							</div>
							<div class="input">
								<p>
									<input type="radio" name="time" id="g:i-a" value="g:i a" <?php echo $time['g:i a'] ?> />
									<label for="g:i-a" title="Format: g:i a"><?php echo date('g:i a') ?></label>
								</p>
								<p>
									<input type="radio" name="time" id="g:i-A" value="g:i A" <?php echo $time['g:i A'] ?> />
									<label for="g:i-A" title="Format: g:i A"><?php echo date('g:i A') ?></label>
								</p>
								<p>
									<input type="radio" name="time" id="H:i" value="H:i" <?php echo $time['H:i'] ?> />
									<label for="H:i" title="Format: H:i"><?php echo date('H:i') ?></label>
								</p>
								<p>
									<input type="radio" name="time" id="custom-time" value="custom" <?php echo $time['custom'] ?> />
									<label for="custom-time"><?php echo l('Custom:');?> </label>
									<input type="text" name="custom_time" id="custom-time-field" value="<?php echo $time['custom_field'] ?>" />
								</p>
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting even">
							<input type="hidden" name="csrf_token" value="<?php echo user()->csrf_token() ?>" />
							<input type="hidden" name="form" value="Settings" />
							<input type="submit" class="submit" name="changesettings" value="<?php echo l('Save'); ?>" />
							<div class="clear"></div>
						</div>
					</form>
				<?php endif; ?>
			</div>
		</div>

		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/Form.js"></script>

<?php include('footer.php') ?>
