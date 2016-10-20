<?php
/**
 * Additional functions for Sosa (based on sosa module)
 *
 * @package webtrees
 * @subpackage PersoLbrary
 * @author: Jonathan Jaubart ($Author: webtrees.geneajaubart $)
 * @version: p_$Revision: 65 $ $Date: 2013-03-09 12:18:52 +0000 (Sat, 09 Mar 2013) $
 * $HeadURL: file:///mnt/atl-fs8-data1/svn/webtrees-geneajaubart/trunk/library/WT/Perso/Functions/Sosa.php $
*/
namespace Wooc\WebtreesAddOns\Perso\Functions;

use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Module;
use PDO;
use Wooc\WebtreesAddOns\Perso\PersoFunctions;

class FunctionsSosa {
	
	const TMP_SOSA_TABLE_LIMIT = 1000; 
	
	private static $_isModuleOperational = -1;
	private static $_statistictab = null;
	private static $_sosaListByGen = null;
	private static $_sosaFamListByGen = null;
	private static $_sosaListWithGen = null;
	
	/**
	 * Return whether the Sosa module is active and the table has been created. 
	 *
	 * @return bool True if module active and table created, false otherwise
	 */
	public static function isModuleOperational(){
		if(self::$_isModuleOperational == -1){
			self::$_isModuleOperational = Module::getModuleByName('perso_sosa');
			if(self::$_isModuleOperational){
				self::$_isModuleOperational = PersoFunctions::doesTableExist('##psosa');
			}
		}
		return self::$_isModuleOperational;
	}
	
	/**
	 * Remove all sosa entries for a specific gedcom file
	 *
	 * @param int $ged_id ID of the gedcom file
	 */
	public static function deleteAllSosas($ged_id) {			
		Database::prepare('DELETE FROM ##psosa WHERE ps_file=?')
			->execute(array($ged_id));
	}

	/**
	 * Return the list of all sosas, with the generations it belongs to
	 *
	 * @param int $ged_id ID of the gedcom file
	 * @return array Associative array of Sosa ancestors, with their generation, comma separated
	 */
	public static function getAllSosaWithGenerations($ged_id){
		if(!self::$_sosaListWithGen) self::$_sosaListWithGen= array();
		if($ged_id){
			self::$_sosaListWithGen = Database::prepare('SELECT ps_i_id AS indi, GROUP_CONCAT(DISTINCT ps_gen ORDER BY ps_gen ASC SEPARATOR ",") AS generations FROM ##psosa WHERE ps_file=? GROUP BY ps_i_id')
				->execute(array($ged_id))
				->fetchAssoc();
		}
		return self::$_sosaListWithGen;
	}
	
	/**
	 * Returns the generation associated with a Sosa number
	 *
	 * @param int $sosa Sosa number
	 * @return number
	 */
	public static function getGeneration($sosa){
		return(int)log($sosa, 2)+1;
	}
	
	/**
	 * Get the last generation of Sosa ancestors
	 * 
	 * @return number Last generation if found, 1 otherwise
	 */
	public static function getLastGeneration(){
		global $WT_TREE;
		return Database::prepare('SELECT MAX(ps_gen) FROM ##psosa WHERE ps_file=?')
			->execute(array($WT_TREE->getTreeId()))->fetchOne(1);
	}
	
	/**
	 * Get an associative array of Sosa individuals in generation G. Keys are Sosa numbers, values individuals.
	 *
	 * @param number $gen Generation
	 * @return array|null Array of Sosa individuals
	 */
	public static function getSosaListAtGeneration($gen){
		global $WT_TREE;
		if(!self::$_sosaListByGen) self::$_sosaListByGen= array();
		if($gen){
			if(!isset(self::$_sosaListByGen[$gen])){
				self::$_sosaListByGen[$gen] = Database::prepare('SELECT ps_sosa AS sosa, ps_i_id AS indi FROM ##psosa WHERE ps_file=? AND ps_gen = ? ORDER BY ps_sosa ASC')
				->execute(array($WT_TREE->getTreeId(), $gen))
				->fetchAssoc();
			}
			return self::$_sosaListByGen[$gen];
		}
		return null;
	}
	
