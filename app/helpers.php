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
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Route;

/**
 * Views do not have namespaces, so create shortcuts to common functions.
 *
 * @return string
 */
function translate(/* var_args */) {
	return call_user_func_array(array('\Fisharebest\Webtrees\I18N', 'translate'), func_get_args());
}

/**
 * Views do not have namespaces, so create shortcuts to common functions.
 *
 * @return string
 */
function plural(/* var_args */) {
	return call_user_func_array(array('\Fisharebest\Webtrees\I18N', 'plural'), func_get_args());
}

/**
 * Views do not have namespaces, so create shortcuts to common functions.
 *
 * @param float $n
 * @param int   $precision
 *
 * @return string
 */
function number($n, $precision = 0) {
	return I18N::number($n, $precision);
}

/**
 * Views do not have namespaces, so create shortcuts to common functions.
 *
 * @param string $string
 *
 * @return string
 */
function escape($string) {
	return Filter::escapeHtml($string);
}

/**
 * Views do not have namespaces, so create shortcuts to common functions.
 *
 * @param string   $route
 * @param string[] $parameters
 *
 * @return string
 */
function url($route, array $parameters = array()) {
	return Route::url($route, $parameters);
}
