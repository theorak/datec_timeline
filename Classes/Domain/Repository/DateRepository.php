<?php
namespace Datec\DatecTimeline\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014
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

/**
 * Repository for date objects.
 *
 * @package datec_timeline
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class DateRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {	
	
	/**
	 * Search dates by range
	 * 
	 * @param int $start timestamp
	 * @param int $stop timestamp
	 * @param array $creatorIds (optional) uid list of creators
	 * @param array $creatorIds (optional) uid list of participants
	 */
	public function findByFilterCriteria($start, $stop, $creatorIds = NULL, $participantIds = NULL) {
		$constraints = array();
		$dateConstraints = array();
		$participantConstraints = array();
		$query = $this->createQuery();
		
		$dateConstraints[] = $query->logicalAnd($query->greaterThanOrEqual('start', $start), $query->lessThanOrEqual('start', $stop));
		$dateConstraints[] = $query->logicalAnd($query->lessThanOrEqual('start', $start), $query->greaterThanOrEqual('stop', $stop));
		$dateConstraints[] = $query->logicalAnd($query->greaterThanOrEqual('stop', $start), $query->lessThanOrEqual('stop', $stop));
		$dateConstraints[] = $query->logicalAnd($query->greaterThanOrEqual('start', $start), $query->lessThanOrEqual('stop', $stop));		
		$constraints[] = $query->logicalOR($dateConstraints);
		
		if (isset($creatorIds) && !empty($creatorIds)) {
			$constraints[] = $query->in('cruser_id', $creatorIds);
		}
		
		if (isset($participantIds) && !empty($participantIds)) {
			foreach ($participantIds as $participantId) {
				$participantConstraints[] = $query->contains('participants', $participantId);
			}
			if (in_array(0, $participantIds)) {
				$participantConstraints[] = $query->equals('participants', 0);
			}
			$constraints[] = $query->logicalOR($participantConstraints);
		} else {
			$constraints[] = $query->equals('participants', 0);
		}
	
		return $query->matching($query->logicalAnd($query->logicalAnd($constraints)))->execute();
	}
	
	/**
	 * Search all future events, optionally filtered by remindable since now.
	 * 
	 * @param string $filterRemindable
	 */
	public function findUpcoming($filterRemindable = FALSE) {
		$constraints = array();
		$now = new \DateTime();
		$now = $now->getTimestamp();
		$query = $this->createQuery();
		
		/** @var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
		$querySettings = $query->getQuerySettings();
		$querySettings->setRespectStoragePage(FALSE);	// default query settings, PID is irelevant for this request	 
		$query->setQuerySettings($querySettings);
		
		$constraints[] = $query->greaterThanOrEqual('start', $now);
		$constraints[] = $query->greaterThanOrEqual('stop', $now);
		
		if ($filterRemindable) {
			$constraints[] = $query->logicalAnd($query->lessThanOrEqual('reminder_start', $now), $query->logicalNot($query->equals('reminder_start', 0)));
		}
		
		return $query->matching($query->logicalAnd($query->logicalAnd($constraints)))->execute();
	}
	


	/**
	 * Delete all entries older (crdate) than given date.
	 * CAUTION: this function removes completely.
	 * 
	 * @param int $deleteDate as UNIX timestamp
	 * @return boolean
	 */
	public function cleanupByDate($deleteDate) {
		// load all feUser IDs from association with date
		$uids = array();
		$this->databaseConnection = $GLOBALS['TYPO3_DB'];
		$table = 'tx_datectimeline_domain_model_date';
		$where = 'crdate <= '.$deleteDate;
		$res = $this->databaseConnection->exec_DELETEquery($table, $where);

		return $res;
	}
	
}
?>