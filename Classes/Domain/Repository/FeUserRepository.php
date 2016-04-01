<?php
namespace Datec\DatecTimeline\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 
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
 * 
 *
 * @package datec_timeline
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class FeUserRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository {
	
	protected $databaseConnection;
	
	/**
	 * Searches for all frontend users who create a date by cruser_id.
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|boolean
	 */
	public function findCreatorsOfDates() {
		// load all feUser IDs from association with date
		$uids = array();
		$this->databaseConnection = $GLOBALS['TYPO3_DB'];
		$table = 'tx_datectimeline_domain_model_date';
		$select = 'cruser_id';
		$where = '';
		$groupBy = 'cruser_id';
		$res = $this->databaseConnection->exec_SELECTquery($select, $table, $where, $groupBy, '', '');
		if ($res) {
			while ($row = $this->databaseConnection->sql_fetch_assoc($res)) {
				$uids[] = $row['cruser_id'];
			}
		} else {
			return $res;
		}
	
		if (!empty($uids)) {
			$query = $this->createQuery();
			$querySettings = $query->getQuerySettings();
			$querySettings->setRespectStoragePage(FALSE);
			$query->setQuerySettings($querySettings);
			$query->matching($query->in('uid', $uids));
			return $query->execute();
		}
	
		return FALSE;
	}
	
	/**
	 * Searches for all frontend users who participated in a date by database relation.
	 * 
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|boolean
	 */
	public function findParticipantsByRelatedDates() {		
		// load all feUser IDs from association with date
		$uids = array();
		$this->databaseConnection = $GLOBALS['TYPO3_DB'];
		$table = 'tx_datectimeline_domain_model_date_fe_users_mm';
		$select = 'uid_foreign';
		$where = '';
		$groupBy = 'uid_foreign';
		$res = $this->databaseConnection->exec_SELECTquery($select, $table, $where, $groupBy, '', '');
		if ($res) {
			while ($row = $this->databaseConnection->sql_fetch_assoc($res)) {
				$uids[] = $row['uid_foreign'];
			}
		} else {
			return $res;
		}
		
		if (!empty($uids)) {
			$query = $this->createQuery();	 	
        	$querySettings = $query->getQuerySettings();
       	 	$querySettings->setRespectStoragePage(FALSE);
        	$query->setQuerySettings($querySettings);
			$query->matching($query->in('uid', $uids));
			return $query->execute();
		}
		
		return FALSE;
	}
	
}
?>