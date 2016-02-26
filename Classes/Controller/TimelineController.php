<?php
namespace Datec\DatecTimeline\Controller;

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

use Datec\DatecTimeline\Domain\Model\Date;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Main controller for timeline operations.
 *
 * @package datec_timeline
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author Philipp "Theorak" Roensch
 */
class TimelineController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	
	/**
	 * $extKey
	 */
	protected $extKey;
	
	/**
	 * $feUser
	 */
	protected $feUser;
	
	/**
	 * $dateRepository
	 *
	 * @var \Datec\DatecTimeline\Domain\Repository\DateRepository
	 * @inject
	 */
	protected $dateRepository;
	
	/**
	 * $feUserRepository
	 *
	 * @var \Datec\DatecTimeline\Domain\Repository\FeUserRepository
	 * @inject
	 */
	protected $feUserRepository;
	
	/**
	 * $mailService
	 *
	 * @var \Datec\DatecTimeline\Service\MailService
	 * @inject
	 */
	protected $mailService;
	
	/**
	 * $persistenceManager
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 * @inject
	 */
	protected $persistenceManager;
	
	/**
	 * $configurationManager
	 *
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
	 * @inject
	 */
	protected $configurationManager;
	
	/**
	 * initialize current action
	 * @return void
	 */
	public function initializeAction() {
		$this->extKey = $this->request->getControllerExtensionKey();
		$this->feUser = $this->getFeUser();
		
	}
	
	/**
	 * action showTimeline
	 *
	 * @return void
	 */
	public function showTimelineAction() {
		$creators = $this->feUserRepository->findDateCreatorsByRelations();
		if ($creators) {
			$creators = $creators->toArray();
			if (!empty($creators)) {
				$this->view->assign('creators', $creators);
			}
		}
		
		$this->view->assign('pageId', $GLOBALS['TSFE']->id);
		$this->view->assign('settings', $this->settings);
	}
	
	/**
	 * action loadDates
	 *
	 * @return string JSON result
	 */
	public function loadDatesAction() {
		$access = $this->checkAccess();		
		if ($access) {
			if ($this->request->hasArgument('start') && $this->request->hasArgument('stop')) {
				if ($this->request->hasArgument('creatorIds')) {
					$creatorIds = $this->request->getArgument('creatorIds');
				}
				$dates = $this->dateRepository->findByFilterCriteria($this->request->getArgument('start'), $this->request->getArgument('stop'), $creatorIds);
			} else {
				$dates = $this->dateRepository->findAll();		
			}
			if ($dates) {
				$dates = $dates->toArray();
				if (!empty($dates)) {
					foreach ($dates as $date) {
						$dateResultBuilder = $this->objectManager->get('Datec\\DatecTimeline\\Builder\\DateResultBuilder');
						$dateResultBuilder->setPluginConfiguration($this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK));
						$datesResult[] = $dateResultBuilder->build($date);
					}
				} else {
					$datesResult = new \stdClass();
					$datesResult->status = 'warning';
					$datesResult->message = LocalizationUtility::translate('tx_datectimeline.errors.noDates',$this->extKey);
				}
			} else {
				$datesResult = new \stdClass();
				$datesResult->status = 'error';
				$datesResult->message = LocalizationUtility::translate('tx_datectimeline.errors.dbError',$this->extKey);
			}
		} else {
			$datesResult = new \stdClass();
			$datesResult->status = 'error';
			$datesResult->message = LocalizationUtility::translate('tx_datectimeline.errors.noAccess',$this->extKey);
		}
		
		return json_encode($datesResult);
	}
	
	/**
	 * action newDate
	 *
	 * @return void
	 */
	public function newDateAction() {
		$access = $this->checkAccess();
		if ($access) {
			$participants = array();
			
			$query = $this->feUserRepository->createQuery();
			$querySettings = $query->getQuerySettings();
			$querySettings->setRespectStoragePage(FALSE); // the repository picks up the wrong storage pid from somewhere
			$query->setQuerySettings($querySettings);
			$feUsers = $query->execute();	
			if ($feUsers) {
				$feUsers = $feUsers->toArray();
				if (!empty($feUsers)) {
					foreach($feUsers as $feUser) {
						if ($feUser->getLastname() !== '') {
							$feUserName = $feUser->getLastname();
							if ($feUser->getFirstname() !== '') {
								$feUserName .= ', '.$feUser->getFirstname();
							}
						}  else {
							$feUserName = $feUser->getUsername();
						}
						$participants[$feUser->getUid()] = $feUserName;
					}
				}
			}
			
			$date = new Date();
			$start = $this->getDateTimeJS(intval(substr($this->request->getArgument('start'), 1), 10));					
			$date->setStart($start);
			if ($this->request->hasArgument('stop')) {
				$stop = $this->getDateTimeJS(intval(substr($this->request->getArgument('stop'), 1), 10));	
				$date->setStop($stop);
			}
			$reminderStart = clone $start;
			$reminderStart->setTime(0, 0, 0);
			$date->setReminderStart($reminderStart);
			
			$this->view->assign('participants', $participants);
			$this->view->assign('date', $date);
			$this->view->assign('settings', $this->settings);
		} else {
			$this->addFlashMessage(LocalizationUtility::translate('tx_datectimeline.errors.noAccess',$this->extKey), '', \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
		}	
	}
	
	/**
	 * action createDate
	 * 
	 * @return string JSON result
	 */
	public function createDateAction() {
		$access = $this->checkAccess();
		if ($access) {
			$now = new \DateTime();
			$date = new Date();		
			$date->setPid($this->settings['storagePid']);
			$date->setCrdate($now);
			$date->setCruserId($this->feUser['uid']);
			
			$creator = $this->feUserRepository->findByUid($date->getCruserId());
			if ($this->settings['reminderMailAfterCreation']) {
				if ($creator->getLastname() !== '') {
					$cruserName = $creator->getLastname();
					if ($creator->getFirstname() !== '') {
						$cruserName .= ', '.$creator->getFirstname();
					}
				}  else {
					$cruserName = $creator->getUsername();
				}
				$recipients[$creator->getEmail()] = $cruserName;
			}
			
			// set all Data from form
			$date->setTitle($_POST['tx_datectimeline_timeline']['date']['title']);
			$date->setDescription($_POST['tx_datectimeline_timeline']['date']['description']);
			xdebug_break();
			// TODO: format the date properly in the form
			$date->setStart($this->getDateTimeForm($_POST['tx_datectimeline_timeline']['date']['start']));			
			if (isset($_POST['tx_datectimeline_timeline']['date']['stop']) && !empty($_POST['tx_datectimeline_timeline']['date']['stop'])) {
				$date->setStop($this->getDateTimeForm($_POST['tx_datectimeline_timeline']['date']['stop']));
			}			
			if (isset($_POST['tx_datectimeline_timeline']['date']['reminderStart'])) {
				$date->setReminderStart($this->getDateTimeForm($_POST['tx_datectimeline_timeline']['date']['reminderStart']));
			}
			
			if (isset($_POST['tx_datectimeline_timeline']['date']['participants']) && !empty($_POST['tx_datectimeline_timeline']['date']['participants'])) {
				foreach ($_POST['tx_datectimeline_timeline']['date']['participants'] as $feUserId) {
					$feUser = $this->feUserRepository->findByUid($feUserId);
					if ($feUser) {
						$date->addParticipant($feUser);
						if ($this->settings['reminderMailAfterCreation']) {
							if ($feUser->getLastname() !== '') {
								$participantName = $feUser->getLastname();
								if ($feUser->getFirstname() !== '') {
									$participantName .= ', '.$feUser->getFirstname();
								}
							} else {
								$participantName = $feUser->getUsername();
							}
							$recipients[$feUser->getEmail()] = $participantName;
						}
					}
				}
			}
			
			$this->dateRepository->add($date);
			$this->persistenceManager->persistAll(); // we need the uid right after
			
			if ($this->settings['reminderMailAfterCreation']) {
				$subject = LocalizationUtility::translate('tx_datectimeline.mail.reminderSubject', 'datec_timeline');
				$msg = $this->mailService->generateReminderMail($date, $recipients, $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK));
				$this->mailService->sendBccMails($subject, $msg, $recipients, $this->settings);
			}
			
			$dateResultBuilder = $this->objectManager->get('Datec\\DatecTimeline\\Builder\\DateResultBuilder');
			$dateResultBuilder->setPluginConfiguration($this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK));
			$datesResult = new \stdClass();
			$datesResult->status = 'ok';
			$datesResult->message = LocalizationUtility::translate('tx_datectimeline.messages.dateCreated',$this->extKey);			
			$datesResult->date = $dateResultBuilder->build($date);
		} else {
			$datesResult = new \stdClass();
			$datesResult->status = 'warning';
			$datesResult->message = LocalizationUtility::translate('tx_datectimeline.errors.noAccess',$this->extKey);
		}
	
		return json_encode($datesResult);
	}
	
	/**
	 * action editDate
	 *
	 * @param int $dateId
	 * @return string JSON result
	 */
	public function editDateAction($dateId) {
		$access = $this->checkAccess();
		if ($access) {			
			$date = $this->dateRepository->findByUid($dateId);
			if ($this->feUser['uid'] !== $date->getCruserId()) { // is not creator? cannot edit
				$args = array();
				$args['dateId'] = $dateId;
				$this->forward('showDate', 'Timeline', 'DatecTimeline', $args);
			}
	
			$participants = array();
			$query = $this->feUserRepository->createQuery();
			$querySettings = $query->getQuerySettings();
			$querySettings->setRespectStoragePage(FALSE); // the repository picks up the wrong storage pid from somewhere
			$query->setQuerySettings($querySettings);
			$feUsers = $query->execute();	
			if ($feUsers) {
				$feUsers = $feUsers->toArray();
				if (!empty($feUsers)) {
					foreach($feUsers as $feUser) {
						if ($feUser->getLastname() !== '') {
							$feUserName = $feUser->getLastname();
							if ($feUser->getFirstname() !== '') {
								$feUserName .= ', '.$feUser->getFirstname();
							}
						}  else {
							$feUserName = $feUser->getUsername();
						}
						$participants[$feUser->getUid()] = $feUserName;
					}
				}
			}
		} else {
			$this->addFlashMessage(LocalizationUtility::translate('tx_datectimeline.errors.noAccess',$this->extKey), '', \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
		}
		
		$this->view->assign('participants', $participants);
		$this->view->assign('date', $date);
		$this->view->assign('settings', $this->settings);	
	}
	
	/**
	 * action updateDate
	 *
	 * @return string JSON result
	 */
	public function updateDateAction() {
		$access = $this->checkAccess();
		if ($access) {
			$date = $this->dateRepository->findByUid($_POST['tx_datectimeline_timeline']['dateId']);
			
			if ($this->feUser['uid'] == $date->getCruserId()) {				// is creator, can save
				$recipients = array();
				
				// set all Data from form
				if (isset($_POST['tx_datectimeline_timeline']['date']['title'])) {
					$date->setTitle($_POST['tx_datectimeline_timeline']['date']['title']);
				}
				if (isset($_POST['tx_datectimeline_timeline']['date']['description'])) {
					$date->setDescription($_POST['tx_datectimeline_timeline']['date']['description']);
				}
				
				// differ only date change (with timestamp) to editing date
				if (strpos($_POST['tx_datectimeline_timeline']['date']['start'], '@') !== FALSE) {	
					$date->setStart($this->getDateTimeJS(intval(substr($_POST['tx_datectimeline_timeline']['date']['start'], 1), 10)));
					$date->setStop($this->getDateTimeJS(intval(substr($_POST['tx_datectimeline_timeline']['date']['stop'], 1), 10)));
				} else {
					// date from form is utc, and without timezone
					$date->setStart($this->getDateTimeForm($_POST['tx_datectimeline_timeline']['date']['start']));
					$date->setStop($this->getDateTimeForm($_POST['tx_datectimeline_timeline']['date']['stop']));
				}
				
				if (isset($_POST['tx_datectimeline_timeline']['date']['reminderStart'])) {
					$date->setReminderStart($this->getDateTimeForm($_POST['tx_datectimeline_timeline']['date']['reminderStart']));
				}
				
				if ($this->settings['reminderMailAfterEdit']) {
					$creator = $this->feUserRepository->findByUid($date->getCruserId());
					if ($creator->getLastname() !== '') {
						$cruserName = $creator->getLastname();
						if ($creator->getFirstname() !== '') {
							$cruserName .= ', '.$creator->getFirstname();
						}
					}  else {
						$cruserName = $creator->getUsername();
					}
					$recipients[$creator->getEmail()] = $cruserName;
				}

				if (isset($_POST['tx_datectimeline_timeline']['date']['participants']) && !empty($_POST['tx_datectimeline_timeline']['date']['participants'])) {
					foreach ($_POST['tx_datectimeline_timeline']['date']['participants'] as $feUserId) {
						$feUser = $this->feUserRepository->findByUid($feUserId);
						if ($feUser) {
							$date->addParticipant($feUser);
						}
					}
				}
				
				if (count($date->getParticipants()) > 0 && $this->settings['reminderMailAfterEdit']) {
					foreach ($date->getParticipants() as $feUser) {
						if ($feUser->getLastname() !== '') {
							$participantName = $feUser->getLastname();
							if ($feUser->getFirstname() !== '') {
								$participantName .= ', '.$feUser->getFirstname();
							}
						} else {
							$participantName = $feUser->getUsername();
						}
						$recipients[$feUser->getEmail()] = $participantName;
					}					
				}

				if ($this->settings['reminderMailAfterEdit']) {					
					$subject = LocalizationUtility::translate('tx_datectimeline.mail.reminderSubject', 'datec_timeline');
					$msg = $this->mailService->generateReminderMail($date, $recipients, $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK));
					$this->mailService->sendBccMails($subject, $msg, $recipients, $this->settings);
				}
				
				$this->dateRepository->update($date);
				
				
				$dateResultBuilder = $this->objectManager->get('Datec\\DatecTimeline\\Builder\\DateResultBuilder');
				$dateResultBuilder->setPluginConfiguration($this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK));
				$datesResult = new \stdClass();
				$datesResult->status = 'ok';
				$datesResult->message = LocalizationUtility::translate('tx_datectimeline.messages.dateSaved',$this->extKey);
				$datesResult->date = $dateResultBuilder->build($date);
			} else {
				$datesResult = new \stdClass();
				$datesResult->status = 'warning';
				$datesResult->message = LocalizationUtility::translate('tx_datectimeline.errors.noAccess',$this->extKey);
			}
		} else {
			$datesResult = new \stdClass();
			$datesResult->status = 'warning';
			$datesResult->message = LocalizationUtility::translate('tx_datectimeline.errors.noAccess',$this->extKey);
		}
	
		return json_encode($datesResult);
	}
	
	/**
	 * action showDate
	 *
	 * @param int $dateId
	 * @return string JSON result
	 */
	public function showDateAction($dateId) {
		$access = $this->checkAccess();
		if ($access) {
			$participants = array();
			
			$date = $this->dateRepository->findByUid($dateId);
		
			$creator = $this->feUserRepository->findByUid($date->getCruserId());
		} else {
			$this->addFlashMessage(LocalizationUtility::translate('tx_datectimeline.errors.noAccess',$this->extKey), '', \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
		}

		$this->view->assign('creator', $creator);
		$this->view->assign('date', $date);
		$this->view->assign('settings', $this->settings);
	}
	
	/**
	 * action deleteDate
	 *
	 * @return string JSON result
	 */
	public function deleteDateAction() {
		$access = $this->checkAccess();
		if ($access) {
			$date = $this->dateRepository->findByUid($_POST['tx_datectimeline_timeline']['dateId']);
			$this->dateRepository->remove($date);
			
			$datesResult = new \stdClass();
			$datesResult->status = 'ok';
			$datesResult->message = LocalizationUtility::translate('tx_datectimeline.messages.dateDeleted',$this->extKey);
		} else {
			$datesResult = new \stdClass();
			$datesResult->status = 'warning';
			$datesResult->message = LocalizationUtility::translate('tx_datectimeline.errors.noAccess',$this->extKey);
		}
	
		return json_encode($datesResult);
	}
	
	/**
	 * Look for current frontend user.
	 *
	 * @return array $feUser or boolean FALSE if no user found
	 */
	private function getFeUser() {
		if(isset($GLOBALS['TSFE']->fe_user->user)) {
			return $GLOBALS['TSFE']->fe_user->user;
		}
	
		return FALSE;
	}
	
	/**
	 * Access gets granted if the plugin CE has either no frontend user groups set, or the set groups match that of the current user.
	 * 
	 * @return boolean
	 */
	private function checkAccess() {
		$cObjData = $this->configurationManager->getContentObject();
		
		if (!empty($cObjData->data['fe_group'])) {
			foreach (explode(',',$GLOBALS['TSFE']->gr_list) as $feUserGroup) {
				if (in_array($feUserGroup, explode(',',$cObjData->data['fe_group']))) {
					return TRUE;
				}				
			}
		} else {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Our JS moves only dates as local time UNIX timestamp.
	 * Before extbase can save in UTC, the DateTime must hold the timezone information.
	 *
	 * @param int $value UNIX timestamp
	 * @return NULL
	 */
	private function getDateTimeJS($value) {
		if ($value === NULL) {
			return NULL;
		}
		$utcTimeZone = new \DateTimeZone('UTC');
		$currentTimeZone = new \DateTimeZone(date_default_timezone_get());
		$currentDate = new \DateTime();
		$offset = $currentTimeZone->getOffset($currentDate) - $utcTimeZone->getOffset($currentDate);		
		$value = $value - $offset;
		$date = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('DateTime', '@'.$value, $utcTimeZone);
		return $date->setTimezone($currentTimeZone);
	}
	
	/**
	 * Our Form moves only dates as UTC in ISO 8601.
	 * Before extbase can save in UTC, the DateTime must hold the timezone information.
	 * 
	 * @param string $value datetime ISO 8601
	 * @return NULL
	 */
	private function getDateTimeForm($value) {
		if ($value === NULL) {
			return NULL;
		}
		$utcTimeZone = new \DateTimeZone('UTC');
		$date = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('DateTime', $value, $utcTimeZone);
		$currentTimeZone = new \DateTimeZone(date_default_timezone_get());
		return $date->setTimezone($currentTimeZone);
	}
	
}
?>