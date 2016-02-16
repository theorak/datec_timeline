<?php
namespace Datec\DatecTimeline\Domain\Model;

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
 * Defines the date object.
 *
 * @package datec_timeline
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Date extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
	 * @var string
	 * @validate notEmpty
	 */
	protected $title;
	
	/**
	 * @var string
	 */
	protected $description;
	
	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FrontendUser>
	 */
	protected $participants;
	
	/**
	 * @var \DateTime
	 */
	protected $crdate;
	/**
	 * @var int
	 */
	protected $cruserId;
	
	/**
	 * @var \DateTime
	 */
	protected $start;
	
	/**
	 * @var \DateTime
	 */
	protected $stop;
	
	/**
	 * @var \DateTime
	 */
	protected $reminderStart;
	
	/**
	 * Contructs this object
	 */
	public function __construct() {
		$this->participants = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}
	
	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * @param $title string
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
	
	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * @param $description string
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $participants
	 * @return void
	 */
	public function setParticipants(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $participants) {
		$this->participants = $participants;
	}
	
	/**
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $participant
	 * @return void
	 */
	public function addParticipant(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $participant) {
		if ($this->participants->contains($participant) === FALSE) { // avoid duplicates
			$this->participants->attach($participant);
		}
	}
	
	/**
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $participants
	 * @return void
	 */
	public function removeParticipant(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $participant) {
		$this->participants->detach($participant);
	}
	
	/**
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage An object storage containing the participants
	 */
	public function getParticipants() {
		return $this->participants;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getCrdate() {
		return $this->crdate;
	}
	
	/**
	 * @param \DateTime $crdate
	 * @return void
	 */
	public function setCrdate($crdate) {
		$this->crdate = $crdate;
	}
	
	/**
	 * @return int
	 */
	public function getCruserId() {
		return $this->cruserId;
	}
	
	/**
	 * @param int $cruserId
	 * @return void
	 */
	public function setCruserId($cruserId) {
		$this->cruserId = $cruserId;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getStart() {
		return $this->start;
	}
	
	/**
	 * @param \DateTime $start
	 * @return void
	 */
	public function setStart($start) {
		$this->start = $start;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getStop() {
		return $this->stop;
	}

	/**
	 * @param \DateTime $stop
	 * @return void
	 */
	public function setStop($stop) {
		$this->stop = $stop;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getReminderStart() {
		return $this->reminderStart;
	}
	
	/**
	 * @param \DateTime $start
	 * @return void
	 */
	public function setReminderStart($reminderStart) {
		$this->reminderStart = $reminderStart;
	}
	
}
?>