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

$title = "Add User";
$css = "settings.css";
$selected = basename($_SERVER['REQUEST_URI']);

include('head.php');

?>

		<div id="contentwrapper">
			<div id="contentcolumn">
				<?php if(permissions(3)): ?>
					<form action="<?php bloginfo('url') ?>Sources/ProcessAJAX.php" method="post" id="adduser">
						<div class="setting">
							<div class="label">
								<label for="username">Username</label>
							</div>
							<div class="input">
								<input type="text" name="username" id="username" value="" autocomplete="off" />
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting even">
							<div class="label">
								<label for="password">Password</label>
							</div>
							<div class="input">
								<input type="password" name="password" id="password" value="" />
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting">
							<div class="label">
								<label for="vpassword">Password (again)</label>
							</div>
							<div class="input">
								<input type="password" name="vpassword" id="vpassword" value="" />
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting even">
							<div class="label">
								<label for="email">Email</label>
							</div>
							<div class="input">
								<input type="text" name="email" id="email" value="" />
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting">
							<div class="label">
								<label for="displayname">Display Name</label>
							</div>
							<div class="input">
								<input type="text" name="displayname" id="displayname" value="" />
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting even">
							<div class="label">
								<label for="role">Role</label>
							</div>
							<div class="input">
								<select name="role" id="role">
									<option value="1">Standard User</option>
									<option value="2">Moderator</option>
									<option value="3">Administrator</option>
								</select>
							</div>
							<div class="clear"></div>
						</div>

						<div class="setting">
							<input type="hidden" name="csrf_token" value="<?php echo user()->csrf_token() ?>" />
							<input type="submit" class="submit" name="addusersubmit" value="Add User" />
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
							$('#ajaxresponse').html('<span class="result" style="color: red; border-color: red;">Failed to create user;<br />(jQuery failure).</span>');
						},
						dataType: 'json',
						success: function(r) {
							if(r.result == 'success') {
								$('#ajaxresponse').html('<span class="result">' + r.response + '</span>');

								$('#username').val('');
								$('#password').val('');
								$('#vpassword').val('');
								$('#email').val('');
								$('#displayname').val('');
								$('#role').val(1);
							}
							else {
								$('#ajaxresponse').html('<span class="result" style="color: red; border-color: red;">Failed to create user;<br />' + r.response + '</span>').css({color:"red"});
							}
						}
					})
					return false;
				})
			});
		//]]>
		</script>

<?php include('footer.php') ?>