<?php
namespace TYPO3\JhMagnificpopup\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Jonathan Heilmann <mail@jonathan-heilmann.de>, Webprogrammierung Jonathan Heilmann
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

 use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 *
 *
 * @package jh_magnificpopup
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class MagnificpopupController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * magnificpopupRepository
	 *
	 * @var \TYPO3\JhMagnificpopup\Domain\Repository\magnificpopupRepository
	 * @inject
	 */
	protected $magnificPopupRepository;

	/**
	 * action show
	 *
	 * @return void
	 */
	public function showAction() {
		// Flush all messages
		$this->flashMessageContainer->flush();
		// Assign multiple values
		$this->extkey = 'jh_magnificpopup';
		$viewAssign = array();
		$this->cObj = $this->configurationManager->getContentObject();
		$this->data = $this->cObj->data;

		switch ($this->settings['contenttype']) {
			case 'iframe':
				$viewAssign['uid'] = $this->data['uid'];
				// Link-setup
				$viewAssign['link-class'] = 'mfp-iframe-'.$this->data['uid'];
				$viewAssign['link'] = $this->settings['mfpOption']['href'];
				$viewAssign['link-text'] = $this->settings['mfpOption']['text'];

				// Get settings from flexform
				// If something else than the default from setup is selected or a value is empty use setting from flexform
				foreach($this->settings['mfpOption'] as $key => $value) {
					if($value != -1 && !empty($value)) {
						if ($value == 'local') {
							$this->settings['type']['iframe'][$key] = $this->settings['mfpOption'][$key.'_local'];
						} else {
							$this->settings['type']['iframe'][$key] = $value;
						}
					}
				}
				// Render javascript
				$javascript = "
jQuery(document).ready(function($) {
	$('.mfp-iframe-".$this->data['uid']."').magnificPopup({
		type: 'iframe',
		tClose: '".LocalizationUtility::translate('iframe.tClose', $this->extkey)."',
		tLoading: '".LocalizationUtility::translate('iframe.tLoading', $this->extkey)."',
		disableOn: ".$this->settings['type']['iframe']['disableOn'].",
		mainClass: '".$this->settings['type']['iframe']['mainClass']."',
		preloader: ".$this->settings['type']['iframe']['preloader'].",
		closeOnContentClick: ".$this->settings['type']['iframe']['closeOnContentClick'].",
		closeOnBgClick: ".$this->settings['type']['iframe']['closeOnBgClick'].",
		closeBtnInside: ".$this->settings['type']['iframe']['closeBtnInside'].",
		showCloseBtn: ".$this->settings['type']['iframe']['showCloseBtn'].",
		enableEscapeKey: ".$this->settings['type']['iframe']['enableEscapeKey'].",
		modal: ".$this->settings['type']['iframe']['modal'].",
		alignTop: ".$this->settings['type']['iframe']['alignTop'].",
		fixedContentPos: '".$this->settings['type']['iframe']['fixedContentPos']."',
		fixedBgPos: '".$this->settings['type']['iframe']['fixedBgPos']."',
		overflowY: '".$this->settings['type']['iframe']['overflowY']."',
		removalDelay: ".$this->settings['type']['iframe']['removalDelay'].",
		closeMarkup: '".$this->settings['type']['iframe']['closeMarkup']."',
	});
});";
				// Wrap javascript
				$viewAssign['javascript'] = '<script type="text/javascript">'.trim($javascript).'</script>';
				break;
			case 'reference':
			case 'inline':
				$viewAssign['uid'] = $this->data['uid'];

				if(($this->settings['content']['procedure_reference'] == 'ajax'  && !empty($this->settings['contenttype'])) || $this->settings['content']['procedure_inline'] == 'ajax') {
					// Use ajax procedure
					$viewAssign['link-class'] = 'mfp-ajax-'.$this->data['uid'];
					if($this->settings['contenttype'] == 'reference') {
						// Get the list of pid's
						$uidList = $this->settings['content']['reference'];
						$uidArray = explode(',', $uidList);
						foreach($uidArray as $uid) {
							$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('pid', 'tt_content', 'uid='.$uid);
							$pidInList .= $row['pid'].',';
						}
						$pidInList = substr($pidInList, 0, -1);
						// Configure the link
						$linkconf = array(
							'parameter' => $this->data['pid'],
							'additionalParams' => '&type=109&jh_magnificpopup[type]=reference&jh_magnificpopup[uid]='.$this->settings['content']['reference'].'&jh_magnificpopup[pid]='.$pidInList
						);
					} else {
						// Configure the link
						$linkconf = array(
							'parameter' => $this->data['pid'],
							'additionalParams' => '&type=109&jh_magnificpopup[type]=inline&jh_magnificpopup[irre_parrentid]='.$this->data['uid']
						);
					}
					// Link-setup
					$viewAssign['link'] = $this->cObj->typolink_URL($linkconf);
					$viewAssign['link-text'] = $this->settings['mfpOption']['text'];

					// Get settings from flexform
					// If something else than the default from setup is selected or a value is empty use setting from flexform
					foreach($this->settings['mfpOption'] as $key => $value) {
						if($value != -1 && !empty($value)) {
							if ($value == 'local') {
								$this->settings['type']['ajax'][$key] = $this->settings['mfpOption'][$key.'_local'];
							} else {
								$this->settings['type']['ajax'][$key] = $value;
							}
						}
					}
					// Render javascript
					$javascript = "
jQuery(document).ready(function($) {
	$('.mfp-ajax-".$this->data['uid']."').magnificPopup({
		type: 'ajax',
		tClose: '".LocalizationUtility::translate('ajax.tClose', $this->extkey)."',
		tLoading: '".LocalizationUtility::translate('ajax.tLoading', $this->extkey)."',
		ajax: {
			cursor: '".$this->settings['type']['ajax']['ajax']['cursor']."',
			tError: '".LocalizationUtility::translate('ajax.ajax.tError', $this->extkey)."'
		},
		disableOn: ".$this->settings['type']['ajax']['disableOn'].",
		mainClass: '".$this->settings['type']['ajax']['mainClass']."',
		preloader: ".$this->settings['type']['ajax']['preloader'].",
		focus: '".$this->settings['type']['ajax']['focus']."',
		closeOnContentClick: ".$this->settings['type']['ajax']['closeOnContentClick'].",
		closeOnBgClick: ".$this->settings['type']['ajax']['closeOnBgClick'].",
		closeBtnInside: ".$this->settings['type']['ajax']['closeBtnInside']."0,
		showCloseBtn: ".$this->settings['type']['ajax']['showCloseBtn'].",
		enableEscapeKey: ".$this->settings['type']['ajax']['enableEscapeKey'].",
		modal: ".$this->settings['type']['ajax']['modal'].",
		alignTop: ".$this->settings['type']['ajax']['alignTop'].",
		fixedContentPos: '".$this->settings['type']['ajax']['fixedContentPos']."',
		fixedBgPos: '".$this->settings['type']['ajax']['fixedBgPos']."',
		overflowY: '".$this->settings['type']['ajax']['overflowY']."',
		removalDelay: ".$this->settings['type']['ajax']['removalDelay'].",
		closeMarkup: '".$this->settings['type']['ajax']['closeMarkup']."',
	});
});";
					// Wrap javascript
					$viewAssign['javascript'] = '<script type="text/javascript">'.trim($javascript).'</script>';
				} else if (($this->settings['content']['procedure_reference'] && !empty($this->settings['contenttype'])) == 'inline' || $this->settings['content']['procedure_inline'] == 'inline'){
					// Use inline procedure
					// Render irre content as inline-htmlcode
					if($this->settings['contenttype'] == 'reference') {
						//get list of pid's
						$uidList = $this->settings['content']['reference'];
						$uidArray = explode(',', $uidList);
						foreach($uidArray as $uid) {
							$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('pid', 'tt_content', 'uid='.$uid);
							$pidInList .= $row['pid'].',';
						}
						$pidInList = substr($pidInList, 0, -1);
						// Configure the content
						$irre_conf = array(
							'table'	=> 'tt_content',
							'select.'	=> array(
								'where' => 'tt_content.uid IN('.$this->settings['content']['reference'].')',
								'languageField' => 'sys_language_uid',
								'pidInList' => $pidInList,
								'orderBy' => 'sorting'
							)
						);
					} else {
						// Configure the content
						$irre_conf = array(
							'table'	=> 'tt_content',
							'select.'	=> array(
								'where' => 'deleted=0 AND hidden=0 AND tx_jhmagnificpopup_irre_parentid = '.$this->data['uid'],
								'languageField' => 'sys_language_uid',
								'orderBy' => 'sorting'
							)
						);
					}
					// Render inlinecontent
					$viewAssign['inlinecontent'] = $this->cObj->CONTENT($irre_conf);
					$viewAssign['inlinecontent_id'] = 'mfp-inline-'.$this->data['uid'];
					// Link-setup
					$viewAssign['link-class'] = 'mfp-inline-'.$this->data['uid'];
					$viewAssign['link'] = '#mfp-inline-'.$this->data['uid'];
					$viewAssign['link-text'] = $this->settings['mfpOption']['text'];

					// Get settings from flexform
					// If something else than the default from setup is selected or a value is empty use setting from flexform
					foreach($this->settings['mfpOption'] as $key => $value) {
						if($value != -1 && !empty($value)) {
							if ($value == 'local') {
								$this->settings['type']['inline'][$key] = $this->settings['mfpOption'][$key.'_local'];
							} else {
								$this->settings['type']['inline'][$key] = $value;
							}
						}
					}
					// Render javascript
					$javascript = "
jQuery(document).ready(function($) {
	$('.mfp-inline-".$this->data['uid']."').magnificPopup({
		type: 'inline',
		tClose: '".LocalizationUtility::translate('inline.tClose', $this->extkey)."',
		tLoading: '".LocalizationUtility::translate('inline.tLoading', $this->extkey)."',
		disableOn: ".$this->settings['type']['inline']['disableOn'].",
		mainClass: '".$this->settings['type']['inline']['mainClass']."',
		preloader: ".$this->settings['type']['inline']['preloader'].",
		focus: '".$this->settings['type']['inline']['focus']."',
		closeOnContentClick: ".$this->settings['type']['inline']['closeOnContentClick'].",
		closeOnBgClick: ".$this->settings['type']['inline']['closeOnBgClick'].",
		closeBtnInside: ".$this->settings['type']['inline']['closeBtnInside']."0,
		showCloseBtn: ".$this->settings['type']['inline']['showCloseBtn'].",
		enableEscapeKey: ".$this->settings['type']['inline']['enableEscapeKey'].",
		modal: ".$this->settings['type']['inline']['modal'].",
		alignTop: ".$this->settings['type']['inline']['alignTop'].",
		fixedContentPos: '".$this->settings['type']['inline']['fixedContentPos']."',
		fixedBgPos: '".$this->settings['type']['inline']['fixedBgPos']."',
		overflowY: '".$this->settings['type']['inline']['overflowY']."',
		removalDelay: ".$this->settings['type']['inline']['removalDelay'].",
		closeMarkup: '".$this->settings['type']['inline']['closeMarkup']."',
	});
});";
				// Wrap javascript
				$viewAssign['javascript'] = '<script type="text/javascript">'.trim($javascript).'</script>';
				} else if ($this->settings['content']['procedure_reference'] == '' && $this->settings['content']['procedure_inline'] == '') {
					// Add error if no method (inline or ajax) has been selected
					$this->flashMessageContainer->add('Please select the method (inline or ajax) to display Magnific Popup content','Select method', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
				} else if ($this->settings['content']['procedure_reference'] != '' && empty($this->settings['contenttype'])) {
					// Add error if no content has been selected
					$this->flashMessageContainer->add('Please select a content to display with Magnific Popup','Select content', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
				}
				break;
			default:
				// Add error if no "Display type" has been selected
				$this->flashMessageContainer->add('Please select a "Display type" to use Magnific Popup','Select "Display type"', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
		}

		// Assign array to fluid-template
		$this->view->assignMultiple($viewAssign);
	}
}
?>