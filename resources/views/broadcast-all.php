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
	<li><a href="<?php echo url('broadcast') ?>"><?php echo translate('Send broadcast messages'); ?></a></li>
	<li class="active"><?php echo translate('Send a message to all users'); ?></li>
</ol>

<h1>
	<?php echo translate('Send a message to all users') ?>
</h1>

<form method="post" class="form-horizontal">
	<div class="form-group">
		<label for="recipients" class="col-sm-3 control-label">
			<?php echo translate('Recipients') ?>
		</label>
		<div class="col-sm-9 control-label">
			<input id="recipients" class="form-control" type="text" disabled value="">
		</div>
	</div>

	<div class="form-group">
		<label for="subject" class="col-sm-3 control-label">
			<?php echo /* I18N: ...of an email */ translate('Subject') ?>
		</label>
		<div class="col-sm-9">
			<input type="text" class="form-control" id="subject" name="subject" placeholder="" maxlength="255">
		</div>
	</div>

	<div class="form-group">
		<label for="message" class="col-sm-3 control-label">
			<?php echo translate('Message') ?>
		</label>
		<div class="col-sm-9">
			<textarea id="message" name="message" class="form-control"></textarea>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<button type="submit" class="btn btn-primary">
				<i class="fa fa-envelope-o"></i>
				<?php echo /* I18N: Button label - send an email */ translate('send') ?>
			</button>
		</div>
	</div>
</form>
