<?php
/**
* Add/save a page dependency.
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
* @version $Id: Change.php 39484 2014-03-03 11:21:06Z weinert $
*/

/**
* Add/save a page dependency.
*
* @package Papaya-Library
* @subpackage Administration
*/
class PapayaAdministrationPagesDependencyCommandChange extends PapayaUiControlCommandDialog {

  /**
  * create a condition that is used to activate the command execution
  */
  public function createCondition() {
    return new PapayaUiControlCommandConditionCallback(
      array($this, 'validatePageId')
    );
  }

  /**
  * Callback method for the condition, if it return FALSE, the command will be ignored.
  */
  public function validatePageId() {
    /** @var PapayaAdministrationPagesDependencyChanger $changer */
    $changer = $this->owner();
    $pageId = $changer->getPageId();
    $originId = $changer->getOriginId();
    return (empty($originId) || $originId != $pageId);
  }

  /**
  * Create the add/edit dialog and assign callbacks.
  *
  * @return PapayaUiDialogDatabaseSave
  */
  public function createDialog() {
    /** @var PapayaAdministrationPagesDependencyChanger $changer */
    $changer = $this->owner();
    $pageId = $changer->getPageId();
    $record = $changer->dependency();
    $synchronizations = $changer->synchronizations();

    $dialog = new PapayaUiDialogDatabaseSave($record);
    $dialog->papaya($this->papaya());

    $dialog->caption = new PapayaUiStringTranslated('Page dependency');
    $dialog->parameterGroup('pagedep');
    $dialog->hiddenFields->merge(
      array(
        'cmd' => 'edit_dependency',
        'id' => $pageId
      )
    );
    $dialog->hiddenValues->merge(
      array(
        'tt' => array(
          'page_id' => $pageId
        )
      )
    );
    $dialog->fields[] = $originIdField = new PapayaUiDialogFieldInputPage(
      new PapayaUiStringTranslated('Origin page'), 'origin_id', NULL, TRUE
    );
    $originIdField->setHint(
      new PapayaUiStringTranslated(
        'The origin id must be a valid page, that is not a dependency itself.'
      )
    );
    $dialog->fields[] = $synchronizationField = new PapayaUiDialogFieldSelectBitmask(
      new PapayaUiStringTranslated('Synchronization'),
      'synchronization',
      $synchronizations->getList()
    );
    $dialog->fields[] = new PapayaUiDialogFieldTextarea(
      new PapayaUiStringTranslated('Note'), 'note', 8, ''
    );
    $dialog->buttons[] = new PapayaUiDialogButtonSubmit(new PapayaUiStringTranslated('Save'));

    $dialog->callbacks()->onBeforeSave = array($this, 'validateOriginAndSynchronizations');
    $dialog->callbacks()->onBeforeSave->context->originIdField = $originIdField;
    $dialog->callbacks()->onBeforeSave->context->synchronizationField = $synchronizationField;

    $this->callbacks()->onExecuteSuccessful = array($this, 'handleExecutionSuccess');
    $this->callbacks()->onExecuteSuccessful->context->synchronizations = $synchronizations;
    $this->callbacks()->onExecuteSuccessful->context->dependency = $record;
    $this->callbacks()->onExecuteFailed = array($this, 'dispatchErrorMessage');

    return $dialog;
  }

  /**
   * Validate the origin id. Callback for the dialog execution
   *
   * @param Object $context
   * @param PapayaContentPageDependency $record
   * @return bool
   */
  public function validateOriginAndSynchronizations($context, $record) {
    if ($record->originId == $record->id) {
      $context->originIdField->handleValidationFailure(
        new PapayaFilterExceptionCallbackFailed(array($this, 'validateOrigin'))
      );
      return FALSE;
    } elseif ($record->isDependency($record->originId)) {
      $context->originIdField->handleValidationFailure(
        new PapayaFilterExceptionCallbackFailed(array($this, 'validateOrigin'))
      );
      return FALSE;
    }
    if (($record->synchronization & PapayaContentPageDependency::SYNC_VIEW) xor
        ($record->synchronization & PapayaContentPageDependency::SYNC_CONTENT)) {
      if (!$this->compareViewModules($record)) {
        $context->synchronizationField->handleValidationFailure(
          new PapayaFilterExceptionCallbackFailed(array($this, 'compareViewModules'))
        );
        return FALSE;
      }
    }
    return TRUE;
  }

  /**
   * Validate that all views in matching translations (language) use the same module
   *
   * @param PapayaContentPageDependency $record
   * @return bool
   */
  private function compareViewModules(PapayaContentPageDependency $record) {
    $databaseAccess = $record->getDatabaseAccess();
    $sql = "SELECT tt.lng_id, COUNT(DISTINCT v.module_guid) module_counter
              FROM %s AS tt, %s AS v
             WHERE tt.topic_id IN (%d, %d)
               AND v.view_id = tt.view_id
             GROUP BY tt.lng_id";
    $parameters = array(
      $databaseAccess->getTableName(PapayaContentTables::PAGE_TRANSLATIONS),
      $databaseAccess->getTableName(PapayaContentTables::VIEWS),
      $record->id,
      $record->originId
    );
    if ($databaseResult = $databaseAccess->queryFmt($sql, $parameters)) {
      while ($row = $databaseResult->fetchRow(PapayaDatabaseResult::FETCH_ASSOC)) {
        if ($row['module_counter'] > 1) {
          $this->papaya()->messages->dispatch(
            new PapayaMessageDisplay(
              PapayaMessage::SEVERITY_WARNING,
              new PapayaUiStringTranslated(
                'Views with different modules found. Please change befor activating'.
                ' synchronization or synchronize view and content.'
              )
            )
          );
          return FALSE;
        }
      }
    }
    return TRUE;
  }

  /**
  * Callback to dispatch a message to the user that the record was saved and trigger initial sync.
  */
  public function handleExecutionSuccess($context) {
    $context->synchronizations->synchronizeDependency($context->dependency);
    $this->papaya()->messages->dispatch(
      new PapayaMessageDisplayTranslated(
        PapayaMessage::SEVERITY_INFO, 'Dependency saved.'
      )
    );
  }

  /**
  * Callback to dispatch a message to the user that here was an input error.
  */
  public function dispatchErrorMessage($context, PapayaUiDialog $dialog) {
    $this->papaya()->messages->dispatch(
      new PapayaMessageDisplayTranslated(
        PapayaMessage::SEVERITY_ERROR,
        'Invalid input. Please check the fields "%s".',
        array(implode(', ', $dialog->errors()->getSourceCaptions()))
      )
    );
  }
}