<?php
/**
 * Decorator class to extend native GedcomRecord class.
 *
 * @package webtrees
 * @subpackage PersoLibrary
 * @author: Jonathan Jaubart ($Author: webtrees.geneajaubart $)
 * @version: p_$Revision: 74 $ $Date: 2013-11-23 11:50:07 +0000 (Sat, 23 Nov 2013) $
 * $HeadURL: file:///mnt/atl-fs8-data1/svn/webtrees-geneajaubart/trunk/library/WT/Perso/GedcomRecord.php $
 */

namespace Wooc\WebtreesAddOns\Perso;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\GedcomRecord;
use Wooc\WebtreesAddOns\Perso\Functions\PersoFunctionsPrint;

class PersoGedcomRecord {

	protected $gedcomrecord;

	// Cached results from various functions.
	protected $_issourced=null;

	/**
	 * Contructor for the decorator
	 *
	 * @param GedcomRecord $gedcomrecord_in The GedcomRecord to extend
	 */
	public function __construct(GedcomRecord $gedcomrecord_in){
		$this->gedcomrecord = $gedcomrecord_in;
	}

	/**
	 * Return the native gedcom record embedded within the decorator
	 *
	 * @return GedcomRecord Embedded gedcom record
	 */
	public function getDerivedRecord(){
		return $this->gedcomrecord;
	}
	
	/**
	 * Get an HTML link to this object, for use in sortable lists.
	 * 
	 * @return string HTML link
	 */
	public function getXrefLink() {
		global $SEARCH_SPIDER;
		if (empty($SEARCH_SPIDER)) {
			return '<a href="'.$this->gedcomrecord->getHtmlUrl().'#content" name="'.preg_replace('/\D/','',$this->gedcomrecord->getXref()).'">'.$this->gedcomrecord->getXref().'</a>';
		} else {
			return $this->gedcomrecord->getXref();
		}
	}
	
	/**
	 * Add additional options to the core format_first_major_facts function.
	 * If no option is suitable, it will try returning the core function.
	 *
	 * Option 10 : display <i>factLabel shortFactDate shortFactPlace</i>
	 *
	 * @param string $facts List of facts to find information from
	 * @param int $style Style to apply to the information. Number >= 10 should be used in this function, lower number will return the core function.
	 * @return string Formatted fact description
	 */
	public function format_first_major_fact($facts, $style) {
		foreach ($this->gedcomrecord->getFacts($facts) as $fact) {
			// Only display if it has a date or place (or both)
			if (($fact->getDate() || $fact->getPlace()) && $fact->canShow()) {
				switch ($style) {
					case 10:
						return '<i>'.$fact->getLabel().' '.PersoFunctionsPrint::formatFactDateShort($fact).'&nbsp;'.PersoFunctionsPrint::formatFactPlaceShort($fact, '%1').'</i>';
						break;
					default:
						return $this->gedcomrecord->format_first_major_fact($facts, $style);
				}
			}
		}
		return '';
	}

	/**
	 * Check if the IsSourced information can be displayed
	 *
	 * @param int $access_level
	 * @return boolean
	 */
	public function canDisplayIsSourced($access_level = null){
		global $WT_TREE, $global_facts;

		if (!$access_level) {
			$access_level = Auth::accessLevel($WT_TREE);
		}
		if(!$this->gedcomrecord->canShow($access_level)) return false;

		if (isset($global_facts['SOUR'])) {
			return $global_facts['SOUR']>=$access_level;
		}

		return true;
	}

	/**
	 * Check if a gedcom record is sourced
	 * Values:
	 * 		- -1, if the record has no sources attached
	 * 		- 1, if the record has a source attached
	 * 		- 2, if the record has a source, and a certificate supporting it
	 *
	 * @return int Level of sources
	 */
	public function isSourced(){
		if($this->_issourced != null) return $this->_issourced;
		$this->_issourced=-1;
		/*
		$sourcesfacts = $this->gedcomrecord->getFacts('SOUR');
		foreach($sourcesfacts as $sourcefact){
			$this->_issourced=max($this->_issourced, 1);
			if($sourcefact->getAttribute('_ACT')){
				$this->_issourced=max($this->_issourced, 2);
			}
		}
		*/
		foreach($this->gedcomrecord->getFacts() as $fact){
			if (preg_match('/(?:^1|\n\d) SOUR/', $fact->getGedcom())) {
				$this->_issourced = max($this->_issourced, 1);
				if (preg_match('/(?:^1|\n\d) PAGE/', $fact->getGedcom())){
					$this->_issourced = max($this->_issourced, 2);
				}
			}
		}
		return $this->_issourced;
	}

	/**
	 * Check is an event associated to this record is sourced
	 *
	 * @param string $eventslist
	 * @return int Level of sources
	 */
	public function isFactSourced($eventslist){
		$isSourced = 0;
		$facts = $this->gedcomrecord->getFacts($eventslist);
		foreach($facts as $fact){
			if($isSourced <= PersoFact::MAX_IS_SOURCED_LEVEL){
				$dfact = new PersoFact($fact);
				$tmpIsSourced = $dfact->isSourced();
				if($tmpIsSourced != 0) {
					if($isSourced == 0) {
						$isSourced =  $tmpIsSourced;
					}
					else{
						$isSourced = max($isSourced, $tmpIsSourced);
					}
				}
			}
		}
		return $isSourced;
	}
}