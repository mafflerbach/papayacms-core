<?php
require_once(dirname(__FILE__).'/../../../../../../../bootstrap.php');

class PapayaFilterFactoryProfileIsCssSizeTest extends PapayaTestCase {

  /**
   * @covers PapayaFilterFactoryProfileIsCssSize::getFilter
   * @dataProvider provideCssSizes
   */
  public function testGetFilterExpectTrue($size) {
    $profile = new PapayaFilterFactoryProfileIsCssSize();
    $this->assertTrue($profile->getFilter()->validate($size));
  }

  /**
   * @covers PapayaFilterFactoryProfileIsCssSize::getFilter
   */
  public function testGetFilterExpectException() {
    $profile = new PapayaFilterFactoryProfileIsCssSize();
    $this->setExpectedException('PapayaFilterException');
    $profile->getFilter()->validate('foo');
  }

  public static function provideCssSizes() {
    return array(
      array('0'),
      array('42px'),
      array('21.42em'),
      array('42%'),
      array('42pt'),
    );
  }
}
