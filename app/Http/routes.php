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
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Http\Controllers\LoginController;
use Fisharebest\Webtrees\Http\Controllers\MessageController;

switch (Filter::get('route')) {
default:
	return;

////////////////////////////////////////////////////////////////////////////////
// Routes for login, logout, password reset, and registration.
////////////////////////////////////////////////////////////////////////////////

case 'login':
	$controller = new LoginController;
	$controller->loginPage();
	break;

case 'login-action':
	$controller = new LoginController;
	$controller->loginAction();
	break;

case 'logout':
	$controller = new LoginController;
	$controller->logoutAction();
	break;

case 'password-reset':
	$controller = new LoginController;
	$controller->passwordResetPage();
	break;

case 'password-reset-action':
	$controller = new LoginController;
	$controller->passwordResetAction();
	break;

case 'registration-action':
	$controller = new LoginController;
	$controller->registrationAction();
	break;

case 'registration-confirm':
	$controller = new LoginController;
	$controller->registrationConfirm();
	break;

case 'registration-page':
	$controller = new LoginController;
	$controller->registrationPage();
	break;

case 'verify-email':
	$controller = new LoginController;
	$controller->verifyEmailAction();
	break;

////////////////////////////////////////////////////////////////////////////////
// Routes for messaging.
////////////////////////////////////////////////////////////////////////////////

case 'broadcast':
	$controller = new MessageController;
	$controller->broadcast();
	break;

case 'broadcast-all':
	$controller = new MessageController;
	$controller->broadcastAll();
	break;

case 'broadcast-inactive':
	$controller = new MessageController;
	$controller->broadcastInactive();
	break;

case 'broadcast-unused':
	$controller = new MessageController;
	$controller->broadcastUnused();
	break;
}
exit;
