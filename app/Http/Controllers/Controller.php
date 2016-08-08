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

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Route;
use Fisharebest\Webtrees\Tree;

/**
 * Base controller
 */
class Controller extends PageController {
	/** @var Tree */
	protected $tree;
	
	/**
	 * Controller constructor.
	 */
	public function __construct() {
		global $WT_TREE;
		
		$this->tree = $WT_TREE;
	}
	
	/**
	 * Redirect to the specified URL.
	 *
	 * @param string $url
	 */
	public function redirectUrl($url) {
		header('Location: ' . WT_BASE_URL . $url);
	}

	/**
	 * Redirect to the specified route.
	 *
	 * @param string   $route
	 * @param string[] $parameters
	 */
	public function redirectRoute($route, $parameters = array()) {
		$this->redirectUrl(Route::url($route, $parameters));
	}
}
