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
namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Mail;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\View;
use Rhumsaa\Uuid\Uuid;

/**
 * Login and registration
 */
class LoginController extends Controller {
	/** @const Create passwords from these characters */
	const PASSWORD_CHARACTERS = 'abcdefghijklmnopqrstuvqxyz0123456789';

	/**
	 * Display the login page.
	 */
	public function loginPage() {
		$welcome_text = '';
		switch (Site::getPreference('WELCOME_TEXT_AUTH_MODE')) {
		case 1:
			$welcome_text =
				I18N::translate('Anyone with a user account can access this website.') . ' ' .
				I18N::translate('You can apply for an account using the link below.');
			break;
		case 2:
			$welcome_text =
				I18N::translate('You need to be an authorized user to access this website.') . ' ' .
				I18N::translate('You can apply for an account using the link below.');
			break;
		case 3:
			$welcome_text =
				I18N::translate('You need to be a family member to access this website.') . ' ' .
				I18N::translate('You can apply for an account using the link below.');
			break;
		case 4:
			$welcome_text = Site::getPreference('WELCOME_TEXT_AUTH_MODE_' . WT_LOCALE);
			break;
		}

		$data = array(
			'allow_registration' => Site::getPreference('USE_REGISTRATION_MODULE') === '1',
			'login_url'          => WT_LOGIN_URL,
			'tree'               => $this->tree,
			'username'           => Filter::get('username'),
			'url'                => Filter::get('url'),
			'welcome_text'       => $welcome_text,
		);
		$this
			->setPageTitle(I18N::translate('Sign in'))
			->setPageContent(View::render('login-page', $data))
			->pageHeader();
	}

	/**
	 * Handle the submission of a login form.
	 */
	public function loginAction() {

		$username = Filter::post('username');
		$password = Filter::post('password');
		$url      = Filter::post('url');

		if (!Filter::checkCsrf()) {
			$this->redirectRoute('login', array('username' => $username, 'url' => $url));

			return;
		}

		// Already logged in?
		if (Auth::check()) {
			Auth::logout();
		}

		try {
			$user = User::findByIdentifier($username);

			if (!$user) {
				Log::addAuthenticationLog('Login failed (no such user/email): ' . $username);
				throw new \Exception(I18N::translate('The username or password is incorrect.'));
			}

			if (!$user->checkPassword($password)) {
				Log::addAuthenticationLog('Login failed (incorrect password): ' . $username);
				throw new \Exception(I18N::translate('The username or password is incorrect.'));
			}

			if ($user->getPreference('verified') !== '1') {
				Log::addAuthenticationLog('Login failed (not verified by user): ' . $username);
				throw new \Exception(I18N::translate('This account has not been verified. Please check your email for a verification message.'));
			}

			if ($user->getPreference('verified_by_admin') !== '1') {
				Log::addAuthenticationLog('Login failed (not approved by admin): ' . $username);
				throw new \Exception(I18N::translate('This account has not been approved. Please wait for an administrator to approve it.'));
			}

			Auth::login($user);
			Log::addAuthenticationLog('Login: ' . Auth::user()->getUserName() . '/' . Auth::user()->getRealName());
			Auth::user()->setPreference('sessiontime', WT_TIMESTAMP);

			Session::put('locale', Auth::user()->getPreference('language'));
			Session::put('theme_id', Auth::user()->getPreference('theme'));

			// We're logging in as an administrator
			if (Auth::isAdmin()) {
				// Check for updates
				$latest_version_txt = Functions::fetchLatestVersion();
				if (preg_match('/^[0-9.]+\|[0-9.]+\|/', $latest_version_txt)) {
					list($latest_version) = explode('|', $latest_version_txt);
					if (version_compare(WT_VERSION, $latest_version) < 0) {
						FlashMessages::addMessage(
							I18N::translate('A new version of webtrees is available.') .
							' <a href="admin_site_upgrade.php"><b>' .
							I18N::translate('Upgrade to webtrees %s.', '<span dir="ltr">' . $latest_version . '</span>') .
							'</b></a>'
						);
					}
				}
			}

			// If we were on a "home page", redirect to "my page"
			if ($url === '' || strpos($url, 'index.php?ctype=gedcom') === 0) {
				$url = 'index.php?ctype=user';
				// Switch to a tree where we have a genealogy record (or keep to the current/default).
				$user_tree = Database::prepare(
					"SELECT gedcom_name FROM `##gedcom` JOIN `##user_gedcom_setting` USING (gedcom_id)" .
					" WHERE setting_name = 'gedcomid' AND user_id = :user_id" .
					" ORDER BY gedcom_id = :tree_id DESC"
				)->execute(array(
					'user_id' => Auth::user()->getUserId(),
					'tree_id' => $this->tree->getTreeId(),
				))->fetchOne();
				$url .= '&ged=' . Filter::escapeUrl($user_tree);
			}

			// Redirect to the target URL
			$this->redirectUrl($url);
		} catch (\Exception $ex) {
			FlashMessages::addMessage($ex->getMessage(), 'danger');
			$this->redirectRoute('login', array('username' => $username, 'url' => $url));
		}
	}

