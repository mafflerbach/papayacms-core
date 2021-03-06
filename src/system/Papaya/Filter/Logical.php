<?php
/**
* Abstract filter class implementing logical links between other Filters
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
* @subpackage Filter
* @version $Id: Logical.php 39526 2014-03-06 10:34:46Z weinert $
*/

/**
* Abstract filter class implementing logical links between other Filters
*
* You can create this class with two or more subfilters classes, these filters are linked
* depending on the concrete implementation of the child classes.
*
* @package Papaya-Library
* @subpackage Filter
*/
abstract class PapayaFilterLogical implements PapayaFilter {

  /**
  * Filter list
  * @var array(PapayaFilter)
  */
  protected $_filters = array();

  /**
   * Construct object and initialize subfilter objects
   *
   * The constructor needs at least two filters
   *
   * @internal param \PapayaFilter $filterOne
   * @internal param \PapayaFilter $filterTwo
   */
  public function __construct() {
    $this->_setFilters(func_get_args());
  }

  /**
   * Check subfilters and save them in a protected property
   *
   * @param array(PapayaFilter) $filters
   * @throws InvalidArgumentException
   * @return void
   */
  protected function _setFilters($filters) {
    if (is_array($filters) &&
        count($filters) > 1) {
      foreach ($filters as $filter) {
        if ($filter instanceof PapayaFilter) {
          $this->_filters[] = $filter;
        } elseif (is_scalar($filter)) {
          $this->_filters[] = new PapayaFilterEquals($filter);
        } else {
          throw new InvalidArgumentException(
            sprintf(
              'Only PapayaFilter classes expected: "%s" found.',
              is_object($filter) ? get_class($filter) : gettype($filter)
            )
          );
        }
      }
    } else {
      throw new InvalidArgumentException(
        'PapayaFilter needs at least two other PapayaFilter classes.'
      );
    }
  }
}