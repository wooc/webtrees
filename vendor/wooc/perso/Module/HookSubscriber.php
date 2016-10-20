<?php
/**
 * Interface for Modules to indicate presence of hooks functions
 *
 * @package webtrees
 * @subpackage PersoLibrary
 * @author: Jonathan Jaubart ($Author: jonathan $)
 * @version: p_$Revision: 11 $ $Date: 2011-04-10 10:16:27 +0000 (Sun, 10 Apr 2011) $
 * $HeadURL: file:///mnt/atl-fs8-data1/svn/webtrees-geneajaubart/trunk/library/WT/Perso/Module/HookSubscriber.php $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

interface WT_Perso_Module_HookSubscriber {
	
	/**
	 * Return the list of functions implementented in the class which needs to be registered as hooks.
	 * The format is either { function1, function 2,...} in which case the priority is the default one
	 * or { function1 => priority1, function2 => priority2, ...}
	 * 
	 * @return Array Array of hooks
	 */
	public function getSubscribedHooks();
	
}

?>