	/**
	 * Handle a logout request.
	 */
	public function logoutAction() {
		if (Auth::check()) {
			Log::addAuthenticationLog('Logout: ' . Auth::user()->getUserName() . '/' . Auth::user()->getRealName());
			Auth::logout();
			FlashMessages::addMessage(I18N::translate('You have signed out.'), 'info');
		}

		$this->redirectUrl('index.php');
	}

	/**
	 * Display the password reset page.
	 */
	public function passwordResetPage() {
		$this
			->setPageTitle(I18N::translate('Request a new password'))
			->setPageContent(View::render('password-reset-page'))
			->pageHeader();
	}

	/**
	 * Handle the submission of a forgotten password form.
	 */
	public function passwordResetAction() {
		$username = Filter::post('username');
		$user     = User::findByIdentifier($username);

		if (!Filter::checkCsrf()) {
			$this->redirectRoute('password-reset', array('username' => $username));

			return;
		}

		if ($user) {
			$password = $this->generateNewPassword(self::PASSWORD_CHARACTERS, 8);

			$user->setPassword($password);
			Log::addAuthenticationLog('Password request was sent to user: ' . $user->getUserName());

			$data = array(
				'user'      => $user,
				'password'  => $password,
				'login_url' => WT_BASE_URL . url('login', array('url' => 'edituser.php', 'username' => $user->getUserName())),
			);

			Mail::systemMessage(
				$this->tree,
				$user,
				I18N::translate('Lost password request'),
				View::render('emails/lost-password.html', $data),
				View::render('emails/lost-password.text', $data)
			);

			FlashMessages::addMessage(I18N::translate('A new password has been created and emailed to %s. You can change this password after you sign in.', Filter::escapeHtml($username)), 'success');
		} else {
			FlashMessages::addMessage(I18N::translate('There is no account with the username or email “%s”.', Filter::escapeHtml($username)), 'danger');
		}

		$this->redirectRoute('login');
	}

	/**
	 * Display the registration page.
	 */
	public function registrationPage() {
		if (Site::getPreference('USE_REGISTRATION_MODULE') !== '1') {
			$this->redirectUrl('index.php');

			return;
		}

		$data = array(
			'comments'              => Filter::get('comments'),
			'email'                 => Filter::get('email'),
			'password_length'       => WT_MINIMUM_PASSWORD_LENGTH,
			'password_regex'        => WT_REGEX_PASSWORD,
			'real_name'             => Filter::get('real_name'),
			'show_register_caution' => Site::getPreference('SHOW_REGISTER_CAUTION'),
			'username'              => Filter::get('username'),
		);

		$this
			->setPageTitle(I18N::translate('Request a new user account'))
			->setPageContent(View::render('registration-page', $data))
			->pageHeader();
	}

	/**
	 * Handle the submission of a registration form.
	 */
	public function registrationAction() {
		$comments  = Filter::post('comments');
		$email     = Filter::post('email');
		$password1 = Filter::post('password1');
		$password2 = Filter::post('password2');
		$real_name = Filter::post('real_name');
		$username  = Filter::post('username');

		if (Site::getPreference('USE_REGISTRATION_MODULE') !== '1') {
			$this->redirectUrl('index.php');

			return;
		}

		if (Filter::checkCsrf() && $username && $password1 && $password1 === $password2 && $real_name && $email && $comments) {

			// These validation errors cannot be shown in the client.
			if (User::findByUserName($username)) {
				FlashMessages::addMessage(I18N::translate('Duplicate username. A user with that username already exists. Please choose another username.'));
			} elseif (User::findByEmail($email)) {
				FlashMessages::addMessage(I18N::translate('Duplicate email address. A user with that email already exists.'));
			} elseif (preg_match('/(?!' . preg_quote(WT_BASE_URL, '/') . ')(((?:ftp|http|https):\/\/)[a-zA-Z0-9.-]+)/', $comments, $match)) {
				FlashMessages::addMessage(
					I18N::translate('You are not allowed to send messages that contain external links.') . ' ' .
					I18N::translate('You should delete the “%1$s” from “%2$s” and try again.', $match[2], $match[1])
				);
				Log::addAuthenticationLog('Possible spam registration from "' . $username . '"/"' . $email . '" comments="' . $comments . '"');
			} else {
				// Everything looks good - create the user
				Log::addAuthenticationLog('User registration requested for: ' . $username);

				$user = User::create($username, $real_name, $email, $password1);
				$user
					->setPreference('language', WT_LOCALE)
					->setPreference('verified', '0')
					->setPreference('verified_by_admin', 0)
					->setPreference('reg_timestamp', date('U'))
					->setPreference('reg_hashcode', Uuid::uuid4())
					->setPreference('contactmethod', 'messaging2')
					->setPreference('comment', $comments)
					->setPreference('visibleonline', '1')
					->setPreference('auto_accept', '0')
					->setPreference('canadmin', '0')
					->setPreference('sessiontime', '0');

				$data = array(
					'base_url'   => WT_BASE_URL,
					'verify_url' => WT_BASE_URL . url('verify-email', array('id' => $user->getUserId(), 'code' => $user->getPreference('reg_hashcode'))),
					'tree'       => $this->tree,
					'user'       => $user,
					'comments'   => $comments,
				);

				// Send admin message by email and internal messaging
				$webmaster = User::find($this->tree->getPreference('WEBMASTER_USER_ID'));
				I18N::init($webmaster->getPreference('language'));
				Mail::send(
				// “From:” header
					$this->tree,
					// “To:” header
					$webmaster->getEmail(),
					$webmaster->getRealName(),
					// “Reply-To:” header
					$user->getEmail(),
					$user->getRealName(),
					// Message
					/* I18N: %s is a server name/URL */ I18N::translate('New registration at %s', WT_BASE_URL . ' ' . $this->tree->getTitle()),
					View::render('emails/register-admin.html', $data),
					View::render('emails/register-admin.text', $data)
				);

				Database::prepare("INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)")
					->execute(array(
						$user->getEmail(),
						WT_CLIENT_IP,
						$webmaster->getUserId(),
						I18N::translate('New registration at %s', WT_BASE_URL . ' ' . $this->tree->getTitle()),
						View::render('emails/register-admin.text', $data)
					));

				// Send user message by email only
				I18N::init($user->getPreference('language'));
				Mail::send(
				// “From:” header
					$this->tree,
					// “To:” header
					$user->getEmail(),
					$user->getRealName(),
					// “Reply-To:” header
					$this->tree->getPreference('WEBTREES_EMAIL'),
					$this->tree->getPreference('WEBTREES_EMAIL'),
					// Message
					/* I18N: %s is a server name/URL */ I18N::translate('Your registration at %s', WT_BASE_URL),
					View::render('emails/register-user.html', $data),
					View::render('emails/register-user.text', $data)
				);

				$this->redirectRoute('registration-confirm', array('id' => $user->getUserId()));

				return;
			}
		}

		// Something was wrong - try again.
		$this->redirectRoute('registration-page', array(
			'comments'  => $comments,
			'email'     => $email,
			'real_name' => $real_name,
			'username'  => $username,
		));
	}

