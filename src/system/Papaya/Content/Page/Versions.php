<?php
/**
* Provide data encapsulation for the content page version list.
*
* The list does not contain all detail data, it is for list outputs etc. To get the full data
* use {@see PapayaContentPageTranslation}.
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
* @subpackage Content
* @version $Id: Versions.php 36028 2011-08-04 10:10:14Z weinert $
*/

/**
* Provide data encapsulation for the content page version list. The versions are created if
* a page is published. They are not changeable.
*
* The list does not contain all detail data, it is for list outputs etc. To get the full data
* use {@see PapayaContentPageVersion}.
*
* @package Papaya-Library
* @subpackage Content
*/
class PapayaContentPageVersions extends PapayaDatabaseObjectList {

  /**
  * Map field names to value identfiers
  *
  * @var array
  */
  protected $_fieldMapping = array(
    'version_id' => 'id',
    'version_time' => 'created',
    'version_author_id' => 'owner',
    'version_message' => 'message',
    'topic_change_level' => 'level',
    'topic_id' => 'page_id'
  );

  /**
  * Version table name
  *
  * @var string
  */
  protected $_versionsTableName = PapayaContentTables::PAGE_VERSIONS;

  /**
  * Load version list informations
  *
  * @param integer $pageId
  * @param NULL|integer $limit maximum records returned
  * @param NULL|integer $offset start offset for limited results
  * @return boolean
  */
  public function load($pageId, $limit = NULL, $offset = NULL) {
    $sql = "SELECT version_id, version_time, version_author_id, version_message,
                   topic_change_level, topic_id
              FROM %s
             WHERE topic_id = %d
             ORDER BY version_time DESC";
    $parameters = array(
      $this->databaseGetTableName($this->_versionsTableName),
      (int)$pageId
    );
    return $this->_loadRecords($sql, $parameters, 'version_id', $limit, $offset);
  }

  /**
  * Create a new version record object and load the specified version data
  *
  * @param integer $versionId
  * @return PapayaContentPageVersion|NULL
  */
  public function getVersion($versionId) {
    $result = new PapayaContentPageVersion();
    $result->setDatabaseAccess($this->getDatabaseAccess());
    $result->load($versionId);
    return $result;
  }
}