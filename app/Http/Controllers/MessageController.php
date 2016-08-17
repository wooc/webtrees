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

use Exception;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Mail;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Theme\AdministrationTheme;
use Fisharebest\Webtrees\User;
use Fisharebest\Webtrees\View;
use Swift_Mailer;
use Swift_Message;

/**
 * Messages
 */
class MessageController extends Controller {
	/** Number of seconds in six months */
	const INACTIVE_THRESHOLD = 15552000;

	/**
	 * Send a message to categories of user.
	 */
	public function broadcast() {
		Theme::theme(new AdministrationTheme)->init($this->tree);
		$this
			->restrictAccess(Auth::isAdmin())
			->setPageTitle(I18N::translate('Send broadcast messages'))
			->setPageContent(View::render('broadcast'))
			->pageHeader();
	}

	/**
	 * Form to send a message to all users.
	 */
	public function broadcastAll() {
		Theme::theme(new AdministrationTheme)->init($this->tree);

		$data = array(
			'recipients' => $this->allUsers(),
		);

		$this
			->restrictAccess(Auth::isAdmin())
			->setPageTitle(I18N::translate('Send a message to all users'))
			->setPageContent(View::render('broadcast-all', $data))
			->pageHeader();
	}

	/**
	 * Send a message all users.
	 */
	public function broadcastAllAction() {
		$subject    = Filter::post('subject');
		$message    = Filter::post('message');
		$body_html  = View::render('broadcast.html', array('message' => $message));
		$body_text  = View::render('broadcast.text', array('message' => $message));
		$recipients = $this->allUsers();

		if (Filter::checkCsrf()) {
			$this
				->restrictAccess(Auth::isAdmin())
				->broadcastAction(Auth::user(), $recipients, $subject, $body_html, $body_text)
				->redirectRoute('broadcast');
		} else {
			$this->redirectRoute('broadcast-all', array('subject' => $subject, 'message' => $message));
		}
	}

	/**
	 * A list of all user accounts.
	 *
	 * @return User[]
	 */
	private function allUsers() {
		$users = array();
		foreach (User::all() as $user) {
			if ($user->getPreference('verified') === '1' && $user->getPreference('verified_by_admin') === '1' && $user->getUserId() !== Auth::id()) {
				$users[] = $user;
			}
		}

		return $users;
	}

	/**
	 * Form to send a message to users who have not signed in recently.
	 */
	public function broadcastInactive() {
		Theme::theme(new AdministrationTheme)->init($this->tree);

		$data = array(
			'recipients' => $this->inactiveUsers(),
		);

		$this
			->restrictAccess(Auth::isAdmin())
			->setPageTitle(I18N::translate('Send a message to users who have not signed in for 6 months'))
			->setPageContent(View::render('broadcast-inactive', $data))
			->pageHeader();
	}

	/**
	 * Send a message users who have not signed in recently.
	 */
	public function broadcastInactiveAction() {
		$subject    = Filter::post('subject');
		$message    = Filter::post('message');
		$body_html  = View::render('broadcast.html', array('message' => $message));
		$body_text  = View::render('broadcast.text', array('message' => $message));
		$recipients = $this->inactiveUsers();

		if (Filter::checkCsrf()) {
			$this
				->restrictAccess(Auth::isAdmin())
				->broadcastAction(Auth::user(), $recipients, $subject, $body_html, $body_text)
				->redirectRoute('broadcast');
		} else {
			$this->redirectRoute('broadcast-inactive', array('subject' => $subject, 'message' => $message));
		}
	}

	/**
	 * A list of unused user accounts.
	 *
	 * @return User[]
	 */
	private function inactiveUsers() {
		$users = array();
		foreach (User::all() as $user) {
			if (
				$user->getPreference('sessiontime') > 0 && WT_TIMESTAMP - $user->getPreference('sessiontime') > self::INACTIVE_THRESHOLD ||
				$user->getPreference('verified_by_admin') !== '1' && WT_TIMESTAMP - $user->getPreference('reg_timestamp') > self::INACTIVE_THRESHOLD) {
				$users[] = $user;
			}
		}

		return $users;
	}

	/**
	 * Form to send a message to users who have never logged in.
	 */
	public function broadcastUnused() {
		Theme::theme(new AdministrationTheme)->init($this->tree);

		$data = array(
			'recipients' => $this->unusedUsers(),
		);

		$this
			->restrictAccess(Auth::isAdmin())
			->setPageTitle(I18N::translate('Send a message to users who have never signed in'))
			->setPageContent(View::render('broadcast-unused', $data))
			->pageHeader();
	}

	/**
	 * Send a message all users.
	 */
	public function broadcastUnusedAction() {
		$subject    = Filter::post('subject');
		$message    = Filter::post('message');
		$body_html  = View::render('broadcast.html', array('message' => $message));
		$body_text  = View::render('broadcast.text', array('message' => $message));
		$recipients = $this->unusedUsers();

		if (Filter::checkCsrf()) {
			$this
				->restrictAccess(Auth::isAdmin())
				->broadcastAction(Auth::user(), $recipients, $subject, $body_html, $body_text)
				->redirectRoute('broadcast');
		} else {
			$this->redirectRoute('broadcast-unused', array('subject' => $subject, 'message' => $message));
		}
	}

	/**
	 * A list of unused user accounts.
	 *
	 * @return User[]
	 */
	private function unusedUsers() {
		$users = array();
		foreach (User::all() as $user) {
			if ($user->getPreference('verified_by_admin') === '1' && $user->getPreference('reg_timestamp') > $user->getPreference('sessiontime')) {
				$users[] = $user;
			}
		}

		return $users;
	}

	/**
	 * Send a broadcast message to a list of users
	 *
	 * @param string $subject
	 * @param string $body_html
	 * @param string $body_text
	 * @param User   $sender
	 * @param User[] $recipients
	 *
	 * @return MessageController
	 */
	private function broadcastAction($sender, $recipients, $subject, $body_html, $body_text) {
		try {
			$mail = Swift_Message::newInstance()
				->setSubject($subject)
				->setFrom($sender->getEmail(), $sender->getRealName())
				->setTo($sender->getEmail(), $sender->getRealName())
				->setBody($body_html, 'text/html')
				->addPart($body_text, 'text/plain');

			foreach ($recipients as $recipient) {
				$mail->addBcc($recipient->getEmail(), $recipient->getRealName());
			}

			Swift_Mailer::newInstance(Mail::transport())->send($mail);
		} catch (Exception $ex) {
			Log::addErrorLog('Mail: ' . $ex->getMessage());
		}

		return $this;
	}

	/**
	 * Form to send a message to the genealogy contact.
	 */
	public function enquiry() {
		$this
			->restrictAccess(Auth::isAdmin())
			->setPageTitle(I18N::translate('Send a message'))
			->setPageContent(View::render('enquiry'))
			->pageHeader();
	}

	/**
	 * Send a message to the genealogy contact.
	 */
	public function enquiryAction() {
		$this->restrictAccess(Auth::isAdmin());
	}
}
