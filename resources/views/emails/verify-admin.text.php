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
<?php echo translate('Hello administrator…') ?>


<?php echo translate('A new user (%1$s) has requested an account (%2$s) and verified an email address (%3$s).', $user->getRealName(), $user->getUserName(), $user->getEmail()) ?>


<?php echo translate('You need to review the account details.') ?>


<?php echo $base_url ?>admin_users.php?action=edit&user_id=<?php echo $user->getUserId() ?>


* <?php echo translate('Set the status to “approved”.') ?>

* <?php echo translate('Set the access level for each tree.') ?>

* <?php echo translate('Link the user account to an individual.') ?>
