<?php
require_once(dirname(__FILE__).'/../../../../bootstrap.php');

class PapayaFilterFileNameTest extends PapayaTestCase {

  /**
   * @covers PapayaFilterFileName
   */
  public function testFilter() {
    $filter = new PapayaFilterFileName();
    $this->assertTrue($filter->validate('/foo/bar'));
  }
}