<?php
/**
* Delete page reference.
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
* @subpackage Administration
* @version $Id: Delete.php 39430 2014-02-28 09:21:51Z weinert $
*/

/**
* Delete a page reference.
*
* @package Papaya-Library
* @subpackage Administration
*/
class PapayaAdministrationPagesReferenceCommandDelete
  extends PapayaUiControlCommandDialog {

  /**
  * Create confirmation dialog and assign callback for confirmation message.
  */
  public function createDialog() {
    /** @var PapayaAdministrationPagesDependencyChanger $changer */
    $changer = $this->owner();
    $dialog = new PapayaUiDialogDatabaseDelete(
      $reference = $changer->reference()
    );
    $dialog->caption = new PapayaUiStringTranslated('Delete');
    $dialog->parameterGroup($this->owner()->parameterGroup());
    $dialog->hiddenFields->merge(
      array(
        'cmd' => 'reference_delete',
        'page_id' => $changer->getPageId(),
        'target_id' => $changer->getPageId() == $reference->sourceId
          ? $reference->targetId : $reference->sourceId
      )
    );
    $dialog->fields[] = new PapayaUiDialogFieldInformation(
      new PapayaUiStringTranslated('Delete reference?'),
      'places-trash'
    );
    $dialog->buttons[] = new PapayaUiDialogButtonSubmit(new PapayaUiStringTranslated('Delete'));

    $this->callbacks()->onExecuteSuccessful = array(
      $this, 'dispatchDeleteMessage'
    );
    return $dialog;
  }

  /**
  * Callback, dispatch the delete confirmation message to the user
  */
  public function dispatchDeleteMessage() {
    $this->papaya()->messages->dispatch(
      new PapayaMessageDisplayTranslated(
        PapayaMessage::SEVERITY_INFO, 'Reference deleted.'
      )
    );
  }
}