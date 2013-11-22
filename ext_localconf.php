<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Configure frontend plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'TYPO3.' . $_EXTKEY,
	'Pi1',
	array(
		'Magnificpopup' => 'show'
	),
	array(
	)
);

// Save the IRRE content (use hook to change colPos)
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][$_EXTKEY] = 'EXT:'.$_EXTKEY.'/Classes/Hooks/class.tx_jhmagnificpopup_tcemain.php:&tx_jhmagnificpopup_tcemain';

// Page TSconfig for global enabled iframe
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
	RTE.classesAnchor {
	  externalLinkInMagnificpopup {
	    class = mfp-link external-link-new-window
	    type = url
	    image >
	    titleText = LLL:EXT:jh_magnificpopup/Resources/Private/Language/locallang.xml:pageTSconfig.externalLinkInMagnificpopup
	  }
	  internalLinkInMagnificpopup {
	    class = mfp-link internal-link-new-window
	    type = page
	    image >
	    titleText = LLL:EXT:jh_magnificpopup/Resources/Private/Language/locallang.xml:pageTSconfig.internalLinkInMagnificpopup
	  }
	  downloadInMagnificpopup {
	    class = mfp-link download
	    type = file
	    image >
	    titleText = LLL:EXT:jh_magnificpopup/Resources/Private/Language/locallang.xml:pageTSconfig.downloadInMagnificpopup
	  }
	}

	RTE.default.proc.allowedClasses := addToList(mfp-link external-link-new-window, mfp-link internal-link-new-window, mfp-link download)
	RTE.default.classesAnchor := addToList(mfp-link external-link-new-window, mfp-link internal-link-new-window, mfp-link download)

	RTE.default.buttons.link.properties.class.allowedClasses := addToList(mfp-link external-link-new-window, mfp-link internal-link-new-window, mfp-link download)
');
?>