	/**
	 * Get an associative array of Sosa families in generation G. Keys are Sosa numbers for the husband, values families.
	 *
	 * @param number $gen Generation
	 * @return array|null Array of Sosa families
	 */
	public static function getFamilySosaListAtGeneration($gen){
		global $WT_TREE;
		if(!self::$_sosaFamListByGen) self::$_sosaFamListByGen= array();
		if($gen){
			if(!isset(self::$_sosaFamListByGen[$gen])){
				self::$_sosaFamListByGen[$gen] = Database::prepare(
						'SELECT s1.ps_sosa AS sosa, f_id AS fam'.
						' FROM ##families'.
						' INNER JOIN ##psosa AS s1 ON (##families.f_husb = s1.ps_i_id AND ##families.f_file = s1.ps_file)'.
						' INNER JOIN ##psosa AS s2 ON (##families.f_wife = s2.ps_i_id AND ##families.f_file = s2.ps_file)'.
						' WHERE s1.ps_sosa + 1 = s2.ps_sosa'.
						' AND s1.ps_file=? AND s1.ps_gen = ? ORDER BY s1.ps_sosa ASC'
					)
				->execute(array($WT_TREE->getTreeId(), $gen))
				->fetchAssoc();
			}
			return self::$_sosaFamListByGen[$gen];
		}
		return null;
	}
	
	/**
	 * Get the statistic array detailed by generation.
	 * Statistics for each generation are:
	 * 	- The number of Sosa in generation
	 * 	- The number of Sosa up to generation
	 *  - The number of distinct Sosa up to generation
	 *  - The year of the first birth in generation
	 *  - The year of the last birth in generation
	 *  - The average year of birth in generation
	 *
	 * @return array Statistics array
	 */
	public static function getStatisticsByGeneration(){
		if(!self::$_statistictab){
			$maxGeneration = self::getLastGeneration();
			self::$_statistictab = array();
			for ($gen = 1; $gen <= $maxGeneration; $gen++) {
				$birthStats = self::getStatsBirthYearInGeneration($gen);
				self::$_statistictab[$gen] = array(
					'sosaCount'				=>	self::getSosaCountAtGeneration($gen),
					'sosaTotalCount'		=>	self::getSosaCountUpToGeneration($gen),
					'diffSosaTotalCount'	=>	self::getDifferentSosaCountUpToGeneration($gen),
					'firstBirth'			=>	$birthStats['first'],
					'lastBirth'				=>	$birthStats['last'],
					'avgBirth'				=>	$birthStats['avg']
				);
			}
		}
		return self::$_statistictab;
	}
	
	/**
	 * Get the total Sosa count for all generations
	 *
	 * @return number Number of Sosas
	 */
	public static function getSosaCount(){
		global $WT_TREE;
		return Database::prepare('SELECT COUNT(ps_sosa) FROM ##psosa WHERE ps_file=?')
			->execute(array($WT_TREE->getTreeId()))->fetchOne(0);
	}
	
	/**
	 * Get the number of Sosa in a specific generation.
	 *
	 * @param number $gen Generation
	 * @return number Number of Sosas in generation
	 */
	public static function getSosaCountAtGeneration($gen){
		global $WT_TREE;
		return Database::prepare('SELECT COUNT(ps_sosa) FROM ##psosa WHERE ps_file=? AND ps_gen=?')
			->execute(array($WT_TREE->getTreeId(), $gen))->fetchOne(0);
	}
	
	/**
	 * Get the total number of Sosa up to a specific generation.
	 *
	 * @param number $gen Generation
	 * @return number Total number of Sosas up to generation
	 */
	public static function getSosaCountUpToGeneration($gen){
		global $WT_TREE;
		return Database::prepare('SELECT COUNT(ps_sosa) FROM ##psosa WHERE ps_file=? AND ps_gen<=?')
			->execute(array($WT_TREE->getTreeId(), $gen))->fetchOne(0);
	}
	
	/**
	 * Get the total number of distinct Sosa individual for all generations.
	 *
	 * @return number Total number of distinct individual
	 */
	public static function getDifferentSosaCount(){
		global $WT_TREE;
		return Database::prepare('SELECT COUNT(DISTINCT ps_i_id) FROM ##psosa WHERE ps_file=?')
			->execute(array($WT_TREE->getTreeId()))->fetchOne(0);
	}
	
