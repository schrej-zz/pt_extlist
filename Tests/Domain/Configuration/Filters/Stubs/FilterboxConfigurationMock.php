<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
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
  * Mock class for filterbox configuration
  * 
  * @package Typo3
  * @subpackage pt_extlist
  * @author Michael Knoll 
  */
class Tx_PtExtlist_Tests_Domain_Configuration_Filters_Stubs_FilterboxConfigurationMock extends Tx_PtExtlist_Domain_Configuration_Filters_FilterboxConfig
{
    public function __construct(Tx_PtExtlist_Domain_Configuration_ConfigurationBuilder $configurationBuilder, $filterboxIdentifier = null, $settings = null)
    {
        if ($filterboxIdentifier == null) {
            $filterboxIdentifier = 'test';
        }
        if ($settings == null) {
            $settings = [];
        }
        parent::__construct($configurationBuilder, $filterboxIdentifier, $settings);
    }
}
