<?php
/**
 * Decorator class to extend native Family class.
 *
 * @package webtrees
 * @subpackage PersoLibrary
 * @author: Jonathan Jaubart ($Author: webtrees.geneajaubart $)
 * @version: p_$Revision: 74 $ $Date: 2013-11-23 11:50:07 +0000 (Sat, 23 Nov 2013) $
 * $HeadURL: file:///mnt/atl-fs8-data1/svn/webtrees-geneajaubart/trunk/library/WT/Perso/Family.php $
 */

namespace Wooc\WebtreesAddOns\Perso;

use Fisharebest\Webtrees\Family;
use Wooc\WebtreesAddOns\Perso\PersoGedcomRecord;

class PersoFamily extends PersoGedcomRecord {

	// Cached results from various functions.
	protected $_ismarriagesourced = null;
	
	/**
	 * Extend WT_Family getInstance, in order to retrieve directly a PersoFamily object 
	 *
	 * @param unknown_type $data Data to identify the individual
	 * @return PersoFamily|null PersoFamily instance
	 */
	public static function getIntance($data){
		$dfam = null;
		$fam = Family::getInstance($data);
		if($fam){
			$dfam = new PersoFamily($fam);
		}
		return $dfam;
	}
	
	/**
	* Check if this family's marriages are sourced
	*
	* @return int Level of sources
	* */
	function isMarriageSourced(){
		if($this->_ismarriagesourced != null) return $this->_ismarriagesourced;
		$this->_ismarriagesourced = $this->isFactSourced(WT_EVENTS_MARR.'|MARC');
		return $this->_ismarriagesourced;
	}
}
