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
<p>
	<?php echo translate('Hello %s…', escape($user->getRealName())) ?>
</p>

<p>
	<?php echo translate('A new password has been requested for your username.') ?>
</p>

<p>
	<?php echo translate('Username') ?> - <?php echo escape($user->getUserName()) ?>
	<br>
	<?php echo translate('Password') ?> - <?php echo escape($password) ?>
</p>

<p>
	<?php echo translate('After you have signed in, select the “My account” link under the “My pages” menu and fill in the password fields to change your password.') ?>
</p>

<a href="<?php echo escape($login_url) ?>">
	<?php echo escape($login_url) ?>
</a>
