<?php
/**
 * Decorator class to extend native Place class.
 *
 * @package webtrees
 * @subpackage PersoLibrary
 * @author: Jonathan Jaubart ($Author: webtrees.geneajaubart $)
 * @version: p_$Revision: 59 $ $Date: 2012-09-15 15:15:52 +0000 (Sat, 15 Sep 2012) $
 * $HeadURL: file:///mnt/atl-fs8-data1/svn/webtrees-geneajaubart/trunk/library/WT/Perso/Place.php $
 */

namespace Wooc\WebtreesAddOns\Perso;

use Fisharebest\Webtrees\Place;

class PersoPlace {

	protected $_place;

	/**
	 * Contructor for the decorator
	 *
	 * @param Place $place_in The Place to extend
	 */
	public function __construct(Place $place_in){
		$this->_place = $place_in;
	}

	/**
	 * 
	 * Returns an instance of PersoPlace, based on the string provided.
	 *
	 * @param string $place_str
	 * @param object $gedcom_id
	 * @return PersoPlace|null Instance of PersoPlace, if relevant
	 */
	public static function getIntance($place_str, $gedcom_id){
		global $WT_TREE;
		if (empty($gedcom_id)) {
			$gedcom_id = $WT_TREE->getTreeId();
		}
		$dplace = null;
		if(strlen($place_str) > 0){
			$dplace = new PersoPlace(new Place($place_str, $gedcom_id));
		}
		return $dplace;
	}
	
	/**
	 * Return the native place record embedded within the decorator
	 *
	 * @return Place Embedded place record
	 */
	public function getDerivedPlace(){
		return $this->_place;
	}
	
	/**
	 * Return HTML code for the place formatted as requested.
	 * The format string should used %n with n to describe the level of division to be printed (in the order of the GEDCOM place).
	 * For instance "%1 (%2)" will display "Subdivision (Town)".
	 *
	 * @param string $format Format for the place
	 * @param bool $anchor Option to print a link to placelist
	 * @return string HTML code for formatted place
	 */
	public function getFormattedName($format, $anchor = false){
		global $SEARCH_SPIDER;
		
		$html='';
		
		$levels = explode(', ', $this->_place->getGedcomName());
		$nbLevels = count($levels);
		$displayPlace = $format;
		preg_match_all('/%[^%]/', $displayPlace, $matches);
		foreach ($matches[0] as $match2) {
			$index = str_replace('%', '', $match2);
			if(is_numeric($index) && $index >0 && $index <= $nbLevels){
				$displayPlace = str_replace($match2, $levels[$index-1] , $displayPlace);
			}
			else{
				$displayPlace = str_replace($match2, '' , $displayPlace);
			}
		}
		if ($anchor && !$SEARCH_SPIDER) {
			$html .='<a href="' . $this->_place->getURL() . '">' . $displayPlace . '</a>';
		} else {
			$html .= $displayPlace;
		}
		
		return $html;
		
	}
	
	/**
	 * Return HTML code for the place formatted as requested.
	 * The format string should used %n with n to describe the level of division to be printed (in the order of the GEDCOM place).
	 * For instance "%1 (%2)" will display "Subdivision (Town)".
	 *
	 * @param string $format Format for the place
	 * @param bool $anchor Option to print a link to placelist
	 * @return string HTML code for formatted place
	 */
	public function htmlFormattedName($format, $anchor = false){		
		$html='';
		
		$levels = array_map('trim', explode(',', $this->_place->getGedcomName()));
		$nbLevels = count($levels);
		$displayPlace = $format;
		preg_match_all('/%[^%]/', $displayPlace, $matches);
		foreach ($matches[0] as $match2) {
			$index = str_replace('%', '', $match2);
			if(is_numeric($index) && $index >0 && $index <= $nbLevels){
				$displayPlace = str_replace($match2, $levels[$index-1] , $displayPlace);
			}
			else{
				$displayPlace = str_replace($match2, '' , $displayPlace);
			}
		}
		if ($anchor && !Auth::isSearchEngine()) {
			$html .='<a href="' . $this->_place->getURL() . '">' . $displayPlace . '</a>';
		} else {
			$html .= $displayPlace;
		}
		
		return $html;
		
	}
}
