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

// Time to process any incoming requests to save the settings.
if(!empty($_POST['changesettings']))
{
	// We will collect the response here.
	$response = array(
								'result' => 'error',
								'response' => array(),
							);

	// Make sure they are the one submitting the request.
	if(empty($_POST['csrf_token']) || $_POST['csrf_token'] != user()->csrf_token())
	{
		$response['response'][] = 'CSRF token incorrect or missing.';
	}
	else
	{
		$options = array();

		// A title is required.
		if(empty($_POST['title']) || utf_strlen($_POST['title']) == 0)
		{
			$response['response'][] = 'A blog title is required.';
		}
		else
		{
			$options['title'] = utf_htmlspecialchars($_POST['title']);
		}

		// Same goes for the URL. It also needs to be valid.
		if(empty($_POST['url']) || !is_url($_POST['url']))
		{
			$response['response'][] = 'A valid URL is required.';
		}
		else
		{
			// We also want it to have a trailing slash.
			$options['url'] = utf_substr($_POST['url'], -1, 1) == '/' ? $_POST['url'] : $_POST['url']. '/';
		}

		// Make sure the time zone is valid.
		if(!array_key_exists('timezone', $_POST) || (float)$_POST['timezone'] < -12 || (float)$_POST['timezone'] > 12)
		{
			$response['response'][] = 'Invalid time zone selected.';
		}
		else
		{
			$options['timezone'] = (float)$_POST['timezone'];
		}

		// Now for the date...
		if(empty($_POST['date']) || ($_POST['date'] == 'custom' && empty($_POST['custom_date'])))
		{
			$response['response'][] = 'Invalid date format.';
		}
		else
		{
			$options['date_format'] = utf_htmlspecialchars($_POST['date'] == 'custom' ? $_POST['custom_date'] : $_POST['date']);
		}

		// Then time formatting.
		if(empty($_POST['time']) || ($_POST['time'] == 'custom' && empty($_POST['custom_time'])))
		{
			$response['response'][] = 'Invalid time format.';
		}
		else
		{
			$options['time_format'] = utf_htmlspecialchars($_POST['time'] == 'custom' ? $_POST['custom_time'] : $_POST['time']);
		}

		// Were there any issues?
		if(count($response['response']) == 0)
		{
			// Nope, so we can save the settings.
			$save_query = array();
			foreach($options as $option => $value)
			{
				$GLOBALS['bloginfo_data'][$option] = $value;

				if(is_string($value))
				{
					$value = sqlite_escape_string($value);
				}

				$save_query[] = 'INSERT OR REPLACE INTO core (variable, value) VALUES(\''. sqlite_escape_string($option). '\', '. (is_string($value) ? '\''. $value. '\'' : $value). ');';
			}

			if($dbh->queryExec(implode("\r\n", $save_query)))
			{
				$response['result'] = 'success';
				$response['response'] = 'Settings saved.';
			}
			else
			{
				$response['response'][] = $error_message;
			}
		}
	}

	// Perhaps they made the request via AJAX?
	if(!empty($_POST['response_type']) && $_POST['response_type'] == 'json')
	{
		// Combine all the messages into one.
		$response['response'] = implode("\r\n", $response['response']);

		echo json_encode($response);
		exit;
	}
	else
	{
		$ajaxresponse_message = $response['response'];
	}
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
		$timezone_options .= '<option value="'.$offset.'" selected="selected">'.$name.'</option>';
	}
	else
	{
		$timezone_options .= '<option value="'.$offset.'">'.$name.'</option>';
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

$title = "General Settings";
$css = "settings.css";
$selected = basename($_SERVER['REQUEST_URI']);

include('head.php');

?>
		<div id="contentwrapper">
			<div id="contentcolumn">
				<?php if(permissions(3)): ?>
					<form action="<?php bloginfo('url') ?>admin/settings.php" method="post" id="settings">
						<div class="setting">
							<div class="label">
								<label for="title">Blog Title</label>
							</div>
							<div class="input">
								<input type="text" name="title" id="title" value="<?php bloginfo('title') ?>" />
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting even">
							<div class="label">
								<label for="url">LightBlog Address (URL)</label>
							</div>
							<div class="input">
								<input type="text" name="url" id="url" value="<?php echo utf_htmlspecialchars(get_bloginfo('url')); ?>" />
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting">
							<div class="label">
								<label for="timezone">Time Zone</label>
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
								<label>Date Format</label>
								<p>
									For information on how to format a custom date or time, refer to <a href="http://php.net/manual/en/function.date.php" target="_blank">the PHP date() documentation</a>.
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
									<label for="custom-date">Custom: </label>
									<input type="text" name="custom_date" id="custom-date-field" value="<?php echo $date['custom_field'] ?>" />
								</p>
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting">
							<div class="label">
								<label>Time Format</label>
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
									<label for="custom-time">Custom: </label>
									<input type="text" name="custom_time" id="custom-time-field" value="<?php echo $time['custom_field'] ?>" />
								</p>
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting even">
							<input type="hidden" name="csrf_token" value="<?php echo user()->csrf_token() ?>" />
							<input type="submit" class="submit" name="changesettings" value="Save" />
							<div class="clear"></div>
						</div>
					</form>
				<?php endif; ?>
			</div>
		</div>

		<script type="text/javascript">
		//<![CDATA[
			$(function() {
				$('form').submit(function() {
					$('#ajaxresponse').html('<img src="style/new/loading.gif" alt="Saving" />');
					var inputs = [];
					$(':input', this).each(function() {
						if($(this).is(':checkbox, :radio') && $(this).is(':not(:checked)')) {
							void(0);
						}
						else {
							inputs.push(this.name + '=' + this.value);
						}
					});

					jQuery.ajax({
						data: inputs.join('&') + '&response_type=json',
						type: "POST",
						url: $(this).attr('action'),
						timeout: 2000,
						error: function() {
							$('#ajaxresponse').html('<span class="result">Failed to save settings;<br />(jQuery failure).</span>');
						},
						dataType: 'json',
						success: function(r) {
							if(r.result == 'success') {
								$('#ajaxresponse').html('<span class="result">Settings saved.</span>');
							}
							else {
								$('#ajaxresponse').html('<span class="result">Failed to save settings;<br />' + r.response + '</span>').css("color","#E36868");
							}
						}
					})
					return false;
				})
			});
		//]]>
		</script>

<?php include('footer.php') ?>
