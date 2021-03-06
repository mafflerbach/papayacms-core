<?php
/**
* Superclass for dialog elements
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
* @subpackage Ui
* @version $Id: Element.php 39721 2014-04-07 13:13:23Z weinert $
*/

/**
* Superclass for dialog elements
*
* An dialog element can be a simple input field, a button or a complex element with several
* child elements.
*
* @package Papaya-Library
* @subpackage Ui
*/
abstract class PapayaUiDialogElement extends PapayaUiControlCollectionItem {

  /**
  * Collect filtered dialog input data into $this->_dialog->data()
  */
  public function collect() {
    return $this->collection()->hasOwner();
  }

  /**
  * Get the parameter name
  *
  * If the dialog has a parameter group this will generate an additional parameter array level.
  *
  * If the key is an array is will be converted to a string
  * compatible to PHPs parameter array syntax.
  *
  * @param string|array $key
  * @param boolean $withGroup
  * @return string
  */
  protected function _getParameterName($key, $withGroup = TRUE) {
    if ($withGroup && $this->hasDialog()) {
      $name = $this->getDialog()->getParameterName($key);
      $prefix = $this->getDialog()->parameterGroup();
      if (isset($prefix)) {
        $name->prepend($prefix);
      }
    } else {
      $name = new PapayaRequestParametersName($key);
    }
    return (string)$name;
  }

  /**
   * Check if the element is attached to a collection and the collection attached to a dialog
   *
   * @return bool
   */
  public function hasDialog() {
    if ($this->hasCollection() &&
        $this->collection()->hasOwner()) {
      return ($this->collection()->owner() instanceof PapayaUiDialog);
    }
    return FALSE;
  }

  /**
   * Return the dialog the elements collection is attached to.
   *
   * @return null|PapayaUiDialog
   */
  public function getDialog() {
    if ($this->hasDialog()) {
      return $this->collection()->owner();
    }
    return NULL;
  }
}
