<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2012 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll
 *  All rights reserved
 *
 *  For further information: http://extlist.punkt.de <extlist@punkt.de>
 *
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * This controller is meant to be used in a backend module, as in backend context we have only one controller
 * on one site we can
 *
 * @package Controller
 * @author Daniel Lienert
 */
abstract class Tx_PtExtlist_Controller_AbstractBackendListController extends Tx_PtExtlist_Controller_AbstractController {

	/**
	 * @var string relative path under settings of this extension to the extlist typoScript configuration
	 */
	protected $extlistTypoScriptSettingsPath = '';


	/**
	 * @var string the pagerIdentifier to use
	 */
	protected $pagerIdentifier = 'default';


	/**
	 * @var string
	 */
	protected $filterboxIdentifier = '';


	/**
	 * Holds an instance of filterbox collection for processed list
	 *
	 * @var Tx_PtExtlist_Domain_Model_Filter_FilterboxCollection
	 */
	protected $filterboxCollection = NULL;


	/**
	 * Holds an instance of filterbox processed by this controller
	 *
	 * @var Tx_PtExtlist_Domain_Model_Filter_Filterbox
	 */
	protected $filterbox = NULL;


	/**
	 * @var Tx_PtExtlist_Domain_Model_Pager_PagerCollection
	 */
	protected $pagerCollection = NULL;


	/**
	 * @var Tx_PtExtlist_ExtlistContext_ExtlistContext
	 */
	protected $extListContext;


	/**
	 * Holds an instance of list renderer
	 *
	 * @var Tx_PtExtlist_Domain_Renderer_RendererChain
	 */
	protected $rendererChain;



	/**
	 * Initialize this controller
	 */
	public function initializeAction() {
		$headerInclusionUtility = $this->objectManager->get('Tx_PtExtbase_Utility_HeaderInclusion');
		$headerInclusionUtility->addCSSFile('EXT:pt_extlist/Resources/Public/CSS/Layout/Backend.css');

		$this->rendererChain = Tx_PtExtlist_Domain_Renderer_RendererChainFactory::getRendererChain($this->configurationBuilder->buildRendererChainConfiguration());

		$this->initFilterBox();
		$this->initPager();
	}



	/**
	 * Init the filterbox
	 */
	protected function initFilterBox() {
		if($this->filterboxIdentifier) {
			$this->filterboxCollection = $this->dataBackend->getFilterboxCollection();
			$this->filterbox = $this->filterboxCollection->getFilterboxByFilterboxIdentifier($this->filterboxIdentifier, true);
		}
	}



	/**
	 * Init the pager
	 */
	protected function initPager() {
		$this->pagerCollection = $this->dataBackend->getPagerCollection();
		$this->pagerCollection->setItemCount($this->dataBackend->getTotalItemsCount());
	}



	/**
	 * Build the configuration builder with settings from the given extlistTypoScriptConfigurationPath
	 * @return Tx_PtExtlist_Domain_Configuration_ConfigurationBuilder
	 */
	protected function buildConfigurationBuilder() {
		$settings = Tx_PtExtbase_Utility_NameSpace::getArrayContentByArrayAndNamespace($this->settings, $this->extlistTypoScriptSettingsPath);

		if(!$this->extlistTypoScriptSettingsPath) throw new Exception('No extlist typoscript settings path given', 1330188161);
		$this->listIdentifier = array_pop(explode('.', $this->extlistTypoScriptSettingsPath));
		$this->extListContext = Tx_PtExtlist_ExtlistContext_ExtlistContextFactory::getContextByCustomConfiguration($settings, $this->listIdentifier);

		return $this->extListContext->getConfigurationBuilder();
	}



