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
<div id="login-register-page">
	<h2>
		<?php echo translate('Request a new user account') ?>
	</h2>

	<?php if ($show_register_caution): ?>
		<div id="register-text">
			<?php echo translate('<div class="largeError">Notice:</div><div class="error">By completing and submitting this form, you agree:<ul><li>to protect the privacy of living individuals listed on our site;</li><li>and in the text box below, to explain to whom you are related, or to provide us with information on someone who should be listed on our website.</li></ul></div>'); ?>
		</div>
	<?php endif; ?>
	<div id="register-box">
		<form action="<?php echo url('registration-action') ?>" id="register-form" name="register-form" method="post" onsubmit="return checkform(this);" autocomplete="off">
			<?php echo $csrf_field ?>
			<input type="hidden" name="action" value="register">
			<h4><?php echo translate('All fields must be completed.'); ?></h4>
			<hr>

			<div>
				<label>
					<?php echo translate('Real name'); ?>
					<input type="text" name="real_name" required maxlength="64" value="<?php echo escape($real_name); ?>" autofocus>
				</label>
				<p class="small text-muted">
					<?php echo translate('This is your real name, as you would like it displayed on screen.'); ?>
				</p>
			</div>

			<div>
				<label>
					<?php echo translate('Email address'); ?>
					<input type="email" name="email" required maxlength="64" value="<?php echo escape($email); ?>">
				</label>
				<p class="small text-muted">
					<?php echo translate('This email address will be used to send password reminders, website notifications, and messages from other family members who are registered on the website.'); ?>
				</p>
			</div>

			<div>
				<label>
					<?php echo translate('Username'); ?>
					<input type="text" name="username" required maxlength="32" value="<?php echo escape($username); ?>">
				</label>

				<p class="small text-muted">
					<?php echo translate('Usernames are case-insensitive and ignore accented letters, so that “chloe”, “chloë”, and “Chloe” are considered to be the same.'); ?>
				</p>
			</div>

			<div>
				<label>
					<?php echo translate('Password'); ?>
					<input required type="password" id="password1" name="password1" placeholder="<?php echo /* I18N: placeholder text for new-password field */ plural('Use at least %s character.', 'Use at least %s characters.', $password_length, number($password_length)); ?>" pattern="<?php echo $password_regex; ?>" onchange="form.user_password2.pattern = regex_quote(this.value);">
				</label>
				<p class="small text-muted">
					<?php echo translate('Passwords must be at least 6 characters long and are case-sensitive, so that “secret” is different from “SECRET”.'); ?>
				</p>
			</div>

			<div>
				<label>
					<?php echo translate('Confirm password'); ?>
					<input required type="password" id="password2" name="password2" placeholder="<?php echo /* I18N: placeholder text for repeat-password field */ translate('Type the password again.'); ?>" pattern="<?php echo $password_regex; ?>">
				</label>
				<p class="small text-muted">
					<?php echo translate('Type your password again, to make sure you have typed it correctly.'); ?>
				</p>
			</div>

			<div>
				<label>
					<?php echo translate('Comments'); ?>
					<textarea required cols="50" rows="5" name="comments" placeholder="<?php /* I18N: placeholder text for registration-comments field */ translate('Explain why you are requesting an account.'); ?>"><?php echo escape($comments); ?></textarea>
				</label>
				<p class="small text-muted">
					<?php echo translate('Use this field to tell the site administrator why you are requesting an account and how you are related to the genealogy displayed on this site. You can also use this to enter any other comments you may have for the site administrator.'); ?>
				</p>
			</div>

			<div id="registration-submit">
				<input type="submit" value="<?php echo translate('continue'); ?>">
			</div>
		</form>
	</div>
</div>
