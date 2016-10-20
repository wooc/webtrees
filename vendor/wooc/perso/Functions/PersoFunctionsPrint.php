<?php
/**
 * Additional functions for displaying information
 *
 * @package webtrees
 * @subpackage PersoLibrary
 * @author: Jonathan Jaubart ($Author: webtrees.geneajaubart $)
 * @version: p_$Revision: 74 $ $Date: 2013-11-23 11:50:07 +0000 (Sat, 23 Nov 2013) $
 * $HeadURL: file:///mnt/atl-fs8-data1/svn/webtrees-geneajaubart/trunk/library/WT/Perso/Functions/Print.php $
 */

namespace Wooc\WebtreesAddOns\Perso\Functions;

use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;

class PersoFunctionsPrint {

	/**
	 * Get an array converted to a list. For example
	 * array("red", "green", "yellow", "blue") => "red, green, yellow and blue"
	 *
	 * @param array $array Array to convert
	 * @return string List of elements
	 */
	static public function getListFromArray($array) {
		$n=count($array);
		switch ($n) {
			case 0:
				return '';
			case 1:
				return $array[0];
			default:
				return implode(I18N::noop(', '), array_slice($array, 0, $n-1)).I18N::noop(' and ').$array[$n-1];
		}
	}

	/**
	 * Return HTML code to include a flag icon in facts description
	 *
	 * @param Fact $factrec Fact record
	 * @return string HTML code of the inserted flag
	 */
	public static function getFactPlaceIcon(Fact $fact) {
		$html='';
		$iconPlace=PersoFunctions_Map::getPlaceIcon($fact->getPlace(), 50);
		if(strlen($iconPlace) > 0){
			$html.='<div class="fact_flag">'.$iconPlace.'</div>';
		}
		return $html;
	}

	/**
	 * Returns HTML code to include a place cloud
	 *
	 * @param array $places Array of places to display in the cloud
	 * @param bool $totals Display totals for a place
	 * @return string Place Cloud HTML Code
	 */
	public static function getPlacesCloud($places, $totals) {

		$cloud=new Zend_Tag_Cloud(
			array(
				'tagDecorator'=>array(
					'decorator'=>'HtmlTag',
					'options'=>array(
						'htmlTags'=>array(),
						'fontSizeUnit'=>'%',
						'minFontSize'=>100,
						'maxFontSize'=>180
					)
				),
				'cloudDecorator'=>array(
					'decorator'=>'HtmlCloud',
					'options'=>array(
						'htmlTags'=>array(
							'div'=>array(
								'class'=>'tag_cloud'
							)
						)
					)
				)
			)
		);
		foreach ($places as $place=>$count) {
			$dplace = WT_Perso_Place::getIntance($place, $WT_TREE->getTreeId());
			$shortplace = $dplace->getFormattedName('%1 (%2)');
			$cloud->appendTag(array(
				'title'=>$totals ? I18N::translate('%1$s (%2$d)', $shortplace, $count) : $shortplace,
				'weight'=>$count,
				'params'=>array(
					'url'=> $dplace->getDerivedPlace()->getURL()
				)
			));
		}
		return (string)$cloud;
	}

	/**
	 * Return HTML Code to display individual in non structured list (e.g. Patronymic Lineages)
	 *
	 * @param Individual $individual Individual to print
	 * @param bool $isStrong Bolden the name ?
	 * @return string HTML Code for individual item
	 */
	public static function getIndividualForList(Individual $individual, $isStrong = true){
		$html = '';
		$tag = 'em';
		if($isStrong) $tag = 'strong';
		if($individual && $individual->canShow()){
			$dindi = new PersoIndividual($individual);
			$html = $individual->getSexImage();
			$html .= '<a class="list_item" href="'.
			$individual->getHtmlUrl().
				'" title="'.
			I18N::translate('Informations for individual %s', $individual->getXref()).
				'">';
			$html .= '<'.$tag.'>'.$individual->getFullName().'</'.$tag.'>&nbsp;('.$individual->getXref().')&nbsp;';
			$html .= PersoFunctions_Print::formatSosaNumbers($dindi->getSosaNumbers(), 1, 'small');
			$html .= '&nbsp;<span><small><em>'.$dindi->format_first_major_fact(WT_EVENTS_BIRT, 10).'</em></small></span>';
			$html .= '&nbsp;<span><small><em>'.$dindi->format_first_major_fact(WT_EVENTS_DEAT, 10).'</em></small></span>';
			$html .= '</a>';
		}
		else {
			$html .= '<span class=\"list_item\"><'.$tag.'>'.I18N::translate('Private').'</'.$tag.'></span>';
		}
		return $html;
	}

