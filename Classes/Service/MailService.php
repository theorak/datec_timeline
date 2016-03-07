<?php
namespace Datec\DatecTimeline\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Datec\DatecTimeline\Domain\Model\Date;

/**
 *
 * @package datec_timeline
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class MailService implements \TYPO3\CMS\Core\SingletonInterface  {
	
	/**
	 * @var array
	 */
	protected $recipients;

	/**
	 * $feUserRepository
	 *
	 * @var \Datec\DatecTimeline\Domain\Repository\FeUserRepository
	 * @inject
	 */
	protected $feUserRepository;
	
	/**
	 * Sends mails with message to all recipients in bcc.
	 * 
	 * @param string $subject Subject of message
	 * @param string $html Message text, should be HTML content
	 * @param array $recipients List of recipients, with arrays of (email => name)
	 * @param $plain (optional) plain text message
	 * 
	 * @return boolean Message was send
	 */
	public function sendBccMails($subject, $html, $settings, $plain = '') {
		$message = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Mail\MailMessage::class);

		$value = reset($this->recipients);
		$key = key($this->recipients);
		$firstRecipient = array($key => $value);
		unset($this->recipients[$key]);
		if (is_array($firstRecipient)) {		
			$message->setFrom(array($settings['mail']['internMailFrom'] => $settings['mail']['internMailFromName']));
			$message->setTo($firstRecipient);
			if (!empty($this->recipients)) {
				$message->setBcc($this->recipients);
			}
			if ($settings['mail']['sendSupportInternMail'] == 1) {
				$message->setBcc(array($settings['mail']['supportInternMailTo'] => ''));
			}
			$message->setSubject($subject);
			$message->setBody($html, 'text/html', 'utf-8');
			if ($plain) {
				$message->addPart($plain, 'text/plain', 'utf-8');
			}
			
			$message->send();
			if($message->isSent()) {
				return TRUE;
			}
		}
	
		return FALSE;
	}
	
	/**
	 * @param Date $date
	 * @param array $recipients
	 * @param array $pluginConfig
	 * @param string $mailName
	 * @return string
	 */
	public function generateMail($date, $pluginConfig, $mailName) {	
		$emailView = GeneralUtility::makeInstance(\TYPO3\CMS\Fluid\View\StandaloneView::class);
		$templateName = 'Email/'.$mailName.'.html';
		$templateRootPath = GeneralUtility::getFileAbsFileName($pluginConfig['view']['templateRootPath']);
		$templatePathAndFilename = $templateRootPath.$templateName;
		
		$emailView->setTemplatePathAndFilename($templatePathAndFilename);
		$emailView->getRequest()->setControllerExtensionName('DatecTimeline');
		
		$emailView->assign('date', $date);
		$emailView->assign('recipients', $this->recipients);
		$emailView->assign('settings', $pluginConfig['settings']);

		// set language by mail setting
		$GLOBALS['LANG'] = GeneralUtility::makeInstance('TYPO3\\CMS\\Lang\\LanguageService');
		$GLOBALS['LANG']->init($pluginConfig['settings']['mail']['translang']);
		
		$emailBody = $emailView->render();
	
		return $emailBody;
	}
	
	/**
	 * Sets all recipients for date, creator first plus participants.
	 * 
	 * @param \Datec\DatecTimeline\Domain\Model\Date $date
	 * @param array $recipients
	 */
	public function setRecpients($date, $recipients = NULL) {
		if (!isset($recipients) && empty($recipients)) {
			$recipients = array();
			
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
			
			if (count($date->getParticipants()) > 0) {
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
		}
		
		$this->recipients = $recipients;
	}
	
}
?>