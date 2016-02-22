<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

/**
 * Add tt_content columns for Date Color
 */
$ttcontentColumns = array(
	'tx_datectimeline_date_color' => array(
		'label' => 'LLL:EXT:datec_timeline/Resources/Private/Language/locallang.xlf:fe_users.dateColor',
		'config' => array(				
			'type' => 'input',
            'size' => 10,
			'eval' => 'trim',
			'wizards' => array(
				'colorChoice' => array(
					'type' => 'colorbox',
					'title' => 'LLL:EXT:datec_timeline/Resources/Private/Language/locallang.xlf:tce.colorPicker',	
					'module' => array(
						'name' => 'wizard_colorpicker'
					),	
					'JSopenParams' => 'height=600,width=380,status=0,menubar=0,scrollbars=1'
				)
			)
		),
	),
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
	'fe_users', 
	$ttcontentColumns
);
unset($ttcontentColumns);

/**
 * Date Color
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', 'tx_datectimeline_date_color', 'Tx_Extbase_Domain_Model_FrontendUser', 'after:image');

?>