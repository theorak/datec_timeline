<?php
namespace Datec\DatecTimeline\Task;


class CleanupTaskAdditionalFieldProvider implements \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface {
	
	/**
	 * Default period in days after wich to cleanup
	 *
	 * @var integer Default number of days
	 */
	protected $defaultNumberOfDays = 30;
	
	public function getAdditionalFields(array &$taskInfo, $task, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject) {
		// Initialize selected fields
		if (empty($taskInfo['date_cleanup_numberOfDays'])) {
			$taskInfo['date_cleanup_numberOfDays'] = $this->defaulNumerOfDays;
			if ($parentObject->CMD === 'edit') {
				$taskInfo['date_cleanup_numberOfDays'] = $task->numberOfDays;
			}
		}
		
		$fieldName = 'tx_scheduler[date_cleanup_numberOfDays]';
		$fieldId = 'task_cleanup_numberOfDays';
		$fieldValue = (int)$taskInfo['date_cleanup_numberOfDays'];
		$fieldHtml = '<input type="text" name="' . $fieldName . '" id="' . $fieldId . '" value="' . htmlspecialchars($fieldValue) . '" />';
		
		$additionalFields[$fieldId] = array(
			'code' => $fieldHtml,
			'label' => 'LLL:EXT:datec_timeline/Resources/Private/Language/locallang.xlf:tx_datectimeline.tasks.fields.numberOfDays',
			'cshKey' => '',
			'cshLabel' => $fieldId
		);
		
		return $additionalFields;
	}
	
	public function validateAdditionalFields(array &$submittedData, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject) {
		$result = TRUE;
		// Check if number of days is indeed a number and greater or equals to 0
		// If not, fail validation and issue error message
		if (!is_numeric($submittedData['date_cleanup_numberOfDays'])) {
			$result = FALSE;
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:datec_timeline/Resources/Private/Language/locallang.xlf:tx_datectimeline.tasks.fields.numberOfDays.invalid'), \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
		}
		return $result;
	}
	
	public function saveAdditionalFields(array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task) {
		$task->numberOfDays = (int)$submittedData['date_cleanup_numberOfDays'];
	}
	
}