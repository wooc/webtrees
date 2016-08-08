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
	<?php echo translate('Hello administrator…') ?>
</p>

<p>
	<?php echo translate('A prospective user has registered with webtrees at %s.', '<a href="' . escape($base_url) . '">' . escape($tree->getTitle()) . '</a>') ?>
</p>

<ul>
	<li>
		<?php echo translate('Username') ?> - <?php echo escape($user->getUserName()) ?>
	</li>
	<li>
		<?php echo translate('Real name') ?> - <?php echo escape($user->getRealName()) ?>
	</li>
	<li>
		<?php echo translate('Email address') ?> - <?php echo escape($user->getEmail()) ?>
	</li>
	<li>
		<?php echo translate('Comments') ?> - <?php echo escape($comments) ?>
	</li>
</ul>

<p>
	<?php echo translate('The user has been sent an email with the information necessary to confirm the access request.') ?>
</p>

<p>
	<?php echo translate('You will be informed by email when this prospective user has confirmed the request. You can then complete the process by activating the username. The new user will not be able to sign in until you activate the account.') ?>
</p>
