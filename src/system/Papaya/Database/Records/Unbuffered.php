<?php

abstract class PapayaDatabaseRecordsUnbuffered
  extends PapayaObject
  implements PapayaDatabaseInterfaceAccess, IteratorAggregate, Countable {
  /**
  * Stored database access object
  * @var PapayaDatabaseAccess
  */
  private $_databaseAccessObject = NULL;

  /**
  * The database result of the last loading query.
  *
  * @var PapayaDatabaseResult
  */
  private $_databaseResult = NULL;

  /**
  * Mapping object
  *
  * @var PapayaDatabaseInterfaceMapping
  */
  private $_mapping = NULL;

  /**
  * Order object
  *
  * @var PapayaDatabaseInterfaceOrder
  */
  private $_orderBy = NULL;

  /**
  * An array of property to field mappings.
  *
  * @var array(string=>string)
  */
  protected $_fields = array();

  /**
  * An array of order by properties and directions.
  *
  * @var array(string=>integer)|NULL
  */
  protected $_orderByProperties = NULL;

  /**
  * An array of order by fields and directions
  */
  protected $_orderByFields = NULL;

  /**
  * Table name for the default loading logic.
  *
  * @var string
  */
  protected $_tableName = '';

  /**
  * Add table prefix from global configuration
  *
  * @var boolean
  */
  protected $_useTablePrefix = TRUE;

  /**
   * A class for individual records, used by {@see self::getItem()}
   *
   * @var string
   */
  protected $_itemClass = NULL;


  /**
   * Load records from the defined table. This method can be overloaded to define an own sql.
   *
   * @param mixed $filter If it is an scalar the value will be used for the id property.
   * @param integer|NULL $limit
   * @param integer|NULL $offset
   * @return bool
   */
  public function load($filter = NULL, $limit = NULL, $offset = NULL) {
    $fields = implode(', ', $this->mapping()->getFields());
    $sql = "SELECT $fields FROM %s";
    $sql .= PapayaUtilString::escapeForPrintf(
      $this->_compileCondition($filter).$this->_compileOrderBy()
    );
    $parameters = array(
      $this->getDatabaseAccess()->getTableName($this->_tableName, $this->_useTablePrefix)
    );
    return $this->_loadSql($sql, $parameters, $limit, $offset);
  }

  /**
   * Execute the sql query and store the result object
   *
   * @param string $sql
   * @param array $parameters
   * @param integer|NULL $limit
   * @param integer|NULL $offset
   * @return bool
   */
  protected function _loadSql($sql, $parameters, $limit = NULL, $offset = NULL) {
    $this->_databaseResult = NULL;
    $databaseAccess = $this->getDatabaseAccess();
    $databaseResult = $databaseAccess->queryFmt($sql, $parameters, $limit, $offset);
    if ($databaseResult instanceof PapayaDatabaseResult) {
      $this->_databaseResult = $databaseResult;
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Create a filter condition object attached to this database accesss and mapping
   * @return PapayaDatabaseConditionRoot
   */
  public function createFilter() {
    return new PapayaDatabaseConditionRoot($this, $this->mapping());
  }

  /**
  * Compile a sql condition specified by the filter. Prefix it, if it is not empty.
  *
  * @param mixed $filter
  * @param string $prefix
  * @return string
  */
  protected function _compileCondition($filter, $prefix = " WHERE ") {
    if (isset($filter)) {
      if ($filter instanceof PapayaDatabaseConditionElement) {
        $condition = $filter->getSql();
        return empty($condition) ? '' : $prefix.$condition;
      } else {
        if (!is_array($filter)) {
          $filter = array('id' => $filter);
        }
        $generator = new PapayaDatabaseConditionGenerator($this, $this->mapping());
        $condition = $generator->fromArray($filter)->getSql(TRUE);
        return empty($condition) ? '' : $prefix.$condition;
      }
    }
    return '';
  }

  /**
  * Convert the order by clause defined by the orderBy() return value into an sql string.
  *
  * @return string
  */
  protected function _compileOrderBy() {
    $result = '';
    if ($orderBy = $this->orderBy()) {
      $result = (string)$orderBy;
    }
    return empty($result) ? '' : " ORDER BY ".$result;
  }

  /**
  * Getter/Setter for the mapping subobject. This is used to convert the property values into
  * a database record and back.
  *
  * @param PapayaDatabaseInterfaceMapping $mapping
  * @return PapayaDatabaseInterfaceMapping
  */
  public function mapping(PapayaDatabaseInterfaceMapping $mapping = NULL) {
    if (isset($mapping)) {
      $this->_mapping = $mapping;
    } elseif (is_null($this->_mapping)) {
      $this->_mapping = $this->_createMapping();
    }
    return $this->_mapping;
  }

  /**
  * Create a standard mapping object for the property $_fields.
  *
  * @return PapayaDatabaseRecordMapping
  */
  protected function _createMapping() {
    return new PapayaDatabaseRecordMapping($this->_fields);
  }

  /**
  * Getter/Setter for the order subobject. This is used to define a order by clause for the
  * select statement. It is possible that the method return FALSE, indicating that
  * here should be no order by clause.
  *
  * @param PapayaDatabaseInterfaceOrder $orderBy
  * @return PapayaDatabaseInterfaceOrder|FALSE
  */
  public function orderBy(PapayaDatabaseInterfaceOrder $orderBy = NULL) {
    if (isset($orderBy)) {
      $this->_orderBy = $orderBy;
    } elseif (is_null($this->_orderBy)) {
      $this->_orderBy = $this->_createOrderBy();
    }
    return $this->_orderBy;
  }

  /**
  * Create a standard order object using the property $_orderByFields. If the property is empty
  * the method will return FALSE.
  *
  * @return PapayaDatabaseInterfaceOrder|FALSE
  */
  protected function _createOrderBy() {
    if (empty($this->_orderByProperties) && empty($this->_orderByFields)) {
      return FALSE;
    }
    $result = new PapayaDatabaseRecordOrderGroup();
    if (!empty($this->_orderByProperties)) {
      $result->add(
        new PapayaDatabaseRecordOrderByProperties($this->_orderByProperties, $this->mapping())
      );
    }
    if (!empty($this->_orderByFields)) {
      $result->add(
        new PapayaDatabaseRecordOrderByFields($this->_orderByFields)
      );
    }
    return $result;
  }

  /**
  * Return the current count of records in the internal buffer
  *
  * @return integer
  */
  public function count() {
    if ($databaseResult = $this->databaseResult()) {
      return $databaseResult->count();
    }
    return 0;
  }

  /**
  * Fetch the absolute count from the last database result. If the result was limited, this
  * number can be different from the record count.
  *
  * @return integer
  */
  public function absCount() {
    if ($databaseResult = $this->databaseResult()) {
      return $databaseResult->absCount();
    } else {
      return $this->count();
    }
  }

  /**
  * Return loaded records as array
  *
  * @return array
  */
  public function toArray() {
    return iterator_to_array($this);
  }

  /**
  * IteratorAggregate interface, return and iterator for the database result
  *
  * @return Iterator
  */
  public function getIterator() {
    return $this->getResultIterator();
  }

  /**
  * Iterator for the curent database result, includes mapping callback
  *
  * @return Iterator
  */
  protected function getResultIterator() {
    if (!($this->databaseResult() instanceof PapayaDatabaseResult)) {
      return new EmptyIterator();
    }
    $iterator = new PapayaDatabaseResultIterator($this->databaseResult());
    $mapping = $this->mapping();
    $iterator->setMapping(
      $mapping instanceof PapayaDatabaseRecordMappingCache
        ? $mapping
        : new PapayaDatabaseRecordMappingCache($mapping)
    );
    return $iterator;
  }

  /**
  * Getter/Setter for the current database result object
  *
  * @param PapayaDatabaseResult $databaseResult
  * @return NULL|PapayaDatabaseResult
  */
  public function databaseResult(PapayaDatabaseResult $databaseResult = NULL) {
    if (isset($databaseResult)) {
      $this->_databaseResult = $databaseResult;
    }
    return $this->_databaseResult;
  }

  /**
   * Set database access object
   *
   * @param PapayaDatabaseAccess $databaseAccessObject
   */
  public function setDatabaseAccess(PapayaDatabaseAccess $databaseAccessObject) {
    $this->_databaseAccessObject = $databaseAccessObject;
  }

  /**
  * Get database access object
  *
  * @return PapayaDatabaseAccess
  */
  public function getDatabaseAccess() {
    if (!isset($this->_databaseAccessObject)) {
      $this->_databaseAccessObject = $this->papaya()->database->createDatabaseAccess($this);
    }
    return $this->_databaseAccessObject;
  }

  /**
   * Protected method to create an item class, you can overload this method or just set
   * the $_itemClass property.
   *
   * @return PapayaDatabaseRecord
   * @throws LogicException
   */
  protected function _createItem() {
    if (isset($this->_itemClass)) {
      $class = $this->_itemClass;
      return new $class;
    } else {
      throw new \LogicException('No item class for records defined');
    }
  }

  /**
   * Get a record item object. If the filter is not empty 'load()' will be called on
   * the $item object.
   *
   * @param null $filter
   * @return PapayaDatabaseRecord
   */
  public function getItem($filter = NULL) {
    $item = $this->_createItem();
    if (!empty($filter)) {
      $item->load($filter);
    }
    return $item;
  }
}