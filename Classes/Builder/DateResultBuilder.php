<?php
namespace Datec\DatecTimeline\Builder;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015
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

/**
 * Builder for the result objects used in JS library (jQuery fullcalendar).
 * 
 * @author Philipp "Theorak" Roensch
 */
class DateResultBuilder implements \TYPO3\CMS\Core\SingletonInterface {	
	
	protected $pluginConfig;
	
	public function setPluginConfiguration($pluginConfig) {
		$this->pluginConfig = $pluginConfig;
	}
	
	/**
	 * $feUserRepository
	 *
	 * @var \Datec\DatecTimeline\Domain\Repository\FeUserRepository
	 * @inject
	 */
	protected $feUserRepository;

	/**
	 * Builds a date result, conforming to timeline JS from Date model.
	 * 
	 * @param Date $date
	 * @return \stdClass
	 */
	public function build(Date $date) {
		$dateResult = new \stdClass();

		$dateResult->id = $date->getUid();
		$dateResult->title = $date->getTitle();
		$dateResult->start = $date->getStart()->format('c');
		if ($date->getStop()) {
			$dateResult->end = $date->getStop()->format('c');
		}
		$dateResult->content = $this->generateContent($date, $pluginConfig);	

		$creator = $this->feUserRepository->findByUid($date->getCruserId());
		if ($creator instanceof \Datec\DatecTimeline\Domain\Model\FeUser) {			
			$dateResult->className = array('date-'.$creator->getUsername());
			if ($creator->getDateColor() != '') {
				$dateResult->borderColor = $creator->getDateColor();
				$dateResult->backgroundColor = $creator->getDateColor();
			}
		}
		
		return $dateResult;
	}
	
	/**
	 * Renders the content for calendar display from a single template.
	 * 
	 * @param Date $date
	 * @param unknown $pluginConfig
	 * @return unknown
	 */
	private function generateContent(Date $date, $pluginConfig) {
		$contentView = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Fluid\View\StandaloneView::class);
		$templateName = 'Date/DateCalendarView.html';
		$templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($this->pluginConfig['view']['templateRootPath']);
		$templatePathAndFilename = $templateRootPath.$templateName;
		
		$contentView->setTemplatePathAndFilename($templatePathAndFilename);
		$contentView->getRequest()->setControllerExtensionName('DatecTimeline');
		$contentView->assign('date', $date);
		$contentView->assign('settings', $this->pluginConfig['settings']);

		$creator = $this->feUserRepository->findByUid($date->getCruserId());
		if ($creator instanceof \Datec\DatecTimeline\Domain\Model\FeUser) {
			$contentView->assign('creator', $creator);
		}
		
		$content = $contentView->render();
		
		return $content;
	}
	
}

?>