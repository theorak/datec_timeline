<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Datec.' . $_EXTKEY,
	'Timeline',
	array(
		'Timeline' => 'showTimeline,loadDates,newDate,createDate,editDate,updateDate,deleteDate,showDate',
	),
	// non-cacheable actions
	array(
		'Timeline' => 'showTimeline,loadDates,newDate,createDate,editDate,updateDate,deleteDate,showDate',
	)
);

// Add caching framework garbage collection task
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Datec\DatecTimeline\Task\ReminderTask::class] = array(
		'extension' => $_EXTKEY,
		'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:tx_datectimeline.tasks.reminderTask.name',
		'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:tx_datectimeline.tasks.reminderTask.description',
		'additionalFields' => ''
);

?>