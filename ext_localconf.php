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
?>