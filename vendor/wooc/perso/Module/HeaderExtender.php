<?php
/**
 * Interface for Module for modules extending header.
 *
 * @package webtrees
 * @subpackage PersoLibrary
 * @author: Jonathan Jaubart ($Author: jonathan $)
 * @version: p_$Revision: 30 $ $Date: 2011-06-19 14:43:19 +0000 (Sun, 19 Jun 2011) $
 * $HeadURL: file:///mnt/atl-fs8-data1/svn/webtrees-geneajaubart/trunk/library/WT/Perso/Module/HeaderExtender.php $
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

interface WT_Perso_Module_HeaderExtender {

	/**
	 * Print additional header.
	 */
	public function h_print_header();
	
}

?>