<?php
/**
 * Interface for Module to indicate configurable modules.
 * Support hooks <strong>h_config_tab_name</strong> and <strong>h_config_tab_content</strong>
 *
 * @package webtrees
 * @subpackage PersoLibrary
 * @author: Jonathan Jaubart ($Author: jonathan $)
 * @version: p_$Revision: 13 $ $Date: 2011-04-11 22:08:20 +0000 (Mon, 11 Apr 2011) $
 * $HeadURL: file:///mnt/atl-fs8-data1/svn/webtrees-geneajaubart/trunk/library/WT/Perso/Module/Configurable.php $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

interface WT_Perso_Module_Configurable {
	
	/**
	 * Print the text to display as title of the config tab for this module.
	 * 
	 * @return string Title of module config tab
	 */
	public function h_config_tab_name();
	
	/**
	 * Print the content of the config tab for this module.
	 * 
	 * @return string Title of module config tab
	 */
	public function h_config_tab_content();
	
	/**
	 * Validate the value sent for the setting against specific module rules.
	 * Can return either a modified value, or the error message 'ERROR_VALIDATION' is the validation fails.
	 * 
	 * @param string $setting Setting name
	 * @param string $value Replacement setting value to be validated.
	 * @return string Result of the validation (value or error message)
	 */
	public function validate_config_settings($setting, $value);
	
}

?>