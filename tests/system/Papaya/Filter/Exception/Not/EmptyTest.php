<?php
require_once(dirname(__FILE__).'/../../../../../bootstrap.php');

class PapayaFilterExceptionNotEmptyTest extends PapayaTestCase {

  /**
  * @covers PapayaFilterExceptionNotEmpty::__construct
  */
  public function testConstructor() {
    $e = new PapayaFilterExceptionNotEmpty('42');
    $this->assertEquals(
      'Value is to not empty. Got "42".',
      $e->getMessage()
    );
  }
}
