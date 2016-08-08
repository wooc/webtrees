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
 * Simple PHP templating system.
 */
class View {
	/**
	 * Render a view, using the supplied data.
	 *
	 * @param string  $path
	 * @param mixed[] $data
	 *
	 * @return string
	 */
	public static function render($path, array $data = []) {
		$filename = WT_ROOT . 'resources/views/' . $path . '.php';
		extract($data);

		// Every form will want one of these
		$csrf_field = Filter::getCsrf();
		$csrf_token = Filter::getCsrfToken();
		
		ob_start();
		require $filename;

		return ob_get_clean();
	}
}