	/**
	 * Get the number of distinct Sosa individual up to a specific generation.
	 *
	 * @param number $gen Generation
	 * @return number Number of distinct Sosa individuals up to generation
	 */
	public static function getDifferentSosaCountUpToGeneration($gen){
		global $WT_TREE;
		return Database::prepare('SELECT COUNT(DISTINCT ps_i_id) FROM ##psosa WHERE ps_file=? AND ps_gen<=?')
			->execute(array($WT_TREE->getTreeId(), $gen))->fetchOne(0);
	}
	
	/**
	 * Get an array of birth statistics for a specific generation
	 * Statistics are : 
	 * 	- first : First birth year in generation
	 *  - last : Last birth year in generation
	 *  - avg : Average birth year
	 *
	 * @param number $gen Generation
	 * @return array Birth statistics array
	 */
	public static function getStatsBirthYearInGeneration($gen){
		global $WT_TREE;
		$birthStats = Database::prepare('SELECT MIN(ps_birth_year) AS first, AVG(ps_birth_year) AS avg, MAX(ps_birth_year) AS last FROM ##psosa WHERE ps_file=? AND ps_gen=? AND NOT ps_birth_year = ?')
			->execute(array($WT_TREE->getTreeId(), $gen, 0))->fetchOneRow(PDO::FETCH_ASSOC);
		if($birthStats) return $birthStats;
		return array('first' => 0, 'avg' => 0, 'last' => 0);
	}
	
	/**
	 * Get the mean generation time, based on a linear regression of birth years and generations
	 *
	 * @return number|NULL Mean generation time
	 */
	public static function getMeanGenerationTime(){
		if(!self::$_statistictab){
			self::getStatisticsByGeneration();
		}
		//Linear regression on x=generation and y=birthdate
		$sum_xy = 0;
		$sum_x=0;
		$sum_y=0;
		$sum_x2=0;
		$n=count(self::$_statistictab);
		foreach(self::$_statistictab as $gen=>$stats){
			$sum_xy+=$gen*$stats['avgBirth'];
			$sum_x+=$gen;
			$sum_y+=$stats['avgBirth'];
			$sum_x2+=$gen*$gen;
		}
		$denom=($n*$sum_x2)-($sum_x*$sum_x);
		if($denom!=0){
			return -(($n*$sum_xy)-($sum_x*$sum_y))/($denom);
		}
		return null;
	}
	
	
	/**
	 * Write sosas in the table, if the number of items is superior to the limit, or if forced.
	 *
	 * @param bool $force Should the flush be forced
	 */
	public static function flushTmpSosaTable($force = false){
		global $tmp_sosatable;
		
		if(count($tmp_sosatable)>0 && ($force ||  count($tmp_sosatable) >= self::TMP_SOSA_TABLE_LIMIT)){
			$questionmarks_table = array();
			$values_table = array();
			foreach  ($tmp_sosatable as $row) {
				$questionmarks_table[] = '(?, ?, ?, ?, ?, ?)';
				$values_table = array_merge($values_table, $row);
			}
			$sql = 'REPLACE INTO ##psosa (ps_i_id, ps_file, ps_sosa, ps_gen, ps_birth_year, ps_death_year) VALUES '.implode(',', $questionmarks_table);
			Database::prepare($sql)
				->execute($values_table);
			$tmp_sosatable = array();
		}
	}
	
	/**
	 * Remove sosas from the table, if the number of items is superior to the limit, or if forced.
	 *
	 * @param bool $force Should the flush be forced
	 */
	public static function flushTmpRemoveTable($force = false){
		global $WT_TREE, $tmp_removeSosaTab;
		
		if(count($tmp_removeSosaTab)>0 && ($force ||  count($tmp_removeSosaTab) >= self::TMP_SOSA_TABLE_LIMIT)){
			$questionmarks_table = array();
			for ($i = 0; $i < count($tmp_removeSosaTab); $i++) {
				$questionmarks_table[] = 'ps_sosa = ?';
			}
			$sql = 'DELETE FROM ##psosa WHERE ps_file = ? AND ('.implode(' OR ', $questionmarks_table).')';
			Database::prepare($sql)
				->execute(array_merge(array($WT_TREE->getTreeId()), $tmp_removeSosaTab));
			$tmp_removeSosaTab = array();
		}
	}
}