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

$timezones = array(
	-12.0 => '(GMT -12:00) Eniwetok, Kwajalein',
	-11.0 => '(GMT -11:00) Midway Island, Samoa',
	-10.0 => '(GMT -10:00) Hawaii',
	-9.0 => '(GMT -9:00) Alaska',
	-8.0 => '(GMT -8:00) Pacific Time (US & Canada)',
	-7.0 => '(GMT -7:00) Mountain Time (US & Canada)',
	-6.0 => '(GMT -6:00) Central Time (US & Canada), Mexico City',
	-5.0 => '(GMT -5:00) Eastern Time (US & Canada), Bogota, Lima',
	-4.0 => '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz',
	-3.5 => '(GMT -3:30) Newfoundland',
	-3.0 => '(GMT -3:00) Brazil, Buenos Aires, Georgetown',
	-2.0 => '(GMT -2:00) Mid-Atlantic',
	-1.0 => '(GMT -1:00 hour) Azores, Cape Verde Islands',
	0.0 => '(GMT) Western Europe Time, London, Lisbon, Casablanca',
	1.0 => '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris',
	2.0 => '(GMT +2:00) Kaliningrad, South Africa',
	3.0 => '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg',
	3.5 => '(GMT +3:30) Tehran',
	4.0 => '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi',
	4.5 => '(GMT +4:30) Kabul',
	5.0 => '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent',
	5.5 => '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi',
	5.75 => '(GMT +5:45) Kathmandu',
	6.0 => '(GMT +6:00) Almaty, Dhaka, Colombo',
	7.0 => '(GMT +7:00) Bangkok, Hanoi, Jakarta',
	8.0 => '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong',
	9.0 => '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk',
	9.5 => '(GMT +9:30) Adelaide, Darwin',
	10.0 => '(GMT +10:00) Eastern Australia, Guam, Vladivostok',
	11.0 => '(GMT +11:00) Magadan, Solomon Islands, New Caledonia',
	12.0 => '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka'
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
					<form action="<?php bloginfo('url') ?>Sources/ProcessAJAX.php" method="post" id="settings">
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
								<input type="text" name="url" id="url" value="<?php bloginfo('url') ?>" />
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
									For information on how to format a custom date or time, refer to <a href="http://php.net/manual/en/function.date.php">the PHP date() documentation</a>.
								</p>
							</div>
							<div class="input">
								<p>
									<input type="radio" name="date" id="M-D-Y" value="F j, Y" <?php echo $date['F j, Y'] ?> />
									<label for="M-D-Y"><?php echo date('F j, Y') ?></label>
								</p>
								<p>
									<input type="radio" name="date" id="m-D-Y" value="m/j/Y" <?php echo $date['m/j/Y'] ?> />
									<label for="m-D-Y"><?php echo date('m/j/Y') ?></label>
								</p>
								<p>
									<input type="radio" name="date" id="Y-M-D" value="Y/m/j" <?php echo $date['Y/m/j'] ?> />
									<label for="Y-M-D"><?php echo date('Y/m/j') ?></label>
								</p>
								<p>
									<input type="radio" name="date" id="D-M-Y" value="j/m/Y" <?php echo $date['j/m/Y'] ?> />
									<label for="D-M-Y"><?php echo date('j/m/Y') ?></label>
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
									<label for="g:i-a"><?php echo date('g:i a') ?></label>
								</p>
								<p>
									<input type="radio" name="time" id="g:i-A" value="g:i A" <?php echo $time['g:i A'] ?> />
									<label for="g:i-A"><?php echo date('g:i A') ?></label>
								</p>
								<p>
									<input type="radio" name="time" id="H:i" value="H:i" <?php echo $time['H:i'] ?> />
									<label for="H:i"><?php echo date('H:i') ?></label>
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
						data: inputs.join('&'),
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
