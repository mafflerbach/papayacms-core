<?php
/**
* Administration interface for changes on the dependencies of a page.
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
* @version $Id: Changer.php 39725 2014-04-07 17:19:34Z weinert $
*/

/**
* Administration interface for changes on the dependencies of a page.
*
* @package Papaya-Library
* @subpackage Administration
*/
class PapayaAdministrationPagesDependencyChanger extends PapayaUiControlInteractive {

  /**
  * Currently selected page
  *
  * @var integer
  */
  private $_pageId = 0;

  /**
  * Currently selected origin page, this will be the origin page of the current dependency or
  * the current page id.
  *
  * @var integer
  */
  private $_originId = 0;

  /**
  * Target page id of the reference to load.
  *
  * @var integer
  */
  private $_targetId = 0;

  /**
  * Buffer variable for the current dependency
  *
  * @var PapayaContentPageDependency
  */
  private $_dependency = NULL;

  /**
  * Buffer variable for the dependencies list of the current origin id
  *
  * @var PapayaContentPageDependencies
  */
  private $_dependencies = NULL;

  private $_reference = NULL;

  private $_references = NULL;

  /**
  * Command controller for the needed actions
  *
  * @var PapayaUiControlCommandController
  */
  private $_commands = NULL;

  /**
  * Menu object, for buttons depending on the current status
  *
  * @var PapayaUiToolbar
  */
  private $_menu = NULL;

  /**
  * Dependencies listview
  *
  * @var PapayaAdministrationPagesDependencyListview
  */
  private $_listview = NULL;

  /**
  * Dependencies synchronization informations
  *
  * @var PapayaAdministrationPagesDependencySynchronizations
  */
  private $_synchronizations = NULL;

  /**
  * Return current page id
  *
  * @return integer
  */
  public function getPageId() {
    return $this->_pageId;
  }

  /**
  * Return current origin page id
  *
  * @return integer
  */
  public function getOriginId() {
    return $this->_originId;
  }

  /**
  * Getter/Setter for the dependency database object
  *
  * @param PapayaContentPageDependency $dependency
  * @return PapayaContentPageDependency
  */
  public function dependency(PapayaContentPageDependency $dependency = NULL) {
    if (isset($dependency)) {
      $this->_dependency = $dependency;
    } elseif (is_null($this->_dependency)) {
      $this->_dependency = new PapayaContentPageDependency();
    }
    return $this->_dependency;
  }

  /**
  * Getter/Setter for the dependencies list database object
  *
  * @param PapayaContentPageDependencies $dependencies
  * @return PapayaContentPageDependencies
  */
  public function dependencies(PapayaContentPageDependencies $dependencies = NULL) {
    if (isset($dependencies)) {
      $this->_dependencies = $dependencies;
    } elseif (is_null($this->_dependencies)) {
      $this->_dependencies = new PapayaContentPageDependencies();
    }
    return $this->_dependencies;
  }

  /**
  * Getter/Setter for the reference database object
  *
  * @param PapayaContentPageReference $reference
  * @return PapayaContentPageReference
  */
  public function reference(PapayaContentPageReference $reference = NULL) {
    if (isset($reference)) {
      $this->_reference = $reference;
    } elseif (is_null($this->_reference)) {
      $this->_reference = new PapayaContentPageReference();
    }
    return $this->_reference;
  }

  /**
  * Getter/Setter for the references list database object
  *
  * @param PapayaContentPageReferences $references
  * @return PapayaContentPageReferences
  */
  public function references(PapayaContentPageReferences $references = NULL) {
    if (isset($references)) {
      $this->_references = $references;
    } elseif (is_null($this->_references)) {
      $this->_references = new PapayaContentPageReferences();
    }
    return $this->_references;
  }

  /**
  * Execute commands and append output to xml.
  *
  * @param PapayaXmlElement $parent
  */
  public function appendTo(PapayaXmlElement $parent) {
    $this->prepare();
    if ($this->getPageId() > 0) {
      $this->appendButtons();
      $this->commands()->appendTo($parent);
      if ($this->getOriginId() > 0) {
        $this->dependencies()->load(
          $this->_originId, $this->papaya()->administrationLanguage->getCurrent()->id
        );
      }
      $this->references()->load(
        $this->_pageId, $this->papaya()->administrationLanguage->getCurrent()->id
      );
      $this->listview()->pages()->load(
        array(
          'id' => $this->getOriginId(),
          'language_id' => $this->papaya()->administrationLanguage->getCurrent()->id
        )
      );
      $this->listview()->parameterGroup($this->parameterGroup());
      $this->listview()->appendTo($parent);
    }
  }

