<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
?>
<div id="login-page">
	<h2>
		<?php echo translate('Request a new password') ?>
	</h2>

	<div id="login-box">
		<div id="new_passwd">
			<form id="new_passwd_form" name="new_passwd_form" action="?route=password-reset-action" method="post">
				<?php echo $csrf_field ?>
				<div>
					<label>
						<?php echo translate('Username or email address') ?>
						<input type="text" name="username">
					</label>
				</div>
				<div>
					<button type="submit">
						<?php echo /* I18N: A button label. */ translate('continue') ?>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
