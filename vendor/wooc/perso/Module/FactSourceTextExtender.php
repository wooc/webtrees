<?php
/**
 * Interface for Module for modules providing an extension feature for texts describing Facts sources.
 * Support hook <strong>h_fs_prepend</strong> and <strong>h_fs_append</strong>
 *
 * @package webtrees
 * @subpackage PersoLibrary
 * @author: Jonathan Jaubart ($Author: jonathan $)
 * @version: p_$Revision: 30 $ $Date: 2011-06-19 14:43:19 +0000 (Sun, 19 Jun 2011) $
 * $HeadURL: file:///mnt/atl-fs8-data1/svn/webtrees-geneajaubart/trunk/library/WT/Perso/Module/FactSourceTextExtender.php $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

interface WT_Perso_Module_FactSourceTextExtender {

	/**
	 * Insert some content before the fact source text.
	 * 
	 * @param string $srec Source fact record
	 */
	public function h_fs_prepend($srec);
	
	/**
	 * Insert some content after the fact source text.
	 * 
	 * @param string $srec Source fact record
	 */
	public function h_fs_append($srec);
	
}

?>