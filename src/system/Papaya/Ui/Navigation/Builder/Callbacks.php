<?php
/**
* Callbacks that are used by the navigation builder object
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
* @version $Id: Callbacks.php 36709 2012-02-06 12:30:12Z weinert $
*/

/**
* Callbacks that are used by the navigation builder object
*
* @package Papaya-Library
* @subpackage Ui
*
* @property PapayaObjectCallback $onBeforeAppend
* @property PapayaObjectCallback $onAfterAppend
* @property PapayaObjectCallback $onCreateItem
* @property PapayaObjectCallback $onAfterAppendItem
* @method void onBeforeAppend
* @method void onAfterAppend
* @method NULL|PapayaUiNavigationItem onCreateItem
* @method void onAfterAppendItem
*/
class PapayaUiNavigationBuilderCallbacks extends PapayaObjectCallbacks {

  public function __construct() {
    parent::__construct(
      array(
        'onBeforeAppend' => NULL,
        'onAfterAppend' => NULL,
        'onCreateItem' => NULL,
        'onAfterAppendItem' => NULL
      )
    );
  }
}