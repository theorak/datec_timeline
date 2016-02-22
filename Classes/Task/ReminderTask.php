<?php
namespace Datec\DatecTimeline\Task;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Datec\DatecTimeline\Domain\Model\Date;
use Datec\DatecTimeline\Domain\Model\FeUser;

/**
 * Task to remind creators and participants of upcomming dates.
 * INFO: The interval of this tasks execution is also the interval in wich reminders will be sent, we recommend once per day (86400s).
 * It is also recommended to start this task just after 00:00:00.
 * 
 * @author Philipp "Theorak" Roensch
 */
class ReminderTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask {

	/**
	 * $dateRepository
	 *
	 * @var \Datec\DatecTimeline\Domain\Repository\DateRepository
	 */
	protected $dateRepository;
	
	/**
	 * $feUserRepository
	 *
	 * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
	 */
	protected $feUserRepository;
	
	/**
	 * $mailService
	 *
	 * @var \Datec\DatecTimeline\Service\MailService
	 */
	protected $mailService;
	
	protected $pluginConfiguration;	
	protected $extensionConfiguration;
	
	/**
	 * injection of DateRepository
	 */
	private function injectDateRepository() {
		$objectManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->dateRepository = $objectManager->get(\Datec\DatecTimeline\Domain\Repository\DateRepository::class);
	}
	
	/**
	 * injection of DateRepository
	 */
	private function injectFeUserRepository() {
		$objectManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->feUserRepository = $objectManager->get(\TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class);
	}
	
	/**
	 * injection of MailService
	 */
	private function injectMailService() {
		$objectManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->mailService = $objectManager->get(\Datec\DatecTimeline\Service\MailService::class);
	}
	
	private function init() {
		$GLOBALS['TT'] = new \TYPO3\CMS\Core\TimeTracker\NullTimeTracker();
		/** @var TypoScriptFrontendController $typoScriptFrontendController */
		$typoScriptFrontendController = GeneralUtility::makeInstance(
				\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class,
				$GLOBALS['TYPO3_CONF_VARS'],
				0,
				0,
				TRUE
		);
		// Call all the methods that set up the Typo3 frontend.
		$GLOBALS['TSFE'] = $typoScriptFrontendController;
		$typoScriptFrontendController->connectToDB();
		$typoScriptFrontendController->fe_user = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Authentication\\FrontendUserAuthentication');
		$typoScriptFrontendController->id = $pageId;
		$typoScriptFrontendController->determineId();
		$typoScriptFrontendController->getCompressedTCarray();
		$typoScriptFrontendController->initTemplate();
		$typoScriptFrontendController->getConfigArray();
		$typoScriptFrontendController->includeTCA();
		
		// Now get the TypoScript from the frontend controller.
		/** @var TypoScriptService $typoScriptService */
		$typoScriptService = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Service\TypoScriptService');
		$pluginTyposcript = $typoScriptFrontendController->tmpl->setup['plugin.']['tx_datectimeline.'];
		$this->pluginConfiguration = $typoScriptService->convertTypoScriptArrayToPlainArray($pluginTyposcript);
		
		// Set configuration to call the plugin
		$extensionConfiguration['settings'] = $this->pluginConfiguration['settings'];
		$extensionConfiguration['persistence'] = $this->pluginConfiguration['persistence'];
		
		$this->extensionConfiguration = $extensionConfiguration;
	}
	
	/**
	 * @return boolean Success state of execution
	 */
	public function execute() {
		$this->init();
		$this->injectDateRepository();
		$this->injectFeUserRepository();
		$this->injectMailService();
		xdebug_break();
		$dates = $this->dateRepository->findUpcoming(TRUE);		
		if (!$dates) { // db error
			return FALSE;
		}

		$dates = $dates->toArray();	
		if (!empty($dates)) {			
			foreach ($dates as $date) {
				$recipients = array();
				
				$creator = $this->feUserRepository->findByUid($date->getCruserId());
				if ($creator instanceof FeUser) {
					if ($creator->getEmail() !== '') {
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
				}				
				
				$participants = $date->getParticipants();
				if (!empty($participants)) {
					foreach ($participants as $participant) {
						if ($participant instanceof FeUser) {
							if ($participant->getEmail() !== '') {
								if ($participant->getLastname() !== '') {
									$participantName = $participant->getLastname();
									if ($participant->getFirstname() !== '') {
										$participantName .= ', '.$participant->getFirstname();
									} 
								} else {
									$participantName = $participant->getUsername();
								}
								$recipients[$participant->getEmail()] = $participantName;
							}
						}
					}
				}
				
				if (empty($recipients)) { // had date without recipients, should not happen
					return FALSE;
				}		
				
				$subject = LocalizationUtility::translate('tx_datectimeline.mail.reminderSubject', 'datec_timeline');
				$msg = $this->mailService->generateReminderMail($date, $recipients, $this->pluginConfiguration);
				$this->mailService->sendBccMails($subject, $msg, $recipients, $this->pluginConfiguration['settings']);
			}			
		}		
		
		return TRUE;
	}
	
}
