<?php
/**
* Field factory profiles for a "color" input.
*
* @copyright 2012 by papaya Software GmbH - All rights reserved.
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
* @version $Id: Color.php 37360 2012-08-03 16:13:16Z weinert $
*/

/**
* Field factory profiles for a "color" input.
*
* Each profile defines how a field {@see PapayaUiDialogField} is created for a specified
* type. Here is an options subobject to provide data for the field configuration.
*
* @package Papaya-Library
* @subpackage Ui
*/
class PapayaUiDialogFieldFactoryProfileColor extends PapayaUiDialogFieldFactoryProfile {

  /**
   * Create a color input field
   *
   * @see PapayaUiDialogFieldInputColor
   * @see PapayaUiDialogFieldFactoryProfile::getField()
   */
  public function getField() {
    $field = new PapayaUiDialogFieldInputColor(
      $this->options()->caption,
      $this->options()->name,
      $this->options()->default,
      $this->options()->mandatory
    );
    return $field;
  }
}