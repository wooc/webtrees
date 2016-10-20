<?php
/**
 * Decorator class to extend native Individual controller.
 *
 * @package webtrees
 * @subpackage PersoLibrary
 * @author: Jonathan Jaubart ($Author: webtrees.geneajaubart $)
 * @version: p_$Revision: 74 $ $Date: 2013-11-23 11:50:07 +0000 (Sat, 23 Nov 2013) $
 * $HeadURL: file:///mnt/atl-fs8-data1/svn/webtrees-geneajaubart/trunk/library/WT/Perso/Controller/Individual.php $
 */
namespace Wooc\WebtreesAddOns\Perso\Controller;
use Fisharebest\Webtrees\Controller\IndividualController;

class PersoIndividualController {

	protected $ctrlIndividual;
	protected $dindi;

	/**
	 * Constructor for the decorator
	 *
	 * @param IndividualController $ctrlIndividual_in The Individual Controller to extend
	 */
	public function __construct(IndividualController  $ctrlIndividual_in){
		$this->ctrlIndividual = $ctrlIndividual_in;
		$this->dindi = new PersoIndividual($this->ctrlIndividual->getSignificantIndividual());
	}
	
	/**
	 * Print individual header extensions.
	 * Use hooks h_extend_indi_header_left and h_extend_indi_header_right
	 *
	 */
	public function print_extensions_header(){
		$hook_extend_indi_header_left = new Hook('h_extend_indi_header_left');
		$hook_extend_indi_header_right = new Hook('h_extend_indi_header_right');
		$hook_extend_indi_header_left = $hook_extend_indi_header_left->execute($this->ctrlIndividual);
		$hook_extend_indi_header_right = $hook_extend_indi_header_right->execute($this->ctrlIndividual);
		
		echo '<div id="indi_perso_header">',
			'<div id="indi_perso_header_left">';
		foreach ($hook_extend_indi_header_left as $div) {
			if(count($div)==2){
				echo '<div id="', $div[0], '" class="indi_perso_header_left_div">',
					$div[1], '</div>';
			}
		}
		echo '</div>',
			'<div id="indi_perso_header_right">';
		foreach ($hook_extend_indi_header_right as $div) {
			if(count($div)==2){
				echo '<div id="', $div[0], '" class="indi_perso_header_right_div">',
					$div[1], '</div>';
			}
		}
		echo '</div>',
		'</div>';
	}
	
	/**
	 * Print individual header extra icons.
	 * Use hook h_extend_indi_header_icons
	 *
	 */
	public function print_extra_icons_header(){
		$hook_extend_indi_header_icons = new Hook('h_extend_indi_header_icons');
		$hook_extend_indi_header_icons = $hook_extend_indi_header_icons->execute($this->ctrlIndividual);
		
		echo '<span id="indi_perso_icons">&nbsp;',
			implode('&nbsp;', $hook_extend_indi_header_icons),
			'</span>';
	}
}
