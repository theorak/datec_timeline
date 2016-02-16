<?php
return array(
	'ctrl' => array(
		'title' => 'LLL:EXT:datec_timeline/Resources/Private/Language/locallang.xlf:tx_datectimeline_domain_model_date',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => 1,
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'searchFields' => 'title,description,cruser_id',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('datec_timeline') . 'Resources/Public/Icons/tx_datectimeline_domain_model_date.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'title,description,start,stop,participants,reminder_start',
	),
	'types' => array(
		'1' => array('showitem' => '
			--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.general;general,
            	title,
                description;LLL:EXT:datec_timeline/Resources/Private/Language/locallang.xlf:tx_datectimeline_domain_model_date.description;;richdescription:rte_transform[flag=rte_enabled|mode=ts_css],
				cruser_id,
				start,
				stop,
				reminder_start,
				participants,
            --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,
            	hidden
		'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'crdate' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.crdate',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),		
		'cruser_id' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:datec_timeline/Resources/Private/Language/locallang.xlf:tx_datectimeline_domain_model_date.cruser',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'fe_users',
				'foreign_label' => 'username',
				'items' => array(
					array('',0)
				),
				'eval' => 'required',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			),
		),
		'start' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:datec_timeline/Resources/Private/Language/locallang.xlf:tx_datectimeline_domain_model_date.start',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
			),
		),
		'stop' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:datec_timeline/Resources/Private/Language/locallang.xlf:tx_datectimeline_domain_model_date.stop',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
			),
		),
		'reminder_start' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:datec_timeline/Resources/Private/Language/locallang.xlf:tx_datectimeline_domain_model_date.reminderStart',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:datec_timeline/Resources/Private/Language/locallang.xlf:tx_datectimeline_domain_model_date.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'description' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:datec_timeline/Resources/Private/Language/locallang.xlf:tx_datectimeline_domain_model_date.description',
			'config' => array(
				'type' => 'text',
				'cols' => 30,
				'rows' => 5,
				'eval' => '',	
			),
			'defaultExtras' => 'richdescription[]:rte_transform[flag=rte_enabled|mode=ts_css]'
		),
		'participants' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:datec_timeline/Resources/Private/Language/locallang.xlf:tx_datectimeline_domain_model_date.participants',
			'config' => array(
				'type'          => 'group',
				'internal_type' => 'db',			
				'foreign_table' => 'fe_users',
				'allowed'       => 'fe_users',
				'MM'            => 'tx_datectimeline_domain_model_date_fe_users_mm',
				'size' => 5,
				'autoSizeMax' => 10,	
				'minitems' => 0,
				'maxitems' => 999,
			),
		),
	),
);

?>