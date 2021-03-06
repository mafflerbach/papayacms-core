<?php
/**
* Define a series of class constants for options, needed by different content objects.
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
* @version $Id: Options.php 36023 2011-08-03 17:22:28Z weinert $
*/

/**
* Define a series of class constants for options, needed by different content objects.
*
* @package Papaya-Library
* @subpackage Content
*/
interface PapayaContentOptions {

  /**
  * Permission inheritance, use only own permission defined for this page
  * @var integer
  */
  const INHERIT_PERMISSIONS_OWN = 1;
  /**
  * Permission inheritance, use only inherited permission for this page
  * @var integer
  */
  const INHERIT_PERMISSIONS_PARENT = 2;
  /**
  * Permission inheritance, add own permission of this page to inherited ones
  * @var integer
  */
  const INHERIT_PERMISSIONS_ADDITIONAL = 3;

  /**
  * Cache/Expires mode, use system option value
  * @var integer
  */
  const CACHE_SYSTEM = 1;
  /**
  * Cache/Expires mode, use special value defined for this page
  * @var integer
  */
  const CACHE_INDIVIDUAL = 2;
  /**
  * Cache/Expires mode, no caching
  * @var integer
  */
  const CACHE_NONE = 0;

  /**
  * Url scheme, use system option PAPAYA_DEFAULT_PROTOCOL
  * @var integer
  */
  const SCHEME_SYSTEM = 0;
  /**
  * Url scheme, allow only http
  * @var integer
  */
  const SCHEME_HTTP = 1;
  /**
  * Url scheme, allow only https
  * @var integer
  */
  const SCHEME_HTTPS = 2;

}