<?php
namespace Datec\DatecTimeline\Task;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Datec\DatecTimeline\Domain\Model\Date;

/**
 * Task to delete Dates by set date parameters completly, thus cleanup the database.
 * 
 * @author Philipp "Theorak" Roensch
 */
class CleanupTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask {

	/**
	 * $dateRepository
	 *
	 * @var \Datec\DatecTimeline\Domain\Repository\DateRepository
	 */
	protected $dateRepository;
	
	/**
	 * @var int
	 */
	public $numberOfDays;
	
	/**
	 * injection of DateRepository
	 */
	private function injectDateRepository() {
		$objectManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->dateRepository = $objectManager->get(\Datec\DatecTimeline\Domain\Repository\DateRepository::class);
	}
	
	/**
	 * @return boolean Success state of execution
	 */
	public function execute() {
		$this->injectDateRepository();
		
		$deleteDate = new \DateTime();
		$deleteDate->setTime(23, 59, 59);
		$deleteDate->modify('- '.$this->numberOfDays.' days');
		
		$datesDelete = $this->dateRepository->cleanupByDate($deleteDate->getTimestamp());		
		if (!$datesDelete) { // db error
			return FALSE;
		}		
		
		return TRUE;
	}
	
}