  /**
  * Initialize parameters and store them into properties.
  */
  public function prepare() {
    $this->_pageId = $this->parameters()->get('page_id', 0, new PapayaFilterInteger(0));
    if ($this->_pageId > 0) {
      if ($this->dependency()->load($this->_pageId)) {
        $this->_originId = (int)$this->dependency()->originId;
      } elseif ($this->dependency()->isOrigin($this->_pageId)) {
        $this->_originId = (int)$this->_pageId;
      }
      $this->_targetId = $this->parameters()->get('target_id', 0, new PapayaFilterInteger(0));
      if ($this->_targetId > 0) {
        $this->reference()->load(
          array('source_id' => $this->_pageId, 'target_id' => $this->_targetId)
        );
      }
    }
  }

  /**
   * Getter/Setter for commands, define commands on implicit create.
   *
   * @param PapayaUiControlCommandController $commands
   * @return \PapayaUiControlCommandController
   */
  public function commands(PapayaUiControlCommandController $commands = NULL) {
    if (isset($commands)) {
      $this->_commands = $commands;
    } elseif (is_null($this->_commands)) {
      $commands = new PapayaUiControlCommandController('cmd', 'dependency_show');
      $commands->owner($this);
      $commands['dependency_show'] = new PapayaAdministrationPagesDependencyCommandChange();
      $commands['dependency_delete'] = new PapayaAdministrationPagesDependencyCommandDelete();
      $commands['reference_change'] = new PapayaAdministrationPagesReferenceCommandChange();
      $commands['reference_delete'] = new PapayaAdministrationPagesReferenceCommandDelete();
      $this->_commands = $commands;
    }
    return $this->_commands;
  }

  /**
  * Getter/Setter for the menu (action/command buttons)
  *
  * @param PapayaUiToolbar $menu
  * @return PapayaUiToolbar
  */
  public function menu(PapayaUiToolbar $menu = NULL) {
    if (isset($menu)) {
      $this->_menu = $menu;
    } elseif (is_null($this->_menu)) {
      $this->_menu = new PapayaUiToolbar();
    }
    return $this->_menu;
  }

  /**
  * Append buttons to menu/toolbar depending on the current status.
  */
  private function appendButtons() {
    if (in_array($this->parameters()->get('cmd'), array('reference_change', 'reference_delete'))) {
      $this->menu()->elements[] = $button = new PapayaUiToolbarButton();
      $button->image = 'status-page-modified';
      $button->caption = new PapayaUiStringTranslated('Edit dependency');
      $button->reference->setParameters(
        array('cmd' => 'dependency_change', 'page_id' => $this->_pageId),
        $this->parameterGroup()
      );
    }
    if ($this->dependency()->id > 0) {
      $this->menu()->elements[] = $button = new PapayaUiToolbarButton();
      $button->image = 'actions-page-delete';
      $button->caption = new PapayaUiStringTranslated('Delete dependency');
      $button->reference->setParameters(
        array('cmd' => 'dependency_delete', 'page_id' => $this->_pageId),
        $this->parameterGroup()
      );
    }
    $this->menu()->elements[] = new PapayaUiToolbarSeparator();
    $this->menu()->elements[] = $button = new PapayaUiToolbarButton();
    $button->image = 'actions-link-add';
    $button->caption = new PapayaUiStringTranslated('Add reference');
    $button->reference->setParameters(
      array('cmd' => 'reference_change', 'page_id' => $this->_pageId, 'target_id' => 0),
      $this->parameterGroup()
    );
    if ($this->reference()->sourceId > 0 && $this->reference()->targetId > 0) {
      $this->menu()->elements[] = $button = new PapayaUiToolbarButton();
      $button->image = 'actions-link-delete';
      $button->caption = new PapayaUiStringTranslated('Delete reference');
      $button->reference->setParameters(
        array(
          'cmd' => 'reference_delete',
          'page_id' => $this->_pageId,
          'target_id' => $this->reference()->sourceId == $this->_pageId
             ? $this->reference()->targetId : $this->reference()->sourceId
        ),
        $this->parameterGroup()
      );
    }
  }

  /**
  * Getter/Setter for the dependencies listview.
  *
  * @param PapayaAdministrationPagesDependencyListview $listview
  * @return PapayaAdministrationPagesDependencyListview
  */
  public function listview(PapayaAdministrationPagesDependencyListview $listview = NULL) {
    if (isset($listview)) {
      $this->_listview = $listview;
    } elseif (is_null($this->_listview)) {
      $this->_listview = new PapayaAdministrationPagesDependencyListview(
        $this->getOriginId(),
        $this->getPageId(),
        $this->dependencies(),
        $this->references(),
        $this->synchronizations()
      );
    }
    return $this->_listview;
  }

  /**
  * Getter/Setter for the synchronizations list
  *
  * @param PapayaAdministrationPagesDependencySynchronizations $synchronizations
  * @return PapayaAdministrationPagesDependencySynchronizations
  */
  public function synchronizations(
    PapayaAdministrationPagesDependencySynchronizations $synchronizations = NULL
  ) {
    if (isset($synchronizations)) {
      $this->_synchronizations = $synchronizations;
    }
    if (is_null($this->_synchronizations)) {
      $this->_synchronizations = new PapayaAdministrationPagesDependencySynchronizations();
    }
    return $this->_synchronizations;
  }
}