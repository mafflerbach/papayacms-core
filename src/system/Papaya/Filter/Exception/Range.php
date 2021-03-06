<?php
/**
* A range exception is thrown if a value in a certain range is expected.
*
* @copyright 2010 by papaya Software GmbH - All rights reserved.
* @link http://www.papaya-cms.com/
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License, version 2
*
* You can redistribute and/or modify this script under the terms of the GNU General Public
* License (GPL) version 2, provided that the copyright and license notes, including these
* lines, remain unmodified. papaya is distributed in the hope that it will be useful, but
* WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE.
*
* @package Papaya-Library
* @subpackage Filter
* @version $Id: Range.php 34328 2010-06-08 13:10:38Z weinert $
*/

/**
* A range exception is thrown if a value in a certain range is expected.
*
* In other words if a value is to small or to large
*
* @package Papaya-Library
* @subpackage Filter
*/
abstract class PapayaFilterExceptionRange extends PapayaFilterException {

  /**
  * The expected value limit (minimum or maximum)
  * @var integer|float
  */
  private $_expectedLimit = 0;
  /**
  * The actual length of the value
  * @var integer|float
  */
  private $_actualValue = 0;

  /**
  * Construct object, set message and range limit informations
  *
  * @param string $message
  * @param integer|float $expected
  * @param integer|float $actual
  */
  public function __construct($message, $expected, $actual) {
    $this->_expectedLimit = $expected;
    $this->_actualValue = $actual;
    parent::__construct($message);
  }

  /**
  * Read private expected value limit property
  *
  * @return integer
  */
  public function getExpectedLimit() {
    return $this->_expectedLimit;
  }

  /**
  * Read private actual value property
  *
  * @return integer
  */
  public function getActualValue() {
    return $this->_actualValue;
  }
}