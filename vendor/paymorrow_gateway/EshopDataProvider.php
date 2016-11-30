<?php
/**
 * @package Paymorrow Gateway
 * @copyright (C) 2015 - Paymorrow GmbH
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

interface EshopDataProvider {

    function collectEshopData();
	
    function collectConfirmData();

    function printPmData();
} 