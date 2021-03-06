<?php
require_once(dirname(__FILE__).'/../../../../bootstrap.php');

class PapayaFilterLogicalOrTest extends PapayaTestCase {

  /**
  * @covers PapayaFilterLogicalOr::validate
  */
  public function testValidateExpectingTrueFromFirstSubFilter() {
    $subFilterOne = $this->getMock('PapayaFilter', array('validate', 'filter'));
    $subFilterOne
      ->expects($this->once())
      ->method('validate')
      ->with($this->equalTo('foo'))
      ->will($this->returnValue(TRUE));
    $subFilterTwo = $this->getMock('PapayaFilter', array('validate', 'filter'));
    $subFilterTwo
      ->expects($this->never())
      ->method('validate');
    $filter = new PapayaFilterLogicalOr($subFilterOne, $subFilterTwo);
    $this->assertTrue(
      $filter->validate('foo')
    );
  }

  /**
  * @covers PapayaFilterLogicalOr::validate
  */
  public function testValidateExpectingTrueFromSecondSubFilter() {
    $subFilterOne = $this->getMock('PapayaFilter', array('validate', 'filter'));
    $subFilterOne
      ->expects($this->once())
      ->method('validate')
      ->with($this->equalTo('foo'))
      ->will($this->returnCallback(array($this, 'callbackThrowFilterException')));
    $subFilterTwo = $this->getMock('PapayaFilter', array('validate', 'filter'));
    $subFilterTwo
      ->expects($this->once())
      ->method('validate')
      ->with($this->equalTo('foo'))
      ->will($this->returnValue(TRUE));
    $filter = new PapayaFilterLogicalOr($subFilterOne, $subFilterTwo);
    $this->assertTrue(
      $filter->validate('foo')
    );
  }

  /**
  * @covers PapayaFilterLogicalOr::validate
  */
  public function testValidateExpectingException() {
    $subFilterOne = $this->getMock('PapayaFilter', array('validate', 'filter'));
    $subFilterOne
      ->expects($this->once())
      ->method('validate')
      ->with($this->equalTo('foo'))
      ->will($this->returnCallback(array($this, 'callbackThrowFilterException')));
    $subFilterTwo = $this->getMock('PapayaFilter', array('validate', 'filter'));
    $subFilterTwo
      ->expects($this->once())
      ->method('validate')
      ->with($this->equalTo('foo'))
      ->will($this->returnCallback(array($this, 'callbackThrowFilterException')));
    $filter = new PapayaFilterLogicalOr($subFilterOne, $subFilterTwo);
    $this->setExpectedException('PapayaFilterException');
    $filter->validate('foo');
  }

  /**
  * @covers PapayaFilterLogicalOr::filter
  */
  public function testFilter() {
    $subFilterOne = $this->getMock('PapayaFilter', array('validate', 'filter'));
    $subFilterOne
      ->expects($this->once())
      ->method('filter')
      ->with($this->equalTo('foo'))
      ->will($this->returnValue('foo'));
    $subFilterTwo = $this->getMock('PapayaFilter', array('validate', 'filter'));
    $subFilterTwo
      ->expects($this->never())
      ->method('filter');
    $filter = new PapayaFilterLogicalOr($subFilterOne, $subFilterTwo);
    $this->assertEquals(
      'foo',
      $filter->filter('foo')
    );
  }

  /**
  * @covers PapayaFilterLogicalOr::filter
  */
  public function testFilterUsingSecondFilter() {
    $subFilterOne = $this->getMock('PapayaFilter', array('validate', 'filter'));
    $subFilterOne
      ->expects($this->once())
      ->method('filter')
      ->with($this->equalTo('foo'))
      ->will($this->returnValue(NULL));
    $subFilterTwo = $this->getMock('PapayaFilter', array('validate', 'filter'));
    $subFilterTwo
      ->expects($this->once())
      ->method('filter')
      ->with($this->equalTo('foo'))
      ->will($this->returnValue('foo'));
    $filter = new PapayaFilterLogicalOr($subFilterOne, $subFilterTwo);
    $this->assertEquals(
      'foo',
      $filter->filter('foo')
    );
  }

  /**
  * @covers PapayaFilterLogicalOr::filter
  */
  public function testFilterExpectingNull() {
    $subFilterOne = $this->getMock('PapayaFilter', array('validate', 'filter'));
    $subFilterOne
      ->expects($this->once())
      ->method('filter')
      ->with($this->equalTo('foo'))
      ->will($this->returnValue(NULL));
    $subFilterTwo = $this->getMock('PapayaFilter', array('validate', 'filter'));
    $subFilterTwo
      ->expects($this->once())
      ->method('filter')
      ->with($this->equalTo('foo'))
      ->will($this->returnValue(NULL));
    $filter = new PapayaFilterLogicalOr($subFilterOne, $subFilterTwo);
    $this->assertNull(
      $filter->filter('foo')
    );
  }

  /*************************************
  * Callbacks
  *************************************/

  public function callbackThrowFilterException() {
    throw $this->getMock('PapayaFilterException', array(), array('Test Exception'));
  }
}