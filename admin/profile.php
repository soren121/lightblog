<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/profile.php

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
			return '<span class="result error">Failed to update profile;<br />'.$response['response'].'</span>';
		}
		elseif($response['result'] == 'success')
		{
			return '<span class="result">Profile updated.</span>';
		}
		else
		{
			return '<span class="result error">Failed to update profile;<br />No response from form processor.</span>';
		}
	}
}

$head_response = formCallback(processForm($_POST));
if(isset($_POST['ajax'])) { die(json_encode(array('response' => $head_response))); }

if(permissions('EditOtherUsers') || (int)$_GET['id'] == user()->id())
{
	$userquery = $dbh->query("SELECT user_role FROM users WHERE user_id=".(int)$_GET['id']) or die(sqlite_error_string($dbh->lastError));
	$role_options = '';
	foreach(get_roles() as $role_id => $role)
	{
		$select = '';
		if((int)$_GET['id'] == user()->id() && $role_id == user()->role())
		{
			$select = 'selected="selected"';
		}
		elseif($userquery->fetchSingle() == $role_id)
		{
			$select = 'selected="selected"';
		}
		$role_options .= '<option value="'.$role_id.'" '.$select.'>'.$role.'</option>';
	}
}

$head_title = "Edit Profile";
$head_css = "settings.css";
if((int)$_GET['id'] != user()->id())
{
	$head_title = "Edit User";
	$selected = "users.php";
}

include('head.php');

?>

		<div id="contentwrapper">
			<div id="contentcolumn">
				<?php if(isset($_GET['id'])): if(permissions('EditOtherUsers') || (int)$_GET['id'] == user()->id()): ?>
					<form action="<?php bloginfo('url') ?>admin/profile.php" method="post" id="settings">
						<div class="setting">
							<div class="label">
								<label for="password">New Password</label>
							</div>
							<div class="input">
								<input type="password" name="password" id="password" />
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting">
							<div class="label">
								<label for="vpassword">Password (again)</label>
							</div>
							<div class="input">
								<input type="password" name="vpassword" id="vpassword" />
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting">
							<div class="label">
								<label for="email">Email Address</label>
							</div>
							<div class="input">
								<input type="text" name="email" id="email" value="<?php echo user()->email() ?>" />
							</div>
							<div class="clear"></div>
						</div>
						
						<div class="setting">
							<div class="label">
								<label for="displayname">Display Name</label>
							</div>
							<div class="input">
								<input type="text" name="displayname" id="displayname" value="<?php echo user()->displayName() ?>" />
							</div>
							<div class="clear"></div>
						</div>

						<?php if(permissions('EditRoles')): ?>
							<div class="setting">
								<div class="label">
									<label for="role">Role</label>
								</div>
								<div class="input">
									<select name="role" id="role">
										<?php echo $role_options ?>
									</select>
								</div>
								<div class="clear"></div>
							</div>
						<?php endif; ?>
						
						<div class="setting">
							<div class="label">
								<label for="cpassword">Current Password</label>
								<p>Required for security purposes.</p>
							</div>
							<div class="input">
								<input type="password" name="cpassword" id="cpassword" />
							</div>
							<div class="clear"></div>
						</div>			

						<div class="setting">
							<input type="hidden" name="uid" value="<?php echo (int)$_GET['id'] ?>" />
							<input type="hidden" name="csrf_token" value="<?php echo user()->csrf_token() ?>" />
							<input type="hidden" name="form" value="Profile" />
							<input type="submit" class="submit" name="editprofile" value="Save" />
							<div class="clear"></div>
						</div>
					</form>
				<?php endif; endif; ?>
			</div>
		</div>

		<script type="text/javascript">
		//<![CDATA[
			$('div.setting:odd').addClass('even');
		
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
						data: 'ajax=true&' + inputs.join('&'),
						type: "POST",
						url: $(this).attr('action'),
						timeout: 2000,
						error: function() {
							$('#ajaxresponse').html('<span class="result">Failed to save settings;<br />(jQuery failure).</span>');
						},
						dataType: 'json',
						success: function(r) {
							$('#ajaxresponse').html(r.response);
						}
					})
					return false;
				})
			});
		//]]>
		</script>

<?php include('footer.php') ?>
