<?php
 /**
 * Base controller for Plain text Ajax pages
 *
 * @package webtrees
 * @subpackage PersoLibrary
 * @author: Jonathan Jaubart ($Author: webtrees.geneajaubart $)
 * @version: p_$Revision: 68 $ $Date: 2013-04-27 15:18:50 +0000 (Sat, 27 Apr 2013) $
 * $HeadURL: file:///mnt/atl-fs8-data1/svn/webtrees-geneajaubart/trunk/library/WT/Perso/Controller/PlainAjax.php $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Perso_Controller_PlainAjax extends WT_Controller_Ajax {

	// Extend class WT_Controller_Ajax
	public function pageHeader() {
		// We have finished writing session data, so release the lock
		Zend_Session::writeClose();
		// Ajax responses are always UTF8
		header('Content-Type: text/plain; charset=UTF-8');
		$this->page_header=true;
		return $this;
	}
	
	// Extend class WT_Controller_Ajax
	public function pageFooter() {
		return $this;
	}
}
