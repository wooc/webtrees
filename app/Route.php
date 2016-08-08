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

/**
 * Simple routing system.
 */
class Route {
	/**
	 * create a URL from a route.
	 *
	 * @param string   $route
	 * @param string[] $parameters
	 *
	 * @return string
	 */
	public static function url($route, array $parameters = array()) {
		$url = 'index.php?route=' . rawurlencode($route);

		foreach ($parameters as $key => $value) {
			$url .= '&' . rawurlencode($key) . '=' . rawurlencode($value);
		}

		return $url;
	}
}
