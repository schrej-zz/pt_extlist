<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Daniel Lienert <lienert@punkt.de>, Michael Knoll <knoll@punkt.de>
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

/**
 * Class implementing a mock for configuration builder
 *
 * @package Typo3
 * @subpackage pt_extlist
 * @author Daniel Lienert <linert@punkt.de>
 */
class Tx_PtExtlist_Tests_Domain_Configuration_Columns_ColumnConfig_testcase extends Tx_Extbase_BaseTestcase {

	
	/**
	 * @var Tx_PtExtlist_Tests_Domain_Configuration_ConfigurationBuilderMock
	 */
	protected $configurationBuilderMock;
	
	/**
	 * Holds a dummy configuration for a column config object
	 * @var array
	 */
	protected $columnSettings = array();
	
	
	/**
	 * Holds an instance of column configuration object
	 * @var Tx_PtExtlist_Domain_Configuration_Columns_ColumnConfig
	 */
	protected $columnConfig = null; 
	
	
	/**
	 * @return void
	 * @author Daniel Lienert <lienert@punkt.de>
	 */
	public function setup() {
		
		$this->configurationBuilderMock = Tx_PtExtlist_Tests_Domain_Configuration_ConfigurationBuilderMock::getInstance();
		$allColumnSettings = $this->configurationBuilderMock->getColumnSettings();
		$this->columnSettings = $allColumnSettings[10];
		
		$this->columnConfig = new Tx_PtExtlist_Domain_Configuration_Columns_ColumnConfig($this->configurationBuilderMock, $this->columnSettings);
	}
	
	
	
	public function testGetFieldIdentifier() {
		$this->assertEquals($this->columnConfig->getFieldIdentifier(), $this->columnSettings['fieldIdentifier']);
	}
	
	
	
	public function testGetColumnIdentifier() {
		$this->assertEquals($this->columnConfig->getColumnIdentifier(), $this->columnSettings['columnIdentifier']);
	}
	
	
	
	public function testGetLabelWhenLabelGiven() {
		$this->assertEquals($this->columnConfig->getLabel(), $this->columnSettings['label']);
	}
	
	
	
	public function testGetLabelWhenLabelNotGiven() {
		unset($this->columnSettings['label']);
		$this->columnConfig = new Tx_PtExtlist_Domain_Configuration_Columns_ColumnConfig($this->configurationBuilderMock, $this->columnSettings);
		$this->assertEquals($this->columnConfig->getLabel(), $this->columnSettings['columnIdentifier']);
	}
	
	
	
	public function testNoColumnIdentifierGivenException() {
		try {
			new Tx_PtExtlist_Domain_Configuration_Columns_ColumnConfig(array('fieldIdentifier' => 'test'));
		} catch(Exception $e) {
			return;
		}
		$this->fail();
	}
	
	
	
	public function testNoFieldIdentifierGivenException() {
		try {
			new Tx_PtExtlist_Domain_Configuration_Columns_ColumnConfig(array('columnIdentifier' => 'test'));
		} catch(Exception $e) {
			return;
		}
		$this->fail();
	}
}
?>