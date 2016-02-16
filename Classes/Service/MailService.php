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
	 * Sends mails with message to all recipients in bcc.
	 * 
	 * @param string $subject Subject of message
	 * @param string $html Message text, should be HTML content
	 * @param array $recipients List of recipients, with arrays of (email => name)
	 * @param $plain (optional) plain text message
	 * 
	 * @return boolean Message was send
	 */
	public function sendBccMails($subject, $html, $recipients, $settings, $plain = '') {
		$message = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Mail\MailMessage::class);

		$value = reset($recipients);
		$key = key($recipients);
		$firstRecipient = array($key => $value);
		unset($recipients[$key]);
		if (is_array($firstRecipient)) {		
			$message->setFrom(array($settings['mail']['internMailFrom'] => $settings['mail']['internMailFromName']));
			$message->setTo($firstRecipient);
			if (!empty($recipients)) {
				$message->setBcc($recipients);
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
	 * @return string
	 */
	public function generateReminderMail($date, $recipients, $pluginConfig) {	
		$emailView = GeneralUtility::makeInstance(\TYPO3\CMS\Fluid\View\StandaloneView::class);
		$templateName = 'Email/ReminderMail.html';
		$templateRootPath = GeneralUtility::getFileAbsFileName($pluginConfig['view']['templateRootPath']);
		$templatePathAndFilename = $templateRootPath.$templateName;
		
		$emailView->setTemplatePathAndFilename($templatePathAndFilename);
		$emailView->getRequest()->setControllerExtensionName('DatecTimeline');
		
		$emailView->assign('date', $date);
		$emailView->assign('recipients', $recipients);
		$emailView->assign('settings', $pluginConfig['settings']);
	
		$emailBody = $emailView->render();
	
		return $emailBody;
	}
	
}
?>