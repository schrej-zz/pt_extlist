<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Daniel Lienert <lienert@punkt.de>, Michael Knoll <knoll@punkt.de>,
*  Christoph Ehscheidt <ehscheidt@punkt.de>
*  All rights reserved
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

class Tx_PtExtlist_Tests_Domain_Renderer_DefaultRenderer_testcase extends Tx_PtExtlist_Tests_BaseTestcase {

	protected $renderer;
	protected $settings;
	
	public function setUp() {

		$this->settings = array('columns' => array(
				 10 => array( 
		         	'columnIdentifier' => 'column1',
		         	'fieldIdentifier' => 'field1',
		         	'label' => 'Column 1'
		         ),
		         20 => array( 
		         	'columnIdentifier' => 'column2',
		         	'fieldIdentifier' => 'field3',
		         	'label' => 'Column 2'
		         ),
	         
			),
			'renderer' => array(
				'rendererClassName' => 'Tx_PtExtlist_Domain_Renderer_DefaultRenderer',
				'enabled' => 1,
				'showCaptionsInBody' => 0,
			)
		);
		
		$columnConfig = Tx_PtExtlist_Domain_Configuration_Columns_ColumnConfigCollectionFactory::getColumnConfigCollection($this->settings['columns']);
		$rendererConfig = Tx_PtExtlist_Domain_Configuration_Renderer_RendererConfigFactory::getRendererConfiguration($this->settings['renderer'],$columnConfig);
		$this->renderer = Tx_PtExtlist_Domain_Renderer_RendererFactory::getRenderer($rendererConfig);
	}
	
	public function testRender() {
		$listData = new Tx_PtExtlist_Domain_Model_List_ListData();
		$row = new Tx_PtExtlist_Domain_Model_List_Row();
		$row->addCell('field1', 'val1');
		$row->addCell('field2', 'val2');
		$row->addCell('field3', 'val3');
		$listData->addRow($row);
		
		$renderedList = $this->renderer->render($listData);

		$this->assertTrue(is_a($renderedList, 'Tx_PtExtlist_Domain_Model_List_ListData'));
		
		$this->assertEquals($renderedList->getItemByIndex(0)->getItemById('column1'),'val1');
		$this->assertEquals($renderedList->getItemByIndex(0)->getItemById('column2'),'val3');

	}
	
	public function testRenderCaptions() {
		
		$captions = $this->renderer->renderCaptions();
		
		$this->assertTrue(is_a($captions, 'Tx_PtExtlist_Domain_Model_List_Row'));
		
		$this->assertEquals($captions->getItemById('column1'), 'Column 1');
		$this->assertEquals($captions->getItemById('column2'), 'Column 2');
		
	}
	
}

?>