	/**
	 * Sucessful submission of a registration form.
	 */
	public function registrationConfirm() {
		$id   = Filter::get('id');
		$user = User::find($id);

		$this
			->setPageTitle(I18N::translate('Sign in'))
			->setPageContent(View::render('registration-confirm', array('user' => $user)))
			->pageHeader();
	}

	/**
	 * Respond to a link which was sent to a user to verify their email address.
	 */
	public function verifyEmailAction() {
		$id   = Filter::get('id');
		$code = Filter::get('code');
		$user = User::find($id);

		if ($user && $user->getPreference('reg_hashcode') === $code) {
			$user
				->setPreference('verified', '1')
				->setPreference('reg_timestamp', date('U'))
				->deletePreference('reg_hashcode');

			Log::addAuthenticationLog('User ' . $user->getUserName() . ' verified their email address');

			// switch language to webmaster settings
			$webmaster = User::find($this->tree->getPreference('WEBMASTER_USER_ID'));
			I18N::init($webmaster->getPreference('language'));

			$data = array(
				'base_url' => WT_BASE_URL,
				'user'     => $user,
			);

			Mail::send(
			// “From:” header
				$this->tree,
				// “To:” header
				$webmaster->getEmail(),
				$webmaster->getRealName(),
				// “Reply-To:” header
				$this->tree->getPreference('WEBTREES_EMAIL'),
				$this->tree->getPreference('WEBTREES_EMAIL'),
				// Message
				/* I18N: %s is a server name/URL */
				I18N::translate('New user at %s', WT_BASE_URL . ' ' . $this->tree->getTitle()),
				View::render('emails/verify-admin.html', $data),
				View::render('emails/verify-admin.text', $data)
			);

			Database::prepare("INSERT INTO `##message` (sender, ip_address, user_id, subject, body) VALUES (? ,? ,? ,? ,?)")
				->execute(array(
					$user->getUserName(),
					WT_CLIENT_IP,
					$webmaster->getUserId(),
					/* I18N: %s is a server name/URL */
					I18N::translate('New user at %s', WT_BASE_URL . ' ' . $this->tree->getTitle()),
					View::render('emails/verify-admin.text', $data)
				));

			// Change back to the new user’s language
			I18N::init($user->getPreference('language'));

			$this
				->setPageTitle(I18N::translate(''))
				->setPageContent(View::render('verify-email-ok'))
				->pageHeader();
		} else {
			$this
				->setPageTitle(I18N::translate(''))
				->setPageContent(View::render('verify-email-fail'))
				->pageHeader();
		}
	}

	/**
	 * Generate a new password.
	 *
	 * @param string $chars
	 * @param int    $length
	 *
	 * @return string
	 */
	private function generateNewPassword($chars, $length) {
		$max      = strlen($chars) - 1;
		$password = '';

		for ($i = 0; $i < $length; $i++) {
			$password .= substr($chars, rand(0, $max), 1);
		}

		return $password;
	}
}
