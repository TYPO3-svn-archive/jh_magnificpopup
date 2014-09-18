<?php
namespace Heilmann\JhMagnificpopup\Core;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Core\Bootstrap;
use TYPO3\CMS\Extbase\Service\TypoScriptService;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Utility\EidUtility;

/**
 * This class is basically taken from:
 * https://lbrmedia.net/ajaxexample/
 *
 * I would not recommend to use it like this, it is just here to demostrate
 * that even with a crippled frontend bootstrap there will be no major performance gain...
 */
class EidRequest {

	/**
	 * @var TypoScriptFrontendController $typoScriptFrontendController
	 */
	protected $typoScriptFrontendController = NULL;

	/**
	 *
	 *
	 * @return string
	 */
	public function run() {
		$cObject = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
		$gp = GeneralUtility::_GP('jh_magnificpopup');
		//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['TSFE']->tmpl->setup['tt_content.']);
		//http://lists.typo3.org/pipermail/typo3-german/2011-July/079128.html
		switch ($gp['type']) {
			case 'inline':
				$cObjConfig = array(
					'name'	=>	'CONTENT',
					'conf'	=> array(
							'table' 	=>	'tt_content',
							'select.'	=> array(
								'where'		=> 'tx_jhmagnificpopup_irre_parentid='.$gp['irre_parrentid'],
								'pidInList'	=> GeneralUtility::_GP('id'),
								'languageField'	=> 'sys_language_uid',
								'orderBy'	=> 'sorting',
							),
							'wrap'	=> '<div class="white-popup-block">|</div>',
							'renderObj'	=> $GLOBALS['TSFE']->tmpl->setup['tt_content'],
							'renderObj.'	=> $GLOBALS['TSFE']->tmpl->setup['tt_content.'],
					),
				);
				break;
			case 'reference':
				$cObjConfig = array(
					'name'	=>	'CONTENT',
					'conf'	=> array(
							'table' 	=>	'tt_content',
							'select.'	=> array(
								'uidInList'		=> $gp['uid'],
								'pidInList'	=> $gp['pid'],
								'orderBy'	=> 'sorting',
								'languageField'	=> 'sys_language_uid',
							),
							'wrap'	=> '<div class="white-popup-block">|</div>',
							'renderObj'	=> $GLOBALS['TSFE']->tmpl->setup['tt_content'],
							'renderObj.'	=> $GLOBALS['TSFE']->tmpl->setup['tt_content.'],
					),
				);
				break;
			default:
				if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['jh_magnificpopup']['EidTypeHook']) {
				   if (!isset($gp['hookConf'])) $gp['hookConf'] = '';
				   $params = array(
				   	'type' => $gp['type'],
				   	'hookConf' => $gp['hookConf']
				   );
				   foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['jh_magnificpopup']['EidTypeHook'] as $_funcRef) {
				      if ($_funcRef) {
				         $cObjConfig = GeneralUtility::callUserFunction($_funcRef, $params, $this);
				         if (isset($cObjConfig['matchedType']) && $cObjConfig['matchedType'] === TRUE) {
				         	break;
				         }
				      }
				   }
				}
		}
		if (!empty($cObjConfig) && is_array($cObjConfig)) {
			$this->typoScriptFrontendController->content = $cObject->getContentObject($cObjConfig['name'])->render($cObjConfig['conf']);
		} else {
			$this->typoScriptFrontendController->content = 'ERROR';
		}

		if ($GLOBALS['TSFE']->isINTincScript()) {
			$GLOBALS['TSFE']->INTincScript();
		}

		if (isset($cObjConfig['wrap']) && !empty($cObjConfig['wrap'])) {
			$this->typoScriptFrontendController->content = $cObject->wrap($this->typoScriptFrontendController->content, $cObjConfig['wrap']);
		}

		return $this->typoScriptFrontendController->content;
	}

	/**
	 * Initialize frontend environment
	 *
	 * @return void
	 */
	public function __construct() {
		$this->bootstrap = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Core\\Bootstrap');

		$feUserObj = EidUtility::initFeUser();

		$pageId = GeneralUtility::_GET('id') ?: 1;
		$pageType = GeneralUtility::_GET('type') ?: 0;

		/** @var TypoScriptFrontendController $typoScriptFrontendController */
		$this->typoScriptFrontendController = GeneralUtility::makeInstance(
			'TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController',
			$GLOBALS['TYPO3_CONF_VARS'],
			$pageId,
			$pageType,
			TRUE
		);
		$GLOBALS['TSFE'] = $this->typoScriptFrontendController;
		$this->typoScriptFrontendController->connectToDB();
		$this->typoScriptFrontendController->fe_user = $feUserObj;
		$this->typoScriptFrontendController->id = $pageId;
		$this->typoScriptFrontendController->determineId();
		$this->typoScriptFrontendController->getCompressedTCarray();
		$this->typoScriptFrontendController->initTemplate();
		$this->typoScriptFrontendController->getConfigArray();
		$this->typoScriptFrontendController->includeTCA();
		$this->typoScriptFrontendController->settingLanguage();
		$this->typoScriptFrontendController->settingLocale();
	}

}