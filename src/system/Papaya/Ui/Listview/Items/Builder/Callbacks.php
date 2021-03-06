<?php
/**
* Callbacks that are used by the listview items builder
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
* @version $Id: Callbacks.php 36835 2012-03-16 14:36:50Z weinert $
*/

/**
* Callbacks that are used by the listview items builder
*
* @package Papaya-Library
* @subpackage Ui
*
* @property PapayaObjectCallback $onBeforeFill
* @property PapayaObjectCallback $onAfterFill
* @property PapayaObjectCallback $onCreateItem
* @method boolean onBeforeFill if the callback returns FALSE, the items will be cleared.
* @method boolean onAfterFill
* @method boolean onCreateItem
*/
class PapayaUiListviewItemsBuilderCallbacks extends PapayaObjectCallbacks {

  /**
  * Initialize object and set callback definition
  */
  public function __construct() {
    parent::__construct(
      array(
        'onBeforeFill' => FALSE,
        'onAfterFill' => NULL,
        'onCreateItem' => NULL
      )
    );
  }
}