	/**
	 * Format date to display short (just years)
	 *
	 * @param WT_Fact $eventObj Fact to display date
	 * @param boolean $anchor option to print a link to calendar
	 * @return string HTML code for short date
	 */
	public static function formatFactDateShort(Fact $fact, $anchor=false) {
		global $SEARCH_SPIDER;

		$html='';
		$date = $fact->getDate();
		if($date->isOK()){
			$html.=' '.$date->Display($anchor && !$SEARCH_SPIDER, '%Y');
		}
		else{
			// 1 DEAT Y with no DATE => print YES
			// 1 BIRT 2 SOUR @S1@ => print YES
			// 1 DEAT N is not allowed
			// It is not proper GEDCOM form to use a N(o) value with an event tag to infer that it did not happen.
			$factdetail = explode(' ', trim($fact->getGedcom()));
			if (isset($factdetail) && (count($factdetail) == 3 && strtoupper($factdetail[2]) == 'Y') || (count($factdetail) == 4 && $factdetail[2] == 'SOUR')) {
				$html.=I18N::translate('yes');
			}
		}
		return $html;
	}

	/**
	 * Format fact place to display short
	 *
	 * @param Fact $eventObj Fact to display date
	 * @param string $format Format of the place
	 * @param boolean $anchor option to print a link to placelist
	 * @return string HTML code for short place
	 */
	public static function formatFactPlaceShort(Fact $fact, $format, $anchor=false){
		$html='';
		
		if ($fact==null) return $html;
		$place = $fact->getPlace();
		if($place){
			$dplace = new WT_Perso_Place($place);
			$html .= $dplace->getFormattedName($format, $anchor);
		}
		return $html;
	}

	/**
	 * Format Sosa number to display next to individual details
	 * Possible format are:
	 * 	- 1 (default) : display an image if the individual is a Sosa, independtly of the number of times he is
	 * 	- 2 : display a list of Sosa numbers, with an image, separated by an hyphen.
	 *
	 * @param array $sosatab List of Sosa numbers
	 * @param int $format Format to apply to the Sosa numbers
	 * @param string $size CSS size for the icon. A CSS style css_$size is required
	 * @return string HTML code for the formatted Sosa numbers
	 */
	public static function formatSosaNumbers($sosatab, $format = 1, $size = 'small'){
		$html = '';
		switch($format){
			case 1:
				if($sosatab && count($sosatab)>0){
					$html = '<i class="icon-perso-sosa_'.$size.'" title="'.I18N::translate('Sosa').'"></i>';
				}
				break;
			case 2:
				if($sosatab && count($sosatab)>0){
					ksort($sosatab);
					$tmp_html = array();
					foreach ($sosatab as $sosa => $gen) {
						$tmp_html[] = '<i class="icon-perso-sosa_'.$size.'" title="'.I18N::translate('Sosa').'"></i>&nbsp;<strong>'.$sosa.'&nbsp;'.I18N::translate('(G%s)', $gen).'</strong>';
					}
					$html = implode(' - ', $tmp_html);
				}
				break;
			default:
				break;
		}
		return $html;
	}

	/**
	 * Format IsSourced icons for display
	 * Possible format are:
	 * 	- 1 (default) : display an icon depending on the level of sources
	 *
	 * @param string $sourceType Type of the record : 'E', 'R'
	 * @param int $isSourced Level to display
	 * @param string $tag Fact to display status
	 * @param int $format Format to apply to the IsSourced parameter
	 * @param string $size CSS size for the icon. A CSS style css_$size is required
	 * @return string HTML code for IsSourced icon
	 */
	public static function formatIsSourcedIcon($sourceType, $isSourced, $tag='EVEN', $format = 1, $size='normal'){
		$html='';
		$image=null;
		$title=null;
		switch($format){
			case 1:
				switch($sourceType){
					case 'E':
						switch($isSourced){
							case 0:
								$image = 'event_unknown';
								$title = I18N::translate('%s not found', GedcomTag::getLabel($tag));
								break;
							case -1:
								$image = 'event_notprecise';
								$title = I18N::translate('%s not precise', GedcomTag::getLabel($tag));
								break;
							case -2:
								$image = 'event_notsourced';
								$title = I18N::translate('%s not sourced', GedcomTag::getLabel($tag));
								break;
							case 1:
								$image = 'event_sourced';
								$title = I18N::translate('%s sourced', GedcomTag::getLabel($tag));
								break;
							case 2:
								$image = 'event_sourcedcertif';
								$title = I18N::translate('%s sourced with a certificate', GedcomTag::getLabel($tag));
								break;
							case 3:
								$image = 'event_sourcedcertifdate';
								$title = I18N::translate('%s sourced with exact certificate', GedcomTag::getLabel($tag));
								break;
							default:
								break;
						}
						break;
					case 'R':
						switch($isSourced){
							case -1:
								$image = 'record_notsourced';
								$title = I18N::translate('%s not sourced', GedcomTag::getLabel($tag));
								break;
							case 1:
								$image = 'record_sourced';
								$title = I18N::translate('%s sourced', GedcomTag::getLabel($tag));
								break;
							case 2:
								$image = 'record_sourcedcertif';
								$title = I18N::translate('%s sourced with a certificate', GedcomTag::getLabel($tag));
								break;
							default:
								break;
						}
						break;
						break;
					default:
						break;
				}
				if($image && $title) $html = '<i class="icon-perso-sourced-'.$size.'_'.$image.'" title="'.$title.'"></i>';
				break;
			default:
				break;
		}
		return $html;
	}
}
