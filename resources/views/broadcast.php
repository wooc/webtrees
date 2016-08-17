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
<ol class="breadcrumb small">
	<li><a href="admin.php"><?php echo translate('Control panel'); ?></a></li>
	<li><a href="admin_users.php"><?php echo translate('User administration'); ?></a></li>
	<li class="active"><?php echo translate('Send broadcast messages'); ?></li>
</ol>

<h1><?php echo translate('Send broadcast messages'); ?></h1>

<p>
	<a href="<?php echo url('broadcast-all') ?>">
		<?php echo translate('Send a message to all users'); ?>
	</a>
</p>
<p>
	<a href="<?php echo url('broadcast-unused') ?>">
		<?php echo translate('Send a message to users who have never signed in'); ?>
	</a>
</p>
<p>
	<a href="<?php echo url('broadcast-inactive') ?>">
		<?php echo translate('Send a message to users who have not signed in for 6 months'); ?>
	</a>
</p>
