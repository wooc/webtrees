<?php
/**
 * Interface for Module for modules providing an extension feature for individual header.
 * Support hooks <strong>h_extend_indi_header_icons</strong>, <strong>h_extend_indi_header_left</strong> and <strong>h_extend_indi_header_right</strong>
 *
 * @package webtrees
 * @subpackage PersoLibrary
 * @author: Jonathan Jaubart ($Author: jonathan $)
 * @version: p_$Revision: 30 $ $Date: 2011-06-19 14:43:19 +0000 (Sun, 19 Jun 2011) $
 * $HeadURL: file:///mnt/atl-fs8-data1/svn/webtrees-geneajaubart/trunk/library/WT/Perso/Module/IndividualHeaderExtender.php $
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

interface WT_Perso_Module_IndividualHeaderExtender {
		
	/**
	 * Get HTML code for extending the icons in the individual header
	 *
	 * @param IndividualController $ctrlIndi Individual page controller
	 * @return string HTML code extension
	 */
	public function h_extend_indi_header_icons(IndividualController $ctrlIndi);
	
	/**
	 * Get HTML code for extending the left part of the individual header
	 *
	 * @param IndividualController $ctrlIndi Individual page controller
	 * @return string HTML code extension
	 */
	public function h_extend_indi_header_left(IndividualController $ctrlIndi);
	
	/**
	 * Get HTML code for extending the right part of the individual header
	 *
	 * @param IndividualController $ctrlIndi Individual page controller
	 * @return string HTML code extension
	 */
	public function h_extend_indi_header_right(IndividualController $ctrlIndi);
	
}

?>