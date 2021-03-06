<?php
/**
* This exception is thrown if an invalid character is found in the given input
*
* @copyright 2011 by papaya Software GmbH - All rights reserved.
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
* @version $Id: Invalid.php 36084 2011-08-15 08:51:40Z weinert $
*/

/**
* This exception is thrown if an invalid character is found in the given input
*
* @package Papaya-Library
* @subpackage Filter
*/
class PapayaFilterExceptionCharacterInvalid extends PapayaFilterException {

  /**
  * Position of invalid character
  *
  * @var integer
  */
  private $_characterPosition = 0;

  /**
  * Initialize object, store chracter position and generate error message.
  *
  * @param string $value
  * @param integer $offset
  */
  public function __construct($value, $offset) {
    $this->_characterPosition = $offset;
    if (strlen($value) > 50) {
      if ($offset > 50) {
        $from = $offset - 50;
        $length = 50;
      } else {
        $from = 0;
        $length = $offset;
      }
      parent::__construct(
        sprintf(
          'Invalid character at offset #%d near "%s".',
          $offset,
          substr($value, $from, $length)
        )
      );
    } else {
      parent::__construct(
        sprintf(
          'Invalid character in value "%s" at offset #%d.',
          $value,
          $offset
        )
      );
    }
  }

  /**
  * Return the character position
  *
  * @return integer
  */
  public function getCharacterPosition() {
    return $this->_characterPosition;
  }
}