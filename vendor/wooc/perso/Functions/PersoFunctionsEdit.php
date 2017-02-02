<?php
/**
 * Additional functions for editing.
 *
 * @package webtrees
 * @subpackage PersoLibrary
 * @author: Jonathan Jaubart ($Author: webtrees.geneajaubart $)
 * @version: p_$Revision: 74 $ $Date: 2013-11-23 11:50:07 +0000 (Sat, 23 Nov 2013) $
 * $HeadURL: file:///mnt/atl-fs8-data1/svn/webtrees-geneajaubart/trunk/library/WT/Perso/Functions/Edit.php $
 */
namespace Wooc\WebtreesAddOns\Perso\Functions;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Wooc\WebtreesAddOns\Perso\Controller\PersoPlainAjaxController;

class PersoFunctionsEdit {
		
	/**
	 * Return HTML code to print an inline editable text box, used in Perso Config saving process.
	 * 
	 * @param string $name Setting id (not setting name)
	 * @param string $value Setting value
	 * @param WT_Controller_Base $controller Page controller
	 * @param string $savingmodule Module to use for saving the setting, default to the Perso Config module
	 * @return string HTML code for inline editable textbox
	 */
	static public function edit_module_field_inline($name, $value, $controller = null, $savingmodule = 'perso_config'){
		$html='<span class="editable" id="' . $name . '">' . Filter::escapeHtml($value) . '</span>';
		$js='jQuery("#' . $name . '").editable("' . WT_BASE_URL . 'module.php?mod='.$savingmodule.'&mod_action=admin_update_setting", {submitdata: {csrf: WT_CSRF_TOKEN}, submit:"&nbsp;&nbsp;' . I18N::translate('OK') . '&nbsp;&nbsp;", style:"inherit", placeholder: "'.I18N::translate('click to edit').'"});';

		if ($controller) {
			$controller->addInlineJavascript($js);
			return $html;
		} else {
			// For AJAX callbacks
			return $html . '<script>' . $js . '</script>';
		}
	}
	
	/**
	 * Return HTML code to print an inline editable text area, used in Perso Config saving process.
	 * 
	 * @param string $name Setting id (not setting name)
	 * @param string $value Setting value
	 * @param WT_Controller_Base $controller Page controller
	 * @param string $savingmodule Module to use for saving the setting, default to the Perso Config module
	 * @return string HTML code for inline editable text area
	 */
	static public function edit_module_longfield_inline($name, $value, $controller = null, $savingmodule = 'perso_config'){
		$html='<span class="editable" id="' . $name . '">' . Filter::escapeHtml($value) . '</span>';
		$js='jQuery("#' . $name . '").editable("' . WT_BASE_URL . 'module.php?mod='.$savingmodule.'&mod_action=admin_update_setting", {type:"textarea", submitdata: {csrf: WT_CSRF_TOKEN}, submit:"&nbsp;&nbsp;' . I18N::translate('OK') . '&nbsp;&nbsp;", style:"inherit", rows: 5, placeholder: "'.I18N::translate('click to edit').'"});';
		
		if ($controller) {
			$controller->addInlineJavascript($js);
			return $html;
		} else {
			// For AJAX callbacks
			return $html . '<script>' . $js . '</script>';
		}
	}
	
	/**
	 * Return HTML code to print an inline editable combobox, used in Perso Config saving process.
	 * 
	 * @param string $name Setting id (not setting name)
	 * @param string $value Setting value
	 * @param string $empty Default value for empty item
	 * @param string $selected Selected item
	 * @param WT_Controller_Base $controller Page controller
	 * @param string $savingmodule Module to use for saving the setting, default to the Perso Config module
	 * @param string $extra
	 * @return string HTML code for inline editable combobox
	 */
	static public function select_edit_control_inline($name, $values, $empty, $selected, $controller=null, $savingmodule = 'perso_config', $extra='') {
		if (!is_null($empty)) {
			// Push ''=>$empty onto the front of the array, maintaining keys
			$tmp=array(''=>Filter::escapeHtml($empty));
			foreach ($values as $key=>$value) {
				$tmp[$key]=Filter::escapeHtml($value);
			}
			$values=$tmp;
		}
		$values['selected']=Filter::escapeHtml($selected);
		
		$html='<span class="editable" id="' . $name . '">' .
			(array_key_exists($selected, $values) ? $values[$selected] : '').
			'</span>';
		$js='jQuery("#' . $name . '").editable("' . WT_ROOT . 'module.php?mod='.$savingmodule.'&mod_action=admin_update_setting",
				{
					type:"select", data:' . json_encode($values) . ', 
					submitdata: {csrf: WT_CSRF_TOKEN},
					submit:"&nbsp;&nbsp;' . I18N::translate('OK') . '&nbsp;&nbsp;", 
					style:"inherit",
					placeholder: "'.I18N::translate('click to edit').'",
					callback: function(value, settings) {
							jQuery(this).html(settings.data[value]);
					}
				});';
		
		if ($controller) {
			$controller->addInlineJavascript($js);
			return $html;
		} else {
			// For AJAX callbacks
			return $html . '<script>' . $js . '</script>';
		}
	}
	
	/**
	 * Return HTML code to print an inline editable combobox with values yes and no, used in Perso Config saving process.
	 * 
	 * @param string $name Setting id (not setting name)
	 * @param bool $selected Selected value
	 * @param WT_Controller_Base $controller Page controller
	 * @param string $savingmodule Module to use for saving the setting, default to the Perso Config module
	 * @param string $extra
	 * @return string HTML code for inline editable yes/no combobox
	 */
	static public function edit_field_yes_no_inline($name, $selected=false, $controller=null, $savingmodule = 'perso_config', $extra='') {
		return self::select_edit_control_inline(
			$name, array(true=>I18N::translate('yes'), false=>I18N::translate('no')), null, (int)$selected, $controller, $savingmodule, $extra
		);
	}
	
	/**
	 * Return HTML code to pring an inline editable combobox with the different possible user access levels.
	 * 
	 * @param string $name Setting id (not setting name)
	 * @param string $selected Selected value
	 * @param WT_Controller_Base $controller Page controller
	 * @param string $savingmodule Module to use for saving the setting, default to the Perso Config module
	 * @param string $extra
	 * @return string HTML code for inline editable access level combobox
	 */
	static public function edit_field_access_level_inline($name, $selected='', $controller=null, $savingmodule = 'perso_config', $extra='') {
		$ACCESS_LEVEL=array(
			WT_PRIV_PUBLIC=>I18N::translate('Show to visitors'),
			WT_PRIV_USER  =>I18N::translate('Show to members'),
			WT_PRIV_NONE  =>I18N::translate('Show to managers'),
			WT_PRIV_HIDE  =>I18N::translate('Hide from everyone')
		);
		return self::select_edit_control_inline($name, $ACCESS_LEVEL, null, $selected, $controller, $savingmodule, $extra);
	}
	
	/**
	 * Is called when saving is successful, and return the value for insertion in the field.
	 *
	 * @param string $value New setting value
	 */
	static public function ok($value) {
		//$controller = new PersoPlainAjaxController();		
		//$controller->pageHeader();
		//echo Filter::escapeHtml($value);
		//exit;
	}
	
	/**
	 * Is called when saving fails, and return an HTML error.
	 */
	static public function fail() {
		$controller = new AjaxController();
		// Any 4xx code should work.  jeditable recommends 406
		$controller->pageHeader();
		header('HTTP/1.0 406 Not Acceptable');
		exit;
	}

}

?>