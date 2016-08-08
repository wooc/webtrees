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
<?php echo translate('Hello %s…', $user->getRealName()) ?>


<?php echo translate('You (or someone claiming to be you) has requested an account at %1$s using the email address %2$s.', $tree->getTitle(), $user->getEmail()) ?>


<?php echo translate('Follow this link to verify your email address.') ?>


<?php echo $verify_url ?>&id=<?php echo rawurlencode($user->getUserId()) ?>&amp;code=<?php echo rawurlencode($user->getPreference('reg_hashcode')) ?>

<?php echo translate('Username') ?> - <?php echo $user->getUserName() ?>

<?php echo translate('Comments') ?> - <?php echo $user->getPreference('comment') ?>


<?php echo translate('If you didn’t request an account, you can just delete this message.') ?>
