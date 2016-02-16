<?php
if(!defined('TYPO3_MODE')){
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Datec Timeline');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Datec.' . $_EXTKEY,
	'Timeline',
	'Datec Timeline'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_datectimeline_domain_model_date');

?>