	/**
	 * List action rendering list
	 *
	 * @return string  Rendered list for given list identifier
	 */
	public function listAction() {
		$list = Tx_PtExtlist_Domain_Model_List_ListFactory::createList($this->dataBackend, $this->configurationBuilder);

		if(count($list->getListData()) == 0) {
			$this->flashMessageContainer->add(Tx_Extbase_Utility_Localization::translate('general.emptyList', 'PtExtlist'), '', t3lib_FlashMessage::INFO);
		}

		$renderedListData = $this->rendererChain->renderList($list->getListData());
		$renderedCaptions = $this->rendererChain->renderCaptions($list->getListHeader());
		$renderedAggregateRows = $this->rendererChain->renderAggregateList($list->getAggregateListData());

		$this->view->assign('config', $this->configurationBuilder);
		$this->view->assign('listHeader', $list->getListHeader());
		$this->view->assign('listCaptions', $renderedCaptions);
		$this->view->assign('listData', $renderedListData);
		$this->view->assign('aggregateRows', $renderedAggregateRows);

		if($this->filterbox) {
			$this->view->assign('filterBoxCollection', $this->filterboxCollection);
			$this->view->assign('filterbox', $this->filterbox);
		}

		if($this->pagerIdentifier) {
			$this->view->assign('pagerCollection', $this->pagerCollection);
			$this->view->assign('pager', $this->pagerCollection->getPagerByIdentifier($this->pagerIdentifier));
		}
	}



	/**
	 * @param $exportIdentifier string
	 * @return string
	 * @throws Exception
	 */
	public function downloadAction($exportIdentifier) {

		$exportSettingsPath = $this->extlistTypoScriptSettingsPath . '.export.exportConfigs.' . $exportIdentifier;
		$exportSettings = Tx_PtExtbase_Utility_NameSpace::getArrayContentByArrayAndNamespace($this->settings, $exportSettingsPath);

		if(!is_array($exportSettings)) {
			throw new Exception('No export settings found within the path ' . $exportSettingsPath, 1331644291);
		}

		$exportConfig = new Tx_PtExtlist_Domain_Configuration_Export_ExportConfig($this->configurationBuilder, $exportSettings);

		if(array_key_exists('exportListSettingsPath', $exportSettings)) {
			$exportListSettings = Tx_PtExtbase_Utility_NameSpace::getArrayContentByArrayAndNamespace($this->settings, $exportSettings['exportListSettingsPath']);
		} else {
			$exportListSettings = $this->configurationBuilder->getSettings();
		}

		$extlistContext = Tx_PtExtlist_ExtlistContext_ExtlistContextFactory::getContextByCustomConfiguration($exportListSettings, $this->listIdentifier, false);
		$list = $extlistContext->getList(true);
		$rendererChain = $extlistContext->getRendererChain();

		$renderedListData = $rendererChain->renderList($list->getListData());
		$renderedCaptions = $rendererChain->renderCaptions($list->getListHeader());
		$renderedAggregateRows = $rendererChain->renderAggregateList($list->getAggregateListData());

		$view = $this->objectManager->get($exportConfig->getViewClassName());
		$view->setConfigurationBuilder($extlistContext->getConfigurationBuilder());
		$view->setExportConfiguration($exportConfig);


		$view->assign('listHeader', $list->getListHeader());
		$view->assign('listCaptions', $renderedCaptions);
		$view->assign('listData', $renderedListData);
		$view->assign('aggregateRows', $renderedAggregateRows);

		return $view->render();
	}


	/**
	 * Resets all filters of filterbox
	 *
	 * @param string $filterboxIdentifier Identifier of filter which should be reset
	 * @return string Rendered reset action
	 */
	public function resetAction($filterboxIdentifier) {
		if ($this->filterboxCollection->hasItem($filterboxIdentifier)) {
			$this->filterboxCollection->getFilterboxByFilterboxIdentifier($filterboxIdentifier)->reset();
		}

		$this->resetPagers();

		$this->redirect('show');
	}


	/**
	 * Sorting action used to change sorting of a list
	 *
	 * @return string Rendered sorting action
	 */
	public function sortAction() {
		$this->dataBackend->resetListDataCache();
        // ATTENTION: When a list header is reset, its GP var data is not reset, so every header that has
        // sorting data set in GP vars will not be effected when reset!
        $this->dataBackend->getSorter()->reset();
		
		$this->forward('list');
	}



	/**
	 * Reset all pagers for this list.
	 *
	 */
	protected function resetPagers() {
		// Reset pagers
		if ($this->pagerCollection === NULL) {
			// Only get pagerCollection if it's not set already. Important for testing.
			$this->pagerCollection = $this->dataBackend->getPagerCollection();
		}
		$this->pagerCollection->reset();
	}
    
}
?>